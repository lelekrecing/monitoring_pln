<?php

namespace Database\Seeders;

use App\Models\KajianParam;
use App\Models\KajianPelanggan;
use App\Models\KajianProyek;
use App\Models\KajianTarifMaster;
use App\Models\KajianUraiRow;
use App\Services\KajianKelayakanService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KajianKelayakanDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(KajianKelayakanService::class);

        // 1. Seed Master Tarif EDIR 0014 (40 tarif resmi KKO-KKF UP3 Jambi)
        $order = 1;
        foreach (KajianKelayakanService::TARIF_DEFAULT_DAYA as $kode => $defaultDaya) {
            $spec = KajianKelayakanService::EDIR_TARIF_SPECS[$kode] ?? [110, 969, 'per VA', 133, 1444.7];
            $kategori = strtoupper(substr(trim($kode), 0, 1));
            KajianTarifMaster::updateOrCreate(
                ['kode_tarif' => $kode],
                [
                    'kategori'         => in_array($kategori, ['R', 'S', 'B', 'I', 'P'], true) ? $kategori : 'R',
                    'tarif_ap2t'       => $kode,
                    'default_daya_kva' => (float) $defaultDaya,
                    'jam_nyala_bulan'  => (float) $spec[0],
                    'bp_satuan_rp'     => (float) $spec[1],
                    'ket_bp'           => (string) $spec[2],
                    'ujl_satuan_rp'    => (float) $spec[3],
                    'ket_ujl'          => 'per VA',
                    'tarif_kwh_rp'     => (float) $spec[4],
                    'ket_tarif'        => 'per kWh',
                    'sort_order'       => $order++,
                ]
            );
        }

        // 2. Seed Data Proyek, Param, Pelanggan & URAI dari DATABASE_WEB_APP_KKO_KKF.xlsx
        $seedPath = database_path('seeders/data/kajian_db_seed.json');
        if (!file_exists($seedPath)) {
            return;
        }

        $payload = json_decode((string) file_get_contents($seedPath), true);
        if (!is_array($payload)) {
            return;
        }

        $paramsByProject = [];
        foreach ($payload['param'] ?? [] as $pr) {
            $paramsByProject[$pr['project_id']][] = $pr;
        }

        $pelangganByProject = [];
        foreach ($payload['pelanggan'] ?? [] as $pl) {
            $pelangganByProject[$pl['project_id']][] = $pl;
        }

        $uraiCellsByProject = [];
        foreach ($payload['urai'] ?? [] as $uc) {
            $pid = $uc['project_id'];
            $sheetRow = max(6, (int) ($uc['row_number'] ?? 6));
            $uiRow = $sheetRow - 5; // Excel ROW_START=6 => baris grid 1
            if (!isset($uraiCellsByProject[$pid][$uiRow])) {
                $uraiCellsByProject[$pid][$uiRow] = [
                    'row_number' => $uiRow,
                    'overrides'  => [],
                    'updated_by' => $uc['updated_by'] ?? null,
                ];
            }
            $col = strtoupper(trim((string) ($uc['column_letter'] ?? '')));
            if ($col !== '') {
                $uraiCellsByProject[$pid][$uiRow][$col] = (string) ($uc['value'] ?? '');
                if (!empty($uc['is_override'])) {
                    $uraiCellsByProject[$pid][$uiRow]['overrides'][$col] = true;
                }
            }
        }

        DB::transaction(function () use ($payload, $paramsByProject, $pelangganByProject, $uraiCellsByProject, $service) {
            foreach ($payload['proyek'] ?? [] as $proj) {
                $kode = trim((string) $proj['project_id']);
                if ($kode === '') {
                    continue;
                }

                $parts = explode('/', $kode);
                $tahun = end($parts);
                if (!is_string($tahun) || !preg_match('/^\d{4}$/', $tahun)) {
                    $tahun = '2026';
                }

                $projParams = $paramsByProject[$kode] ?? [];
                $jarakGudang = 0.0;
                foreach ($projParams as $prm) {
                    if ($prm['parameter'] === 'jarakGudang') {
                        $jarakGudang = (float) $prm['value'];
                    }
                }

                $rawPelanggans = [];
                foreach ($pelangganByProject[$kode] ?? [] as $pl) {
                    $rawPelanggans[] = [
                        'slot'              => (int) ($pl['slot'] ?? 1),
                        'jenis_permohonan'  => $pl['jenis_permohonan'] ?? 'PB',
                        'kelompok_tarif'    => $pl['kelompok_tarif'] ?? '',
                        'jumlah_pelanggan'  => (int) ($pl['jumlah_pelanggan'] ?? 1),
                        'daya_sebelum_kva'  => (float) ($pl['daya_sebelum_kva'] ?? 0),
                        'daya_sesudah_kva'  => (float) ($pl['daya_sesudah_kva'] ?? $pl['daya_kva'] ?? 0),
                    ];
                }
                $custSummary = $service->normalizeCustomerGroups($rawPelanggans);

                $rawUraiRows = $uraiCellsByProject[$kode] ?? [];
                ksort($rawUraiRows);
                $uraiParsed = $service->normalizeAndCalculateUrai(array_values($rawUraiRows));
                $uraiSummary = $uraiParsed['summary'];

                $feasibility = $service->evaluateFeasibility([
                    'total_pelanggan'     => $custSummary['total_pelanggan'],
                    'total_daya_kva'      => $custSummary['total_daya_kva'],
                    'jarak_gudang_km'     => $jarakGudang,
                    'jumlah_trafo'        => $uraiSummary['jumlah_trafo'],
                    'kapasitas_trafo_kva' => $uraiSummary['kapasitas_trafo_kva'],
                    'estimasi_sutm_km'    => $uraiSummary['estimasi_sutm_km'],
                    'estimasi_sutr_km'    => $uraiSummary['estimasi_sutr_km'],
                    'jumlah_tiang'        => $uraiSummary['jumlah_tiang'],
                    'pelanggans'          => $custSummary['groups'],
                ]);

                $proyek = KajianProyek::updateOrCreate(
                    ['kode_kajian' => $kode],
                    [
                        'mode'                      => 'kajian',
                        'ulp'                       => $proj['ulp'] ?? 'Telanai Pura',
                        'nama_pekerjaan'            => $proj['nama_pekerjaan'] ?? 'Kajian Tanpa Judul',
                        'jarak_gudang_km'           => $jarakGudang,
                        'row_count'                 => (int) ($proj['row_count'] ?? 25),
                        'tahun'                     => $tahun,
                        'total_pelanggan'           => $custSummary['total_pelanggan'],
                        'total_daya_kva'            => $custSummary['total_daya_kva'],
                        'jumlah_trafo'              => $uraiSummary['jumlah_trafo'],
                        'kapasitas_trafo_kva'       => $uraiSummary['kapasitas_trafo_kva'],
                        'estimasi_sutm_km'          => $uraiSummary['estimasi_sutm_km'],
                        'estimasi_sutr_km'          => $uraiSummary['estimasi_sutr_km'],
                        'jumlah_tiang'              => $feasibility['jumlah_tiang_terhitung'],
                        'estimasi_nilai_rab'        => $feasibility['estimasi_nilai_rab'],
                        'estimasi_pendapatan_tahun' => $feasibility['estimasi_pendapatan_tahun'],
                        'payback_tahun'             => $feasibility['payback_tahun'],
                        'layak_otomatis'            => $feasibility['layak_otomatis'],
                        'hasil_ringkas'             => $feasibility['hasil_ringkas'],
                        'status'                    => count($uraiParsed['rows']) > 0 ? 'dilengkapi_perencanaan' : 'diajukan',
                        'kebutuhan_jaringan'        => $uraiSummary,
                        'analisa_kkf'               => [
                            'total_bp_rp'  => $feasibility['total_bp_rp'],
                            'total_ujl_rp' => $feasibility['total_ujl_rp'],
                            'box_app'      => $feasibility['box_app'],
                        ],
                        'created_by_name'           => 'anggi.adilta6@gmail.com',
                        'created_by_role'           => 'ulp',
                        'updated_by_name'           => 'anggi.adilta6@gmail.com',
                    ]
                );

                // Pertahankan timestamp historis dari sheet
                if (!empty($proj['created_at']) || !empty($proj['updated_at'])) {
                    $proyek->timestamps = false;
                    if (!empty($proj['created_at'])) {
                        $proyek->created_at = $proj['created_at'];
                    }
                    if (!empty($proj['updated_at'])) {
                        $proyek->updated_at = $proj['updated_at'];
                    }
                    $proyek->save();
                }

                // Sync Params (DB_PARAM)
                $proyek->params()->delete();
                foreach ($projParams as $prm) {
                    KajianParam::create([
                        'kajian_proyek_id' => $proyek->id,
                        'parameter'        => $prm['parameter'],
                        'value'            => $prm['value'],
                    ]);
                }

                // Sync Pelanggans (DB_PELANGGAN)
                $proyek->pelanggans()->delete();
                foreach ($custSummary['groups'] as $grp) {
                    KajianPelanggan::create(array_merge($grp, [
                        'kajian_proyek_id' => $proyek->id,
                    ]));
                }

                // Sync URAI rows (DB_URAI)
                $proyek->uraiRows()->delete();
                foreach ($uraiParsed['rows'] as $row) {
                    KajianUraiRow::create([
                        'kajian_proyek_id' => $proyek->id,
                        'row_number'       => $row['row_number'],
                        'row_data'         => $row['row_data'],
                        'updated_by'       => $row['updated_by'] ?? 'anggi.adilta6@gmail.com',
                    ]);
                }
            }
        });
    }
}
