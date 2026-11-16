<?php

namespace App\Http\Controllers;

use App\Models\KajianParam;
use App\Models\KajianPelanggan;
use App\Models\KajianProyek;
use App\Models\KajianUraiRow;
use App\Models\KontrakKuota;
use App\Services\KajianExportService;
use App\Services\KajianKelayakanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KajianKelayakanController extends Controller
{
    public function __construct(
        private readonly KajianKelayakanService $service,
        private readonly KajianExportService $exportService,
    ) {
    }

    public function userIndex(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $dashboardController = app(DashboardController::class);
        $availableYears = $dashboardController->getAvailableYearsData();
        $selectedYear = trim((string) $request->query('tahun', ''));
        $sidebarMenu = $dashboardController->getSidebarMenuData($selectedYear);

        $projects = KajianProyek::with('pelanggans')
            ->when($selectedYear !== '', fn ($q) => $q->where('tahun', $selectedYear))
            ->orderByDesc('updated_at')
            ->get();

        $vendorQuotas = $this->service->getCombinedVendorQuotas($selectedYear !== '' ? $selectedYear : null);

        return view('kajian-kelayakan.index', [
            'sidebarMenu'     => $sidebarMenu,
            'availableYears'  => $availableYears,
            'selectedYear'    => $selectedYear,
            'projects'        => $projects,
            'vendorQuotas'    => $vendorQuotas,
            'ulpList'         => array_keys(KajianKelayakanService::ULP_CODES),
            'tarifDefaultDaya'=> KajianKelayakanService::TARIF_DEFAULT_DAYA,
            'tarifDayaList'   => array_keys(KajianKelayakanService::TARIF_DEFAULT_DAYA),
            'edirTarifSpecs'  => KajianKelayakanService::EDIR_TARIF_SPECS,
            'boxAppCapacities'=> KajianKelayakanService::BOX_APP_CAPACITIES,
            'dayaOptions'     => KajianKelayakanService::DAYA_OPTIONS,
            'trafoOptions'    => KajianKelayakanService::TRAFO_OPTIONS,
            'uraiMasters'     => KajianKelayakanService::URAI_MASTERS,
            'unitCosts'       => KajianKelayakanService::UNIT_COSTS,
            'uraiColumns'     => KajianKelayakanService::URAI_COLUMNS,
            'uraiColumnMeta'  => KajianKelayakanService::URAI_COLUMN_META,
        ]);
    }

    public function adminIndex(Request $request)
    {
        if (!session('admin')) {
            return redirect('/admin');
        }

        $selectedYear = trim((string) $request->query('tahun', ''));
        $availableYears = KajianProyek::query()
            ->select('tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->filter()
            ->values()
            ->all();

        if (!in_array((string) date('Y'), $availableYears, true)) {
            array_unshift($availableYears, (string) date('Y'));
        }

        $projects = KajianProyek::with('pelanggans')
            ->when($selectedYear !== '', fn ($q) => $q->where('tahun', $selectedYear))
            ->orderByDesc('updated_at')
            ->get();

        $vendorQuotas = $this->service->getCombinedVendorQuotas($selectedYear !== '' ? $selectedYear : null);
        $masterKuotas = KontrakKuota::orderByDesc('tahun')->orderBy('vendor')->get();

        return view('kajian-kelayakan.admin', [
            'availableYears'  => $availableYears,
            'selectedYear'    => $selectedYear,
            'projects'        => $projects,
            'vendorQuotas'    => $vendorQuotas,
            'masterKuotas'    => $masterKuotas,
            'ulpList'         => array_keys(KajianKelayakanService::ULP_CODES),
            'tarifDefaultDaya'=> KajianKelayakanService::TARIF_DEFAULT_DAYA,
            'tarifDayaList'   => array_keys(KajianKelayakanService::TARIF_DEFAULT_DAYA),
            'edirTarifSpecs'  => KajianKelayakanService::EDIR_TARIF_SPECS,
            'boxAppCapacities'=> KajianKelayakanService::BOX_APP_CAPACITIES,
            'dayaOptions'     => KajianKelayakanService::DAYA_OPTIONS,
            'trafoOptions'    => KajianKelayakanService::TRAFO_OPTIONS,
            'uraiMasters'     => KajianKelayakanService::URAI_MASTERS,
            'unitCosts'       => KajianKelayakanService::UNIT_COSTS,
            'uraiColumns'     => KajianKelayakanService::URAI_COLUMNS,
            'uraiColumnMeta'  => KajianKelayakanService::URAI_COLUMN_META,
        ]);
    }

    public function simulate(Request $request)
    {
        $groups = $request->input('pelanggans', []);
        $customerSummary = $this->service->normalizeCustomerGroups(is_array($groups) ? $groups : []);

        $totalPelanggan = (int) $request->input('total_pelanggan', $customerSummary['total_pelanggan']);
        $totalDayaKva = (float) $request->input('total_daya_kva', $customerSummary['total_daya_kva']);
        if ($customerSummary['total_pelanggan'] > 0) {
            $totalPelanggan = $customerSummary['total_pelanggan'];
            $totalDayaKva = $customerSummary['total_daya_kva'];
        }

        $uraiRowsInput = $request->input('urai_rows', []);
        $uraiParsed = is_array($uraiRowsInput) && count($uraiRowsInput) > 0
            ? $this->service->normalizeAndCalculateUrai($uraiRowsInput)
            : null;

        $uraiSummary = $uraiParsed['summary'] ?? [];

        $jumlahTrafo = (int) ($request->input('jumlah_trafo') ?: ($uraiSummary['jumlah_trafo'] ?? 0));
        $kapasitasTrafoKva = (float) ($request->input('kapasitas_trafo_kva') ?: ($uraiSummary['kapasitas_trafo_kva'] ?? 0));
        $sutmKm = (float) ($request->input('estimasi_sutm_km') ?: ($uraiSummary['estimasi_sutm_km'] ?? 0));
        $sutrKm = (float) ($request->input('estimasi_sutr_km') ?: ($uraiSummary['estimasi_sutr_km'] ?? 0));
        $jumlahTiang = (int) ($request->input('jumlah_tiang') ?: ($uraiSummary['jumlah_tiang'] ?? 0));

        $feasibility = $this->service->evaluateFeasibility([
            'total_pelanggan'     => $totalPelanggan,
            'total_daya_kva'      => $totalDayaKva,
            'jarak_gudang_km'     => (float) $request->input('jarak_gudang_km', 0),
            'jumlah_trafo'        => $jumlahTrafo,
            'kapasitas_trafo_kva' => $kapasitasTrafoKva,
            'estimasi_sutm_km'    => $sutmKm,
            'estimasi_sutr_km'    => $sutrKm,
            'jumlah_tiang'        => $jumlahTiang,
            'estimasi_nilai_rab'  => $request->input('estimasi_nilai_rab'),
            'pelanggans'          => $customerSummary['groups'],
        ]);

        $tahun = (string) ($request->input('tahun') ?: date('Y'));
        $quotas = $this->service->getCombinedVendorQuotas($tahun);
        $availableVendors = array_values(array_filter($quotas, fn ($q) => !$q['kuota_habis'] && $q['sisa_kuota_rp'] >= $feasibility['estimasi_nilai_rab']));

        return response()->json([
            'status'          => 'success',
            'total_pelanggan' => $totalPelanggan,
            'total_daya_kva'  => $totalDayaKva,
            'urai_summary'    => $uraiSummary,
            'feasibility'     => $feasibility,
            'kuota_tersedia'  => count($availableVendors),
            'kuota_total'     => count($quotas),
        ]);
    }

    public function show(Request $request, int $id)
    {
        if (!Auth::check() && !session('admin')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $proyek = KajianProyek::with(['pelanggans', 'uraiRows', 'params'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $proyek,
        ]);
    }

    public function store(Request $request)
    {
        $isAdmin = (bool) session('admin');
        if (!Auth::check() && !$isAdmin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'id'                   => 'nullable|integer|exists:kajian_proyeks,id',
            'mode'                 => 'required|in:simulasi,kajian',
            'ulp'                  => 'required|string|max:100',
            'nama_pekerjaan'       => 'required|string|max:255',
            'jarak_gudang_km'      => 'nullable|numeric|min:0',
            'row_count'            => 'nullable|integer|min:1|max:2000',
            'tahun'                => 'nullable|string|max:10',
            'status'               => 'nullable|in:draft,diajukan,dilengkapi_perencanaan,layak,tidak_layak,terkontrak',
            'total_pelanggan'      => 'nullable|integer|min:0',
            'total_daya_kva'       => 'nullable|numeric|min:0',
            'jumlah_trafo'         => 'nullable|integer|min:0',
            'kapasitas_trafo_kva'  => 'nullable|numeric|min:0',
            'estimasi_sutm_km'     => 'nullable|numeric|min:0',
            'estimasi_sutr_km'     => 'nullable|numeric|min:0',
            'jumlah_tiang'         => 'nullable|integer|min:0',
            'estimasi_nilai_rab'   => 'nullable|numeric|min:0',
            'catatan_ulp'          => 'nullable|string|max:2000',
            'catatan_perencanaan'  => 'nullable|string|max:2000',
            'vendor_kontrak'       => 'nullable|string|max:200',
            'no_kontrak'           => 'nullable|string|max:120',
            'pelanggans'           => 'nullable|array',
            'urai_rows'            => 'nullable|array',
        ]);

        $actorName = $isAdmin
            ? (session('admin')->name ?? session('admin')->username ?? 'Admin Perencanaan')
            : (Auth::user()->name ?? Auth::user()->username ?? 'User ULP');
        $actorRole = $isAdmin ? 'perencanaan' : 'ulp';

        $tahun = trim((string) ($validated['tahun'] ?? date('Y'))) ?: date('Y');
        $pelanggans = $this->service->normalizeCustomerGroups($validated['pelanggans'] ?? []);
        $uraiParsed = $this->service->normalizeAndCalculateUrai($validated['urai_rows'] ?? []);
        $uraiSummary = $uraiParsed['summary'];

        $totalPelanggan = $pelanggans['total_pelanggan'] > 0
            ? $pelanggans['total_pelanggan']
            : (int) ($validated['total_pelanggan'] ?? 0);
        $totalDayaKva = $pelanggans['total_daya_kva'] > 0
            ? $pelanggans['total_daya_kva']
            : (float) ($validated['total_daya_kva'] ?? 0);

        $jumlahTrafo = (int) (($validated['jumlah_trafo'] ?? 0) ?: $uraiSummary['jumlah_trafo']);
        $kapasitasTrafoKva = (float) (($validated['kapasitas_trafo_kva'] ?? 0) ?: $uraiSummary['kapasitas_trafo_kva']);
        $sutmKm = (float) (($validated['estimasi_sutm_km'] ?? 0) ?: $uraiSummary['estimasi_sutm_km']);
        $sutrKm = (float) (($validated['estimasi_sutr_km'] ?? 0) ?: $uraiSummary['estimasi_sutr_km']);
        $jumlahTiang = (int) (($validated['jumlah_tiang'] ?? 0) ?: $uraiSummary['jumlah_tiang']);

        $feasibility = $this->service->evaluateFeasibility([
            'total_pelanggan'     => $totalPelanggan,
            'total_daya_kva'      => $totalDayaKva,
            'jarak_gudang_km'     => (float) ($validated['jarak_gudang_km'] ?? 0),
            'jumlah_trafo'        => $jumlahTrafo,
            'kapasitas_trafo_kva' => $kapasitasTrafoKva,
            'estimasi_sutm_km'    => $sutmKm,
            'estimasi_sutr_km'    => $sutrKm,
            'jumlah_tiang'        => $jumlahTiang,
            'estimasi_nilai_rab'  => $validated['estimasi_nilai_rab'] ?? null,
            'pelanggans'          => $pelanggans['groups'],
        ]);

        // Validasi kuota vendor STRICT sebelum terkontrak (berlaku untuk halaman User Perencanaan maupun Admin)
        $requestedStatus = $validated['status'] ?? ($validated['mode'] === 'simulasi' ? 'draft' : 'diajukan');
        $vendorKontrak = trim((string) ($validated['vendor_kontrak'] ?? ''));
        $noKontrak = trim((string) ($validated['no_kontrak'] ?? ''));
        $nilaiKontrakPakai = 0.0;

        if ($requestedStatus === 'terkontrak') {
            $nilaiKontrakPakai = (float) ($validated['estimasi_nilai_rab'] ?? $feasibility['estimasi_nilai_rab']);
            $existingId = !empty($validated['id']) ? (int) $validated['id'] : null;
            $quotaCheck = $this->validateVendorQuotaForContract($vendorKontrak, $tahun, $nilaiKontrakPakai, $existingId);
            $vendorKontrak = $quotaCheck['vendor'];
            if ($noKontrak === '' && !empty($quotaCheck['no_kontrak'])) {
                $noKontrak = $quotaCheck['no_kontrak'];
            }
        } else {
            $vendorKontrak = null;
            $noKontrak = null;
        }

        $proyek = DB::transaction(function () use (
            $validated,
            $tahun,
            $totalPelanggan,
            $totalDayaKva,
            $jumlahTrafo,
            $kapasitasTrafoKva,
            $sutmKm,
            $sutrKm,
            $feasibility,
            $requestedStatus,
            $pelanggans,
            $uraiParsed,
            $uraiSummary,
            $vendorKontrak,
            $noKontrak,
            $nilaiKontrakPakai,
            $actorName,
            $actorRole,
            $isAdmin
        ) {
            if (!empty($validated['id'])) {
                $proyek = KajianProyek::findOrFail((int) $validated['id']);
                // Cegah ULP mengubah kajian yang sudah terkontrak
                if (!$isAdmin && $proyek->status === 'terkontrak') {
                    throw ValidationException::withMessages([
                        'status' => ['Kajian yang sudah terkontrak oleh Perencanaan tidak dapat diubah oleh ULP.'],
                    ]);
                }
            } else {
                $proyek = new KajianProyek();
                $proyek->kode_kajian = $this->service->generateKodeKajian($validated['ulp'], $validated['mode'], $tahun);
                $proyek->user_id = Auth::id();
                $proyek->created_by_name = $actorName;
                $proyek->created_by_role = $actorRole;
            }

            $proyek->mode = $validated['mode'];
            $proyek->ulp = $validated['ulp'];
            $proyek->nama_pekerjaan = $validated['nama_pekerjaan'];
            $proyek->jarak_gudang_km = round((float) ($validated['jarak_gudang_km'] ?? 0), 2);
            $proyek->row_count = max(1, min(2000, (int) ($validated['row_count'] ?? ($proyek->row_count ?: 25))));
            $proyek->tahun = $tahun;
            $proyek->total_pelanggan = $totalPelanggan;
            $proyek->total_daya_kva = $totalDayaKva;
            $proyek->jumlah_trafo = $jumlahTrafo;
            $proyek->kapasitas_trafo_kva = $kapasitasTrafoKva;
            $proyek->estimasi_sutm_km = $sutmKm;
            $proyek->estimasi_sutr_km = $sutrKm;
            $proyek->jumlah_tiang = $feasibility['jumlah_tiang_terhitung'];
            $proyek->estimasi_nilai_rab = $feasibility['estimasi_nilai_rab'];
            $proyek->estimasi_pendapatan_tahun = $feasibility['estimasi_pendapatan_tahun'];
            $proyek->payback_tahun = $feasibility['payback_tahun'];
            $proyek->layak_otomatis = $feasibility['layak_otomatis'];
            $proyek->hasil_ringkas = $feasibility['hasil_ringkas'];
            $proyek->status = $requestedStatus;
            $proyek->catatan_ulp = $validated['catatan_ulp'] ?? $proyek->catatan_ulp;
            if (array_key_exists('catatan_perencanaan', $validated)) {
                $proyek->catatan_perencanaan = $validated['catatan_perencanaan'];
            }
            $proyek->kebutuhan_jaringan = $uraiSummary;
            $proyek->analisa_kkf = [
                'total_bp_rp'  => $feasibility['total_bp_rp'] ?? 0,
                'total_ujl_rp' => $feasibility['total_ujl_rp'] ?? 0,
                'box_app'      => $feasibility['box_app'] ?? [],
            ];
            $proyek->updated_by_name = $actorName;

            if ($requestedStatus === 'terkontrak') {
                $proyek->vendor_kontrak = $vendorKontrak;
                $proyek->no_kontrak = $noKontrak ?: '-';
                $proyek->nilai_kontrak_pakai = $nilaiKontrakPakai;
                $proyek->terkontrak_at = now();
            } elseif (in_array($requestedStatus, ['draft', 'diajukan', 'dilengkapi_perencanaan', 'layak', 'tidak_layak'], true)) {
                $proyek->vendor_kontrak = null;
                $proyek->no_kontrak = null;
                $proyek->nilai_kontrak_pakai = 0;
                $proyek->terkontrak_at = null;
            }

            $proyek->save();

            // Sync parameter jarakGudang pada kajian_params
            KajianParam::updateOrCreate(
                ['kajian_proyek_id' => $proyek->id, 'parameter' => 'jarakGudang'],
                ['value' => (string) $proyek->jarak_gudang_km]
            );

            // Replace pelanggans jika array pelanggans dikirim
            if (array_key_exists('pelanggans', $validated)) {
                $proyek->pelanggans()->delete();
                foreach ($pelanggans['groups'] as $g) {
                    KajianPelanggan::create(array_merge($g, ['kajian_proyek_id' => $proyek->id]));
                }
            }

            // Replace URAI rows jika array urai_rows dikirim
            if (array_key_exists('urai_rows', $validated)) {
                $proyek->uraiRows()->delete();
                foreach ($uraiParsed['rows'] as $row) {
                    KajianUraiRow::create([
                        'kajian_proyek_id' => $proyek->id,
                        'row_number'       => $row['row_number'],
                        'row_data'         => $row['row_data'],
                        'updated_by'       => $actorName,
                    ]);
                }
            }

            return $proyek->load(['pelanggans', 'uraiRows', 'params']);
        });

        return response()->json([
            'status'  => 'success',
            'message' => $requestedStatus === 'terkontrak'
                ? 'Kajian berhasil dikontrakkan ke vendor ' . $proyek->vendor_kontrak . ' dan kuota kontrak telah diperbarui.'
                : 'Data kajian kelayakan berhasil disimpan (' . $proyek->kode_kajian . ').',
            'data'    => $proyek,
        ]);
    }

    public function adminContract(Request $request, int $id)
    {
        $isAdmin = (bool) session('admin');
        if (!Auth::check() && !$isAdmin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'status'              => 'required|in:dilengkapi_perencanaan,layak,tidak_layak,terkontrak',
            'vendor_kontrak'      => 'nullable|string|max:200',
            'no_kontrak'          => 'nullable|string|max:120',
            'nilai_kontrak_pakai' => 'nullable|numeric|min:0',
            'catatan_perencanaan' => 'nullable|string|max:2000',
            'jumlah_trafo'        => 'nullable|integer|min:0',
            'kapasitas_trafo_kva' => 'nullable|numeric|min:0',
            'estimasi_sutm_km'    => 'nullable|numeric|min:0',
            'estimasi_sutr_km'    => 'nullable|numeric|min:0',
            'jumlah_tiang'        => 'nullable|integer|min:0',
            'estimasi_nilai_rab'  => 'nullable|numeric|min:0',
            'urai_rows'           => 'nullable|array',
        ]);

        $proyek = KajianProyek::findOrFail($id);
        $adminName = session('admin')->name ?? session('admin')->username ?? 'Admin Perencanaan';

        // Update kebutuhan URAI / teknis dari Perencanaan jika diisi
        if (array_key_exists('urai_rows', $validated)) {
            $uraiParsed = $this->service->normalizeAndCalculateUrai($validated['urai_rows'] ?? []);
            $uraiSummary = $uraiParsed['summary'];
            $proyek->uraiRows()->delete();
            foreach ($uraiParsed['rows'] as $row) {
                KajianUraiRow::create([
                    'kajian_proyek_id' => $proyek->id,
                    'row_number'       => $row['row_number'],
                    'row_data'         => $row['row_data'],
                ]);
            }
            $proyek->kebutuhan_jaringan = $uraiSummary;
        } else {
            $uraiSummary = is_array($proyek->kebutuhan_jaringan) ? $proyek->kebutuhan_jaringan : [];
        }

        $jumlahTrafo = array_key_exists('jumlah_trafo', $validated) && $validated['jumlah_trafo'] !== null
            ? (int) $validated['jumlah_trafo']
            : ($uraiSummary['jumlah_trafo'] ?? $proyek->jumlah_trafo);
        $kapasitasTrafo = array_key_exists('kapasitas_trafo_kva', $validated) && $validated['kapasitas_trafo_kva'] !== null
            ? (float) $validated['kapasitas_trafo_kva']
            : ($uraiSummary['kapasitas_trafo_kva'] ?? $proyek->kapasitas_trafo_kva);
        $sutmKm = array_key_exists('estimasi_sutm_km', $validated) && $validated['estimasi_sutm_km'] !== null
            ? (float) $validated['estimasi_sutm_km']
            : ($uraiSummary['estimasi_sutm_km'] ?? $proyek->estimasi_sutm_km);
        $sutrKm = array_key_exists('estimasi_sutr_km', $validated) && $validated['estimasi_sutr_km'] !== null
            ? (float) $validated['estimasi_sutr_km']
            : ($uraiSummary['estimasi_sutr_km'] ?? $proyek->estimasi_sutr_km);
        $jumlahTiang = array_key_exists('jumlah_tiang', $validated) && $validated['jumlah_tiang'] !== null
            ? (int) $validated['jumlah_tiang']
            : ($uraiSummary['jumlah_tiang'] ?? $proyek->jumlah_tiang);

        $feasibility = $this->service->evaluateFeasibility([
            'total_pelanggan'     => $proyek->total_pelanggan,
            'total_daya_kva'      => $proyek->total_daya_kva,
            'jarak_gudang_km'     => $proyek->jarak_gudang_km,
            'jumlah_trafo'        => $jumlahTrafo,
            'kapasitas_trafo_kva' => $kapasitasTrafo,
            'estimasi_sutm_km'    => $sutmKm,
            'estimasi_sutr_km'    => $sutrKm,
            'jumlah_tiang'        => $jumlahTiang,
            'estimasi_nilai_rab'  => $validated['estimasi_nilai_rab'] ?? $proyek->estimasi_nilai_rab,
        ]);

        $newStatus = $validated['status'];

        if ($newStatus === 'terkontrak') {
            $vendor = trim((string) ($validated['vendor_kontrak'] ?? ''));
            $nilaiPakai = (float) (($validated['nilai_kontrak_pakai'] ?? null) !== null
                ? $validated['nilai_kontrak_pakai']
                : $feasibility['estimasi_nilai_rab']);

            $quotaInfo = $this->validateVendorQuotaForContract($vendor, $proyek->tahun, $nilaiPakai, $proyek->id);

            $proyek->vendor_kontrak = $quotaInfo['vendor'];
            $proyek->no_kontrak = trim((string) ($validated['no_kontrak'] ?? '')) ?: $quotaInfo['no_kontrak'];
            $proyek->nilai_kontrak_pakai = $nilaiPakai;
            $proyek->terkontrak_at = now();
        } else {
            $proyek->vendor_kontrak = null;
            $proyek->no_kontrak = null;
            $proyek->nilai_kontrak_pakai = 0;
            $proyek->terkontrak_at = null;
        }

        $proyek->jumlah_trafo = $jumlahTrafo;
        $proyek->kapasitas_trafo_kva = $kapasitasTrafo;
        $proyek->estimasi_sutm_km = $sutmKm;
        $proyek->estimasi_sutr_km = $sutrKm;
        $proyek->jumlah_tiang = $feasibility['jumlah_tiang_terhitung'];
        $proyek->estimasi_nilai_rab = $feasibility['estimasi_nilai_rab'];
        $proyek->estimasi_pendapatan_tahun = $feasibility['estimasi_pendapatan_tahun'];
        $proyek->payback_tahun = $feasibility['payback_tahun'];
        $proyek->layak_otomatis = $feasibility['layak_otomatis'];
        $proyek->hasil_ringkas = $feasibility['hasil_ringkas'];
        $proyek->status = $newStatus;
        $proyek->catatan_perencanaan = $validated['catatan_perencanaan'] ?? $proyek->catatan_perencanaan;
        $proyek->updated_by_name = $adminName;
        $proyek->save();

        return response()->json([
            'status'  => 'success',
            'message' => $newStatus === 'terkontrak'
                ? 'Proyek berhasil dikontrakkan ke ' . $proyek->vendor_kontrak . '.'
                : 'Data kajian berhasil diperbarui oleh Perencanaan.',
            'data'    => $proyek->load(['pelanggans', 'uraiRows']),
        ]);
    }

    public function storeKuota(Request $request)
    {
        if (!Auth::check() && !session('admin')) {
            return redirect('/login');
        }

        $data = $request->validate([
            'id'                   => 'nullable|integer|exists:kontrak_kuotas,id',
            'vendor'               => 'required|string|max:200',
            'no_kontrak'           => 'nullable|string|max:120',
            'jenis_pekerjaan'      => 'nullable|string|max:150',
            'tahun'                => 'required|string|max:10',
            'kuota_awal_rp'        => 'required|numeric|min:0',
            'terpakai_manual_rp'   => 'nullable|numeric|min:0',
            'kuota_pekerjaan_maks' => 'nullable|integer|min:0',
            'status_aktif'         => 'nullable|boolean',
            'catatan'              => 'nullable|string|max:1000',
        ]);

        $kuota = !empty($data['id'])
            ? KontrakKuota::findOrFail((int) $data['id'])
            : new KontrakKuota();

        $kuota->vendor = trim($data['vendor']);
        $kuota->no_kontrak = trim((string) ($data['no_kontrak'] ?? ''));
        $kuota->jenis_pekerjaan = trim((string) ($data['jenis_pekerjaan'] ?? 'Pembangunan Jaringan & PB/PD')) ?: 'Pembangunan Jaringan & PB/PD';
        $kuota->tahun = trim($data['tahun']);
        $kuota->kuota_awal_rp = (float) $data['kuota_awal_rp'];
        $kuota->terpakai_manual_rp = (float) ($data['terpakai_manual_rp'] ?? 0);
        $kuota->kuota_pekerjaan_maks = (int) ($data['kuota_pekerjaan_maks'] ?? 0);
        $kuota->status_aktif = $request->boolean('status_aktif', true);
        $kuota->catatan = $data['catatan'] ?? null;
        $kuota->save();

        if ($request->expectsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Master kuota kontrak berhasil disimpan.',
                'data'    => $kuota,
            ]);
        }

        return back()->with('success', 'Kuota kontrak vendor "' . $kuota->vendor . '" berhasil disimpan.');
    }

    public function destroyKuota(int $id)
    {
        if (!Auth::check() && !session('admin')) {
            return redirect('/login');
        }

        $kuota = KontrakKuota::findOrFail($id);
        $kuota->delete();

        return back()->with('success', 'Data master kuota kontrak berhasil dihapus.');
    }

    public function destroy(int $id)
    {
        $isAdmin = (bool) session('admin');
        if (!Auth::check() && !$isAdmin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $proyek = KajianProyek::findOrFail($id);
        if (!$isAdmin && $proyek->status === 'terkontrak') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kajian yang sudah terkontrak tidak dapat dihapus oleh ULP.',
            ], 422);
        }

        $proyek->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data kajian berhasil dihapus.',
        ]);
    }

    public function exportExcel(Request $request)
    {
        if (!Auth::check() && !session('admin')) {
            return redirect('/login');
        }

        [$projects, $vendorQuotas, $filters] = $this->filterProjectsForExport($request);
        $file = $this->exportService->exportRecapExcel($projects, $vendorQuotas, $filters);

        return response($file['content'], 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $file['filename'] . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function exportSingleExcel(int $id)
    {
        if (!Auth::check() && !session('admin')) {
            return redirect('/login');
        }

        $proyek = KajianProyek::with(['pelanggans', 'uraiRows'])->findOrFail($id);
        $vendorQuotas = $this->service->getCombinedVendorQuotas($proyek->tahun ?: null);
        $file = $this->exportService->exportSingleExcel($proyek, $vendorQuotas);

        return response($file['content'], 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $file['filename'] . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function exportPdf(Request $request)
    {
        if (!Auth::check() && !session('admin')) {
            return redirect('/login');
        }

        [$projects, $vendorQuotas, $filters] = $this->filterProjectsForExport($request);
        if ($request->boolean('preview')) {
            return view('kajian-kelayakan.export-pdf', [
                'mode'         => 'recap',
                'title'        => 'REKAP KAJIAN KELAYAKAN PROYEK (KKO-KKF)',
                'projects'     => $projects,
                'vendorQuotas' => $vendorQuotas,
            ]);
        }

        $file = $this->exportService->exportRecapPdf($projects, $vendorQuotas, $filters);

        return response($file['content'], 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $file['filename'] . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    public function exportSinglePdf(Request $request, int $id)
    {
        if (!Auth::check() && !session('admin')) {
            return redirect('/login');
        }

        $proyek = KajianProyek::with(['pelanggans', 'uraiRows'])->findOrFail($id);
        $vendorQuotas = $this->service->getCombinedVendorQuotas($proyek->tahun ?: null);

        if ($request->boolean('preview')) {
            return view('kajian-kelayakan.export-pdf', [
                'mode'         => 'single',
                'title'        => 'KKO-KKF • ' . $proyek->kode_kajian . ' — ' . $proyek->nama_pekerjaan,
                'proyek'       => $proyek,
                'vendorQuotas' => $vendorQuotas,
            ]);
        }

        $file = $this->exportService->exportSinglePdf($proyek, $vendorQuotas);

        return response($file['content'], 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $file['filename'] . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    private function filterProjectsForExport(Request $request): array
    {
        $tahun = trim((string) $request->query('tahun', ''));
        $ulp = trim((string) $request->query('ulp', ''));
        $status = trim((string) $request->query('status', ''));
        $search = trim((string) $request->query('search', ''));

        $projects = KajianProyek::with('pelanggans')
            ->when($tahun !== '', fn ($q) => $q->where('tahun', $tahun))
            ->when($ulp !== '', fn ($q) => $q->where('ulp', $ulp))
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when($search !== '', function ($q) use ($search) {
                $like = '%' . $search . '%';
                $q->where(function ($sub) use ($like) {
                    $sub->where('kode_kajian', 'like', $like)
                        ->orWhere('nama_pekerjaan', 'like', $like)
                        ->orWhere('ulp', 'like', $like)
                        ->orWhere('vendor_kontrak', 'like', $like)
                        ->orWhere('catatan_ulp', 'like', $like);
                });
            })
            ->orderByDesc('updated_at')
            ->get();

        $vendorQuotas = $this->service->getCombinedVendorQuotas($tahun !== '' ? $tahun : null);

        return [$projects, $vendorQuotas, compact('tahun', 'ulp', 'status', 'search')];
    }

    /**
     * Memastikan kuota kontrak masih tersedia sebelum proyek dapat dikontrakkan.
     */
    private function validateVendorQuotaForContract(string $vendorName, string $tahun, float $nilaiPakai, ?int $excludeProjectId = null): array
    {
        if (trim($vendorName) === '') {
            throw ValidationException::withMessages([
                'vendor_kontrak' => ['Pilih vendor/kontrak terlebih dahulu sebelum mengontrakkan proyek.'],
            ]);
        }

        $quotas = $this->service->getCombinedVendorQuotas($tahun);
        $targetKey = DashboardController::normalizeVendorKey($vendorName);
        $matched = null;
        foreach ($quotas as $q) {
            if ($q['key'] === $targetKey) {
                $matched = $q;
                break;
            }
        }

        if (!$matched) {
            throw ValidationException::withMessages([
                'vendor_kontrak' => ['Vendor "' . $vendorName . '" tidak terdaftar pada kuota kontrak tahun ' . $tahun . '.'],
            ]);
        }

        // Jika mengedit proyek yang sudah terkontrak ke vendor yang sama, kembalikan nilainya sementara ke sisa sebelum cek
        $availableRp = (float) $matched['sisa_kuota_rp'];
        $usedJobs = (int) $matched['jumlah_terkontrak'];
        if ($excludeProjectId) {
            $prev = KajianProyek::find($excludeProjectId);
            if ($prev && $prev->status === 'terkontrak' && $prev->vendor_kontrak && DashboardController::normalizeVendorKey($prev->vendor_kontrak) === $targetKey) {
                $availableRp += (float) ($prev->nilai_kontrak_pakai > 0 ? $prev->nilai_kontrak_pakai : $prev->estimasi_nilai_rab);
                $usedJobs = max(0, $usedJobs - 1);
            }
        }

        $maksJobs = (int) ($matched['kuota_pekerjaan_maks'] ?? 0);
        if (!$matched['status_aktif']) {
            throw ValidationException::withMessages([
                'vendor_kontrak' => ['Kontrak vendor "' . $matched['vendor'] . '" sedang nonaktif dan tidak bisa menerima kontrak baru.'],
            ]);
        }

        if ($maksJobs > 0 && $usedJobs >= $maksJobs) {
            throw ValidationException::withMessages([
                'vendor_kontrak' => ['Kuota jumlah pekerjaan vendor "' . $matched['vendor'] . '" sudah habis (' . $usedJobs . '/' . $maksJobs . '). Tidak bisa mengontrak lagi!'],
            ]);
        }

        if ($availableRp <= 0) {
            throw ValidationException::withMessages([
                'vendor_kontrak' => ['Kuota kontrak vendor "' . $matched['vendor'] . '" sudah HABIS (Sisa Kuota Rp 0). Tidak bisa mengontrak lagi!'],
            ]);
        }

        if ($nilaiPakai > $availableRp) {
            throw ValidationException::withMessages([
                'nilai_kontrak_pakai' => [
                    'Nilai kontrak proyek (Rp ' . number_format($nilaiPakai, 0, ',', '.') .
                    ') melebihi sisa kuota vendor "' . $matched['vendor'] .
                    '" (Sisa: Rp ' . number_format($availableRp, 0, ',', '.') . ').',
                ],
            ]);
        }

        return $matched;
    }
}
