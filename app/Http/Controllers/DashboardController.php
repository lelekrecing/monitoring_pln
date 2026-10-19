<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; 
use Illuminate\Http\Request;
use App\Models\Sheet;
use App\Models\SheetData;
use App\Models\Upload; 
use App\Models\Khs; 

class DashboardController extends Controller
{
    // ==========================
    // Dashboard User
    // ==========================
    public function userIndex()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $sidebarMenu = $this->buildSidebarMenu();
        $progressChart = $this->getProgressChartData();
        $kuotaVendorChart = $this->getKuotaVendorChartData();

        $resMfsSheetId      = $this->findSheetIdByNama('RES MFS');
        $detailKr2026SheetId = $this->findSheetIdByNama('Detail KR', '2026');
        $pelangganList      = $this->getNamaPelangganList();

        return view('dashboard', compact(
            'sidebarMenu',
            'progressChart',
            'kuotaVendorChart',
            'resMfsSheetId',
            'detailKr2026SheetId',
            'pelangganList'
        ));
    }

    private function buildSidebarMenu()
    {
        return [
            'Monitoring KHS' => [
                'type'   => 'monitoring-khs',
                'groups' => $this->buildKhsMenu(),
            ],
            'Monitoring Tiang' => [
                'type'  => 'flat',
                'items' => $this->filterAllowedSheets('tiang', [
                    'RPB per SPB',
                    'TA per SPB',
                    'Maxima per SPB',
                    'WIKA per SPB',
                    'Total Vendor',
                    'Rekap KR',
                ]),
            ],
            'Monitoring Pelanggan' => [
                'type'  => 'flat',
                'items' => $this->filterAllowedSheets('pelanggan', [
                    'Pelanggan 2026',
                ]),
            ],
        ];
    }

    private function filterAllowedSheets(string $jenisMonitoring, array $allowedNames)
    {
        $sheets = Sheet::where('jenis_monitoring', $jenisMonitoring)
            ->where('is_active', 1)
            ->orderBy('urutan')
            ->get();

        $allowedLower = array_map(fn ($n) => mb_strtolower(trim($n)), $allowedNames);

        return $sheets
            ->filter(function ($sheet) use ($allowedLower) {
                return in_array(mb_strtolower(trim($sheet->nama_sheet ?? '')), $allowedLower, true);
            })
            ->sortBy(function ($sheet) use ($allowedLower) {
                $pos = array_search(mb_strtolower(trim($sheet->nama_sheet ?? '')), $allowedLower, true);
                return $pos === false ? 999 : $pos;
            })
            ->values()
            ->all();
    }

    private function buildKhsMenu()
    {
        $sheets = Sheet::where('jenis_monitoring', 'khs')
            ->where('is_active', 1)
            ->orderBy('kategori')
            ->orderBy('tahun')
            ->orderBy('urutan')
            ->get();

        $menu = [];
        $khsTambahan = ['AMR', 'GANTER', 'KR PBPD'];

        foreach ($sheets as $sheet) {
            $kategori  = strtoupper(trim($sheet->kategori ?? ''));
            $namaSheet = strtoupper(trim($sheet->nama_sheet ?? ''));

            if ($kategori === 'BKJ' || $namaSheet === 'BKJ') continue;
            if ($kategori === 'SHEET1' || str_starts_with($namaSheet, 'SHEET1')) continue;
            if ($namaSheet === 'REKAP') continue;

            if (str_starts_with($kategori, 'RES')) {
                $menu['Reservasi']['type'] = 'flat';
                $menu['Reservasi']['items'][] = $sheet;
                continue;
            }

            if (str_starts_with($kategori, 'REKAP')) {
                $menu['Rekap']['type'] = 'flat';
                $menu['Rekap']['items'][] = $sheet;
                continue;
            }

            $isKhsTambahan = false;
            foreach ($khsTambahan as $prefix) {
                if ($namaSheet === $prefix || str_starts_with($namaSheet, $prefix . ' ')) {
                    $isKhsTambahan = true;
                    break;
                }
            }

            if (str_starts_with($kategori, 'KHS') || $isKhsTambahan) {
                $subKategori = $sheet->kategori ?: $sheet->nama_sheet;
                $menu['KHS']['type'] = 'nested';

                if (str_starts_with($subKategori, 'KHS PEMBESIAN')) {
                    $menu['KHS']['children']['KHS PEMBESIAN'][$subKategori][] = $sheet;
                } else {
                    $menu['KHS']['children'][$subKategori][] = $sheet;
                }
                continue;
            }

            $label = $sheet->kategori ?: 'Lainnya';
            $menu[$label]['type'] = 'flat';
            $menu[$label]['items'][] = $sheet;
        }

        $prioritas = [
            'KHS'                             => 0,
            'Reservasi'                       => 1,
            'Monitoring PBJ'                  => 2,
            'Monitoring Permohonan Pelanggan' => 3,
            'Monitoring Tiang'                => 4,
            'Detail KR'                       => 5,
        ];
        uksort($menu, function ($a, $b) use ($prioritas) {
            if ($a === 'Rekap' || $b === 'Rekap') {
                if ($a === $b) return 0;
                return $a === 'Rekap' ? 1 : -1;
            }
            $posA = $prioritas[$a] ?? 99;
            $posB = $prioritas[$b] ?? 99;
            if ($posA === $posB) return strcmp($a, $b);
            return $posA <=> $posB;
        });

        return $menu;
    }

    // ====================================================================================
    // Grafik Progress per Vendor
    // ====================================================================================
    private function getProgressChartData()
    {
        $jenisMonitoringDetailKr = 'khs';

        $sheetsDitemukan = Sheet::whereRaw('LOWER(nama_sheet) LIKE ?', ['%detail kr%'])
            ->where('jenis_monitoring', $jenisMonitoringDetailKr)
            ->where('is_active', 1)
            ->get();

        $sheets2026 = $sheetsDitemukan->filter(function ($s) {
            return (string) $s->tahun === '2026' || str_contains((string) $s->nama_sheet, '2026');
        });

        if ($sheets2026->isNotEmpty()) {
            $sheetsDitemukan = $sheets2026;
        }

        $sheetAktif = $sheetsDitemukan->sortByDesc(fn ($s) => [$s->created_at, $s->id])->first();
        $sheets = $sheetAktif ? collect([$sheetAktif]) : collect();

        $vendorAgg   = [];
        $vendorLabel = []; 
        $vendorOrder = []; 

        foreach ($sheets as $sheet) {
            $rows = SheetData::where('sheet_id', $sheet->id)->orderBy('row_number')->get();
            if ($rows->isEmpty()) continue;

            $vendorCol = null;
            $lokasiCol = null;
            $nilaiKontrakCol = null;
            $statusCol = null;
            $kontrakRinciCol = null;
            $prkCol = null;
            $headerRowNumber = null;

            $foundVendor = null;
            $foundLokasi = null;
            $foundNilaiKontrak = null;
            $foundStatus = null;
            $foundKontrakRinci = null;
            $foundPrk = null;

            foreach ($rows->take(15) as $row) { 
                $cells = json_decode($row->row_data, true);
                if (!is_array($cells)) continue;

                $filledCellsCount = count(array_filter($cells, fn($c) => trim((string)$c) !== ''));
                if ($filledCellsCount <= 2) {
                    $foundVendor = null;
                    $foundLokasi = null;
                    $foundNilaiKontrak = null;
                    $foundStatus = null;
                    $foundKontrakRinci = null;
                    $foundPrk = null;
                    continue;
                }

                foreach ($cells as $colIndex => $cellValue) {
                    $val = strtolower(trim((string) $cellValue));
                    if ($val === '') continue;

                    if ($foundVendor === null && (str_contains($val, 'vendor') || str_contains($val, 'penyedia') || str_contains($val, 'pelaksana'))) {
                        // PROTEKSI MUTLAK ANTI-JUDUL DOKUMEN: Hindari mengunci teks "Monitoring Pagu Vendor" dll
                        if (str_contains($val, 'monitoring') || str_contains($val, 'pagu') || str_contains($val, 'laporan') || strlen($val) > 40) {
                            continue;
                        }
                        $foundVendor = $colIndex;
                        continue;
                    }

                    if ($foundLokasi === null && (str_contains($val, 'lokasi') || str_contains($val, 'uraian') || str_contains($val, 'pekerjaan'))) {
                        $foundLokasi = $colIndex;
                        continue;
                    }

                    if ($foundNilaiKontrak === null && (str_contains($val, 'nilai') || str_contains($val, 'harga')) && (str_contains($val, 'kontrak') || str_contains($val, 'spb'))) {
                        $foundNilaiKontrak = $colIndex;
                        continue;
                    }

                    if ($foundKontrakRinci === null && str_contains($val, 'kontrak') && !str_contains($val, 'nilai') && !str_contains($val, 'harga')) {
                        $foundKontrakRinci = $colIndex;
                        continue;
                    }

                    if ($foundPrk === null && preg_match('/\bprk\b/', $val)) {
                        $foundPrk = $colIndex;
                        continue;
                    }

                    if ($foundStatus === null && (str_contains($val, 'status') || str_contains($val, 'progres') || str_contains($val, 'progress') || str_contains($val, '%'))) {
                        $foundStatus = $colIndex;
                    }
                }

                if ($foundVendor !== null && $foundLokasi !== null && $foundNilaiKontrak !== null && $foundStatus !== null) {
                    $vendorCol       = $foundVendor;
                    $lokasiCol       = $foundLokasi;
                    $nilaiKontrakCol = $foundNilaiKontrak;
                    $statusCol       = $foundStatus;
                    $kontrakRinciCol = $foundKontrakRinci; 
                    $prkCol          = $foundPrk;          
                    $headerRowNumber = $row->row_number;
                    break;
                }
            }

            if ($headerRowNumber === null) continue;

            foreach ($rows as $row) {
                if ($row->row_number <= $headerRowNumber) continue;

                $cells = json_decode($row->row_data, true);
                if (!is_array($cells)) continue;

                $vendorAsli = trim((string) ($cells[$vendorCol] ?? ''));
                $lokasiRaw  = trim((string) ($cells[$lokasiCol] ?? ''));

                if ($vendorAsli === '' || $lokasiRaw === '') continue;

                $vendorKey = $this->normalizeVendorName($vendorAsli);
                if ($vendorKey === '') continue;

                $nilaiKontrakRaw = $cells[$nilaiKontrakCol] ?? 0;
                $statusRaw       = $cells[$statusCol] ?? 0;

                $nilai  = $this->parseNilaiRupiah($nilaiKontrakRaw);
                $status = $this->parseStatusPersen($statusRaw);

                if (!isset($vendorAgg[$vendorKey])) {
                    $vendorAgg[$vendorKey] = ['paket' => []];
                    $vendorLabel[$vendorKey] = preg_replace('/\s+/', ' ', $vendorAsli);
                    $vendorOrder[] = $vendorKey;
                }

                $kontrakRinciRaw = $kontrakRinciCol !== null ? trim((string) ($cells[$kontrakRinciCol] ?? '')) : '';
                $prkRaw = $prkCol !== null ? trim((string) ($cells[$prkCol] ?? '')) : '';

                $identParts = array_filter([$kontrakRinciRaw, $prkRaw], fn ($v) => $v !== '');
                $kunciPaket = !empty($identParts) ? implode('|', $identParts) : trim((string) $nilaiKontrakRaw);
                if ($kunciPaket === '') {
                    $kunciPaket = '__row_' . $row->row_number;
                }

                $labelPaket = $kontrakRinciRaw !== '' ? $kontrakRinciRaw : ($prkRaw !== '' ? $prkRaw : '');

                if (!isset($vendorAgg[$vendorKey]['paket'][$kunciPaket])) {
                    $vendorAgg[$vendorKey]['paket'][$kunciPaket] = [
                        'label'    => $labelPaket, 
                        'nilai'    => $nilai, 
                        'statuses' => [],
                        'lokasi'   => [],
                    ];
                }

                $vendorAgg[$vendorKey]['paket'][$kunciPaket]['statuses'][] = $status;
                $vendorAgg[$vendorKey]['paket'][$kunciPaket]['lokasi'][]   = $lokasiRaw;
            }
        }

        $vendors         = [];
        $progressPersen  = []; 
        $selesaiPersen   = []; 
        $lokasiPerVendor = [];
        $detailPerVendor = []; 

        foreach ($vendorOrder as $vendorKey) {
            $namaTampilan = $vendorLabel[$vendorKey];
            $totalKr           = 0;
            $selesaiKr         = 0;
            $jumlahStatusPaket = 0.0; 
            $lokasiDetailSemua = []; 
            $paketDetail       = []; 
            $paketKe           = 0;  

            foreach ($vendorAgg[$vendorKey]['paket'] as $paket) {
                $totalKr++;
                $paketKe++;

                $jumlahStatus = 0.0;
                $isSelesai    = count($paket['statuses']) > 0;

                foreach ($paket['statuses'] as $s) {
                    $jumlahStatus += $s;
                    if ($s < 99.995) $isSelesai = false;
                }

                $statusRataPaket = count($paket['statuses']) > 0 ? $jumlahStatus / count($paket['statuses']) : 0.0;
                $jumlahStatusPaket += $statusRataPaket;

                if ($isSelesai) $selesaiKr++;

                foreach ($paket['lokasi'] as $idxLok => $lok) {
                    $lokasiDetailSemua[] = [
                        'lokasi' => $lok,
                        'status' => $paket['statuses'][$idxLok] ?? $statusRataPaket,
                    ];
                }

                $paketDetail[] = [
                    'paket'   => $paket['label'] !== '' ? $paket['label'] : ('Paket ' . $paketKe),
                    'nilai'   => round($paket['nilai'], 2),
                    'lokasi'  => array_values($paket['lokasi']),
                    'selesai' => $isSelesai,
                ];
            }

            $progressPersenV = $totalKr > 0 ? round($jumlahStatusPaket / $totalKr, 2) : 0.0;
            $selesaiPersenV  = $totalKr > 0 ? round(($selesaiKr / $totalKr) * 100, 2) : 0.0;

            $lokasiNamaSaja = array_map(fn ($l) => $l['lokasi'], $lokasiDetailSemua);

            $vendors[]          = $namaTampilan;
            $progressPersen[]   = $progressPersenV;
            $selesaiPersen[]    = $selesaiPersenV;
            $lokasiPerVendor[]  = array_values($lokasiNamaSaja);

            $detailPerVendor[] = [
                'vendor'          => $namaTampilan,
                'total_kr'        => $totalKr,
                'paket_detail'    => $paketDetail, 
                'progress_persen' => $progressPersenV,
                'selesai_persen'  => $selesaiPersenV,
                'selesai_kr'      => $selesaiKr,
                'lokasi'          => array_values($lokasiNamaSaja),
                'lokasi_detail'   => array_values($lokasiDetailSemua), 
            ];
        }

        return [
            'vendors'       => $vendors,
            'progress'      => $progressPersen, 
            'selesai'       => $selesaiPersen,  
            'lokasi_vendor' => $lokasiPerVendor,
            'detail_vendor' => $detailPerVendor,
        ];
    }

    // ==========================
    // Grafik Sisa Kuota Vendor
    // ==========================
    private function getKuotaVendorChartData()
    {
        $sheetsDitemukan = Sheet::whereRaw('LOWER(nama_sheet) LIKE ?', ['%kuota vendor%'])
            ->where('is_active', 1)
            ->get();
            
        $kosong = ['vendors' => [], 'sisa_kuota_persen' => [], 'detail_vendor' => []];

        if ($sheetsDitemukan->isEmpty()) return $kosong;

        $sheets2026 = $sheetsDitemukan->filter(function ($s) {
            return (string) $s->tahun === '2026' || str_contains((string) $s->nama_sheet, '2026');
        });

        if ($sheets2026->isNotEmpty()) {
            $sheetsDitemukan = $sheets2026;
        }

        $sheetAktif = $sheetsDitemukan->sortByDesc(fn ($s) => [$s->created_at, $s->id])->first();
        $rows = SheetData::where('sheet_id', $sheetAktif->id)->orderBy('row_number')->get();

        if ($rows->isEmpty()) return $kosong;

        $vendorCol = null;
        $noKontrakCol = null;
        $nilaiKontrakCol = null;
        $amdCol = null;
        $realisasiCol = null;
        $sisaRpCol = null;
        $sisaPersenCol = null;
        $headerRowNumber = null;

        $foundVendor = null;
        $foundNoKontrak = null;
        $foundNilaiKontrak = null;
        $foundAmd = null;
        $foundRealisasi = null;
        $foundSisaRp = null;
        $foundSisaPersen = null;

        foreach ($rows->take(15) as $row) {
            $cells = json_decode($row->row_data, true);
            if (!is_array($cells)) continue;

            $filledCellsCount = count(array_filter($cells, fn($c) => trim((string)$c) !== ''));
            if ($filledCellsCount <= 2) {
                $foundVendor = null;
                $foundNoKontrak = null;
                $foundNilaiKontrak = null;
                $foundAmd = null;
                $foundRealisasi = null;
                $foundSisaRp = null;
                $foundSisaPersen = null;
                continue;
            }

            foreach ($cells as $colIndex => $cellValue) {
                $val = strtolower(trim((string) $cellValue));
                if ($val === '') continue;

                if ($foundVendor === null && (str_contains($val, 'vendor') || str_contains($val, 'penyedia') || str_contains($val, 'pelaksana'))) {
                    // PROTEKSI MUTLAK ANTI-JUDUL DOKUMEN: Hindari mengunci teks "Monitoring Pagu Vendor" dll
                    if (str_contains($val, 'monitoring') || str_contains($val, 'pagu') || str_contains($val, 'laporan') || strlen($val) > 40) {
                        continue;
                    }
                    $foundVendor = $colIndex;
                    continue;
                }

                if ($foundNoKontrak === null && str_contains($val, 'no') && str_contains($val, 'kontrak') && !str_contains($val, 'nilai')) {
                    $foundNoKontrak = $colIndex;
                    continue;
                }

                if ($foundNilaiKontrak === null && str_contains($val, 'nilai') && str_contains($val, 'kontrak')) {
                    $foundNilaiKontrak = $colIndex;
                    continue;
                }

                if ($foundAmd === null && str_contains($val, 'amd')) {
                    $foundAmd = $colIndex;
                    continue;
                }

                if ($foundRealisasi === null && str_contains($val, 'realisasi')) {
                    $foundRealisasi = $colIndex;
                    continue;
                }

                if (str_contains($val, 'sisa') && str_contains($val, 'kuota')) {
                    if (str_contains($val, '%')) {
                        if ($foundSisaPersen === null) $foundSisaPersen = $colIndex;
                    } else {
                        if ($foundSisaRp === null) $foundSisaRp = $colIndex;
                    }
                    continue;
                }
            }

            if ($foundVendor !== null && $foundSisaPersen !== null) {
                $vendorCol       = $foundVendor;
                $noKontrakCol    = $foundNoKontrak;
                $nilaiKontrakCol = $foundNilaiKontrak;
                $amdCol          = $foundAmd;
                $realisasiCol    = $foundRealisasi;
                $sisaRpCol       = $foundSisaRp;
                $sisaPersenCol   = $foundSisaPersen;
                $headerRowNumber = $row->row_number;
                break;
            }
        }

        if ($headerRowNumber === null) return $kosong;

        $vendors = [];
        $sisaKuotaPersen = [];
        $detailVendor = [];

        foreach ($rows as $row) {
            if ($row->row_number <= $headerRowNumber) continue;

            $cells = json_decode($row->row_data, true);
            if (!is_array($cells)) continue;

            $vendorAsli = trim((string) ($cells[$vendorCol] ?? ''));
            $vendorAsli = preg_replace('/\s+/', ' ', $vendorAsli);

            if ($vendorAsli === '') continue;

            $noKontrak = $noKontrakCol !== null ? trim((string) ($cells[$noKontrakCol] ?? '')) : '';
            $nilaiKontrak = $nilaiKontrakCol !== null ? $this->parseNilaiRupiah($cells[$nilaiKontrakCol] ?? 0) : 0.0;
            $amd1 = $amdCol !== null ? $this->parseNilaiRupiah($cells[$amdCol] ?? 0) : 0.0;
            $realisasi = $realisasiCol !== null ? $this->parseNilaiRupiah($cells[$realisasiCol] ?? 0) : 0.0;
            $sisaRp = $sisaRpCol !== null ? $this->parseNilaiRupiah($cells[$sisaRpCol] ?? 0) : 0.0;
            $sisaPersen = $this->parseStatusPersen($cells[$sisaPersenCol] ?? 0);

            $vendors[] = $vendorAsli;
            $sisaKuotaPersen[] = $sisaPersen;

            $detailVendor[] = [
                'vendor'              => $vendorAsli,
                'no_kontrak'          => $noKontrak,
                'nilai_kontrak'       => round($nilaiKontrak, 2),
                'amd_1'               => round($amd1, 2),
                'realisasi_kr'        => round($realisasi, 2),
                'sisa_kuota_rp'       => round($sisaRp, 2),
                'sisa_kuota_persen'   => $sisaPersen,
            ];
        }

        return [
            'vendors'           => $vendors,
            'sisa_kuota_persen' => $sisaKuotaPersen,
            'detail_vendor'     => $detailVendor,
        ];
    }

    private function normalizeVendorName(string $vendorName): string
    {
        $name = trim($vendorName);
        $name = preg_replace('/\s+/', ' ', $name);
        return mb_strtoupper($name);
    }

    public static function normalizeVendorKey(string $vendorName): string
    {
        $name = trim($vendorName);
        $name = str_replace('.', '', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        $name = trim($name);
        return mb_strtoupper($name);
    }

    private function parseNilaiRupiah($value): float
    {
        if (is_int($value) || is_float($value)) return (float) $value;

        $str = trim((string) $value);
        if ($str === '') return 0.0;

        $str = preg_replace('/[^0-9,.\-]/', '', $str);
        if ($str === '' || $str === '-') return 0.0;

        $hasComma = str_contains($str, ',');
        $hasDot   = str_contains($str, '.');

        if ($hasComma && $hasDot) {
            $posKomaTerakhir = strrpos($str, ',');
            $posTitikTerakhir = strrpos($str, '.');
            if ($posKomaTerakhir > $posTitikTerakhir) {
                $str = str_replace('.', '', $str);
                $str = str_replace(',', '.', $str);
            } else {
                $str = str_replace(',', '', $str);
            }
        } elseif ($hasComma) {
            if (preg_match('/^-?\d{1,3}(,\d{3})+$/', $str)) {
                $str = str_replace(',', '', $str);
            } else {
                $str = str_replace(',', '.', $str);
            }
        } elseif ($hasDot) {
            if (substr_count($str, '.') > 1 || preg_match('/^-?\d{1,3}(\.\d{3})+$/', $str)) {
                $str = str_replace('.', '', $str);
            }
        }
        return is_numeric($str) ? (float) $str : 0.0;
    }

    private function parseStatusPersen($value): float
    {
        if (is_int($value) || is_float($value)) {
            $num = (float) $value;
            if ($num > 0 && $num <= 1) $num *= 100;
            return round($num, 2);
        }

        $str = trim((string) $value);
        if ($str === '') return 0.0;

        $adaTandaPersen = str_contains($str, '%');
        $str = str_replace('%', '', $str);
        $str = trim($str);
        $str = str_replace(',', '.', $str);

        if (!is_numeric($str)) return 0.0;

        $num = (float) $str;
        if (!$adaTandaPersen && $num > 0 && $num <= 1) $num *= 100;

        return round($num, 2);
    }

    private function findSheetIdByNama(string $needle, ?string $preferTahun = null): ?int
    {
        $sheets = Sheet::whereRaw('LOWER(nama_sheet) LIKE ?', ['%' . strtolower($needle) . '%'])
            ->where('is_active', 1)
            ->orderBy('urutan')
            ->get();

        if ($sheets->isEmpty()) return null;

        if ($preferTahun) {
            $preferred = $sheets->first(function ($s) use ($preferTahun) {
                return (string) $s->tahun === $preferTahun || str_contains((string) $s->nama_sheet, $preferTahun);
            });
            if ($preferred) return $preferred->id;
        }

        return $sheets->first()->id;
    }

    private function getNamaPelangganList(): array
    {
        $sheets = Sheet::whereRaw('LOWER(nama_sheet) LIKE ?', ['%detail kr%'])
            ->where('is_active', 1)
            ->get();

        $sheets2026 = $sheets->filter(function ($s) {
            return (string) $s->tahun === '2026' || str_contains((string) $s->nama_sheet, '2026');
        });

        if ($sheets2026->isNotEmpty()) $sheets = $sheets2026;

        $result = []; 

        foreach ($sheets as $sheet) {
            $rows = SheetData::where('sheet_id', $sheet->id)->orderBy('row_number')->get();
            if ($rows->isEmpty()) continue;

            $pelangganCol = null;
            $headerRowNumber = null;

            foreach ($rows->take(10) as $row) {
                $cells = json_decode($row->row_data, true);
                if (!is_array($cells)) continue;

                foreach ($cells as $colIndex => $cellValue) {
                    $val = strtolower(trim((string) $cellValue));
                    if ($val !== '' && str_contains($val, 'pelanggan')) {
                        $pelangganCol = $colIndex;
                        break;
                    }
                }
                if ($pelangganCol !== null) {
                    $headerRowNumber = $row->row_number;
                    break;
                }
            }

            if ($headerRowNumber === null) continue;

            foreach ($rows as $row) {
                if ($row->row_number <= $headerRowNumber) continue;

                $cells = json_decode($row->row_data, true);
                if (!is_array($cells)) continue;

                $nama = trim((string) ($cells[$pelangganCol] ?? ''));
                if ($nama === '') continue;

                if (!isset($result[$nama])) $result[$nama] = $sheet->id;
            }
        }
        ksort($result);
        return $result;
    }

    public function adminIndex()
    {
        if (!session()->has('admin')) return redirect()->route('login.view');

        $totalDataMonitoring = SheetData::whereIn('sheet_id', Sheet::where('is_active', 1)->pluck('id'))->count();
        $progressChart = $this->getProgressChartData();
        $totalVendor = count($progressChart['vendors']);
        $lastUpdated = Upload::max('created_at');

        return view('dashboard-admin.dashboard-admin', compact(
            'totalDataMonitoring',
            'totalVendor',
            'lastUpdated'
        ));
    }

    public function searchSheet(Request $request)
    {
        if (!Auth::check()) return response()->json(['message' => 'Unauthorized'], 401);

        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2) return response()->json(['sheets' => [], 'data' => []]);

        $qLower = mb_strtolower($q);

        $sheetMatches = Sheet::whereRaw('LOWER(nama_sheet) LIKE ?', ['%' . $qLower . '%'])
            ->whereRaw('LOWER(TRIM(nama_sheet)) != ?', ['rekap'])
            ->where('is_active', 1)
            ->orderBy('nama_sheet')
            ->limit(20)
            ->get()
            ->map(function ($s) {
                return [
                    'id'         => $s->id,
                    'nama_sheet' => $s->nama_sheet,
                    'kategori'   => $s->kategori,
                ];
            });

        $dataRows = SheetData::whereRaw('LOWER(row_data) LIKE ?', ['%' . $qLower . '%'])
            ->orderBy('id')
            ->limit(40)
            ->get();

        $dataResults = [];
        $seen = [];

        foreach ($dataRows as $row) {
            if (count($dataResults) >= 15) break;

            $sheet = Sheet::find($row->sheet_id);
            if (!$sheet) continue;
            if (!$sheet->is_active) continue;
            if (mb_strtolower(trim($sheet->nama_sheet ?? '')) === 'rekap') continue;

            $key = $sheet->id . '-' . $row->row_number;
            if (isset($seen[$key])) continue;
            $seen[$key] = true;

            $cells = json_decode($row->row_data, true);
            $snippet = '';

            if (is_array($cells)) {
                foreach ($cells as $val) {
                    $val = trim((string) $val);
                    if ($val !== '' && str_contains(mb_strtolower($val), $qLower)) {
                        $snippet = $val;
                        break;
                    }
                }
            }

            $dataResults[] = [
                'sheet_id'   => $sheet->id,
                'nama_sheet' => $sheet->nama_sheet,
                'row_number' => $row->row_number,
                'snippet'    => $snippet !== '' ? $snippet : 'Ditemukan pada baris ' . $row->row_number,
            ];
        }

        return response()->json([
            'sheets' => $sheetMatches,
            'data'   => $dataResults,
        ]);
    }

    public function show($id)
    {
        $sheet = Sheet::findOrFail($id);

        if (mb_strtoupper(trim($sheet->nama_sheet ?? '')) === 'REKAP') abort(404);

        $rows = SheetData::where('sheet_id', $id)->orderBy('row_number')->get();

        $resVendorMap        = $this->buildResVendorSheetMap();
        $detailKr2026SheetId = $this->findSheetIdByNama('Detail KR', '2026');

        if (strtoupper(substr($sheet->nama_sheet, 0, 3)) == 'RES') {
            return view('sheet_res', compact('sheet', 'rows', 'resVendorMap', 'detailKr2026SheetId'));
        }

        if (mb_strtoupper(trim($sheet->nama_sheet)) === 'PELANGGAN 2026') {
            return view('sheet_pelanggan2026', compact('sheet', 'rows'));
        }

        $isFlatTableSheet = $this->isFlatTableSheet($sheet->nama_sheet);

        return view('sheet', compact('sheet', 'rows', 'resVendorMap', 'detailKr2026SheetId', 'isFlatTableSheet'));
    }

    private function isFlatTableSheet(?string $namaSheet): bool
    {
        $nama = mb_strtoupper(trim($namaSheet ?? ''));
        $nama = preg_replace('/\s+/', ' ', $nama);
        $target = ['AMR', 'GANTER', 'COVER', 'PEMBESIAN', 'PENGIKAT', 'TERMI JOINTING', 'KR PBPD', 'REKAP CONNECTOR'];

        foreach ($target as $keyword) {
            $words = preg_split('/\s+/', $keyword);
            $allWordsFound = true;

            foreach ($words as $w) {
                if (!preg_match('/\b' . preg_quote($w, '/') . '\b/u', $nama)) {
                    $allWordsFound = false;
                    break;
                }
            }

            if ($allWordsFound) return true;
        }

        return false;
    }

    private function buildResVendorSheetMap(): array
    {
        $sheets = Sheet::where(function ($q) {
            $q->whereRaw('LOWER(nama_sheet) LIKE ?', ['res%'])
              ->orWhereRaw('LOWER(kategori) LIKE ?', ['res%']);
        })->where('is_active', 1)->get();

        $map = [];

        foreach ($sheets as $sheet) {
            $namaSheetLower = mb_strtolower(trim($sheet->nama_sheet ?? ''));

            if (str_contains($namaSheetLower, 'rekap')) continue;

            $rows = SheetData::where('sheet_id', $sheet->id)->orderBy('row_number')->limit(40)->get();
            $found = false;

            foreach ($rows as $row) {
                $cells = json_decode($row->row_data, true);
                if (!is_array($cells)) continue;

                $found = false;

                foreach ($cells as $idx => $val) {
                    $labelRaw = trim((string) $val);
                    $label    = mb_strtolower($labelRaw);

                    if ($label === '' || (!str_contains($label, 'pelaksana') && !str_contains($label, 'penyedia'))) {
                        continue;
                    }

                    $vendorName = '';
                    for ($i = $idx + 1; $i < count($cells); $i++) {
                        $next = trim((string) ($cells[$i] ?? ''));
                        if ($next !== '') {
                            $vendorName = $next;
                            break;
                        }
                    }

                    if ($vendorName === '' && preg_match('/(?:pelaksana|penyedia)\s*[:\-]\s*(.+)/i', $labelRaw, $m)) {
                        $vendorName = trim($m[1]);
                    }

                    if ($vendorName !== '') {
                        $key = self::normalizeVendorKey($vendorName);
                        if (!isset($map[$key])) {
                            $map[$key] = $sheet->id;
                        }
                        $found = true;
                    }

                    if ($found) break;
                }

                if ($found) break;
            }
        }

        return $map;
    }
}