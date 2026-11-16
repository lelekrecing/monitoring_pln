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
     * Daftar Tarif & Default Daya (kVA) sesuai standar KKO-KKF EDIR 0014 PLN UP3 Jambi.
     */
    public const TARIF_DEFAULT_DAYA = [
        'R.1 / 450 VA I'              => 0.45,
        'R.1 / 900 VA I'              => 0.9,
        'R.1M / 900 VA I'             => 0.9,
        'R.1 / 1.300 VA'              => 1.3,
        'R.1 / 2.200 VA'              => 2.2,
        'R.2 / 3.500 VA s/d 5.500'    => 3.5,
        'R.3 / 6.600 VA s.d 200 kVA I'=> 6.6,
        'S.1 / 220 VA'                => 0.22,
        'S.2 / 450 VA I'              => 0.45,
        'S.2 / 900 VA I'              => 0.9,
        'S.2 / 1.300 VA'              => 1.3,
        'S.2 / 2.200 VA'              => 2.2,
        'S.2 / 3.500 VA s/d 82,5 kVA' => 3.5,
        'S.2 / 105 kVA s/d 200 kVA'   => 105.0,
        'S.3 / >200 KVA'              => 200.0,
        'B.1 / 450 VA I'              => 0.45,
        'B.1 / 900 VA I'              => 0.9,
        'B.1 / 1.300 VA'              => 1.3,
        'B.1 / 2.200 VA'              => 2.2,
        'B.1 / 3.500 VA s/d 5.500 V'  => 3.5,
        'B.2 / 6.600 VA s/d 82,5 kVA' => 6.6,
        'B.2 / 105 kVA s/d 200 kVA'   => 105.0,
        'B.3 / > 200 kVA'             => 200.0,
        'I.1 / 450 VA I'              => 0.45,
        'I.1 / 900 VA I'              => 0.9,
        'I.1 / 1.300 VA'              => 1.3,
        'I.1 / 2.200 VA'              => 2.2,
        'I.1 / 3.500 s/d 14 kVA'      => 3.5,
        'I.2 / > 14 kVA s/d 82,5 kVA' => 14.0,
        'I.2 / > 105 kVA s/d 200 kVA' => 105.0,
        'I.3 / > 200 kVA'             => 200.0,
        'I.4 / 30.000 kVA keatas'     => 30000.0,
        'P.1 / 450 VA'                => 0.45,
        'P.1 / 900 VA'                => 0.9,
        'P.1 / 1.300 VA'              => 1.3,
        'P.1 / 2.200 VA'              => 2.2,
        'P.1 / 3.500 VA s/d 5.500 V'  => 3.5,
        'P.1 / 6.600 VA s/d 82,5 kVA' => 6.6,
        'P.1 / 105 kVA s/d 200 kVA'   => 105.0,
        'P.2 / > 200 kVA'             => 200.0,
    ];

    /**
     * Spesifikasi EDIR 0014 (sheet BP UJL TRF): [jam_nyala, bp_satuan, ket_bp, ujl_satuan, tarif_kwh].
     */
    public const EDIR_TARIF_SPECS = [
        'R.1 / 450 VA I'              => [255, 421000, 'fix', 72, 415.0],
        'R.1 / 900 VA I'              => [139, 843000, 'fix', 72, 605.0],
        'R.1M / 900 VA I'             => [112, 843000, 'fix', 72, 1352.0],
        'R.1 / 1.300 VA'              => [93, 1218000, 'fix', 133, 1444.7],
        'R.1 / 2.200 VA'              => [129, 2062000, 'fix', 141, 1444.7],
        'R.2 / 3.500 VA s/d 5.500'    => [90, 969, 'per VA', 157, 1699.53],
        'R.3 / 6.600 VA s.d 200 kVA I'=> [108, 969, 'per VA', 140, 1699.53],
        'S.1 / 220 VA'                => [0, 0, 'fix', 49, 0.0],
        'S.2 / 450 VA I'              => [379, 421000, 'fix', 45, 325.0],
        'S.2 / 900 VA I'              => [105, 843000, 'fix', 45, 455.0],
        'S.2 / 1.300 VA'              => [90, 1218000, 'fix', 84, 708.0],
        'S.2 / 2.200 VA'              => [116, 2062000, 'fix', 94, 760.0],
        'S.2 / 3.500 VA s/d 82,5 kVA' => [100, 969, 'per VA', 104, 900.0],
        'S.2 / 105 kVA s/d 200 kVA'   => [100, 775, 'per VA', 104, 900.0],
        'S.3 / >200 KVA'              => [90, 631, 'per VA', 148, 735.0],
        'B.1 / 450 VA I'              => [239, 421000, 'fix', 80, 535.0],
        'B.1 / 900 VA I'              => [135, 843000, 'fix', 80, 630.0],
        'B.1 / 1.300 VA'              => [114, 1218000, 'fix', 132, 966.0],
        'B.1 / 2.200 VA'              => [112, 2062000, 'fix', 130, 1100.0],
        'B.1 / 3.500 VA s/d 5.500 V'  => [112, 969, 'per VA', 130, 1100.0],
        'B.2 / 6.600 VA s/d 82,5 kVA' => [126, 969, 'per VA', 165, 1444.7],
        'B.2 / 105 kVA s/d 200 kVA'   => [126, 775, 'per VA', 165, 1444.7],
        'B.3 / > 200 kVA'             => [146, 631, 'per VA', 200, 1035.78],
        'I.1 / 450 VA I'              => [0, 421000, 'fix', 88, 485.0],
        'I.1 / 900 VA I'              => [77, 843000, 'fix', 88, 600.0],
        'I.1 / 1.300 VA'              => [78, 1218000, 'fix', 128, 930.0],
        'I.1 / 2.200 VA'              => [97, 2062000, 'fix', 112, 960.0],
        'I.1 / 3.500 s/d 14 kVA'      => [96, 969, 'per VA', 102, 1112.0],
        'I.2 / > 14 kVA s/d 82,5 kVA' => [115, 969, 'per VA', 154, 972.0],
        'I.2 / > 105 kVA s/d 200 kVA' => [115, 775, 'per VA', 154, 972.0],
        'I.3 / > 200 kVA'             => [134, 631, 'per VA', 225, 1035.78],
        'I.4 / 30.000 kVA keatas'     => [0, 535, 'per VA', 272, 996.74],
        'P.1 / 450 VA'                => [535, 421000, 'fix', 127, 685.0],
        'P.1 / 900 VA'                => [109, 843000, 'fix', 87, 760.0],
        'P.1 / 1.300 VA'              => [76, 1218000, 'fix', 138, 1049.0],
        'P.1 / 2.200 VA'              => [78, 2062000, 'fix', 133, 1076.0],
        'P.1 / 3.500 VA s/d 5.500 V'  => [78, 969, 'per VA', 133, 1076.0],
        'P.1 / 6.600 VA s/d 82,5 kVA' => [89, 969, 'per VA', 172, 1699.53],
        'P.1 / 105 kVA s/d 200 kVA'   => [89, 775, 'per VA', 172, 1699.53],
        'P.2 / > 200 kVA'             => [65, 631, 'per VA', 117, 1035.78],
    ];

    public const BOX_APP_CAPACITIES = [53.0, 66.0, 82.5, 105.0, 131.0, 147.0, 164.0, 197.0];

    public const DAYA_OPTIONS = [
        0.22, 0.45, 0.9, 1.3, 2.2, 3.5, 4.4, 5.5, 6.6, 7.7, 10.6, 11.0,
        13.2, 14.0, 16.5, 22.0, 23.0, 33.0, 41.5, 53.0, 66.0, 82.5, 105.0,
        131.0, 147.0, 164.0, 197.0, 200.0, 240.0, 345.0, 555.0, 30000.0,
    ];

    public const TRAFO_OPTIONS = [
        'cantol' => ['2JRS - 2JRS', '2JRS - 1JRS', 'TNP PHB', 'EX', 'TRAFO BAE'],
        'portal' => ['4JRS - 4JRS', '4JRS - 2JRS', 'TNP PHB', '2JRS - 2JRS', '2JRS - 1JRS', 'EX', 'TRAFO BAE'],
    ];

    public const SUTM_MASTER_OPTIONS = [
        'TM-1', 'TM-3', 'TM-4', 'TM-4X', 'TM-5', 'TM-8', 'TM-8X',
        'TM-10', 'TM-10X', 'TM-PORTAL', 'TM_PTPS', 'TM-KOOI', 'TM-KOIGAR', 'TM-KOPOR',
    ];

    public const SUTR_MASTER_OPTIONS = [
        'Tr.1', 'Tr.2', 'Tr.3', 'Tr.4', 'Tr.5', 'Tr.6', 'Tr.6A', 'Tr.7', 'Tr.7A',
    ];

    public const URAI_MASTERS = [
        'AJ' => self::SUTM_MASTER_OPTIONS,
        'AK' => self::SUTM_MASTER_OPTIONS,
        'AL' => self::SUTM_MASTER_OPTIONS,
        'AM' => self::SUTM_MASTER_OPTIONS,
        'BG' => ['25 kVA', '50 kVA', 'C 100 kVA', 'P 100 kVA', '160 kVA', '200 kVA', '250 kVA'],
        'DN' => self::SUTR_MASTER_OPTIONS,
        'DO' => self::SUTR_MASTER_OPTIONS,
        'DP' => self::SUTR_MASTER_OPTIONS,
        'DQ' => self::SUTR_MASTER_OPTIONS,
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
        'AJ'     => ['label' => 'Bongkar (AJ)', 'group' => 'SUTM'],
        'AK'     => ['label' => 'Eks (AK)', 'group' => 'SUTM'],
        'AL'     => ['label' => 'Baru (AL)', 'group' => 'SUTM'],
        'AM'     => ['label' => 'Tanpa Tiang (AM)', 'group' => 'SUTM'],
        'VOL150' => ['label' => 'VOL150', 'group' => 'VOLUME SUTM'],
        'BA'     => ['label' => 'Baru (BA)', 'group' => 'GW'],
        'BD'     => ['label' => 'Baru (BD)', 'group' => 'HGW'],
        'BG'     => ['label' => 'Baru (BG)', 'group' => 'TRAFO'],
        'CANTOL' => ['label' => 'Cantol', 'group' => 'TRAFO'],
        'PORTAL' => ['label' => 'Portal', 'group' => 'TRAFO'],
        'DN'     => ['label' => 'Murni (DN)', 'group' => 'SUTR BONGKAR'],
        'DO'     => ['label' => 'Tanpa Tiang (DO)', 'group' => 'SUTR BONGKAR'],
        'DP'     => ['label' => 'Murni (DP)', 'group' => 'SUTR PASANG'],
        'DQ'     => ['label' => 'Tanpa Tiang (DQ)', 'group' => 'SUTR PASANG'],
        'VOLJTR' => ['label' => 'VOLJTR', 'group' => 'VOLUME SUTR'],
        'DR'     => ['label' => 'Bongkar (DR)', 'group' => 'SCHOER / GW-TR'],
        'DS'     => ['label' => 'Pasang (DS)', 'group' => 'SCHOER / GW-TR'],
        'DT'     => ['label' => 'Bongkar (DT)', 'group' => 'CROSS / HGW-TR'],
        'DU'     => ['label' => 'Pasang (DU)', 'group' => 'CROSS / HGW-TR'],
        'DV'     => ['label' => 'Bongkar (DV)', 'group' => 'TAP CONNECTOR'],
        'DW'     => ['label' => 'Pasang (DW)', 'group' => 'TAP CONNECTOR'],
        'PTM'    => ['label' => 'PTM', 'group' => 'PATING'],
        'PTR'    => ['label' => 'PTR', 'group' => 'PATING'],
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

    public static function normalizeTarifSpace(?string $str): string
    {
        return preg_replace('/\s+/', ' ', trim((string) $str)) ?? '';
    }

    public static function matchTarifKey(?string $tarifDaya): ?string
    {
        $norm = self::normalizeTarifSpace($tarifDaya);
        if ($norm === '') {
            return null;
        }
        if (isset(self::TARIF_DEFAULT_DAYA[$norm])) {
            return $norm;
        }
        $lower = strtolower($norm);
        foreach (array_keys(self::TARIF_DEFAULT_DAYA) as $key) {
            if (strtolower(self::normalizeTarifSpace($key)) === $lower) {
                return $key;
            }
        }
        return null;
    }

    public static function parseTarifDaya(?string $tarifDaya): array
    {
        $raw = self::normalizeTarifSpace($tarifDaya);
        $kategori = $raw !== '' ? strtoupper(substr($raw, 0, 1)) : 'R';
        $matched = self::matchTarifKey($raw);

        if ($matched !== null) {
            $kva = (float) self::TARIF_DEFAULT_DAYA[$matched];
            return [
                'matched'  => $matched,
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
            'matched'  => $raw,
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
            $tarif = self::normalizeTarifSpace((string) ($g['kelompok_tarif'] ?? $g['tarif'] ?? ''));
            $jml = max(0, (int) ($g['jumlah_pelanggan'] ?? $g['jml'] ?? $g['jumlah'] ?? 0));
            $jenis = strtoupper(trim((string) ($g['jenis_permohonan'] ?? $g['jenisPermohonan'] ?? 'PB')));
            if (!in_array($jenis, ['PB', 'PD'], true)) {
                $jenis = 'PB';
            }

            if ($tarif === '' || $jml <= 0) {
                continue;
            }

            $parsedSesudah = self::parseTarifDaya($tarif);
            $canonicalTarif = $parsedSesudah['matched'] ?: $tarif;
            $dayaSesudahKva = (float) ($g['daya_sesudah_kva'] ?? $g['dayaSesudah'] ?? $parsedSesudah['kva']);
            $dayaSebelumKva = $jenis === 'PD' ? max(0.0, (float) ($g['daya_sebelum_kva'] ?? $g['dayaSebelum'] ?? 0)) : 0.0;
            $deltaPerPelanggan = max(0.0, $dayaSesudahKva - $dayaSebelumKva);
            $penambahanTotalKva = round($deltaPerPelanggan * $jml, 3);

            $totalPelanggan += $jml;
            $totalDayaKva += $penambahanTotalKva;

            $normalized[] = [
                'slot'                => (int) ($g['slot'] ?? ($idx + 1)),
                'jenis_permohonan'    => $jenis,
                'kategori_tarif'      => $parsedSesudah['kategori'],
                'kelompok_tarif'      => $canonicalTarif,
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
        $totalPtm = 0.0;
        $totalPtr = 0.0;
        $totalDw = 0.0;
        $totalDv = 0.0;
        $tiangTmRows = 0;
        $tiangTrRows = 0;
        $trafoKvaSum = 0.0;
        $trafoCantol = [];
        $trafoPortal = [];

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
            $baInt = (int) ($clean['BA'] !== '' ? $clean['BA'] : 0);
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
            if (empty($overrides['DU']) && $clean['DU'] === '') {
                $clean['DU'] = '';
            }

            $clean['overrides'] = $overrides;

            if ($clean['AJ'] !== '' || $clean['AK'] !== '' || $clean['AL'] !== '') {
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
                if (preg_match('/(\d+(?:[.,]\d+)?)/', $clean['BG'], $m)) {
                    $trafoKvaSum += (float) str_replace(',', '.', $m[1]);
                }
            }
            if ($clean['CANTOL'] !== '') {
                $trafoCantol[$clean['CANTOL']] = ($trafoCantol[$clean['CANTOL']] ?? 0) + 1;
            }
            if ($clean['PORTAL'] !== '') {
                $trafoPortal[$clean['PORTAL']] = ($trafoPortal[$clean['PORTAL']] ?? 0) + 1;
            }
            if ($clean['PTM'] !== '') {
                $totalPtm += is_numeric(str_replace(',', '.', $clean['PTM'])) ? (float) str_replace(',', '.', $clean['PTM']) : 1;
            }
            if ($clean['PTR'] !== '') {
                $totalPtr += is_numeric(str_replace(',', '.', $clean['PTR'])) ? (float) str_replace(',', '.', $clean['PTR']) : 1;
            }
            if ($clean['DW'] !== '') {
                $totalDw += is_numeric(str_replace(',', '.', $clean['DW'])) ? (float) str_replace(',', '.', $clean['DW']) : 1;
            }
            if ($clean['DV'] !== '') {
                $totalDv += is_numeric(str_replace(',', '.', $clean['DV'])) ? (float) str_replace(',', '.', $clean['DV']) : 1;
            }

            $rowNum = isset($r['row_number']) && (int) $r['row_number'] > 0
                ? (int) $r['row_number']
                : ($idx + 1);

            $rows[] = [
                'row_number' => $rowNum,
                'row_data'   => $clean,
                'updated_by' => $r['updated_by'] ?? null,
            ];
        }

        $sutmKm = $totalVol150 >= 20 ? round($totalVol150 / 1000, 3) : round($totalVol150, 3);
        $sutrKm = $totalVolJtr >= 20 ? round($totalVolJtr / 1000, 3) : round($totalVolJtr, 3);

        return [
            'rows'    => $rows,
            'summary' => [
                'jumlah_baris_urai'   => count($rows),
                'jumlah_tiang_tm'     => (int) round($tiangTmRows + $totalPtm),
                'jumlah_tiang_tr'     => (int) round($tiangTrRows + $totalPtr),
                'jumlah_tiang'        => (int) round($tiangTmRows + $tiangTrRows + $totalPtm + $totalPtr),
                'jumlah_trafo'        => $totalBg,
                'kapasitas_trafo_kva' => $trafoKvaSum,
                'estimasi_sutm_km'    => $sutmKm,
                'estimasi_sutr_km'    => $sutrKm,
                'total_vol150_m'      => round($totalVol150, 2),
                'total_voljtr_m'      => round($totalVolJtr, 2),
                'total_gw'            => $totalBa,
                'total_hgw'           => $totalBd,
                'total_ptm'           => $totalPtm,
                'total_ptr'           => $totalPtr,
                'total_dw'            => $totalDw,
                'total_dv'            => $totalDv,
                'trafo_cantol'        => $trafoCantol,
                'trafo_portal'        => $trafoPortal,
            ],
        ];
    }

    /**
     * Hitung jumlah Box APP berdasarkan daya sesudah (53-197 kVA, sesuai MDU!Q527:Q534).
     */
    public function calculateBoxAppCounts(array $pelanggans): array
    {
        $counts = [];
        foreach (self::BOX_APP_CAPACITIES as $cap) {
            $counts[(string) $cap] = 0;
        }
        foreach ($pelanggans as $p) {
            $daya = (float) ($p['daya_sesudah_kva'] ?? 0);
            $jml = max(0, (int) ($p['jumlah_pelanggan'] ?? 0));
            if ($jml <= 0) {
                continue;
            }
            foreach (self::BOX_APP_CAPACITIES as $cap) {
                if (abs($daya - $cap) < 0.001) {
                    $counts[(string) $cap] += $jml;
                    break;
                }
            }
        }
        return $counts;
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
        $pelanggans = is_array($input['pelanggans'] ?? null) ? $input['pelanggans'] : [];
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

        // Hitung EDIR 0014 BP, UJL, dan pendapatan per slot bila detail pelanggan tersedia
        $totalBpRp = 0.0;
        $totalUjlRp = 0.0;
        $edirRevYear = 0.0;
        if (count($pelanggans) > 0) {
            foreach ($pelanggans as $p) {
                $matched = self::matchTarifKey($p['kelompok_tarif'] ?? '') ?? '';
                $spec = self::EDIR_TARIF_SPECS[$matched] ?? [110, 969, 'per VA', 133, 1444.7];
                [$jamNyala, $bpSatuan, $ketBp, $ujlSatuan, $tarifKwh] = $spec;
                $jml = max(0, (int) ($p['jumlah_pelanggan'] ?? 0));
                $jenis = strtoupper((string) ($p['jenis_permohonan'] ?? 'PB'));
                $sebelum = $jenis === 'PD' ? max(0.0, (float) ($p['daya_sebelum_kva'] ?? 0)) : 0.0;
                $sesudah = max(0.0, (float) ($p['daya_sesudah_kva'] ?? 0));
                $deltaKva = max(0.0, $sesudah - $sebelum);
                $deltaVa = $deltaKva * 1000;

                if ($ketBp === 'fix' && $jenis === 'PB') {
                    $totalBpRp += $bpSatuan * $jml;
                } else {
                    $bpUnit = $ketBp === 'fix' ? 969 : $bpSatuan;
                    $totalBpRp += $deltaVa * $bpUnit * $jml;
                }
                $totalUjlRp += $deltaVa * $ujlSatuan * $jml;

                $effectiveHours = $jamNyala > 0 ? $jamNyala : 110;
                $edirRevYear += ($deltaKva * $jml) * 0.85 * $effectiveHours * 12 * ($tarifKwh > 0 ? $tarifKwh : 1444.7);
            }
        }

        $estimasiPendapatanTahun = $edirRevYear > 0
            ? round($edirRevYear, 2)
            : round($totalDayaKva * 0.85 * 110 * 12 * 1444.7, 2);

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
            'total_bp_rp'               => round($totalBpRp, 2),
            'total_ujl_rp'              => round($totalUjlRp, 2),
            'box_app'                   => $this->calculateBoxAppCounts($pelanggans),
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
