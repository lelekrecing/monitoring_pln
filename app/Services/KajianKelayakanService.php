<?php

namespace App\Services;

use App\Http\Controllers\DashboardController;
use App\Models\KajianProyek;
use App\Models\KontrakKuota;

class KajianKelayakanService
{
    public const ULP_CODES = [
        'Telanai Pura'  => 'ULP-TELANAI-PURA',
        'Kota Baru'     => 'ULP-KOTA-BARU',
        'Seberang Kota' => 'ULP-SEBERANG-KOTA',
        'Muara Bulian'  => 'ULP-MUARA-BULIAN',
        'Muara Sabak'   => 'ULP-MUARA-SABAK',
        'Kuala Tungkal' => 'ULP-KUALA-TUNGKAL',
    ];

    /**
     * Daftar Tarif & Default Daya (kVA) sesuai standar KKO-KKF PLN UP3 Jambi.
     */
    public const TARIF_DEFAULT_DAYA = [
        'R.1 / 450 VA'                  => 0.45,
        'R.1 / 900 VA'                  => 0.9,
        'R.1M / 900 VA'                 => 0.9,
        'R.1 / 1.300 VA'                => 1.3,
        'R.1 / 2.200 VA'                => 2.2,
        'R.2 / 3.500 VA s/d 5.500 VA'   => 3.5,
        'R.3 / 6.600 VA s/d 200 kVA'    => 6.6,
        'S.1 / 220 VA'                  => 0.22,
        'S.2 / 450 VA'                  => 0.45,
        'S.2 / 900 VA'                  => 0.9,
        'S.2 / 1.300 VA'                => 1.3,
        'S.2 / 2.200 VA'                => 2.2,
        'S.2 / 3.500 VA s/d 82,5 kVA'   => 3.5,
        'S.2 / 105 kVA s/d 200 kVA'     => 105.0,
        'S.3 / > 200 kVA'               => 200.0,
        'B.1 / 450 VA'                  => 0.45,
        'B.1 / 900 VA'                  => 0.9,
        'B.1 / 1.300 VA'                => 1.3,
        'B.1 / 2.200 VA'                => 2.2,
        'B.1 / 3.500 VA s/d 5.500 VA'   => 3.5,
        'B.2 / 6.600 VA s/d 82,5 kVA'   => 6.6,
        'B.2 / 105 kVA s/d 200 kVA'     => 105.0,
        'B.3 / > 200 kVA'               => 200.0,
        'I.1 / 450 VA'                  => 0.45,
        'I.1 / 900 VA'                  => 0.9,
        'I.1 / 1.300 VA'                => 1.3,
        'I.1 / 2.200 VA'                => 2.2,
        'I.1 / 3.500 s/d 14 kVA'        => 3.5,
        'I.2 / > 14 kVA s/d 82,5 kVA'   => 14.0,
        'I.2 / > 105 kVA s/d 200 kVA'   => 105.0,
        'I.3 / > 200 kVA'               => 200.0,
        'I.4 / 30.000 kVA keatas'       => 30000.0,
        'P.1 / 450 VA'                  => 0.45,
        'P.1 / 900 VA'                  => 0.9,
        'P.1 / 1.300 VA'                => 1.3,
        'P.1 / 2.200 VA'                => 2.2,
        'P.1 / 3.500 VA s/d 5.500 VA'   => 3.5,
        'P.1 / 6.600 VA s/d 82,5 kVA'   => 6.6,
        'P.1 / 105 kVA s/d 200 kVA'     => 105.0,
        'P.2 / > 200 kVA'               => 200.0,
    ];

    public const DAYA_OPTIONS = [
        0.22, 0.45, 0.9, 1.3, 2.2, 3.5, 4.4, 5.5, 6.6, 7.7, 10.6, 11.0,
        13.2, 14.0, 16.5, 23.0, 33.0, 41.5, 53.0, 66.0, 82.5, 105.0,
        131.0, 147.0, 164.0, 197.0, 200.0, 240.0, 345.0, 555.0, 30000.0,
    ];

    public const TRAFO_OPTIONS = [
        'cantol' => ['2JRS - 2JRS', '2JRS - 1JRS', 'TNP PHB', 'EX', 'TRAFO BAE'],
        'portal' => ['4JRS - 4JRS', '4JRS - 2JRS', 'TNP PHB', '2JRS - 2JRS', '2JRS - 1JRS', 'EX', 'TRAFO BAE'],
    ];

    public const URAI_MASTERS = [
        'AJ' => ['Tiang TM Baru 12m/200daN', 'Tiang TM Baru 13m/350daN', 'Tiang TM Eksisting', 'SUTM Ext', 'G-01'],
        'AK' => ['Beton 12m / 200 daN', 'Beton 13m / 350 daN', 'Besi 11m / 200 daN', 'Eksisting'],
        'AL' => ['TM-1', 'TM-2', 'TM-3', 'TM-4', 'TM-4X', 'TM-5', 'TM-8', 'TM-8X', 'TM-10', 'TM-10X', 'TM-11'],
        'AM' => ['AAACS 3x70 mm2', 'AAACS 3x150 mm2', 'AAACS 3x240 mm2', 'MVTIC 3x150 mm2', 'Tanpa Penarikan'],
        'BG' => ['50', '100', '160', '200', '250', '315', '400', '630'],
        'DN' => ['Bongkar Tiang TR 9m', 'Bongkar Tiang TR Besi', 'Tanpa Bongkar'],
        'DO' => ['Beton 9m / 200 daN', 'Besi 9m', 'Konstruksi Lama'],
        'DP' => ['Tr.1', 'Tr.2', 'Tr.3', 'Tr.4', 'Tr.5', 'Tr.6', 'Tr.7', 'Tr.8'],
        'DQ' => ['TIC 3x35+25 mm2', 'TIC 3x50+35 mm2', 'TIC 3x70+50 mm2', 'TIC 2x10 mm2'],
    ];

    // Standar satuan biaya perkiraan RAB (Rp) untuk Simulasi & Kajian Awal PLN UP3 Jambi
    public const UNIT_COSTS = [
        'trafo_per_unit'    => 125000000,
        'trafo_per_kva'     => 450000,
        'sutm_per_km'       => 285000000,
        'sutr_per_km'       => 145000000,
        'tiang_tm_unit'     => 9500000,
        'tiang_tr_unit'     => 5200000,
        'gw_unit'           => 2100000,
        'hgw_unit'          => 3400000,
        'mobilisasi_per_km' => 350000,
        'sr_per_pelanggan'  => 1750000,
    ];

    public const URAI_COLUMNS = [
        'AJ', 'AK', 'AL', 'AM', 'VOL150', 'BA', 'BD', 'BG', 'CANTOL', 'PORTAL',
        'DN', 'DO', 'DP', 'DQ', 'VOLJTR', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'PTM', 'PTR',
    ];

    public const URAI_COLUMN_META = [
        'AJ'     => ['label' => 'Tiang TM', 'group' => 'SUTM'],
        'AK'     => ['label' => 'Jenis Tiang TM', 'group' => 'SUTM'],
        'AL'     => ['label' => 'Konstruksi TM', 'group' => 'SUTM'],
        'AM'     => ['label' => 'Konduktor TM', 'group' => 'SUTM'],
        'VOL150' => ['label' => 'Vol. SUTM', 'group' => 'SUTM'],
        'BA'     => ['label' => 'GW (Auto/Override)', 'group' => 'GW/HGW'],
        'BD'     => ['label' => 'HGW (Auto/Override)', 'group' => 'GW/HGW'],
        'BG'     => ['label' => 'Trafo (kVA)', 'group' => 'TRAFO'],
        'CANTOL' => ['label' => 'Gardu Cantol', 'group' => 'TRAFO'],
        'PORTAL' => ['label' => 'Gardu Portal', 'group' => 'TRAFO'],
        'DN'     => ['label' => 'Bongkar Tiang TR', 'group' => 'SUTR BONGKAR'],
        'DO'     => ['label' => 'Jenis Bongkar TR', 'group' => 'SUTR BONGKAR'],
        'DP'     => ['label' => 'Konstruksi TR', 'group' => 'SUTR PASANG'],
        'DQ'     => ['label' => 'Konduktor TR', 'group' => 'SUTR PASANG'],
        'VOLJTR' => ['label' => 'Vol. SUTR', 'group' => 'SUTR PASANG'],
        'DR'     => ['label' => 'Schoer', 'group' => 'SUTR PASANG'],
        'DS'     => ['label' => 'Cross (Auto/Override)', 'group' => 'SUTR PASANG'],
        'DT'     => ['label' => 'TAP', 'group' => 'SUTR PASANG'],
        'DU'     => ['label' => 'Dead End (Auto/Override)', 'group' => 'SUTR PASANG'],
        'DV'     => ['label' => 'Line Tap', 'group' => 'SUTR PASANG'],
        'DW'     => ['label' => 'Grounding', 'group' => 'SUTR PASANG'],
        'PTM'    => ['label' => 'Pating PTM', 'group' => 'PATING'],
        'PTR'    => ['label' => 'Pating PTR', 'group' => 'PATING'],
    ];

    public static function getMonthRoman(int $month): string
    {
        $romans = [1 => 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        return $romans[$month] ?? 'I';
    }

    public function generateKodeKajian(string $ulp, string $mode = 'kajian', ?string $tahun = null): string
    {
        $year = $tahun ?: date('Y');
        $ulpCode = self::ULP_CODES[$ulp] ?? ('ULP-' . strtoupper(preg_replace('/\s+/', '-', trim($ulp))));
        $roman = self::getMonthRoman((int) date('n'));
        $prefix = $mode === 'simulasi' ? 'SIM' : 'KKF';

        $count = KajianProyek::where('ulp', $ulp)
            ->where('tahun', $year)
            ->where('mode', $mode)
            ->count() + 1;

        do {
            $kode = sprintf('%03d/%s/%s/%s/%s', $count, $prefix, $ulpCode, $roman, $year);
            $exists = KajianProyek::where('kode_kajian', $kode)->exists();
            $count++;
        } while ($exists);

        return $kode;
    }

    public static function parseTarifDaya(?string $tarifDaya): array
    {
        $raw = trim((string) $tarifDaya);
        $kategori = $raw !== '' ? strtoupper(substr($raw, 0, 1)) : 'R';

        if (isset(self::TARIF_DEFAULT_DAYA[$raw])) {
            $kva = (float) self::TARIF_DEFAULT_DAYA[$raw];
            return [
                'kategori' => in_array($kategori, ['R', 'S', 'B', 'I', 'P'], true) ? $kategori : 'R',
                'va'       => $kva * 1000,
                'kva'      => $kva,
            ];
        }

        $kva = 0.0;
        if (preg_match('/([\d.,]+)\s*kVA/i', $raw, $m)) {
            $num = str_replace('.', '', $m[1]);
            $kva = (float) str_replace(',', '.', $num);
        } elseif (preg_match('/([\d.,]+)\s*VA/i', $raw, $m)) {
            $num = str_replace(['.', ','], '', $m[1]);
            $kva = round(((float) $num) / 1000, 3);
        } elseif (preg_match('/([\d.,]+)/', $raw, $m)) {
            $kva = (float) str_replace(',', '.', $m[1]);
        }

        return [
            'kategori' => in_array($kategori, ['R', 'S', 'B', 'I', 'P'], true) ? $kategori : 'R',
            'va'       => $kva * 1000,
            'kva'      => round($kva, 3),
        ];
    }

    public function normalizeCustomerGroups(array $groups): array
    {
        $normalized = [];
        $totalPelanggan = 0;
        $totalDayaKva = 0.0;

        foreach (array_values($groups) as $idx => $g) {
            $tarif = trim((string) ($g['kelompok_tarif'] ?? $g['tarif'] ?? ''));
            $jml = max(0, (int) ($g['jumlah_pelanggan'] ?? $g['jml'] ?? 0));
            $jenis = strtoupper(trim((string) ($g['jenis_permohonan'] ?? 'PB')));
            if (!in_array($jenis, ['PB', 'PD'], true)) {
                $jenis = 'PB';
            }

            if ($tarif === '' || $jml <= 0) {
                continue;
            }

            $parsedSesudah = self::parseTarifDaya($tarif);
            $dayaSesudahKva = (float) ($g['daya_sesudah_kva'] ?? $parsedSesudah['kva']);
            $dayaSebelumKva = $jenis === 'PD' ? max(0.0, (float) ($g['daya_sebelum_kva'] ?? 0)) : 0.0;
            $deltaPerPelanggan = max(0.0, $dayaSesudahKva - $dayaSebelumKva);
            $penambahanTotalKva = round($deltaPerPelanggan * $jml, 3);

            $totalPelanggan += $jml;
            $totalDayaKva += $penambahanTotalKva;

            $normalized[] = [
                'slot'                => $idx + 1,
                'jenis_permohonan'    => $jenis,
                'kategori_tarif'      => $parsedSesudah['kategori'],
                'kelompok_tarif'      => $tarif,
                'jumlah_pelanggan'    => $jml,
                'daya_sebelum_kva'    => round($dayaSebelumKva, 2),
                'daya_sesudah_kva'    => round($dayaSesudahKva, 2),
                'penambahan_daya_kva' => round($penambahanTotalKva, 2),
            ];
        }

        return [
            'groups'          => $normalized,
            'total_pelanggan' => $totalPelanggan,
            'total_daya_kva'  => round($totalDayaKva, 2),
        ];
    }

    public function normalizeAndCalculateUrai(array $rawRows): array
    {
        $rows = [];
        $totalBa = 0;
        $totalBd = 0;
        $totalBg = 0;
        $totalVol150 = 0.0;
        $totalVolJtr = 0.0;
        $totalPtm = 0;
        $totalPtr = 0;
        $tiangTmRows = 0;
        $tiangTrRows = 0;
        $trafoKvaSum = 0.0;

        foreach (array_values($rawRows) as $idx => $r) {
            $clean = [];
            $hasAny = false;
            foreach (self::URAI_COLUMNS as $col) {
                $val = isset($r[$col]) ? trim((string) $r[$col]) : '';
                if ($val !== '') {
                    $hasAny = true;
                }
                $clean[$col] = $val;
            }
            if (!$hasAny) {
                continue;
            }

            $overrides = is_array($r['overrides'] ?? null) ? $r['overrides'] : [];
            $al = strtoupper($clean['AL']);
            $dp = $clean['DP'];

            if (empty($overrides['BA'])) {
                $autoBa = ($al === 'TM-3' || $al === 'TM-4') ? 1 : ($al === 'TM-10' ? 2 : 0);
                if ($clean['BA'] === '' && $al !== '') {
                    $clean['BA'] = (string) $autoBa;
                }
            }
            $baInt = (int) $clean['BA'];
            if (empty($overrides['BD'])) {
                $autoBd = (($al === 'TM-3' && $baInt === 1) || ($al === 'TM-10' && $baInt === 2))
                    ? 0
                    : ($al === 'TM-10' ? 2 : ($al === 'TM-3' ? 1 : 0));
                if ($clean['BD'] === '' && $al !== '') {
                    $clean['BD'] = (string) $autoBd;
                }
            }
            if (empty($overrides['DS']) && $clean['DS'] === '' && $dp !== '') {
                $clean['DS'] = ($dp === 'Tr.3' || $dp === 'Tr.2') ? '1' : '0';
            }

            $clean['overrides'] = $overrides;

            if ($clean['AJ'] !== '' || $clean['AL'] !== '') {
                $tiangTmRows++;
            }
            if ($clean['DN'] !== '' || $clean['DP'] !== '') {
                $tiangTrRows++;
            }

            $totalVol150 += (float) str_replace(',', '.', $clean['VOL150'] ?: '0');
            $totalVolJtr += (float) str_replace(',', '.', $clean['VOLJTR'] ?: '0');
            $totalBa += (int) ($clean['BA'] ?: 0);
            $totalBd += (int) ($clean['BD'] ?: 0);

            if ($clean['BG'] !== '') {
                $totalBg++;
                if (preg_match('/([\d.]+)/', $clean['BG'], $m)) {
                    $trafoKvaSum += (float) $m[1];
                }
            }
            if ($clean['PTM'] !== '') {
                $totalPtm += is_numeric($clean['PTM']) ? (int) $clean['PTM'] : 1;
            }
            if ($clean['PTR'] !== '') {
                $totalPtr += is_numeric($clean['PTR']) ? (int) $clean['PTR'] : 1;
            }

            $rows[] = [
                'row_number' => $idx + 1,
                'row_data'   => $clean,
            ];
        }

        $sutmKm = $totalVol150 >= 20 ? round($totalVol150 / 1000, 3) : round($totalVol150, 3);
        $sutrKm = $totalVolJtr >= 20 ? round($totalVolJtr / 1000, 3) : round($totalVolJtr, 3);

        return [
            'rows'    => $rows,
            'summary' => [
                'jumlah_baris_urai'   => count($rows),
                'jumlah_tiang_tm'     => $tiangTmRows + $totalPtm,
                'jumlah_tiang_tr'     => $tiangTrRows + $totalPtr,
                'jumlah_tiang'        => $tiangTmRows + $tiangTrRows + $totalPtm + $totalPtr,
                'jumlah_trafo'        => $totalBg,
                'kapasitas_trafo_kva' => $trafoKvaSum,
                'estimasi_sutm_km'    => $sutmKm,
                'estimasi_sutr_km'    => $sutrKm,
                'total_gw'            => $totalBa,
                'total_hgw'           => $totalBd,
            ],
        ];
    }

    public function evaluateFeasibility(array $input): array
    {
        $totalPelanggan = max(0, (int) ($input['total_pelanggan'] ?? 0));
        $totalDayaKva = max(0.0, (float) ($input['total_daya_kva'] ?? 0));
        $jarakGudangKm = max(0.0, (float) ($input['jarak_gudang_km'] ?? 0));
        $jumlahTrafo = max(0, (int) ($input['jumlah_trafo'] ?? 0));
        $kapasitasTrafoKva = max(0.0, (float) ($input['kapasitas_trafo_kva'] ?? 0));
        $sutmKm = max(0.0, (float) ($input['estimasi_sutm_km'] ?? 0));
        $sutrKm = max(0.0, (float) ($input['estimasi_sutr_km'] ?? 0));
        $jumlahTiang = max(0, (int) ($input['jumlah_tiang'] ?? 0));
        $manualRab = isset($input['estimasi_nilai_rab']) && (float) $input['estimasi_nilai_rab'] > 0
            ? (float) $input['estimasi_nilai_rab']
            : null;

        $derivedTiang = $jumlahTiang > 0
            ? $jumlahTiang
            : (int) round(($sutmKm * 22) + ($sutrKm * 25));

        $calculatedRab =
            ($jumlahTrafo * self::UNIT_COSTS['trafo_per_unit']) +
            ($kapasitasTrafoKva * self::UNIT_COSTS['trafo_per_kva']) +
            ($sutmKm * self::UNIT_COSTS['sutm_per_km']) +
            ($sutrKm * self::UNIT_COSTS['sutr_per_km']) +
            ($derivedTiang * self::UNIT_COSTS['tiang_tr_unit']) +
            ($jarakGudangKm * self::UNIT_COSTS['mobilisasi_per_km']) +
            ($totalPelanggan * self::UNIT_COSTS['sr_per_pelanggan']);

        $estimasiRab = round($manualRab ?? $calculatedRab, 2);
        $estimasiPendapatanTahun = round($totalDayaKva * 0.85 * 110 * 12 * 1444.7, 2);

        $paybackTahun = $estimasiPendapatanTahun > 0
            ? round($estimasiRab / $estimasiPendapatanTahun, 2)
            : null;

        $trafoMemadai = $kapasitasTrafoKva <= 0 || ($kapasitasTrafoKva * 0.8) >= ($totalDayaKva * 0.65);
        $layakOtomatis = $totalDayaKva > 0 && ($estimasiRab <= 0 || ($paybackTahun !== null && $paybackTahun <= 6.5)) && $trafoMemadai;

        if ($totalDayaKva <= 0) {
            $hasil = 'Data Daya Belum Diisi';
        } elseif (!$trafoMemadai) {
            $hasil = 'Belum Layak (Kapasitas Trafo Terlalu Kecil vs Beban)';
        } elseif ($layakOtomatis) {
            $hasil = 'Layak / Dapat Diterima';
        } else {
            $hasil = 'Belum Layak (Payback > 6,5 Tahun / Investasi Tinggi)';
        }

        return [
            'jumlah_tiang_terhitung'    => $derivedTiang,
            'estimasi_nilai_rab'        => $estimasiRab,
            'estimasi_pendapatan_tahun' => $estimasiPendapatanTahun,
            'payback_tahun'             => $paybackTahun,
            'layak_otomatis'            => $layakOtomatis,
            'hasil_ringkas'             => $hasil,
        ];
    }

    public function getCombinedVendorQuotas(?string $selectedYear = null): array
    {
        $yearFilter = ($selectedYear !== null && $selectedYear !== '') ? $selectedYear : null;
        $dashboardController = app(DashboardController::class);
        $sheetQuota = $dashboardController->getKuotaVendorSummary($yearFilter);

        $map = [];

        foreach ($sheetQuota['detail_vendor'] ?? [] as $item) {
            $vendorName = trim((string) ($item['vendor'] ?? ''));
            if ($vendorName === '') {
                continue;
            }
            $key = DashboardController::normalizeVendorKey($vendorName);
            $kuotaAwal = (float) ($item['nilai_kontrak'] ?? 0) + (float) ($item['amd_1'] ?? 0);
            $realisasiSheet = (float) ($item['realisasi_kr'] ?? 0);
            $sisaSheetRp = (float) ($item['sisa_kuota_rp'] ?? max(0, $kuotaAwal - $realisasiSheet));

            $map[$key] = [
                'key'                   => $key,
                'vendor'                => $vendorName,
                'no_kontrak'            => trim((string) ($item['no_kontrak'] ?? '-')),
                'jenis_pekerjaan'       => 'Kontrak KHS / KUOTA VENDOR',
                'tahun'                 => $yearFilter ?? date('Y'),
                'sumber'                => 'Excel KUOTA VENDOR',
                'kuota_awal_rp'         => round($kuotaAwal, 2),
                'realisasi_sheet_rp'    => round($realisasiSheet, 2),
                'sisa_sebelum_kajian'   => round($sisaSheetRp, 2),
                'terpakai_kajian_rp'    => 0.0,
                'jumlah_terkontrak'     => 0,
                'kuota_pekerjaan_maks'  => 0,
                'sisa_kuota_rp'         => round($sisaSheetRp, 2),
                'sisa_kuota_persen'     => (float) ($item['sisa_kuota_persen'] ?? 0),
                'status_aktif'          => true,
                'kuota_id'              => null,
            ];
        }

        $dbQuotas = KontrakKuota::query()
            ->when($yearFilter, fn ($q) => $q->where('tahun', $yearFilter))
            ->orderBy('vendor')
            ->get();

        foreach ($dbQuotas as $q) {
            $key = DashboardController::normalizeVendorKey($q->vendor);
            $sisaManual = max(0.0, (float) $q->kuota_awal_rp - (float) $q->terpakai_manual_rp);

            if (isset($map[$key])) {
                if ($q->kuota_awal_rp > 0) {
                    $map[$key]['kuota_awal_rp'] = round((float) $q->kuota_awal_rp, 2);
                    $map[$key]['realisasi_sheet_rp'] = round((float) $q->terpakai_manual_rp, 2);
                    $map[$key]['sisa_sebelum_kajian'] = round($sisaManual, 2);
                    $map[$key]['sisa_kuota_rp'] = round($sisaManual, 2);
                }
                if ($q->no_kontrak) {
                    $map[$key]['no_kontrak'] = $q->no_kontrak;
                }
                $map[$key]['jenis_pekerjaan'] = $q->jenis_pekerjaan ?: $map[$key]['jenis_pekerjaan'];
                $map[$key]['kuota_pekerjaan_maks'] = (int) $q->kuota_pekerjaan_maks;
                $map[$key]['status_aktif'] = (bool) $q->status_aktif;
                $map[$key]['kuota_id'] = $q->id;
                $map[$key]['sumber'] = 'Sheet + Perencanaan';
            } else {
                $map[$key] = [
                    'key'                   => $key,
                    'vendor'                => $q->vendor,
                    'no_kontrak'            => $q->no_kontrak ?: '-',
                    'jenis_pekerjaan'       => $q->jenis_pekerjaan ?: 'Pembangunan Jaringan & PB/PD',
                    'tahun'                 => $q->tahun,
                    'sumber'                => 'Master Perencanaan',
                    'kuota_awal_rp'         => round((float) $q->kuota_awal_rp, 2),
                    'realisasi_sheet_rp'    => round((float) $q->terpakai_manual_rp, 2),
                    'sisa_sebelum_kajian'   => round($sisaManual, 2),
                    'terpakai_kajian_rp'    => 0.0,
                    'jumlah_terkontrak'     => 0,
                    'kuota_pekerjaan_maks'  => (int) $q->kuota_pekerjaan_maks,
                    'sisa_kuota_rp'         => round($sisaManual, 2),
                    'sisa_kuota_persen'     => $q->kuota_awal_rp > 0 ? round(($sisaManual / $q->kuota_awal_rp) * 100, 2) : 0.0,
                    'status_aktif'          => (bool) $q->status_aktif,
                    'kuota_id'              => $q->id,
                ];
            }
        }

        $terkontrakRows = KajianProyek::query()
            ->where('status', 'terkontrak')
            ->whereNotNull('vendor_kontrak')
            ->when($yearFilter, fn ($q) => $q->where('tahun', $yearFilter))
            ->get(['vendor_kontrak', 'nilai_kontrak_pakai', 'estimasi_nilai_rab']);

        foreach ($terkontrakRows as $row) {
            $key = DashboardController::normalizeVendorKey((string) $row->vendor_kontrak);
            $pakai = (float) ($row->nilai_kontrak_pakai > 0 ? $row->nilai_kontrak_pakai : $row->estimasi_nilai_rab);
            if (!isset($map[$key])) {
                continue;
            }
            $map[$key]['terpakai_kajian_rp'] += $pakai;
            $map[$key]['jumlah_terkontrak'] += 1;
        }

        $result = [];
        foreach ($map as $item) {
            $sisaFinalRp = max(0.0, round($item['sisa_sebelum_kajian'] - $item['terpakai_kajian_rp'], 2));
            $kuotaBase = max($item['kuota_awal_rp'], $item['sisa_sebelum_kajian']);
            $sisaPersen = $kuotaBase > 0 ? round(($sisaFinalRp / $kuotaBase) * 100, 2) : 0.0;
            $maksJobs = (int) $item['kuota_pekerjaan_maks'];
            $jobsHabis = $maksJobs > 0 && $item['jumlah_terkontrak'] >= $maksJobs;
            $kuotaHabis = !$item['status_aktif'] || $sisaFinalRp <= 0 || $sisaPersen <= 0 || $jobsHabis;

            $item['sisa_kuota_rp'] = $sisaFinalRp;
            $item['sisa_kuota_persen'] = $sisaPersen;
            $item['kuota_habis'] = $kuotaHabis;
            $item['alasan_habis'] = !$item['status_aktif']
                ? 'Kontrak dinonaktifkan oleh Perencanaan'
                : ($jobsHabis
                    ? 'Kuota jumlah pekerjaan sudah penuh (' . $item['jumlah_terkontrak'] . '/' . $maksJobs . ')'
                    : ($sisaFinalRp <= 0 ? 'Sisa kuota nilai kontrak (Rp) sudah habis' : ''));

            $result[] = $item;
        }

        usort($result, function ($a, $b) {
            if ($a['kuota_habis'] !== $b['kuota_habis']) {
                return $a['kuota_habis'] <=> $b['kuota_habis'];
            }
            return $b['sisa_kuota_rp'] <=> $a['sisa_kuota_rp'];
        });

        return $result;
    }
}
