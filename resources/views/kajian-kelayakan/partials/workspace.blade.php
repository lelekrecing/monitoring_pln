@php
    $routePrefix = $isAdminPortal ? 'admin.kajian-kelayakan' : 'kajian-kelayakan';
    $exportBaseUrl = url($isAdminPortal ? '/admin/kajian-kelayakan' : '/kajian-kelayakan');
    $totalProjects = $projects->count();
    $layakCount = $projects->where('layak_otomatis', true)->count();
    $terkontrakCount = $projects->where('status', 'terkontrak')->count();
    $availableQuotaCount = collect($vendorQuotas)->where('kuota_habis', false)->count();
    $exhaustedQuotaCount = collect($vendorQuotas)->where('kuota_habis', true)->count();
    $totalDayaAll = $projects->sum('total_daya_kva');
    $maxPerUlp = max(1, (int) collect($ulpList)->map(fn ($u) => $projects->where('ulp', $u)->count())->max());
@endphp

<style>
    .kk-wrap{ width:min(1680px, 96%); margin:22px auto 48px; }
    .kk-hero{
        background:linear-gradient(135deg, #0d2137 0%, #153459 100%);
        color:#fff; border-radius:16px; padding:22px 26px; margin-bottom:18px;
        display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap;
        box-shadow:0 12px 28px -8px rgba(13, 33, 55, .28);
        border:1px solid rgba(255,255,255,.08);
    }
    .kk-hero h2{ font-size:22px; font-weight:800; margin:0 0 4px; letter-spacing:-0.02em; }
    .kk-hero p{ font-size:13px; color:#cbd5e1; margin:0; max-width:760px; line-height:1.5; }
    .kk-hero-actions{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .kk-btn-white{
        display:inline-flex; align-items:center; gap:7px; height:38px; padding:0 15px;
        background:#fff; color:#0f172a; border-radius:10px; font-size:13px; font-weight:600;
        text-decoration:none; border:none; cursor:pointer; box-shadow:0 1px 3px rgba(0,0,0,.15);
    }
    .kk-btn-white:hover{ background:#f1f5f9; color:#0d2137; }

    .kk-stats{
        display:grid; grid-template-columns:repeat(auto-fit, minmax(210px, 1fr));
        gap:14px; margin-bottom:18px;
    }
    .kk-stat-card{
        background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:16px 18px;
        box-shadow:0 1px 3px rgba(15,23,42,.05); display:flex; align-items:center; gap:14px;
    }
    .kk-stat-icon{
        width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center;
        font-size:18px; flex-shrink:0;
    }
    .kk-stat-label{ font-size:12px; font-weight:600; color:#64748b; }
    .kk-stat-val{ font-size:21px; font-weight:800; color:#0f172a; margin-top:2px; }
    .kk-stat-sub{ font-size:11.5px; color:#64748b; margin-top:2px; }

    .kk-tabs{
        display:flex; gap:8px; flex-wrap:wrap; background:#fff; padding:8px; border-radius:14px;
        border:1px solid #e2e8f0; margin-bottom:18px; box-shadow:0 1px 3px rgba(15,23,42,.04);
    }
    .kk-tab-btn{
        display:inline-flex; align-items:center; gap:8px; padding:10px 16px; border-radius:10px;
        border:none; background:transparent; color:#475569; font-size:13.5px; font-weight:600;
        cursor:pointer; transition:.15s ease;
    }
    .kk-tab-btn:hover{ background:#f1f5f9; color:#0f172a; }
    .kk-tab-btn.active{ background:#0d2137; color:#fff; box-shadow:0 4px 12px -4px rgba(13,33,55,.4); }
    .kk-tab-panel{ display:none; }
    .kk-tab-panel.active{ display:block; }

    .kk-card{
        background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:20px 22px;
        box-shadow:0 1px 3px rgba(15,23,42,.05); margin-bottom:18px;
    }
    .kk-card-head{
        display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;
        padding-bottom:14px; margin-bottom:16px; border-bottom:1px solid #f1f5f9;
    }
    .kk-card-title{ font-size:16px; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:8px; }
    .kk-card-sub{ font-size:12.5px; color:#64748b; margin-top:2px; }

    .kk-grid-2{ display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:16px; }
    .kk-grid-3{ display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:14px; }
    .kk-grid-4{ display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:14px; }
    @media (max-width: 980px){
        .kk-grid-2, .kk-grid-3, .kk-grid-4{ grid-template-columns:1fr; }
    }

    .kk-field{ margin-bottom:12px; }
    .kk-label{ display:block; font-size:12.5px; font-weight:600; color:#334155; margin-bottom:5px; }
    .kk-input, .kk-select, .kk-textarea{
        width:100%; border:1px solid #cbd5e1; border-radius:10px; padding:9px 12px;
        font-size:13.5px; color:#0f172a; background:#f8fafc; transition:.15s ease;
    }
    .kk-input:focus, .kk-select:focus, .kk-textarea:focus{
        outline:none; background:#fff; border-color:#0d2137; box-shadow:0 0 0 3px rgba(13,33,55,.09);
    }

    .kk-btn{
        display:inline-flex; align-items:center; justify-content:center; gap:7px;
        padding:9px 16px; border-radius:10px; font-size:13px; font-weight:600;
        border:1px solid transparent; cursor:pointer; transition:.15s ease; text-decoration:none;
    }
    .kk-btn-primary{ background:#0d2137; color:#fff; }
    .kk-btn-primary:hover{ background:#153459; }
    .kk-btn-emerald{ background:#059669; color:#fff; }
    .kk-btn-emerald:hover{ background:#047857; }
    .kk-btn-amber{ background:#d97706; color:#fff; }
    .kk-btn-amber:hover{ background:#b45309; }
    .kk-btn-pdf{ background:#b91c1c; color:#fff; }
    .kk-btn-pdf:hover{ background:#991b1b; }
    .kk-btn-excel{ background:#15803d; color:#fff; }
    .kk-btn-excel:hover{ background:#166534; }
    .kk-btn-outline{ background:#fff; color:#334155; border-color:#cbd5e1; }
    .kk-btn-outline:hover{ background:#f8fafc; border-color:#94a3b8; }
    .kk-btn-danger{ background:#fef2f2; color:#dc2626; border-color:#fecaca; }
    .kk-btn-danger:hover{ background:#fee2e2; }
    .kk-btn-sm{ padding:6px 10px; font-size:12px; border-radius:8px; }
    .kk-btn-xs{ padding:4px 8px; font-size:11.5px; border-radius:7px; }

    .kk-result-box{
        border-radius:14px; padding:18px; border:1px solid #cbd5e1; background:#f8fafc;
    }
    .kk-result-box.is-layak{ background:#f0fdf4; border-color:#86efac; }
    .kk-result-box.is-unlayak{ background:#fef2f2; border-color:#fca5a5; }
    .kk-badge{
        display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:999px;
        font-size:11.5px; font-weight:700; white-space:nowrap;
    }
    .kk-badge-green{ background:#dcfce7; color:#166534; }
    .kk-badge-red{ background:#fee2e2; color:#991b1b; }
    .kk-badge-amber{ background:#fef3c7; color:#92400e; }
    .kk-badge-blue{ background:#dbeafe; color:#1e40af; }
    .kk-badge-slate{ background:#e2e8f0; color:#334155; }

    .kk-table-wrap{ overflow-x:auto; border:1px solid #e2e8f0; border-radius:12px; background:#fff; }
    table.kk-table{ width:100%; border-collapse:collapse; font-size:12.5px; }
    table.kk-table th{
        background:#0d2137; color:#fff; padding:9px 10px; text-align:left; font-weight:600;
        white-space:nowrap; border-right:1px solid rgba(255,255,255,.1);
    }
    table.kk-table td{
        padding:8px 10px; border-bottom:1px solid #f1f5f9; border-right:1px solid #f1f5f9; vertical-align:middle;
    }
    table.kk-table tbody tr:hover{ background:#f8fafc; }

    /* Distribusi ULP clickable grid (adapted from KKO Demo) */
    .kk-ulp-dist-grid{
        display:grid; grid-template-columns:repeat(auto-fit, minmax(215px, 1fr));
        gap:12px; margin-bottom:16px;
    }
    .kk-ulp-card{
        background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:12px 14px;
        cursor:pointer; transition:.15s ease; text-align:left;
    }
    .kk-ulp-card:hover{ border-color:#0d2137; background:#f1f5f9; }
    .kk-ulp-card.active{ border-color:#0d2137; background:#eff6ff; box-shadow:0 0 0 2px rgba(13,33,55,.14); }

    /* Pagination & Per-Page Bar */
    .kk-pager{
        display:flex; justify-content:space-between; align-items:center; gap:12px;
        flex-wrap:wrap; margin-top:14px; padding-top:12px; border-top:1px solid #f1f5f9;
        font-size:12.5px; color:#475569;
    }
    .kk-pager-controls{ display:flex; align-items:center; gap:5px; flex-wrap:wrap; }
    .kk-page-btn{
        min-width:32px; height:32px; padding:0 9px; border-radius:8px; border:1px solid #cbd5e1;
        background:#fff; color:#334155; font-size:12.5px; font-weight:600; cursor:pointer;
        display:inline-flex; align-items:center; justify-content:center;
    }
    .kk-page-btn:hover:not(:disabled){ background:#f1f5f9; border-color:#94a3b8; }
    .kk-page-btn.active{ background:#0d2137; color:#fff; border-color:#0d2137; }
    .kk-page-btn:disabled{ opacity:.45; cursor:not-allowed; }

    /* Spreadsheet Input URAI (GAS KKO/KKF Palette & UX) */
    .urai-toolbar{
        display:flex; justify-content:space-between; align-items:center; gap:10px;
        flex-wrap:wrap; margin-bottom:10px; padding:10px 12px; border-radius:12px;
        background:#f8fafc; border:1px solid #e2e8f0;
    }
    .urai-sheet-wrap{ overflow:auto; max-height:520px; border:1px solid #cbd5e1; border-radius:12px; background:#fff; }
    table.urai-table{ border-collapse:collapse; width:max-content; min-width:100%; font-size:11px; }
    table.urai-table thead th{
        position:sticky; top:0; z-index:4; background:#e5e7eb; color:#0f172a;
        padding:5px 4px; border:1px solid #cbd5e1; text-align:center; white-space:nowrap; font-weight:700;
    }
    table.urai-table thead tr.urai-tophead th.ug-no{ background:#dce6f1; }
    table.urai-table thead tr.urai-tophead th.ug-sutm,
    table.urai-table thead tr.urai-subhead th.ug-sutm{ background:#e7b9b7; }
    table.urai-table thead tr.urai-subhead th.ug-sutm{ background:#f6dada; }
    table.urai-table thead tr.urai-tophead th.ug-vol150,
    table.urai-table thead tr.urai-subhead th.ug-vol150{ background:#fde4cf; }
    table.urai-table thead tr.urai-tophead th.ug-gw,
    table.urai-table thead tr.urai-subhead th.ug-gw{ background:#d9ead3; }
    table.urai-table thead tr.urai-tophead th.ug-trafo,
    table.urai-table thead tr.urai-subhead th.ug-trafo{ background:#ead1dc; }
    table.urai-table thead tr.urai-tophead th.ug-sutrb,
    table.urai-table thead tr.urai-subhead th.ug-sutrb{ background:#fce5cd; }
    table.urai-table thead tr.urai-tophead th.ug-sutrp,
    table.urai-table thead tr.urai-subhead th.ug-sutrp{ background:#f9cb9c; }
    table.urai-table thead tr.urai-tophead th.ug-voljtr,
    table.urai-table thead tr.urai-subhead th.ug-voljtr{ background:#fde4cf; }
    table.urai-table thead tr.urai-tophead th.ug-acc,
    table.urai-table thead tr.urai-subhead th.ug-acc{ background:#d0e0e3; }
    table.urai-table thead tr.urai-tophead th.ug-tap,
    table.urai-table thead tr.urai-subhead th.ug-tap{ background:#fff2cc; }
    table.urai-table thead tr.urai-tophead th.ug-pat,
    table.urai-table thead tr.urai-subhead th.ug-pat{ background:#e2efda; }
    table.urai-table thead tr.urai-subhead th{
        top:27px; font-size:10px; padding:4px 4px;
    }
    table.urai-table td{ border:1px solid #d1d5db; padding:2px 3px; min-width:84px; background:#fff; }
    table.urai-table td.urai-row-no{
        position:sticky; left:0; z-index:3; background:#f8fafc; color:#334155;
        font-weight:700; text-align:center; min-width:38px; width:38px;
    }
    table.urai-table select, table.urai-table input[type="text"], table.urai-table input[type="number"]{
        width:100%; min-width:74px; border:none; padding:4px 4px; font-size:11px;
        background:transparent; color:#0f172a; outline:none; border-radius:4px;
    }
    table.urai-table input:focus, table.urai-table select:focus{
        background:#eff6ff; box-shadow:inset 0 0 0 1px #2563eb;
    }
    .urai-override-cell{
        display:flex; align-items:center; gap:3px; min-width:76px;
    }
    .urai-override-cell input[type="text"]{
        min-width:46px; text-align:center; font-weight:700; cursor:pointer;
    }
    .urai-override-cell input.is-auto{ background:#e9edf1; color:#0f172a; }
    .urai-override-cell input.is-override{ background:#ffe1b8; color:#92400e; cursor:text; }
    .urai-ov-cb{
        width:13px !important; min-width:13px !important; height:13px; margin:0 1px;
        cursor:pointer; accent-color:#d97706;
    }
    .urai-col-hidden{ display:none !important; }
    .kk-status-banner{
        margin-top:10px; padding:9px 12px; border-radius:10px; font-size:12.5px; font-weight:600;
        display:none;
    }
    .kk-status-banner.ok{ display:block; background:#e8f5e9; color:#1b5e20; border:1px solid #a7f3d0; }
    .kk-status-banner.err{ display:block; background:#ffebee; color:#b71c1c; border:1px solid #fecaca; }
    .kk-status-banner.info{ display:block; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
    .kk-mini-grid{ display:grid; grid-template-columns:repeat(auto-fit, minmax(170px, 1fr)); gap:10px; }
    .kk-mini-box{ background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:10px 12px; }
    .kk-mini-box .lbl{ font-size:11px; color:#64748b; font-weight:600; }
    .kk-mini-box .val{ font-size:15px; font-weight:800; color:#0f172a; margin-top:2px; }

    .kk-progress-bg{ width:100%; height:8px; background:#e2e8f0; border-radius:999px; overflow:hidden; margin-top:6px; }
    .kk-progress-bar{ height:100%; border-radius:999px; transition:width .25s ease; }

    .kk-modal-backdrop{
        position:fixed; inset:0; background:rgba(15,23,42,.58); z-index:999;
        display:none; align-items:center; justify-content:center; padding:18px;
    }
    .kk-modal-backdrop.open{ display:flex; }
    .kk-modal{
        background:#fff; border-radius:16px; width:min(720px, 100%); max-height:92vh;
        overflow-y:auto; padding:22px 24px; box-shadow:0 24px 50px rgba(0,0,0,.3);
    }
</style>

<div class="kk-wrap">
    @if (session('success'))
        <div style="background:#f0fdf4;border:1px solid #86efac;border-left:4px solid #16a34a;color:#166534;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-size:13.5px;font-weight:600;">
            <i class="fa fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="kk-stats">
        <div class="kk-stat-card">
            <div class="kk-stat-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="fa fa-folder-tree"></i></div>
            <div>
                <div class="kk-stat-label">Total Kajian &amp; Simulasi</div>
                <div class="kk-stat-val">{{ $totalProjects }}</div>
                <div class="kk-stat-sub">{{ $projects->where('mode', 'simulasi')->count() }} simulasi &bull; {{ $projects->where('mode', 'kajian')->count() }} KKF</div>
            </div>
        </div>
        <div class="kk-stat-card">
            <div class="kk-stat-icon" style="background:#f0fdf4;color:#15803d;"><i class="fa fa-circle-check"></i></div>
            <div>
                <div class="kk-stat-label">Proyek Layak / Diterima</div>
                <div class="kk-stat-val">{{ $layakCount }}</div>
                <div class="kk-stat-sub">{{ $terkontrakCount }} sudah terkontrak</div>
            </div>
        </div>
        <div class="kk-stat-card">
            <div class="kk-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fa fa-bolt"></i></div>
            <div>
                <div class="kk-stat-label">Total Tambahan Daya</div>
                <div class="kk-stat-val">{{ number_format($totalDayaAll, 1, ',', '.') }} kVA</div>
                <div class="kk-stat-sub">Akumulasi PB / PD tersimpan</div>
            </div>
        </div>
        <div class="kk-stat-card">
            <div class="kk-stat-icon" style="background:{{ $exhaustedQuotaCount > 0 ? '#fef2f2' : '#f0f9ff' }};color:{{ $exhaustedQuotaCount > 0 ? '#dc2626' : '#0369a1' }};">
                <i class="fa fa-file-contract"></i>
            </div>
            <div>
                <div class="kk-stat-label">Kuota Kontrak Vendor</div>
                <div class="kk-stat-val">{{ $availableQuotaCount }} Aktif</div>
                <div class="kk-stat-sub">{{ $exhaustedQuotaCount }} vendor kuota habis (terkunci)</div>
            </div>
        </div>
    </div>

    <div class="kk-tabs" role="tablist">
        <button type="button" class="kk-tab-btn active" data-tab="tabKkf" onclick="switchKkTab('tabKkf', this)">
            <i class="fa fa-table-cells"></i> Kajian KKF &amp; Input URAI (ULP / Perencanaan)
        </button>
        <button type="button" class="kk-tab-btn" data-tab="tabSimulasi" onclick="switchKkTab('tabSimulasi', this)">
            <i class="fa fa-bolt-lightning"></i> Simulasi Cepat Pelanggan Baru
        </button>
        <button type="button" class="kk-tab-btn" data-tab="tabDaftar" onclick="switchKkTab('tabDaftar', this)">
            <i class="fa fa-file-contract"></i> Daftar Kajian, Export &amp; Kuota Kontrak
        </button>
    </div>

    {{-- ================= TAB 1: SIMULASI CEPAT ================= --}}
    <div id="tabSimulasi" class="kk-tab-panel">
        <div class="kk-grid-2">
            <div class="kk-card">
                <div class="kk-card-head">
                    <div>
                        <div class="kk-card-title"><i class="fa fa-comments" style="color:#0284c7;"></i> Simulasi Cepat Saat Bertemu Pelanggan</div>
                        <div class="kk-card-sub">Pilih golongan tarif &amp; daya PLN, kebutuhan trafo buah, serta panjang SUTM/SUTR untuk cek kelayakan proyek.</div>
                    </div>
                </div>

                <div class="kk-grid-2">
                    <div class="kk-field">
                        <label class="kk-label">ULP</label>
                        <select id="simUlp" class="kk-select">
                            @foreach($ulpList as $ulp)
                                <option value="{{ $ulp }}">{{ $ulp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="kk-field">
                        <label class="kk-label">Tahun Anggaran</label>
                        <input type="text" id="simTahun" class="kk-input" value="{{ date('Y') }}">
                    </div>
                </div>

                <div class="kk-field">
                    <label class="kk-label">Nama Proyek / Nama Calon Pelanggan</label>
                    <input type="text" id="simNama" class="kk-input" placeholder="Contoh: Pasang Baru Perumahan / Pabrik Sawit PT X">
                </div>

                <div class="kk-grid-3">
                    <div class="kk-field">
                        <label class="kk-label">Jenis Permohonan</label>
                        <select id="simJenis" class="kk-select" onchange="toggleSimPdFields()">
                            <option value="PB">PB (Pasang Baru)</option>
                            <option value="PD">PD (Perubahan Daya)</option>
                        </select>
                    </div>
                    <div class="kk-field">
                        <label class="kk-label">Tarif &amp; Golongan (40 Tarif PLN)</label>
                        <select id="simTarif" class="kk-select" onchange="syncSimDefaultDaya(); runQuickSimulation();">
                            @foreach($tarifDefaultDaya as $td => $defKva)
                                <option value="{{ $td }}" {{ $td === 'B.2 / 105 kVA s/d 200 kVA' ? 'selected' : '' }}>{{ $td }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="kk-field">
                        <label class="kk-label">Jumlah Pelanggan</label>
                        <input type="number" id="simJmlPlg" class="kk-input" min="1" value="1" oninput="runQuickSimulation()">
                    </div>
                </div>

                <div class="kk-grid-2">
                    <div class="kk-field" id="simDayaLamaWrap" style="display:none;">
                        <label class="kk-label">Daya Sebelum (kVA) — Khusus PD</label>
                        <select id="simDayaSebelum" class="kk-select" onchange="runQuickSimulation()">
                            <option value="0">0 kVA</option>
                            @foreach($dayaOptions as $dOpt)
                                <option value="{{ $dOpt }}">{{ number_format($dOpt, $dOpt < 10 ? 2 : 1, ',', '.') }} kVA</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="kk-field">
                        <label class="kk-label">Daya Sesudah / Target per Pelanggan (kVA)</label>
                        <select id="simDayaSesudah" class="kk-select" onchange="runQuickSimulation()">
                            @foreach($dayaOptions as $dOpt)
                                <option value="{{ $dOpt }}" {{ (float)$dOpt === 105.0 ? 'selected' : '' }}>{{ number_format($dOpt, $dOpt < 10 ? 2 : 1, ',', '.') }} kVA</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr style="border:none;border-top:1px dashed #cbd5e1;margin:14px 0;">

                <div class="kk-grid-3">
                    <div class="kk-field">
                        <label class="kk-label">Kebutuhan Trafo (Buah)</label>
                        <input type="number" id="simTrafoQty" class="kk-input" min="0" value="1" oninput="runQuickSimulation()">
                    </div>
                    <div class="kk-field">
                        <label class="kk-label">Kapasitas per Trafo (kVA)</label>
                        <select id="simTrafoCap" class="kk-select" onchange="runQuickSimulation()">
                            <option value="0">Tanpa Trafo Baru</option>
                            @foreach($uraiMasters['BG'] as $cap)
                                <option value="{{ $cap }}" {{ $cap === '250' ? 'selected' : '' }}>{{ $cap }} kVA</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="kk-field">
                        <label class="kk-label">Jarak dari Gudang (km)</label>
                        <input type="number" id="simJarak" class="kk-input" min="0" step="0.5" value="15" oninput="runQuickSimulation()">
                    </div>
                </div>

                <div class="kk-grid-3">
                    <div class="kk-field">
                        <label class="kk-label">Perkiraan SUTM (km)</label>
                        <input type="number" id="simSutm" class="kk-input" min="0" step="0.05" value="0.25" oninput="runQuickSimulation()">
                    </div>
                    <div class="kk-field">
                        <label class="kk-label">Perkiraan SUTR (km)</label>
                        <input type="number" id="simSutr" class="kk-input" min="0" step="0.05" value="0.3" oninput="runQuickSimulation()">
                    </div>
                    <div class="kk-field">
                        <label class="kk-label">Jumlah Tiang (Opsional, 0=Auto)</label>
                        <input type="number" id="simTiang" class="kk-input" min="0" value="0" oninput="runQuickSimulation()">
                    </div>
                </div>

                <div class="kk-field">
                    <label class="kk-label">Catatan Hasil Obrolan / Survei Singkat</label>
                    <textarea id="simCatatan" rows="2" class="kk-textarea" placeholder="Contoh: Pelanggan siap pasang bulan depan, perlu perluasan SUTM 250m + 1 trafo 250 kVA"></textarea>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:8px;">
                    <button type="button" class="kk-btn kk-btn-primary" onclick="runQuickSimulation(true)">
                        <i class="fa fa-calculator"></i> Hitung Kelayakan
                    </button>
                    <button type="button" class="kk-btn kk-btn-emerald" onclick="saveQuickSimulation()">
                        <i class="fa fa-floppy-disk"></i> Simpan Simulasi
                    </button>
                    <button type="button" class="kk-btn kk-btn-outline" onclick="promoteSimulationToKkf()">
                        <i class="fa fa-arrow-right"></i> Lanjutkan ke Form Kajian KKF
                    </button>
                </div>
            </div>

            <div class="kk-card">
                <div class="kk-card-head">
                    <div>
                        <div class="kk-card-title"><i class="fa fa-scale-balanced" style="color:#059669;"></i> Hasil Evaluasi Kelayakan Proyek</div>
                        <div class="kk-card-sub">Penilaian otomatis antara biaya pembangunan jaringan (RAB), potensi kWh, payback period, &amp; kuota kontrak.</div>
                    </div>
                </div>

                <div id="simResultBox" class="kk-result-box is-layak">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px;">
                        <div>
                            <div style="font-size:12px;font-weight:600;color:#475569;">Keputusan Sistem:</div>
                            <div id="simVerdictTitle" style="font-size:20px;font-weight:800;color:#166534;margin-top:2px;">
                                Layak / Dapat Diterima
                            </div>
                        </div>
                        <span id="simVerdictBadge" class="kk-badge kk-badge-green"><i class="fa fa-check-circle"></i> LAYAK</span>
                    </div>

                    <div class="kk-grid-2" style="gap:10px;">
                        <div style="background:#fff;padding:12px;border-radius:10px;border:1px solid #e2e8f0;">
                            <div style="font-size:11.5px;color:#64748b;">Total Tambahan Daya</div>
                            <div id="simOutDaya" style="font-size:18px;font-weight:800;color:#0f172a;">105 kVA</div>
                        </div>
                        <div style="background:#fff;padding:12px;border-radius:10px;border:1px solid #e2e8f0;">
                            <div style="font-size:11.5px;color:#64748b;">Total Kapasitas Trafo</div>
                            <div id="simOutTrafo" style="font-size:18px;font-weight:800;color:#0f172a;">1 Buah (250 kVA)</div>
                        </div>
                        <div style="background:#fff;padding:12px;border-radius:10px;border:1px solid #e2e8f0;">
                            <div style="font-size:11.5px;color:#64748b;">Perkiraan Biaya Jaringan (RAB)</div>
                            <div id="simOutRab" style="font-size:17px;font-weight:800;color:#0d2137;">Rp 0</div>
                        </div>
                        <div style="background:#fff;padding:12px;border-radius:10px;border:1px solid #e2e8f0;">
                            <div style="font-size:11.5px;color:#64748b;">Proyeksi Pendapatan / Tahun</div>
                            <div id="simOutRev" style="font-size:17px;font-weight:800;color:#059669;">Rp 0</div>
                        </div>
                    </div>

                    <div style="margin-top:12px;background:#fff;padding:12px 14px;border-radius:10px;border:1px solid #e2e8f0;display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                        <div>
                            <div style="font-size:11.5px;color:#64748b;">Perkiraan Payback Period (Ambang &le; 6,5 Th)</div>
                            <div id="simOutPayback" style="font-size:15px;font-weight:800;color:#0f172a;">- Tahun</div>
                        </div>
                        <div>
                            <div style="font-size:11.5px;color:#64748b;">Vendor Kuota Mencukupi</div>
                            <div id="simOutKuota" style="font-size:15px;font-weight:800;color:#0369a1;">{{ $availableQuotaCount }} Vendor Tersedia</div>
                        </div>
                    </div>
                </div>

                <div style="margin-top:16px;padding:14px;border-radius:12px;background:#f8fafc;border:1px solid #e2e8f0;font-size:12.5px;color:#475569;line-height:1.6;">
                    <strong>Panduan Cepat:</strong><br>
                    &bull; <strong>ULP</strong> menggunakan simulasi ini saat konsultasi awal dengan calon pelanggan untuk mengecek apakah pemasangan trafo/jaringan sudah layak.<br>
                    &bull; Jika ULP belum memiliki kebutuhan teknis rinci (tiang/konduktor/aksesoris URAI), ajukan ke <strong>Form Kajian KKF</strong> agar <strong>Perencanaan</strong> dapat melengkapi kebutuhan URAI &amp; menetapkan vendor kontrak.
                </div>
            </div>
        </div>
    </div>

    {{-- ================= TAB 2: FORM KAJIAN KKF & URAI ================= --}}
    <div id="tabKkf" class="kk-tab-panel active">
        <div class="kk-card">
            <div class="kk-card-head">
                <div>
                    <div class="kk-card-title">
                        <i class="fa fa-file-pen" style="color:#1d4ed8;"></i>
                        <span id="kkfFormTitle">KAJIAN FINANSIAL (KKO / KKF) &amp; INPUT URAI — PLN UP3 JAMBI</span>
                    </div>
                    <div class="kk-card-sub">
                        Input Permohonan Pelanggan PB/PD &amp; Kebutuhan Material URAI 23 Kolom. Klik <strong>Buka Kajian</strong> untuk membuka proyek dari database, atau isi kajian baru lalu simpan.
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                    <span id="kkfBadgeKode" class="kk-badge kk-badge-blue">Kajian Baru</span>
                    <a id="kkfBtnExportPdf" href="#" target="_blank" class="kk-btn kk-btn-pdf kk-btn-sm" style="display:none;">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </a>
                    <a id="kkfBtnExportExcel" href="#" class="kk-btn kk-btn-excel kk-btn-sm" style="display:none;">
                        <i class="fa fa-file-excel"></i> Export Excel
                    </a>
                    <button type="button" class="kk-btn kk-btn-amber kk-btn-sm" onclick="testCalculateKajian()">
                        <i class="fa fa-calculator"></i> Hitung Kajian (Uji)
                    </button>
                    <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="resetKkfForm()">
                        <i class="fa fa-rotate-left"></i> Reset Formulir
                    </button>
                </div>
            </div>

            <input type="hidden" id="kkfId" value="">

            {{-- META PROYEK & BUKA KAJIAN TERSIMPAN (MIRIP GAS META BAR) --}}
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:14px;margin-bottom:14px;">
                <div class="kk-grid-4" style="margin-bottom:4px;">
                    <div class="kk-field">
                        <label class="kk-label">Buka Kajian Tersimpan (Database)</label>
                        <div style="display:flex;gap:6px;">
                            <select id="kkfSavedProjectSelect" class="kk-select" style="flex:1;" onchange="onSelectSavedProject(this.value)">
                                <option value="">-- Pilih Proyek Tersimpan ({{ $projects->count() }}) --</option>
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}" data-kode="{{ $proj->kode_kajian }}">
                                        {{ $proj->kode_kajian }} — {{ $proj->nama_pekerjaan }} ({{ $proj->ulp }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="kk-field">
                        <label class="kk-label">ID Kajian (untuk membuka)</label>
                        <div style="display:flex;gap:6px;">
                            <input type="text" id="kkfKodeLookup" class="kk-input" placeholder="001/KKF/ULP-.../X/2026" style="flex:1;">
                            <button type="button" class="kk-btn kk-btn-primary kk-btn-sm" onclick="openProjectByInput()">
                                <i class="fa fa-folder-open"></i> Buka Kajian
                            </button>
                        </div>
                    </div>
                    <div class="kk-field">
                        <label class="kk-label">ULP *</label>
                        <select id="kkfUlp" class="kk-select">
                            @foreach($ulpList as $ulp)
                                <option value="{{ $ulp }}">{{ $ulp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="kk-field">
                        <label class="kk-label">Jarak Proyek dari Gudang (km)</label>
                        <input type="number" id="kkfJarak" class="kk-input" min="0" step="0.1" value="0" oninput="recalcUraiTotals()" placeholder="Contoh: 12.5">
                    </div>
                </div>

                <div class="kk-grid-3" style="margin-top:6px;">
                    <div class="kk-field" style="grid-column:span 2 / span 2;">
                        <label class="kk-label">Nama Pekerjaan *</label>
                        <input type="text" id="kkfNama" class="kk-input" placeholder="Isi nama pekerjaan / judul kajian proyek...">
                    </div>
                    <div class="kk-field">
                        <label class="kk-label">Tahun Anggaran</label>
                        <input type="text" id="kkfTahun" class="kk-input" value="{{ date('Y') }}">
                    </div>
                </div>

                <div class="kk-grid-2">
                    <div class="kk-field" style="margin-bottom:0;">
                        <label class="kk-label">Catatan ULP (Lokasi, Kondisi Lapangan, Target Nyala)</label>
                        <textarea id="kkfCatatanUlp" rows="2" class="kk-textarea" placeholder="Jika ULP belum mengetahui kebutuhan tiang/material rinci, tuliskan kondisi lapangan di sini..."></textarea>
                    </div>
                    <div class="kk-field" style="margin-bottom:0;">
                        <label class="kk-label">Catatan Perencanaan / Evaluasi Teknis</label>
                        <textarea id="kkfCatatanPerencanaan" rows="2" class="kk-textarea" placeholder="Catatan hasil verifikasi teknis Perencanaan, penyesuaian RAB, atau rekomendasi kontrak..."></textarea>
                    </div>
                </div>

                <div id="kkfStatusBanner" class="kk-status-banner"></div>
            </div>
        </div>

        {{-- ENTRY PELANGGAN PB / PD (MIRIP GAS #pelangganPanel) --}}
        <div class="kk-card">
            <div class="kk-card-head">
                <div>
                    <div class="kk-card-title"><i class="fa fa-users" style="color:#0284c7;"></i> 1. Entry Data Permohonan Pelanggan (PB / PD — Maks 12 Kelompok Tarif)</div>
                    <div class="kk-card-sub">Pilih Jenis Permohonan (PB/PD), Kategori (R/S/B/I/P), dan Kelompok Tarif (40 standar EDIR 0014). Opsi daya otomatis menyesuaikan batas tarif.</div>
                </div>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                    <span class="kk-badge kk-badge-blue" id="kkfTotalPlgBadge">0 Pelanggan &bull; +0 kVA</span>
                    <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="addCustomerRow()">
                        <i class="fa fa-plus"></i> Tambah Kelompok Tarif
                    </button>
                </div>
            </div>

            <div class="kk-table-wrap">
                <table class="kk-table" id="customerTable">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th style="width:105px;">Jenis Permohonan</th>
                            <th style="width:85px;">Kategori</th>
                            <th style="min-width:235px;">Kelompok Tarif</th>
                            <th style="width:100px;">Jumlah Pelanggan</th>
                            <th style="width:130px;">Daya Sebelum (kVA)</th>
                            <th style="width:130px;">Daya Sesudah (kVA)</th>
                            <th style="width:135px;">Penambahan Daya (kVA)</th>
                            <th style="width:55px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="customerTableBody"></tbody>
                </table>
            </div>
        </div>

        {{-- INPUT URAI --}}
        <div class="kk-card">
            <div class="kk-card-head">
                <div>
                    <div class="kk-card-title"><i class="fa fa-table" style="color:#7c3aed;"></i> 2. Input URAI — 23 Kolom Kebutuhan Jaringan &amp; Material (PLN UP3 Jambi)</div>
                    <div class="kk-card-sub">
                        Sel abu-abu (<code>BA</code>, <code>BD</code>, <code>DS</code>, <code>DU</code>) dihitung otomatis dari konstruksi <code>AL</code> / <code>DP</code>. <strong>Double-click</strong> atau centang kotak untuk override manual (kuning); <strong>klik kanan</strong> sel atau lepas centang untuk reset otomatis. Mendukung Ctrl+V paste langsung dari Excel.
                    </div>
                </div>
                <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="addUraiRows(10)">+10 Baris</button>
                    <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="addUraiRows(50)">+50 Baris</button>
                    <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="addUraiRows(100)">+100 Baris</button>
                    <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="openRowCountModal()"><i class="fa fa-sliders"></i> Atur Baris (1–2.000)</button>
                    <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="openPasteModal()"><i class="fa fa-paste"></i> Tempel dari Excel</button>
                    <button type="button" class="kk-btn kk-btn-danger kk-btn-sm" onclick="clearUraiRows()"><i class="fa fa-trash"></i> Bersihkan</button>
                </div>
            </div>

            {{-- TOOLBAR FILTER & PER-PAGE INPUT URAI --}}
            <div class="urai-toolbar">
                <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                    <label style="font-size:12px;font-weight:600;color:#334155;">
                        Baris / Halaman:
                        <select id="uraiPerPage" class="kk-select" style="width:auto;display:inline-block;padding:5px 10px;font-size:12.5px;margin-left:4px;" onchange="changeUraiPerPage()">
                            <option value="10">10 baris</option>
                            <option value="25" selected>25 baris</option>
                            <option value="50">50 baris</option>
                            <option value="100">100 baris</option>
                        </select>
                    </label>
                    <label style="font-size:12px;font-weight:600;color:#334155;">
                        Kelompok Kolom:
                        <select id="uraiColGroup" class="kk-select" style="width:auto;display:inline-block;padding:5px 10px;font-size:12.5px;margin-left:4px;" onchange="applyUraiColumnGroup()">
                            <option value="all">Semua 23 kolom (AJ–PTR)</option>
                            <option value="sutm">SUTM, GW &amp; Trafo (AJ–PORTAL)</option>
                            <option value="sutr">SUTR Bongkar &amp; Pasang (DN–DW)</option>
                            <option value="pating">Pating &amp; Aksesoris (DR–PTR)</option>
                        </select>
                    </label>
                    <label style="font-size:12px;font-weight:600;color:#334155;">
                        Lompat ke kolom:
                        <select id="uraiJumpCol" class="kk-select" style="width:auto;display:inline-block;padding:5px 10px;font-size:12.5px;margin-left:4px;" onchange="jumpToUraiColumn(this.value)">
                            <option value="">-- Pilih Kolom --</option>
                            @foreach($uraiColumnMeta as $cKey => $cMeta)
                                <option value="{{ $cKey }}">{{ $cKey }} — {{ $cMeta['label'] }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div style="font-size:12px;color:#475569;font-weight:600;">
                    Total Baris URAI: <span id="uraiTotalRowsInfo" style="color:#0d2137;">25</span>
                </div>
            </div>

            <div class="urai-sheet-wrap" id="uraiSheetWrap">
                <table class="urai-table" id="uraiTable">
                    <thead>
                        <tr class="urai-tophead">
                            <th rowspan="2" class="ug-no">NO</th>
                            <th colspan="4" class="ug-sutm" data-head-group="sutm">SUTM</th>
                            <th colspan="1" class="ug-vol150" data-head-group="sutm">VOLUME SUTM</th>
                            <th colspan="1" class="ug-gw" data-head-group="sutm">GW</th>
                            <th colspan="1" class="ug-gw" data-head-group="sutm">HGW</th>
                            <th colspan="3" class="ug-trafo" data-head-group="sutm">TRAFO</th>
                            <th colspan="2" class="ug-sutrb" data-head-group="sutr">SUTR BONGKAR</th>
                            <th colspan="2" class="ug-sutrp" data-head-group="sutr">SUTR PASANG</th>
                            <th colspan="1" class="ug-voljtr" data-head-group="sutr">VOLUME SUTR</th>
                            <th colspan="2" class="ug-acc" data-head-group="sutr">SCHOER / GW-TR</th>
                            <th colspan="2" class="ug-acc" data-head-group="sutr">CROSS / HGW-TR</th>
                            <th colspan="2" class="ug-tap" data-head-group="sutr">TAP CONNECTOR</th>
                            <th colspan="2" class="ug-pat" data-head-group="pating">PATING</th>
                        </tr>
                        <tr class="urai-subhead">
                            <th class="ug-sutm" data-ucol="AJ">BONGKAR</th>
                            <th class="ug-sutm" data-ucol="AK">EKS</th>
                            <th class="ug-sutm" data-ucol="AL">BARU</th>
                            <th class="ug-sutm" data-ucol="AM">TANPA TIANG</th>
                            <th class="ug-vol150" data-ucol="VOL150">VOL150</th>
                            <th class="ug-gw" data-ucol="BA">BARU</th>
                            <th class="ug-gw" data-ucol="BD">BARU</th>
                            <th class="ug-trafo" data-ucol="BG">BARU</th>
                            <th class="ug-trafo" data-ucol="CANTOL">CANTOL</th>
                            <th class="ug-trafo" data-ucol="PORTAL">PORTAL</th>
                            <th class="ug-sutrb" data-ucol="DN">MURNI</th>
                            <th class="ug-sutrb" data-ucol="DO">TANPA TIANG</th>
                            <th class="ug-sutrp" data-ucol="DP">MURNI</th>
                            <th class="ug-sutrp" data-ucol="DQ">TANPA TIANG</th>
                            <th class="ug-voljtr" data-ucol="VOLJTR">VOLJTR</th>
                            <th class="ug-acc" data-ucol="DR">BONGKAR</th>
                            <th class="ug-acc" data-ucol="DS">PASANG</th>
                            <th class="ug-acc" data-ucol="DT">BONGKAR</th>
                            <th class="ug-acc" data-ucol="DU">PASANG</th>
                            <th class="ug-tap" data-ucol="DV">BONGKAR</th>
                            <th class="ug-tap" data-ucol="DW">PASANG</th>
                            <th class="ug-pat" data-ucol="PTM">PTM</th>
                            <th class="ug-pat" data-ucol="PTR">PTR</th>
                        </tr>
                    </thead>
                    <tbody id="uraiTableBody"></tbody>
                </table>
            </div>

            {{-- PAGINATION INPUT URAI --}}
            <div class="kk-pager">
                <div id="uraiPagerInfo">Menampilkan baris 1–25 dari 25 baris</div>
                <div class="kk-pager-controls">
                    <button type="button" class="kk-page-btn" id="uraiPrevBtn" onclick="goUraiPage(uraiState.page - 1)">&lsaquo; Sebelumnya</button>
                    <select id="uraiPageJump" class="kk-select" style="width:auto;padding:4px 10px;font-size:12.5px;" onchange="goUraiPage(parseInt(this.value, 10))"></select>
                    <button type="button" class="kk-page-btn" id="uraiNextBtn" onclick="goUraiPage(uraiState.page + 1)">Berikutnya &rsaquo;</button>
                </div>
            </div>

            {{-- REKAP LIVE & SIMPAN --}}
            <div style="margin-top:16px;padding:16px;border-radius:12px;background:#f8fafc;border:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
                <div style="display:flex;gap:18px;flex-wrap:wrap;font-size:13px;">
                    <div><span style="color:#64748b;">Trafo:</span> <strong id="kkfSumTrafo">0 bh (0 kVA)</strong></div>
                    <div><span style="color:#64748b;">SUTM / SUTR:</span> <strong id="kkfSumKm">0 / 0 km</strong></div>
                    <div><span style="color:#64748b;">Tiang Total:</span> <strong id="kkfSumTiang">0 btg</strong></div>
                    <div><span style="color:#64748b;">Estimasi RAB:</span> <strong id="kkfSumRab">Rp 0</strong></div>
                    <div><span style="color:#64748b;">Kelayakan:</span> <span id="kkfSumVerdict" class="kk-badge kk-badge-slate">Belum Dihitung</span></div>
                </div>

                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <button type="button" class="kk-btn kk-btn-amber" onclick="testCalculateKajian()">
                        <i class="fa fa-calculator"></i> Hitung Kajian (Uji)
                    </button>
                    <button type="button" class="kk-btn kk-btn-outline" onclick="saveKkfForm('draft')">
                        <i class="fa fa-floppy-disk"></i> Simpan Draft
                    </button>
                    <button type="button" class="kk-btn kk-btn-primary" onclick="saveKkfForm('diajukan')">
                        <i class="fa fa-paper-plane"></i> Ajukan Kajian (ULP)
                    </button>
                    <button type="button" class="kk-btn kk-btn-emerald" onclick="saveKkfForm('dilengkapi_perencanaan')">
                        <i class="fa fa-check-double"></i> Simpan Kebutuhan (Perencanaan)
                    </button>
                </div>
            </div>

            {{-- PANEL HASIL UJI PERHITUNGAN MASTER (KKO / KKF / MDU / BOX APP) --}}
            <div id="kkfCalcResultCard" style="display:none;margin-top:16px;padding:18px;border-radius:14px;background:#fff;border:1px solid #cbd5e1;box-shadow:0 2px 10px rgba(15,23,42,.05);">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:12px;padding-bottom:10px;border-bottom:1px solid #f1f5f9;">
                    <div>
                        <strong style="font-size:14.5px;color:#0d2137;"><i class="fa fa-chart-column"></i> Hasil Uji Perhitungan KKO &amp; KKF (EDIR 0014 + Rekap MDU + Box APP)</strong>
                        <div style="font-size:12px;color:#64748b;margin-top:2px;">Hasil simulasi langsung dari form Pelanggan PB/PD &amp; tabel URAI sebelum/sesudah disimpan ke database.</div>
                    </div>
                    <span id="calcVerdictBadge" class="kk-badge kk-badge-slate">-</span>
                </div>

                <div class="kk-mini-grid" style="margin-bottom:12px;">
                    <div class="kk-mini-box"><div class="lbl">Est. Nilai RAB</div><div class="val" id="calcRabRp">Rp 0</div></div>
                    <div class="kk-mini-box"><div class="lbl">BP (EDIR 0014)</div><div class="val" id="calcBpRp">Rp 0</div></div>
                    <div class="kk-mini-box"><div class="lbl">UJL (EDIR 0014)</div><div class="val" id="calcUjlRp">Rp 0</div></div>
                    <div class="kk-mini-box"><div class="lbl">Est. Pendapatan / Thn</div><div class="val" id="calcRevRp" style="color:#059669;">Rp 0</div></div>
                    <div class="kk-mini-box"><div class="lbl">Payback Period</div><div class="val" id="calcPayback">-</div></div>
                    <div class="kk-mini-box"><div class="lbl">Vendor Kuota Cukup</div><div class="val" id="calcKuota" style="color:#0369a1;">0 / 0</div></div>
                </div>

                <div class="kk-grid-3" style="gap:12px;">
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px;font-size:12px;">
                        <strong style="color:#0d2137;display:block;margin-bottom:6px;">Rekap MDU &amp; Jaringan</strong>
                        <div id="calcMduSummary" style="line-height:1.7;color:#334155;"></div>
                    </div>
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px;font-size:12px;">
                        <strong style="color:#0d2137;display:block;margin-bottom:6px;">Rekap Konfigurasi Trafo (Cantol / Portal)</strong>
                        <div id="calcTrafoSummary" style="line-height:1.7;color:#334155;"></div>
                    </div>
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px;font-size:12px;">
                        <strong style="color:#0d2137;display:block;margin-bottom:6px;">Rekap Kebutuhan Box APP (53–197 kVA)</strong>
                        <div id="calcBoxAppSummary" style="line-height:1.7;color:#334155;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= TAB 3: DAFTAR KAJIAN, EXPORT & KUOTA KONTRAK ================= --}}
    <div id="tabDaftar" class="kk-tab-panel">
        {{-- DISTRIBUSI KAJIAN PER ULP --}}
        <div class="kk-card">
            <div class="kk-card-head">
                <div>
                    <div class="kk-card-title"><i class="fa fa-map-location-dot" style="color:#0d2137;"></i> Distribusi Kajian per ULP UP3 Jambi</div>
                    <div class="kk-card-sub">Klik kartu ULP untuk menyaring daftar kajian di bawah berdasarkan ULP pengusul.</div>
                </div>
                <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="filterByUlpCard('')">
                    <i class="fa fa-filter-circle-xmark"></i> Tampilkan Semua ULP
                </button>
            </div>
            <div class="kk-ulp-dist-grid">
                @foreach($ulpList as $ulp)
                    @php
                        $uCount = $projects->where('ulp', $ulp)->count();
                        $uDaya = $projects->where('ulp', $ulp)->sum('total_daya_kva');
                        $uPct = round(($uCount / $maxPerUlp) * 100, 1);
                    @endphp
                    <button type="button" class="kk-ulp-card" data-ulp-card="{{ $ulp }}" onclick="filterByUlpCard({{ json_encode($ulp) }})">
                        <div style="display:flex;justify-content:space-between;align-items:center;font-weight:700;font-size:13px;color:#0f172a;">
                            <span>{{ $ulp }}</span>
                            <span class="kk-badge kk-badge-blue">{{ $uCount }} kajian</span>
                        </div>
                        <div style="font-size:11.5px;color:#64748b;margin-top:4px;">Total Daya: {{ number_format($uDaya, 1, ',', '.') }} kVA</div>
                        <div class="kk-progress-bg">
                            <div class="kk-progress-bar" style="width:{{ $uPct }}%;background:#0d2137;"></div>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- MONITOR KUOTA KONTRAK --}}
        <div class="kk-card">
            <div class="kk-card-head">
                <div>
                    <div class="kk-card-title"><i class="fa fa-gauge-high" style="color:#dc2626;"></i> Monitor Kuota Pekerjaan &amp; Kontrak Vendor</div>
                    <div class="kk-card-sub">
                        Sumber: Sheet <strong>KUOTA VENDOR</strong> + Master Kuota Perencanaan dikurangi proyek terkontrak.
                        Jika sisa kuota habis (Rp 0 / slot penuh), vendor <strong>dikunci otomatis</strong>.
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                    <input type="text" id="quotaSearch" class="kk-input" style="width:200px;" placeholder="Cari vendor / kontrak..." oninput="applyQuotaPagination(1)">
                    <select id="quotaPerPage" class="kk-select" style="width:115px;" onchange="applyQuotaPagination(1)">
                        <option value="10" selected>10 / hal</option>
                        <option value="25">25 / hal</option>
                        <option value="50">50 / hal</option>
                        <option value="100">100 / hal</option>
                    </select>
                    <button type="button" class="kk-btn kk-btn-primary kk-btn-sm" onclick="openQuotaModal()">
                        <i class="fa fa-plus"></i> Tambah / Atur Kuota Kontrak
                    </button>
                </div>
            </div>

            <div class="kk-table-wrap">
                <table class="kk-table" id="quotaTable">
                    <thead>
                        <tr>
                            <th>Vendor Pelaksana</th>
                            <th>No. Kontrak &amp; Pekerjaan</th>
                            <th>Sumber</th>
                            <th>Kuota Awal (Rp)</th>
                            <th>Terpakai (Sheet + Kajian)</th>
                            <th>Sisa Kuota</th>
                            <th>Slot Pekerjaan</th>
                            <th>Status Kontrak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendorQuotas as $vq)
                            @php
                                $terpakaiTotal = ($vq['realisasi_sheet_rp'] ?? 0) + ($vq['terpakai_kajian_rp'] ?? 0);
                                $persen = max(0, min(100, (float) ($vq['sisa_kuota_persen'] ?? 0)));
                                $barColor = $vq['kuota_habis'] ? '#dc2626' : ($persen < 20 ? '#d97706' : '#059669');
                            @endphp
                            <tr class="quota-data-row">
                                <td><strong>{{ $vq['vendor'] }}</strong></td>
                                <td>
                                    <div>{{ $vq['no_kontrak'] ?: '-' }}</div>
                                    <small style="color:#64748b;">{{ $vq['jenis_pekerjaan'] }}</small>
                                </td>
                                <td><span class="kk-badge kk-badge-slate">{{ $vq['sumber'] }}</span></td>
                                <td>Rp {{ number_format($vq['kuota_awal_rp'], 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($terpakaiTotal, 0, ',', '.') }}</td>
                                <td style="min-width:170px;">
                                    <div style="display:flex;justify-content:space-between;font-weight:700;color:{{ $vq['kuota_habis'] ? '#dc2626' : '#0f172a' }};">
                                        <span>Rp {{ number_format($vq['sisa_kuota_rp'], 0, ',', '.') }}</span>
                                        <span>{{ number_format($persen, 1) }}%</span>
                                    </div>
                                    <div class="kk-progress-bg">
                                        <div class="kk-progress-bar" style="width:{{ $persen }}%;background:{{ $barColor }};"></div>
                                    </div>
                                </td>
                                <td>
                                    @if(($vq['kuota_pekerjaan_maks'] ?? 0) > 0)
                                        <strong>{{ $vq['jumlah_terkontrak'] }} / {{ $vq['kuota_pekerjaan_maks'] }}</strong> pekerjaan
                                    @else
                                        {{ $vq['jumlah_terkontrak'] }} kajian (berbasis Rp)
                                    @endif
                                </td>
                                <td>
                                    @if($vq['kuota_habis'])
                                        <span class="kk-badge kk-badge-red" title="{{ $vq['alasan_habis'] }}">
                                            <i class="fa fa-lock"></i> KUOTA HABIS &bull; TIDAK BISA KONTRAK
                                        </span>
                                    @else
                                        <span class="kk-badge kk-badge-green"><i class="fa fa-check"></i> Kuota Tersedia</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr id="quotaEmptyRow">
                                <td colspan="8" style="text-align:center;color:#64748b;padding:24px;">
                                    Belum ada data kuota vendor dari sheet KUOTA VENDOR atau Master Kuota Kontrak. Klik <strong>Tambah / Atur Kuota Kontrak</strong> di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="kk-pager">
                <div id="quotaPagerInfo">Menampilkan 0 vendor</div>
                <div class="kk-pager-controls" id="quotaPagerBtns"></div>
            </div>
        </div>

        {{-- DAFTAR KAJIAN --}}
        <div class="kk-card">
            <div class="kk-card-head">
                <div>
                    <div class="kk-card-title"><i class="fa fa-list-check" style="color:#0d2137;"></i> Daftar Kajian Kelayakan &amp; Simulasi Proyek</div>
                    <div class="kk-card-sub">Pilih tampil 10 / 25 / 50 / 100 data per halaman, filter ULP/Tahun/Status, atau Export Rekap PDF &amp; Excel.</div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                    <a href="{{ $exportBaseUrl . '/export-pdf' }}" id="btnRecapExportPdf" target="_blank" class="kk-btn kk-btn-pdf kk-btn-sm">
                        <i class="fa fa-file-pdf"></i> Export PDF (Rekap)
                    </a>
                    <a href="{{ $exportBaseUrl . '/export-excel' }}" id="btnRecapExportExcel" class="kk-btn kk-btn-excel kk-btn-sm">
                        <i class="fa fa-file-excel"></i> Export Excel (Rekap)
                    </a>
                </div>
            </div>

            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;align-items:center;">
                <input type="text" id="filterSearch" class="kk-input" style="width:230px;" placeholder="Cari kode, pekerjaan, ULP, vendor..." oninput="filterProjectsTable(true)">
                <select id="filterUlp" class="kk-select" style="width:160px;" onchange="syncUlpCardSelection(); filterProjectsTable(true);">
                    <option value="">Semua ULP</option>
                    @foreach($ulpList as $ulp)
                        <option value="{{ $ulp }}">{{ $ulp }}</option>
                    @endforeach
                </select>
                <select id="filterYear" class="kk-select" style="width:125px;" onchange="filterProjectsTable(true)">
                    <option value="">Semua Tahun</option>
                    @foreach(array_unique(array_merge([(string) date('Y')], $projects->pluck('tahun')->filter()->all())) as $yr)
                        <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                    @endforeach
                </select>
                <select id="filterStatus" class="kk-select" style="width:170px;" onchange="filterProjectsTable(true)">
                    <option value="">Semua Status</option>
                    <option value="draft">Draft / Simulasi</option>
                    <option value="diajukan">Diajukan ULP</option>
                    <option value="dilengkapi_perencanaan">Dilengkapi Perencanaan</option>
                    <option value="layak">Disetujui Layak</option>
                    <option value="tidak_layak">Tidak Layak</option>
                    <option value="terkontrak">Terkontrak</option>
                </select>
                <select id="projectsPerPage" class="kk-select" style="width:120px;margin-left:auto;" onchange="filterProjectsTable(true)">
                    <option value="10" selected>Tampil: 10</option>
                    <option value="25">Tampil: 25</option>
                    <option value="50">Tampil: 50</option>
                    <option value="100">Tampil: 100</option>
                </select>
            </div>

            <div class="kk-table-wrap">
                <table class="kk-table" id="projectsListTable">
                    <thead>
                        <tr>
                            <th>Kode &amp; Mode</th>
                            <th>ULP &amp; Nama Pekerjaan</th>
                            <th>Beban Pelanggan</th>
                            <th>Kebutuhan Jaringan</th>
                            <th>RAB &amp; Payback</th>
                            <th>Hasil Kelayakan</th>
                            <th>Status &amp; Kontrak</th>
                            <th>Aksi &amp; Export</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $p)
                            @php
                                $statusMap = [
                                    'draft' => ['Draft', 'kk-badge-slate'],
                                    'diajukan' => ['Diajukan ULP', 'kk-badge-blue'],
                                    'dilengkapi_perencanaan' => ['Diisi Perencanaan', 'kk-badge-amber'],
                                    'layak' => ['Layak / Diterima', 'kk-badge-green'],
                                    'tidak_layak' => ['Belum Layak', 'kk-badge-red'],
                                    'terkontrak' => ['Terkontrak', 'kk-badge-green'],
                                ];
                                [$stLabel, $stClass] = $statusMap[$p->status] ?? [$p->status, 'kk-badge-slate'];
                            @endphp
                            <tr class="project-data-row" data-year="{{ $p->tahun }}" data-status="{{ $p->status }}" data-ulp="{{ $p->ulp }}">
                                <td>
                                    <div style="font-weight:700;color:#0d2137;">{{ $p->kode_kajian }}</div>
                                    <span class="kk-badge {{ $p->mode === 'simulasi' ? 'kk-badge-amber' : 'kk-badge-blue' }}" style="margin-top:3px;">
                                        {{ strtoupper($p->mode) }} &bull; {{ $p->tahun }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight:700;color:#0f172a;">{{ $p->nama_pekerjaan }}</div>
                                    <small style="color:#64748b;">ULP {{ $p->ulp }} &bull; Jarak {{ number_format($p->jarak_gudang_km, 1) }} km</small>
                                </td>
                                <td>
                                    <strong>{{ number_format($p->total_pelanggan) }} plg</strong><br>
                                    <small style="color:#0369a1;font-weight:600;">+{{ number_format($p->total_daya_kva, 2, ',', '.') }} kVA</small>
                                </td>
                                <td>
                                    <div>Trafo: <strong>{{ $p->jumlah_trafo }} bh</strong> ({{ number_format($p->kapasitas_trafo_kva, 0) }} kVA)</div>
                                    <small style="color:#64748b;">SUTM {{ $p->estimasi_sutm_km }} km &bull; SUTR {{ $p->estimasi_sutr_km }} km</small>
                                </td>
                                <td>
                                    <div><strong>Rp {{ number_format($p->estimasi_nilai_rab, 0, ',', '.') }}</strong></div>
                                    <small style="color:#64748b;">Payback: {{ $p->payback_tahun !== null ? $p->payback_tahun . ' th' : '-' }}</small>
                                </td>
                                <td>
                                    <span class="kk-badge {{ $p->layak_otomatis ? 'kk-badge-green' : 'kk-badge-red' }}">
                                        {{ $p->hasil_ringkas ?: ($p->layak_otomatis ? 'Layak' : 'Belum Layak') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="kk-badge {{ $stClass }}">{{ $stLabel }}</span>
                                    @if($p->vendor_kontrak)
                                        <div style="margin-top:4px;font-size:11.5px;font-weight:600;color:#166534;">
                                            <i class="fa fa-building"></i> {{ $p->vendor_kontrak }}
                                        </div>
                                        <small style="color:#64748b;">{{ $p->no_kontrak }}</small>
                                    @endif
                                </td>
                                <td>
                                    <div style="display:flex;gap:5px;flex-wrap:wrap;">
                                        <button type="button" class="kk-btn kk-btn-outline kk-btn-xs" onclick="loadProjectIntoKkf({{ $p->id }})">
                                            <i class="fa fa-pen-to-square"></i> URAI
                                        </button>
                                        <a href="{{ $exportBaseUrl . '/' . $p->id . '/export-pdf' }}" target="_blank" class="kk-btn kk-btn-pdf kk-btn-xs" title="Export PDF KKO-KKF">
                                            <i class="fa fa-file-pdf"></i> PDF
                                        </a>
                                        <a href="{{ $exportBaseUrl . '/' . $p->id . '/export-excel' }}" class="kk-btn kk-btn-excel kk-btn-xs" title="Export Excel KKO-KKF">
                                            <i class="fa fa-file-excel"></i> XLS
                                        </a>
                                        <button type="button" class="kk-btn kk-btn-primary kk-btn-xs" onclick="openContractModal({{ $p->id }}, {{ json_encode($p->kode_kajian) }}, {{ json_encode($p->nama_pekerjaan) }}, {{ json_encode($p->status) }}, {{ (float) $p->estimasi_nilai_rab }}, {{ json_encode($p->vendor_kontrak ?? '') }}, {{ json_encode($p->no_kontrak ?? '') }}, {{ json_encode($p->catatan_perencanaan ?? '') }})">
                                            <i class="fa fa-file-signature"></i> Kontrak
                                        </button>
                                        <button type="button" class="kk-btn kk-btn-danger kk-btn-xs" onclick="deleteProject({{ $p->id }}, {{ json_encode($p->kode_kajian) }})" title="Hapus Kajian">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="projectsEmptyRow">
                                <td colspan="8" style="text-align:center;color:#64748b;padding:28px;">
                                    Belum ada data kajian kelayakan atau simulasi. Silakan mulai dari Tab <strong>Simulasi Cepat</strong> atau <strong>Kajian KKF</strong>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="kk-pager">
                <div id="projectsPagerInfo">Menampilkan 0 kajian</div>
                <div class="kk-pager-controls" id="projectsPagerBtns"></div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL ATUR JUMLAH BARIS URAI (1-2000) --}}
<div id="rowCountModal" class="kk-modal-backdrop">
    <div class="kk-modal" style="width:min(460px, 100%);">
        <div class="kk-card-head">
            <div>
                <div class="kk-card-title"><i class="fa fa-sliders"></i> Atur Jumlah Baris Input URAI</div>
                <div class="kk-card-sub">Masukkan jumlah total baris (1 s/d 2.000 baris), sesuai kapasitas KKO–KKF PLN UP3 Jambi.</div>
            </div>
            <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="closeModal('rowCountModal')">Tutup</button>
        </div>
        <div class="kk-field">
            <label class="kk-label">Jumlah Total Baris (1 – 2.000)</label>
            <input type="number" id="uraiTargetRowCount" class="kk-input" min="1" max="2000" value="25">
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;">
            <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="document.getElementById('uraiTargetRowCount').value=25">25</button>
            <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="document.getElementById('uraiTargetRowCount').value=50">50</button>
            <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="document.getElementById('uraiTargetRowCount').value=100">100</button>
            <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="document.getElementById('uraiTargetRowCount').value=250">250</button>
            <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="document.getElementById('uraiTargetRowCount').value=500">500</button>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;">
            <button type="button" class="kk-btn kk-btn-outline" onclick="closeModal('rowCountModal')">Batal</button>
            <button type="button" class="kk-btn kk-btn-primary" onclick="applyUraiRowCount()">Terapkan Baris</button>
        </div>
    </div>
</div>

{{-- MODAL PASTE EXCEL URAI --}}
<div id="pasteUraiModal" class="kk-modal-backdrop">
    <div class="kk-modal">
        <div class="kk-card-head">
            <div>
                <div class="kk-card-title"><i class="fa fa-paste"></i> Tempel Data URAI dari Excel</div>
                <div class="kk-card-sub">Copy baris kolom AJ s/d PTR dari Excel Anda lalu paste di bawah ini (dipisah Tab).</div>
            </div>
            <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="closeModal('pasteUraiModal')">Tutup</button>
        </div>
        <textarea id="pasteUraiText" rows="9" class="kk-textarea" placeholder="Paste data tabel Excel (Tab-separated)..."></textarea>
        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:14px;">
            <button type="button" class="kk-btn kk-btn-outline" onclick="closeModal('pasteUraiModal')">Batal</button>
            <button type="button" class="kk-btn kk-btn-primary" onclick="applyExcelPaste()">Masukkan ke Tabel URAI</button>
        </div>
    </div>
</div>

{{-- MODAL KONTRAK & KEPUTUSAN KELAYAKAN --}}
<div id="contractModal" class="kk-modal-backdrop">
    <div class="kk-modal">
        <div class="kk-card-head">
            <div>
                <div class="kk-card-title"><i class="fa fa-file-signature"></i> Keputusan Kelayakan &amp; Penetapan Kontrak Vendor</div>
                <div class="kk-card-sub" id="cmSubtitle">-</div>
            </div>
            <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="closeModal('contractModal')">Tutup</button>
        </div>
        <input type="hidden" id="cmProjectId">
        <div class="kk-grid-2">
            <div class="kk-field">
                <label class="kk-label">Status Keputusan</label>
                <select id="cmStatus" class="kk-select" onchange="toggleCmVendorSection()">
                    <option value="dilengkapi_perencanaan">Dilengkapi Perencanaan</option>
                    <option value="layak">Layak / Dapat Diterima</option>
                    <option value="tidak_layak">Belum Layak / Ditolak</option>
                    <option value="terkontrak">Terkontrak (Potong Kuota Vendor)</option>
                </select>
            </div>
            <div class="kk-field">
                <label class="kk-label">Nilai RAB / Kontrak (Rp)</label>
                <input type="number" id="cmNilai" class="kk-input" min="0" step="1000">
            </div>
        </div>

        <div id="cmVendorWrap" style="padding:14px;border-radius:12px;background:#f8fafc;border:1px solid #cbd5e1;margin-bottom:12px;">
            <div class="kk-field">
                <label class="kk-label">Pilih Vendor Kontrak (Vendor Kuota Habis Dikunci)</label>
                <select id="cmVendor" class="kk-select" onchange="syncCmContractNo()">
                    <option value="">-- Pilih Vendor Pelaksana --</option>
                    @foreach($vendorQuotas as $vq)
                        <option
                            value="{{ $vq['vendor'] }}"
                            data-no="{{ $vq['no_kontrak'] }}"
                            data-sisa="{{ $vq['sisa_kuota_rp'] }}"
                            data-habis="{{ $vq['kuota_habis'] ? '1' : '0' }}"
                            {{ $vq['kuota_habis'] ? 'disabled' : '' }}
                        >
                            {{ $vq['vendor'] }} — Sisa: Rp {{ number_format($vq['sisa_kuota_rp'], 0, ',', '.') }}
                            {{ $vq['kuota_habis'] ? ' [KUOTA HABIS - TIDAK BISA KONTRAK]' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="kk-field" style="margin-bottom:0;">
                <label class="kk-label">Nomor Kontrak / SPK</label>
                <input type="text" id="cmNoKontrak" class="kk-input" placeholder="Otomatis dari data kuota / isi manual...">
            </div>
            <div id="cmQuotaWarning" style="display:none;margin-top:10px;padding:10px;border-radius:8px;background:#fef2f2;color:#b91c1c;font-size:12.5px;font-weight:600;"></div>
        </div>

        <div class="kk-field">
            <label class="kk-label">Catatan Perencanaan / Alasan Keputusan</label>
            <textarea id="cmCatatan" rows="2" class="kk-textarea"></textarea>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:14px;">
            <button type="button" class="kk-btn kk-btn-outline" onclick="closeModal('contractModal')">Batal</button>
            <button type="button" class="kk-btn kk-btn-emerald" onclick="submitContractModal()"><i class="fa fa-check"></i> Simpan Keputusan &amp; Kontrak</button>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH / ATUR KUOTA KONTRAK --}}
<div id="quotaModal" class="kk-modal-backdrop">
    <div class="kk-modal">
        <div class="kk-card-head">
            <div>
                <div class="kk-card-title"><i class="fa fa-coins"></i> Tambah / Atur Kuota Pekerjaan &amp; Kontrak Vendor</div>
                <div class="kk-card-sub">Atur batas kuota (Rp) dan batas jumlah pekerjaan. Jika sisa kuota 0, vendor otomatis tidak bisa mengontrak lagi.</div>
            </div>
            <button type="button" class="kk-btn kk-btn-outline kk-btn-sm" onclick="closeModal('quotaModal')">Tutup</button>
        </div>
        <form method="POST" action="{{ route($routePrefix . '.kuota.store') }}">
            @csrf
            <div class="kk-grid-2">
                <div class="kk-field">
                    <label class="kk-label">Nama Vendor *</label>
                    <input type="text" name="vendor" class="kk-input" required placeholder="PT Contoh Vendor Energi">
                </div>
                <div class="kk-field">
                    <label class="kk-label">Nomor Kontrak</label>
                    <input type="text" name="no_kontrak" class="kk-input" placeholder="001.PJ/DAN.01.02/...">
                </div>
            </div>
            <div class="kk-grid-2">
                <div class="kk-field">
                    <label class="kk-label">Jenis Pekerjaan</label>
                    <input type="text" name="jenis_pekerjaan" class="kk-input" value="Pembangunan Jaringan & PB/PD">
                </div>
                <div class="kk-field">
                    <label class="kk-label">Tahun Kontrak *</label>
                    <input type="text" name="tahun" class="kk-input" value="{{ date('Y') }}" required>
                </div>
            </div>
            <div class="kk-grid-3">
                <div class="kk-field">
                    <label class="kk-label">Kuota Awal (Rp) *</label>
                    <input type="number" name="kuota_awal_rp" class="kk-input" min="0" step="1000" value="1000000000" required>
                </div>
                <div class="kk-field">
                    <label class="kk-label">Realisasi Manual Sebelumnya (Rp)</label>
                    <input type="number" name="terpakai_manual_rp" class="kk-input" min="0" step="1000" value="0">
                </div>
                <div class="kk-field">
                    <label class="kk-label">Maks. Pekerjaan (0 = ikut Rp)</label>
                    <input type="number" name="kuota_pekerjaan_maks" class="kk-input" min="0" value="0">
                </div>
            </div>
            <div class="kk-field">
                <label style="display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:600;">
                    <input type="checkbox" name="status_aktif" value="1" checked> Kontrak Aktif (boleh menerima proyek baru selama kuota tersedia)
                </label>
            </div>
            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:14px;">
                <button type="button" class="kk-btn kk-btn-outline" onclick="closeModal('quotaModal')">Batal</button>
                <button type="submit" class="kk-btn kk-btn-primary"><i class="fa fa-floppy-disk"></i> Simpan Kuota Vendor</button>
            </div>
        </form>
    </div>
</div>

<script>
const KK_CSRF = @json(csrf_token());
const KK_ROUTES = {
    simulate: @json(route($routePrefix . '.simulate')),
    store: @json(route($routePrefix . '.store')),
    showBase: @json($exportBaseUrl)
};
const KK_MASTERS = {
    tarifDefaultDaya: @json($tarifDefaultDaya),
    tarifDaya: @json($tarifDayaList),
    edirTarifSpecs: {!! json_encode($edirTarifSpecs ?? []) !!},
    boxAppCapacities: {!! json_encode($boxAppCapacities ?? [53, 66, 82.5, 105, 131, 147, 164, 197]) !!},
    dayaOptions: @json($dayaOptions),
    trafoOptions: @json($trafoOptions),
    uraiMasters: @json($uraiMasters),
    unitCosts: @json($unitCosts),
    uraiCols: @json($uraiColumns),
    quotas: @json($vendorQuotas)
};

const URAI_COL_GROUPS = {
    sutm: ['AJ','AK','AL','AM','VOL150','BA','BD','BG','CANTOL','PORTAL'],
    sutr: ['DN','DO','DP','DQ','VOLJTR','DR','DS','DT','DU','DV','DW'],
    pating: ['DR','DS','DT','DU','DV','DW','PTM','PTR']
};

const uraiState = {
    page: 1,
    perPage: 25
};
const listPagerState = {
    projectsPage: 1,
    quotaPage: 1
};

function formatRp(val){
    return 'Rp ' + Math.round(Number(val) || 0).toLocaleString('id-ID');
}

function escapeHtml(str){
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function switchKkTab(tabId, btn){
    document.querySelectorAll('.kk-tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.kk-tab-btn').forEach(b => b.classList.remove('active'));
    const panel = document.getElementById(tabId);
    if (panel) panel.classList.add('active');
    if (btn) btn.classList.add('active');
}

function closeModal(id){
    const el = document.getElementById(id);
    if (el) el.classList.remove('open');
}

function setKkfStatus(msg, type = 'info'){
    const el = document.getElementById('kkfStatusBanner');
    if (!el) return;
    if (!msg) {
        el.className = 'kk-status-banner';
        el.textContent = '';
        return;
    }
    el.className = 'kk-status-banner ' + (type === 'ok' ? 'ok' : (type === 'err' ? 'err' : 'info'));
    el.textContent = msg;
}

function normTarifStr(v){
    return String(v ?? '').replace(/\s+/g, ' ').trim();
}

function resolveCanonicalTarif(raw){
    const n = normTarifStr(raw).toLowerCase();
    if (!n) return '';
    return KK_MASTERS.tarifDaya.find(t => normTarifStr(t).toLowerCase() === n) || normTarifStr(raw);
}

function getDefaultDayaFromTarif(tarifStr){
    const canon = resolveCanonicalTarif(tarifStr);
    if (canon && Object.prototype.hasOwnProperty.call(KK_MASTERS.tarifDefaultDaya, canon)) {
        return Number(KK_MASTERS.tarifDefaultDaya[canon]) || 0;
    }
    const mKva = canon.match(/([\d.,]+)\s*kVA/i);
    if (mKva) return parseFloat(mKva[1].replace(/\./g, '').replace(',', '.')) || 0;
    const mVa = canon.match(/([\d.,]+)\s*VA/i);
    if (mVa) return (parseFloat(mVa[1].replace(/[.,]/g, '')) || 0) / 1000;
    return 0;
}

const GAS_MASTER_DAYA = [0.22,0.45,0.9,1.3,2.2,3.5,4.4,5.5,6.6,7.7,10.6,11,13.2,16.5,22,23,33,41.5,53,66,82.5,105,131,147,164,197,200,30000];
function dayaSesuaiTarif(tarifStr){
    const def = getDefaultDayaFromTarif(tarifStr);
    const semua = [...new Set([...GAS_MASTER_DAYA, ...(KK_MASTERS.dayaOptions || []), def].filter(n => Number.isFinite(Number(n)) && Number(n) > 0))].sort((a,b) => a - b);
    const t = normTarifStr(tarifStr).replace(/s\.d/gi, 's/d').replace(/(\d+)\.(\d{3})/g, '$1$2').replace(',', '.');
    const parseToken = (s, u) => (/kva/i.test(u) || !/va/i.test(u)) ? Number(s) : Number(s) / 1000;
    let m = t.match(/(\d+(?:\.\d+)?)\s*(kva|va)?\s*s\/d\s*(\d+(?:\.\d+)?)\s*(kva|va)?/i);
    if (m) {
        const u2 = m[4] || m[2] || 'VA', u1 = m[2] || u2;
        const a = parseToken(m[1], u1), b = parseToken(m[3], u2);
        const list = semua.filter(n => n >= a - 1e-6 && n <= b + 1e-6);
        return list.length ? list : [def || a];
    }
    m = t.match(/(\d+(?:\.\d+)?)\s*(kva|va)\s*ke\s*atas|(\d+(?:\.\d+)?)\s*(kva|va)\s*keatas/i);
    if (m) {
        const num = m[1] || m[3], unit = m[2] || m[4];
        const a = parseToken(num, unit);
        const list = semua.filter(n => n >= a - 1e-6);
        return list.length ? list : [a];
    }
    m = t.match(/>\s*(\d+(?:\.\d+)?)\s*(kva|va)/i);
    if (m) {
        const a = parseToken(m[1], m[2]);
        const list = semua.filter(n => n > a + 1e-6);
        return list.length ? list : [def || a];
    }
    return def ? [def] : semua;
}

function makeDayaSelectOptions(selectedVal, includeZero = false, tarifStr = null){
    const target = Number(selectedVal);
    const baseList = tarifStr ? dayaSesuaiTarif(tarifStr) : KK_MASTERS.dayaOptions;
    let hasMatch = false;
    let html = includeZero ? `<option value="0" ${target === 0 ? 'selected' : ''}>0 kVA</option>` : '';
    if (includeZero && target === 0) hasMatch = true;

    baseList.forEach(d => {
        const isSel = Math.abs(Number(d) - target) < 0.001;
        if (isSel) hasMatch = true;
        const label = Number(d).toLocaleString('id-ID', { minimumFractionDigits: d < 10 ? 2 : 0, maximumFractionDigits: 2 }) + ' kVA';
        html += `<option value="${d}" ${isSel ? 'selected' : ''}>${label}</option>`;
    });

    if (!hasMatch && target > 0) {
        html = `<option value="${target}" selected>${target} kVA</option>` + html;
    }
    return html;
}

/* ==================== TAB 1: SIMULASI CEPAT ==================== */
function toggleSimPdFields(){
    const isPd = document.getElementById('simJenis').value === 'PD';
    document.getElementById('simDayaLamaWrap').style.display = isPd ? 'block' : 'none';
    runQuickSimulation();
}

function syncSimDefaultDaya(){
    const tarif = document.getElementById('simTarif').value;
    const defKva = getDefaultDayaFromTarif(tarif);
    const sel = document.getElementById('simDayaSesudah');
    if (!sel) return;
    sel.innerHTML = makeDayaSelectOptions(defKva, false);
    sel.value = String(defKva);
}

let simDebounceTimer = null;
function runQuickSimulation(immediate = false){
    clearTimeout(simDebounceTimer);
    const exec = async () => {
        const jenis = document.getElementById('simJenis').value;
        const jmlPlg = Math.max(1, parseInt(document.getElementById('simJmlPlg').value || '1', 10));
        const dayaSesudah = parseFloat(document.getElementById('simDayaSesudah').value || '0') || getDefaultDayaFromTarif(document.getElementById('simTarif').value);
        const dayaSebelum = jenis === 'PD' ? Math.max(0, parseFloat(document.getElementById('simDayaSebelum').value || '0')) : 0;
        const deltaKva = Math.max(0, dayaSesudah - dayaSebelum) * jmlPlg;

        const trafoQty = Math.max(0, parseInt(document.getElementById('simTrafoQty').value || '0', 10));
        const trafoCapUnit = Math.max(0, parseFloat(document.getElementById('simTrafoCap').value || '0'));

        const payload = {
            tahun: document.getElementById('simTahun').value || '{{ date("Y") }}',
            jarak_gudang_km: parseFloat(document.getElementById('simJarak').value || '0'),
            total_pelanggan: jmlPlg,
            total_daya_kva: deltaKva,
            jumlah_trafo: trafoQty,
            kapasitas_trafo_kva: trafoQty * trafoCapUnit,
            estimasi_sutm_km: parseFloat(document.getElementById('simSutm').value || '0'),
            estimasi_sutr_km: parseFloat(document.getElementById('simSutr').value || '0'),
            jumlah_tiang: parseInt(document.getElementById('simTiang').value || '0', 10),
        };

        try {
            const res = await fetch(KK_ROUTES.simulate, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': KK_CSRF },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (!data.feasibility) return;
            const f = data.feasibility;
            const box = document.getElementById('simResultBox');
            const title = document.getElementById('simVerdictTitle');
            const badge = document.getElementById('simVerdictBadge');
            box.classList.remove('is-layak', 'is-unlayak');
            if (f.layak_otomatis) {
                box.classList.add('is-layak');
                title.style.color = '#166534';
                badge.className = 'kk-badge kk-badge-green';
                badge.innerHTML = '<i class="fa fa-check-circle"></i> LAYAK / DITERIMA';
            } else {
                box.classList.add('is-unlayak');
                title.style.color = '#991b1b';
                badge.className = 'kk-badge kk-badge-red';
                badge.innerHTML = '<i class="fa fa-triangle-exclamation"></i> BELUM LAYAK';
            }
            title.textContent = f.hasil_ringkas;
            document.getElementById('simOutDaya').textContent = (data.total_daya_kva || 0).toLocaleString('id-ID') + ' kVA (' + jmlPlg + ' plg)';
            document.getElementById('simOutTrafo').textContent = trafoQty + ' Buah (' + (trafoQty * trafoCapUnit) + ' kVA)';
            document.getElementById('simOutRab').textContent = formatRp(f.estimasi_nilai_rab);
            document.getElementById('simOutRev').textContent = formatRp(f.estimasi_pendapatan_tahun);
            document.getElementById('simOutPayback').textContent = f.payback_tahun !== null ? f.payback_tahun + ' Tahun' : '-';
            document.getElementById('simOutKuota').textContent = data.kuota_tersedia + ' dari ' + data.kuota_total + ' Vendor Mencukupi';
        } catch (e) {}
    };
    if (immediate) exec();
    else simDebounceTimer = setTimeout(exec, 200);
}

async function saveQuickSimulation(){
    const nama = document.getElementById('simNama').value.trim();
    if (!nama) {
        alert('Isi Nama Proyek / Calon Pelanggan terlebih dahulu.');
        return;
    }
    const jenis = document.getElementById('simJenis').value;
    const tarif = document.getElementById('simTarif').value;
    const jmlPlg = Math.max(1, parseInt(document.getElementById('simJmlPlg').value || '1', 10));
    const dayaSesudah = parseFloat(document.getElementById('simDayaSesudah').value || '0') || getDefaultDayaFromTarif(tarif);
    const dayaSebelum = jenis === 'PD' ? Math.max(0, parseFloat(document.getElementById('simDayaSebelum').value || '0')) : 0;
    const trafoQty = Math.max(0, parseInt(document.getElementById('simTrafoQty').value || '0', 10));
    const trafoCapUnit = Math.max(0, parseFloat(document.getElementById('simTrafoCap').value || '0'));

    const payload = {
        mode: 'simulasi',
        status: 'draft',
        ulp: document.getElementById('simUlp').value,
        nama_pekerjaan: nama,
        tahun: document.getElementById('simTahun').value || '{{ date("Y") }}',
        jarak_gudang_km: parseFloat(document.getElementById('simJarak').value || '0'),
        jumlah_trafo: trafoQty,
        kapasitas_trafo_kva: trafoQty * trafoCapUnit,
        estimasi_sutm_km: parseFloat(document.getElementById('simSutm').value || '0'),
        estimasi_sutr_km: parseFloat(document.getElementById('simSutr').value || '0'),
        jumlah_tiang: parseInt(document.getElementById('simTiang').value || '0', 10),
        catatan_ulp: document.getElementById('simCatatan').value,
        pelanggans: [{
            jenis_permohonan: jenis,
            kelompok_tarif: tarif,
            jumlah_pelanggan: jmlPlg,
            daya_sebelum_kva: dayaSebelum,
            daya_sesudah_kva: dayaSesudah
        }]
    };

    const res = await fetch(KK_ROUTES.store, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': KK_CSRF },
        body: JSON.stringify(payload)
    });
    const json = await res.json();
    if (!res.ok) {
        alert(json.message || 'Gagal menyimpan simulasi.');
        return;
    }
    alert(json.message);
    window.location.reload();
}

function promoteSimulationToKkf(){
    resetKkfForm();
    document.getElementById('kkfUlp').value = document.getElementById('simUlp').value;
    document.getElementById('kkfNama').value = document.getElementById('simNama').value;
    document.getElementById('kkfJarak').value = document.getElementById('simJarak').value;
    document.getElementById('kkfTahun').value = document.getElementById('simTahun').value;
    document.getElementById('kkfCatatanUlp').value = document.getElementById('simCatatan').value;

    setCustomerRows([{
        jenis_permohonan: document.getElementById('simJenis').value,
        kelompok_tarif: document.getElementById('simTarif').value,
        jumlah_pelanggan: parseInt(document.getElementById('simJmlPlg').value || '1', 10),
        daya_sebelum_kva: parseFloat(document.getElementById('simDayaSebelum').value || '0'),
        daya_sesudah_kva: parseFloat(document.getElementById('simDayaSesudah').value || '0'),
    }]);

    switchKkTab('tabKkf', document.querySelector('[data-tab="tabKkf"]'));
}

/* ==================== TAB 2: PELANGGAN & INPUT URAI ==================== */
function inferKategoriFromTarif(tarif){
    const m = normTarifStr(tarif).toUpperCase().match(/^([RSBIP])\b|^([RSBIP])\./);
    return m ? (m[1] || m[2]) : 'R';
}

function makeTarifOptions(selected, kategoriFilter = ''){
    const raw = resolveCanonicalTarif(selected || '');
    const rawNorm = normTarifStr(raw).toLowerCase();
    const list = kategoriFilter
        ? KK_MASTERS.tarifDaya.filter(t => inferKategoriFromTarif(t) === kategoriFilter)
        : KK_MASTERS.tarifDaya;
    const items = list.length ? list : KK_MASTERS.tarifDaya;

    let hasSel = false;
    let html = items.map(t => {
        const isSel = normTarifStr(t).toLowerCase() === rawNorm;
        if (isSel) hasSel = true;
        return `<option value="${escapeHtml(t)}" ${isSel ? 'selected' : ''}>${escapeHtml(t)}</option>`;
    }).join('');
    if (!hasSel && raw) {
        html = `<option value="${escapeHtml(raw)}" selected>${escapeHtml(raw)}</option>` + html;
    }
    return html;
}

function onCustomerKategoriChange(katEl){
    const tr = katEl.closest('tr');
    const kat = katEl.value;
    const tarifEl = tr.querySelector('.cust-tarif');
    if (tarifEl) {
        const current = tarifEl.value;
        const nextList = KK_MASTERS.tarifDaya.filter(t => inferKategoriFromTarif(t) === kat);
        const keepCurrent = nextList.some(t => normTarifStr(t).toLowerCase() === normTarifStr(current).toLowerCase());
        const nextTarif = keepCurrent ? current : (nextList[0] || current);
        tarifEl.innerHTML = makeTarifOptions(nextTarif, kat);
        tarifEl.value = nextTarif;
        onCustomerTarifChange(tarifEl, false);
        return;
    }
    recalcCustomers();
}

function onCustomerTarifChange(selEl, syncKat = true){
    const tr = selEl.closest('tr');
    if (syncKat) {
        const katEl = tr.querySelector('.cust-kategori');
        if (katEl) katEl.value = inferKategoriFromTarif(selEl.value);
    }
    const defKva = getDefaultDayaFromTarif(selEl.value);
    const sesudahEl = tr.querySelector('.cust-sesudah');
    if (sesudahEl) {
        sesudahEl.innerHTML = makeDayaSelectOptions(defKva, false, selEl.value);
        sesudahEl.value = String(sesudahEl.value || defKva);
    }
    recalcCustomers();
}

function addCustomerRow(row = {}){
    const tbody = document.getElementById('customerTableBody');
    if (tbody.children.length >= 12) {
        alert('Maksimal 12 kelompok pelanggan per kajian.');
        return;
    }
    const idx = tbody.children.length + 1;
    const jenis = row.jenis_permohonan || 'PB';
    const tarif = resolveCanonicalTarif(row.kelompok_tarif || 'R.1 / 2.200 VA');
    const kat = row.kategori || inferKategoriFromTarif(tarif);
    const jml = row.jumlah_pelanggan ?? 1;
    const sebelum = row.daya_sebelum_kva ?? 0;
    const defKva = getDefaultDayaFromTarif(tarif);
    const sesudah = (row.daya_sesudah_kva !== undefined && row.daya_sesudah_kva !== null && row.daya_sesudah_kva !== '')
        ? Number(row.daya_sesudah_kva)
        : defKva;

    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td style="text-align:center;font-weight:700;">${idx}</td>
        <td>
            <select class="kk-select cust-jenis" style="padding:6px 8px;" onchange="recalcCustomers()">
                <option value="PB" ${jenis === 'PB' ? 'selected' : ''}>PB</option>
                <option value="PD" ${jenis === 'PD' ? 'selected' : ''}>PD</option>
            </select>
        </td>
        <td>
            <select class="kk-select cust-kategori" style="padding:6px 8px;" onchange="onCustomerKategoriChange(this)">
                <option value="R" ${kat === 'R' ? 'selected' : ''}>R (Rumah Tangga)</option>
                <option value="S" ${kat === 'S' ? 'selected' : ''}>S (Sosial)</option>
                <option value="B" ${kat === 'B' ? 'selected' : ''}>B (Bisnis)</option>
                <option value="I" ${kat === 'I' ? 'selected' : ''}>I (Industri)</option>
                <option value="P" ${kat === 'P' ? 'selected' : ''}>P (Pemerintah)</option>
            </select>
        </td>
        <td>
            <select class="kk-select cust-tarif" style="padding:6px 8px;" onchange="onCustomerTarifChange(this)">
                ${makeTarifOptions(tarif, kat)}
            </select>
        </td>
        <td><input type="number" class="kk-input cust-jml" min="1" value="${jml}" style="padding:6px 8px;" oninput="recalcCustomers()"></td>
        <td>
            <select class="kk-select cust-sebelum" style="padding:6px 8px;" onchange="recalcCustomers()">
                ${makeDayaSelectOptions(sebelum, true)}
            </select>
        </td>
        <td>
            <select class="kk-select cust-sesudah" style="padding:6px 8px;" onchange="recalcCustomers()">
                ${makeDayaSelectOptions(sesudah, false, tarif)}
            </select>
        </td>
        <td class="cust-delta" style="font-weight:700;color:#0369a1;">0 kVA</td>
        <td><button type="button" class="kk-btn kk-btn-danger kk-btn-sm" onclick="this.closest('tr').remove();recalcCustomers();"><i class="fa fa-xmark"></i></button></td>
    `;
    tbody.appendChild(tr);
    recalcCustomers();
}

function setCustomerRows(rows){
    document.getElementById('customerTableBody').innerHTML = '';
    if (!rows || !rows.length) addCustomerRow();
    else rows.forEach(r => addCustomerRow(r));
}

function getCustomerRowsPayload(){
    const list = [];
    document.querySelectorAll('#customerTableBody tr').forEach(tr => {
        const jenis = tr.querySelector('.cust-jenis').value;
        const tarif = tr.querySelector('.cust-tarif').value;
        const jml = Math.max(0, parseInt(tr.querySelector('.cust-jml').value || '0', 10));
        const sebelum = jenis === 'PD' ? Math.max(0, parseFloat(tr.querySelector('.cust-sebelum').value || '0')) : 0;
        const sesudah = parseFloat(tr.querySelector('.cust-sesudah').value || '0') || getDefaultDayaFromTarif(tarif);
        if (jml > 0 && tarif) {
            list.push({
                jenis_permohonan: jenis,
                kelompok_tarif: tarif,
                jumlah_pelanggan: jml,
                daya_sebelum_kva: sebelum,
                daya_sesudah_kva: sesudah
            });
        }
    });
    return list;
}

function recalcCustomers(){
    let totalPlg = 0;
    let totalKva = 0;
    const rows = document.querySelectorAll('#customerTableBody tr');
    rows.forEach((tr, i) => {
        tr.firstElementChild.textContent = i + 1;
        const jenis = tr.querySelector('.cust-jenis').value;
        const jml = Math.max(0, parseInt(tr.querySelector('.cust-jml').value || '0', 10));
        const sebEl = tr.querySelector('.cust-sebelum');
        if (jenis === 'PB') {
            sebEl.value = '0';
            sebEl.disabled = true;
        } else {
            sebEl.disabled = false;
        }
        const sebelum = parseFloat(sebEl.value || '0') || 0;
        const sesudah = parseFloat(tr.querySelector('.cust-sesudah').value || '0') || 0;
        const delta = Math.max(0, sesudah - sebelum) * jml;
        tr.querySelector('.cust-delta').textContent = '+' + delta.toFixed(2) + ' kVA';
        totalPlg += jml;
        totalKva += delta;
    });
    document.getElementById('kkfTotalPlgBadge').textContent = totalPlg + ' Pelanggan • +' + totalKva.toFixed(2) + ' kVA';
    recalcUraiTotals();
}

function buildSelectCell(col, optionsList, currentVal, triggerAuto = false){
    const val = String(currentVal ?? '').trim();
    let matched = (val === '');
    let opts = `<option value="">--</option>`;
    (optionsList || []).forEach(opt => {
        const isSel = String(opt) === val;
        if (isSel) matched = true;
        opts += `<option value="${escapeHtml(opt)}" ${isSel ? 'selected' : ''}>${escapeHtml(opt)}</option>`;
    });
    if (!matched && val !== '') {
        opts += `<option value="${escapeHtml(val)}" selected>${escapeHtml(val)}</option>`;
    }
    return `<select data-col="${col}" onchange="onUraiInput(this, ${triggerAuto ? 'true' : 'false'})">${opts}</select>`;
}

function enableUraiOverrideInput(inp){
    const td = inp.closest('.urai-override-cell');
    const cb = td ? td.querySelector('.urai-ov-cb') : null;
    if (cb && !cb.checked) {
        cb.checked = true;
        toggleUraiOverride(cb);
    } else if (inp) {
        inp.removeAttribute('readonly');
        inp.dataset.override = '1';
        inp.classList.remove('is-auto');
        inp.classList.add('is-override');
        inp.focus();
    }
}

function resetUraiOverrideInput(e, inp){
    if (e) e.preventDefault();
    const td = inp.closest('.urai-override-cell');
    const cb = td ? td.querySelector('.urai-ov-cb') : null;
    if (cb) {
        cb.checked = false;
        toggleUraiOverride(cb);
    } else if (inp) {
        inp.setAttribute('readonly', 'readonly');
        inp.dataset.override = '0';
        inp.classList.remove('is-override');
        inp.classList.add('is-auto');
        recalcUraiRowAuto(inp.closest('tr'));
        recalcUraiTotals();
    }
}

function buildOverrideCell(col, currentVal, ov){
    const isOv = Boolean(ov && ov[col]);
    const v = escapeHtml(currentVal ?? '');
    return `
        <div class="urai-override-cell">
            <input type="text" data-col="${col}" value="${v}" class="${isOv ? 'is-override' : 'is-auto'}" data-override="${isOv ? '1' : '0'}" ${isOv ? '' : 'readonly'} oninput="onUraiInput(this)" ondblclick="enableUraiOverrideInput(this)" oncontextmenu="resetUraiOverrideInput(event, this)" title="Auto-hitung (abu-abu). Double-click / centang untuk override manual (oranye); klik kanan untuk reset otomatis.">
            <input type="checkbox" class="urai-ov-cb" data-ov-for="${col}" ${isOv ? 'checked' : ''} onchange="toggleUraiOverride(this)" title="Override manual ${col}">
        </div>
    `;
}

function createUraiRowHtml(rowIdx, data = {}){
    const val = (k) => escapeHtml(data[k] ?? '');
    const ov = data.overrides || {};
    const m = KK_MASTERS.uraiMasters;
    const t = KK_MASTERS.trafoOptions;

    return `
        <td class="urai-row-no">${rowIdx}</td>
        <td data-ucol="AJ">${buildSelectCell('AJ', m.AJ, data.AJ)}</td>
        <td data-ucol="AK">${buildSelectCell('AK', m.AK, data.AK)}</td>
        <td data-ucol="AL">${buildSelectCell('AL', m.AL, data.AL, true)}</td>
        <td data-ucol="AM">${buildSelectCell('AM', m.AM, data.AM)}</td>
        <td data-ucol="VOL150"><input type="number" step="0.01" data-col="VOL150" value="${val('VOL150')}" placeholder="0" oninput="onUraiInput(this)"></td>
        <td data-ucol="BA">${buildOverrideCell('BA', data.BA, ov)}</td>
        <td data-ucol="BD">${buildOverrideCell('BD', data.BD, ov)}</td>
        <td data-ucol="BG">${buildSelectCell('BG', m.BG, data.BG)}</td>
        <td data-ucol="CANTOL">${buildSelectCell('CANTOL', t.cantol, data.CANTOL)}</td>
        <td data-ucol="PORTAL">${buildSelectCell('PORTAL', t.portal, data.PORTAL)}</td>
        <td data-ucol="DN">${buildSelectCell('DN', m.DN, data.DN)}</td>
        <td data-ucol="DO">${buildSelectCell('DO', m.DO, data.DO)}</td>
        <td data-ucol="DP">${buildSelectCell('DP', m.DP, data.DP, true)}</td>
        <td data-ucol="DQ">${buildSelectCell('DQ', m.DQ, data.DQ)}</td>
        <td data-ucol="VOLJTR"><input type="number" step="0.01" data-col="VOLJTR" value="${val('VOLJTR')}" placeholder="0" oninput="onUraiInput(this)"></td>
        <td data-ucol="DR"><input type="text" data-col="DR" value="${val('DR')}" oninput="onUraiInput(this)"></td>
        <td data-ucol="DS">${buildOverrideCell('DS', data.DS, ov)}</td>
        <td data-ucol="DT"><input type="text" data-col="DT" value="${val('DT')}" oninput="onUraiInput(this)"></td>
        <td data-ucol="DU">${buildOverrideCell('DU', data.DU, ov)}</td>
        <td data-ucol="DV"><input type="text" data-col="DV" value="${val('DV')}" oninput="onUraiInput(this)"></td>
        <td data-ucol="DW"><input type="text" data-col="DW" value="${val('DW')}" oninput="onUraiInput(this)"></td>
        <td data-ucol="PTM"><input type="text" data-col="PTM" value="${val('PTM')}" oninput="onUraiInput(this)"></td>
        <td data-ucol="PTR"><input type="text" data-col="PTR" value="${val('PTR')}" oninput="onUraiInput(this)"></td>
    `;
}

function addUraiRows(count = 10){
    const tbody = document.getElementById('uraiTableBody');
    const current = tbody.children.length;
    const allowed = Math.min(count, 2000 - current);
    for (let i = 1; i <= allowed; i++) {
        const tr = document.createElement('tr');
        tr.innerHTML = createUraiRowHtml(current + i);
        tbody.appendChild(tr);
    }
    applyUraiColumnGroup();
    renderUraiPagination();
}

function openRowCountModal(){
    const current = document.getElementById('uraiTableBody').children.length || 25;
    document.getElementById('uraiTargetRowCount').value = current;
    document.getElementById('rowCountModal').classList.add('open');
}

function applyUraiRowCount(){
    const target = Math.max(1, Math.min(2000, parseInt(document.getElementById('uraiTargetRowCount').value || '25', 10)));
    const tbody = document.getElementById('uraiTableBody');
    const current = tbody.children.length;
    if (target > current) {
        addUraiRows(target - current);
    } else if (target < current) {
        while (tbody.children.length > target) {
            tbody.removeChild(tbody.lastElementChild);
        }
        renderUraiPagination();
        recalcUraiTotals();
    }
    closeModal('rowCountModal');
}

function clearUraiRows(){
    if (!confirm('Kosongkan seluruh isi tabel Input URAI?')) return;
    document.getElementById('uraiTableBody').innerHTML = '';
    uraiState.page = 1;
    addUraiRows(uraiState.perPage);
    recalcUraiTotals();
}

function toggleUraiOverride(cb){
    const col = cb.dataset.ovFor;
    const tr = cb.closest('tr');
    const inp = tr.querySelector(`[data-col="${col}"]`);
    if (!inp) return;
    if (cb.checked) {
        inp.removeAttribute('readonly');
        inp.dataset.override = '1';
        inp.classList.remove('is-auto');
        inp.classList.add('is-override');
        inp.focus();
    } else {
        inp.setAttribute('readonly', 'readonly');
        inp.dataset.override = '0';
        inp.classList.remove('is-override');
        inp.classList.add('is-auto');
        recalcUraiRowAuto(tr);
    }
    recalcUraiTotals();
}

function recalcUraiRowAuto(tr){
    const al = (tr.querySelector('[data-col="AL"]').value || '').trim().toUpperCase();
    const dp = (tr.querySelector('[data-col="DP"]').value || '').trim();
    const baEl = tr.querySelector('[data-col="BA"]');
    const bdEl = tr.querySelector('[data-col="BD"]');
    const dsEl = tr.querySelector('[data-col="DS"]');
    const duEl = tr.querySelector('[data-col="DU"]');

    if (baEl.dataset.override !== '1') {
        baEl.value = (al === 'TM-3' || al === 'TM-4') ? 1 : (al === 'TM-10' ? 2 : (al ? 0 : ''));
    }
    const baInt = parseInt(baEl.value || '0', 10);
    if (bdEl.dataset.override !== '1') {
        bdEl.value = !al ? '' : (((al === 'TM-3' && baInt === 1) || (al === 'TM-10' && baInt === 2)) ? 0 : (al === 'TM-10' ? 2 : (al === 'TM-3' ? 1 : 0)));
    }
    if (dsEl.dataset.override !== '1') {
        dsEl.value = !dp ? '' : ((dp === 'Tr.3' || dp === 'Tr.2') ? 1 : 0);
    }
    if (duEl && duEl.dataset.override !== '1') {
        duEl.value = '';
    }
}

function onUraiInput(el, triggerAuto = false){
    if (triggerAuto) recalcUraiRowAuto(el.closest('tr'));
    recalcUraiTotals();
}

function changeUraiPerPage(){
    uraiState.perPage = parseInt(document.getElementById('uraiPerPage').value || '25', 10);
    uraiState.page = 1;
    const tbody = document.getElementById('uraiTableBody');
    if (tbody.children.length < uraiState.perPage) {
        addUraiRows(uraiState.perPage - tbody.children.length);
    } else {
        renderUraiPagination();
    }
}

function goUraiPage(pageNum){
    const tbody = document.getElementById('uraiTableBody');
    const total = tbody.children.length;
    const totalPages = Math.max(1, Math.ceil(total / uraiState.perPage));
    uraiState.page = Math.max(1, Math.min(totalPages, pageNum));
    renderUraiPagination();
}

function renderUraiPagination(){
    const tbody = document.getElementById('uraiTableBody');
    const rows = Array.from(tbody.children);
    const total = rows.length;
    const totalPages = Math.max(1, Math.ceil(total / uraiState.perPage));
    if (uraiState.page > totalPages) uraiState.page = totalPages;

    const start = (uraiState.page - 1) * uraiState.perPage;
    const end = Math.min(total, start + uraiState.perPage);

    rows.forEach((tr, idx) => {
        tr.style.display = (idx >= start && idx < end) ? '' : 'none';
    });

    document.getElementById('uraiTotalRowsInfo').textContent = total.toLocaleString('id-ID');
    document.getElementById('uraiPagerInfo').textContent = total === 0
        ? 'Tidak ada baris'
        : `Menampilkan baris ${start + 1}–${end} dari ${total.toLocaleString('id-ID')} baris`;

    const jumpSel = document.getElementById('uraiPageJump');
    let jumpHtml = '';
    for (let p = 1; p <= totalPages; p++) {
        const s = (p - 1) * uraiState.perPage + 1;
        const e = Math.min(total, p * uraiState.perPage);
        jumpHtml += `<option value="${p}" ${p === uraiState.page ? 'selected' : ''}>Hal ${p} (Baris ${s}–${e})</option>`;
    }
    jumpSel.innerHTML = jumpHtml;
    document.getElementById('uraiPrevBtn').disabled = uraiState.page <= 1;
    document.getElementById('uraiNextBtn').disabled = uraiState.page >= totalPages;
}

function applyUraiColumnGroup(){
    const group = document.getElementById('uraiColGroup').value;
    const allowed = group === 'all' ? null : (URAI_COL_GROUPS[group] || []);
    document.querySelectorAll('#uraiTable [data-ucol]').forEach(el => {
        const col = el.getAttribute('data-ucol');
        if (!allowed || allowed.includes(col)) el.classList.remove('urai-col-hidden');
        else el.classList.add('urai-col-hidden');
    });
    const topHead = document.querySelector('#uraiTable tr.urai-tophead');
    if (topHead) {
        topHead.style.display = group === 'all' ? '' : 'none';
    }
}

function jumpToUraiColumn(col){
    if (!col) return;
    if (document.getElementById('uraiColGroup').value !== 'all') {
        document.getElementById('uraiColGroup').value = 'all';
        applyUraiColumnGroup();
    }
    const th = document.querySelector(`#uraiTable th[data-ucol="${col}"]`);
    const wrap = document.getElementById('uraiSheetWrap');
    if (th && wrap) {
        wrap.scrollTo({ left: Math.max(0, th.offsetLeft - 60), behavior: 'smooth' });
    }
}

function getUraiRowsPayload(){
    const list = [];
    document.querySelectorAll('#uraiTableBody tr').forEach((tr, idx) => {
        const obj = { row_number: idx + 1, overrides: {} };
        let hasData = false;
        KK_MASTERS.uraiCols.forEach(c => {
            const inp = tr.querySelector(`[data-col="${c}"]`);
            const v = inp ? String(inp.value ?? '').trim() : '';
            obj[c] = v;
            if (v !== '') hasData = true;
            if (inp && inp.dataset.override === '1') obj.overrides[c] = true;
        });
        if (hasData) list.push(obj);
    });
    return list;
}

function parseKvaFromBg(bgStr){
    const s = String(bgStr || '').replace(',', '.');
    const m = s.match(/(\d+(?:\.\d+)?)/);
    return m ? (parseFloat(m[1]) || 0) : 0;
}

function recalcUraiTotals(){
    let trafoCount = 0, trafoKva = 0, vol150 = 0, volJtr = 0, tiangTm = 0, tiangTr = 0, ptm = 0, ptr = 0;
    document.querySelectorAll('#uraiTableBody tr').forEach(tr => {
        const aj = (tr.querySelector('[data-col="AJ"]').value || '').trim();
        const al = (tr.querySelector('[data-col="AL"]').value || '').trim();
        const dn = (tr.querySelector('[data-col="DN"]').value || '').trim();
        const dp = (tr.querySelector('[data-col="DP"]').value || '').trim();
        const bg = (tr.querySelector('[data-col="BG"]').value || '').trim();
        const v150 = parseFloat(tr.querySelector('[data-col="VOL150"]').value || '0') || 0;
        const vjtr = parseFloat(tr.querySelector('[data-col="VOLJTR"]').value || '0') || 0;
        const vptm = (tr.querySelector('[data-col="PTM"]').value || '').trim();
        const vptr = (tr.querySelector('[data-col="PTR"]').value || '').trim();

        if (aj || al) tiangTm++;
        if (dn || dp) tiangTr++;
        if (vptm) ptm += parseFloat(vptm.replace(',', '.')) || 1;
        if (vptr) ptr += parseFloat(vptr.replace(',', '.')) || 1;
        if (bg) {
            trafoCount++;
            trafoKva += parseKvaFromBg(bg);
        }
        vol150 += v150;
        volJtr += vjtr;
    });

    const sutmKm = vol150 >= 20 ? +(vol150 / 1000).toFixed(3) : +vol150.toFixed(3);
    const sutrKm = volJtr >= 20 ? +(volJtr / 1000).toFixed(3) : +volJtr.toFixed(3);
    const tiangTot = Math.round(tiangTm + tiangTr + ptm + ptr);

    const cust = getCustomerRowsPayload();
    const totalPlg = cust.reduce((s, r) => s + r.jumlah_pelanggan, 0);
    const totalKva = cust.reduce((s, r) => s + Math.max(0, r.daya_sesudah_kva - r.daya_sebelum_kva) * r.jumlah_pelanggan, 0);
    const jarak = parseFloat(document.getElementById('kkfJarak').value || '0') || 0;
    const uc = KK_MASTERS.unitCosts;
    const rab = (trafoCount * uc.trafo_per_unit) + (trafoKva * uc.trafo_per_kva) +
                (sutmKm * uc.sutm_per_km) + (sutrKm * uc.sutr_per_km) +
                (tiangTot * uc.tiang_tr_unit) + (jarak * uc.mobilisasi_per_km) +
                (totalPlg * uc.sr_per_pelanggan);

    const revYear = totalKva * 0.85 * 110 * 12 * 1444.7;
    const payback = revYear > 0 ? (rab / revYear) : null;
    const trafoOk = trafoKva <= 0 || (trafoKva * 0.8) >= (totalKva * 0.65);
    const layak = totalKva > 0 && (rab <= 0 || (payback !== null && payback <= 6.5)) && trafoOk;

    document.getElementById('kkfSumTrafo').textContent = `${trafoCount} bh (${trafoKva} kVA)`;
    document.getElementById('kkfSumKm').textContent = `${sutmKm} / ${sutrKm} km`;
    document.getElementById('kkfSumTiang').textContent = `${tiangTot} btg`;
    document.getElementById('kkfSumRab').textContent = formatRp(rab);

    const badge = document.getElementById('kkfSumVerdict');
    if (totalKva <= 0) {
        badge.className = 'kk-badge kk-badge-slate';
        badge.textContent = 'Isi Daya Pelanggan';
    } else if (layak) {
        badge.className = 'kk-badge kk-badge-green';
        badge.textContent = `LAYAK (${payback !== null ? payback.toFixed(1) + ' th' : 'OK'})`;
    } else {
        badge.className = 'kk-badge kk-badge-red';
        badge.textContent = `BELUM LAYAK (${payback !== null ? payback.toFixed(1) + ' th' : 'Cek Trafo'})`;
    }
}

function openPasteModal(){
    document.getElementById('pasteUraiText').value = '';
    document.getElementById('pasteUraiModal').classList.add('open');
}

function applyExcelPasteRaw(raw, startRowIdx = 0, startColIdx = 0){
    if (!raw || !raw.trim()) return 0;
    const lines = raw.replace(/\r\n/g, '\n').replace(/\r/g, '\n').split('\n');
    while (lines.length && lines[lines.length - 1] === '') lines.pop();
    if (!lines.length) return 0;

    const tbody = document.getElementById('uraiTableBody');
    const needed = Math.min(2000, startRowIdx + lines.length);
    if (tbody.children.length < needed) {
        addUraiRows(needed - tbody.children.length);
    }
    const rows = tbody.querySelectorAll('tr');
    const overrideCols = ['BA', 'BD', 'DS', 'DU'];
    let pastedCount = 0;

    lines.forEach((line, rIdx) => {
        const tr = rows[startRowIdx + rIdx];
        if (!tr) return;
        const cells = line.split('\t');
        cells.forEach((rawCell, offset) => {
            const cIdx = startColIdx + offset;
            if (cIdx >= KK_MASTERS.uraiCols.length) return;
            const col = KK_MASTERS.uraiCols[cIdx];
            const val = String(rawCell ?? '').trim();
            const inp = tr.querySelector(`[data-col="${col}"]`);
            if (!inp) return;
            if (inp.tagName === 'SELECT' && val && !Array.from(inp.options).some(o => o.value === val)) {
                const opt = document.createElement('option');
                opt.value = val;
                opt.textContent = val;
                inp.appendChild(opt);
            }
            if (overrideCols.includes(col) && val !== '') {
                const cb = tr.querySelector(`.urai-ov-cb[data-ov-for="${col}"]`);
                if (cb && !cb.checked) {
                    cb.checked = true;
                    inp.removeAttribute('readonly');
                    inp.dataset.override = '1';
                    inp.classList.remove('is-auto');
                    inp.classList.add('is-override');
                }
            }
            inp.value = val;
        });
        recalcUraiRowAuto(tr);
        pastedCount++;
    });

    renderUraiPagination();
    recalcUraiTotals();
    return pastedCount;
}

function applyExcelPaste(){
    const raw = document.getElementById('pasteUraiText').value;
    const pasted = applyExcelPasteRaw(raw, 0, 0);
    if (pasted > 0) {
        setKkfStatus(`Berhasil menempel ${pasted} baris data Excel ke tabel URAI.`, 'ok');
    }
    closeModal('pasteUraiModal');
}

function bindUraiDirectPaste(){
    const tbody = document.getElementById('uraiTableBody');
    if (!tbody) return;
    tbody.addEventListener('paste', function(e){
        const text = (e.clipboardData || window.clipboardData)?.getData('text/plain') || '';
        if (!text.includes('\t') && !text.includes('\n')) return;
        const target = e.target;
        const tr = target ? target.closest('tr') : null;
        const col = target ? target.getAttribute('data-col') : null;
        const rowIdx = tr ? Array.from(tbody.children).indexOf(tr) : 0;
        const colIdx = col ? Math.max(0, KK_MASTERS.uraiCols.indexOf(col)) : 0;
        e.preventDefault();
        const pasted = applyExcelPasteRaw(text, Math.max(0, rowIdx), colIdx);
        if (pasted > 0) {
            setKkfStatus(`Berhasil paste langsung ${pasted} baris dari Excel mulai baris ${Math.max(1, rowIdx + 1)}.`, 'ok');
        }
    });
}

function syncKkfExportButtons(id){
    const pdfBtn = document.getElementById('kkfBtnExportPdf');
    const xlsBtn = document.getElementById('kkfBtnExportExcel');
    if (id) {
        pdfBtn.href = `${KK_ROUTES.showBase}/${id}/export-pdf`;
        xlsBtn.href = `${KK_ROUTES.showBase}/${id}/export-excel`;
        pdfBtn.style.display = 'inline-flex';
        xlsBtn.style.display = 'inline-flex';
    } else {
        pdfBtn.style.display = 'none';
        xlsBtn.style.display = 'none';
    }
}

function resetKkfForm(){
    document.getElementById('kkfId').value = '';
    const selSaved = document.getElementById('kkfSavedProjectSelect');
    if (selSaved) selSaved.value = '';
    const kodeInp = document.getElementById('kkfKodeLookup');
    if (kodeInp) kodeInp.value = '';
    document.getElementById('kkfNama').value = '';
    document.getElementById('kkfJarak').value = '0';
    document.getElementById('kkfTahun').value = '{{ date("Y") }}';
    document.getElementById('kkfCatatanUlp').value = '';
    document.getElementById('kkfCatatanPerencanaan').value = '';
    document.getElementById('kkfBadgeKode').textContent = 'Kajian Baru';
    const calcCard = document.getElementById('kkfCalcResultCard');
    if (calcCard) calcCard.style.display = 'none';
    setKkfStatus('');
    syncKkfExportButtons(null);
    setCustomerRows([]);
    document.getElementById('uraiTableBody').innerHTML = '';
    uraiState.page = 1;
    addUraiRows(uraiState.perPage);
    recalcUraiTotals();
}

async function saveKkfForm(targetStatus){
    const nama = document.getElementById('kkfNama').value.trim();
    if (!nama) {
        alert('Nama Pekerjaan wajib diisi.');
        return;
    }
    const pelanggans = getCustomerRowsPayload();
    const uraiRows = getUraiRowsPayload();
    if (!pelanggans.length && !uraiRows.length) {
        alert('Minimal isi 1 kelompok pelanggan atau 1 baris URAI sebelum menyimpan.');
        return;
    }

    const totalGridRows = document.querySelectorAll('#uraiTableBody tr').length;
    const payload = {
        id: document.getElementById('kkfId').value || null,
        mode: 'kajian',
        status: targetStatus,
        ulp: document.getElementById('kkfUlp').value,
        nama_pekerjaan: nama,
        jarak_gudang_km: parseFloat(document.getElementById('kkfJarak').value || '0'),
        row_count: Math.max(25, totalGridRows),
        tahun: document.getElementById('kkfTahun').value || '{{ date("Y") }}',
        catatan_ulp: document.getElementById('kkfCatatanUlp').value,
        catatan_perencanaan: document.getElementById('kkfCatatanPerencanaan').value,
        pelanggans: pelanggans,
        urai_rows: uraiRows
    };

    setKkfStatus('Menyimpan data kajian & URAI ke database...', 'info');
    const res = await fetch(KK_ROUTES.store, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': KK_CSRF },
        body: JSON.stringify(payload)
    });
    const json = await res.json();
    if (!res.ok) {
        setKkfStatus(json.message || 'Gagal menyimpan kajian.', 'err');
        alert(json.message || 'Gagal menyimpan kajian.');
        return;
    }
    setKkfStatus(json.message || 'Data kajian berhasil disimpan.', 'ok');
    alert(json.message);
    window.location.reload();
}

function renderKkfCalcResult(simJson){
    const card = document.getElementById('kkfCalcResultCard');
    if (!card || !simJson || !simJson.feasibility) return;

    const f = simJson.feasibility;
    const u = simJson.urai_summary || {};
    card.style.display = 'block';

    document.getElementById('calcRabRp').textContent = formatRp(f.estimasi_nilai_rab || 0);
    document.getElementById('calcBpRp').textContent = formatRp(f.total_bp_rp || 0);
    document.getElementById('calcUjlRp').textContent = formatRp(f.total_ujl_rp || 0);
    document.getElementById('calcRevRp').textContent = formatRp(f.estimasi_pendapatan_tahun || 0);
    document.getElementById('calcPayback').textContent = f.payback_tahun !== null && f.payback_tahun !== undefined
        ? `${Number(f.payback_tahun).toFixed(2)} Tahun`
        : '-';
    document.getElementById('calcKuota').textContent = `${simJson.kuota_tersedia ?? 0} / ${simJson.kuota_total ?? 0}`;

    const badge = document.getElementById('calcVerdictBadge');
    badge.className = 'kk-badge ' + (f.layak_otomatis ? 'kk-badge-green' : 'kk-badge-red');
    badge.textContent = f.hasil_ringkas || '-';

    document.getElementById('calcMduSummary').innerHTML = `
        <div>Tiang TM / PTM: <strong>${u.jumlah_tiang_tm ?? 0} btg</strong> (PTM: ${u.total_ptm ?? 0})</div>
        <div>Tiang TR / PTR: <strong>${u.jumlah_tiang_tr ?? 0} btg</strong> (PTR: ${u.total_ptr ?? 0})</div>
        <div>SUTM 150mm: <strong>${u.total_vol150_m ?? 0} m</strong> (${u.estimasi_sutm_km ?? 0} km)</div>
        <div>SUTR / JTR: <strong>${u.total_voljtr_m ?? 0} m</strong> (${u.estimasi_sutr_km ?? 0} km)</div>
        <div>Guy Wire (BA) / HGW (BD): <strong>${u.total_gw ?? 0} / ${u.total_hgw ?? 0}</strong></div>
        <div>Row URAI Terisi: <strong>${u.jumlah_baris_urai ?? 0} baris</strong></div>
    `;

    const cantolEntries = Object.entries(u.trafo_cantol || {});
    const portalEntries = Object.entries(u.trafo_portal || {});
    let trafoHtml = `<div>Total Trafo (BG): <strong>${u.jumlah_trafo ?? 0} bh (${u.kapasitas_trafo_kva ?? 0} kVA)</strong></div>`;
    if (cantolEntries.length) {
        trafoHtml += `<div style="margin-top:4px;"><strong>Cantol:</strong> ` + cantolEntries.map(([k,v]) => `${escapeHtml(k)} (${v})`).join(', ') + `</div>`;
    }
    if (portalEntries.length) {
        trafoHtml += `<div style="margin-top:2px;"><strong>Portal:</strong> ` + portalEntries.map(([k,v]) => `${escapeHtml(k)} (${v})`).join(', ') + `</div>`;
    }
    if (!cantolEntries.length && !portalEntries.length) {
        trafoHtml += `<div style="color:#64748b;">Belum ada kolom Cantol/Portal dipilih.</div>`;
    }
    document.getElementById('calcTrafoSummary').innerHTML = trafoHtml;

    const boxAppEntries = Object.entries(f.box_app || {}).filter(([,cnt]) => Number(cnt) > 0);
    document.getElementById('calcBoxAppSummary').innerHTML = boxAppEntries.length
        ? boxAppEntries.map(([cap, cnt]) => `<div>Box APP ${escapeHtml(cap)} kVA: <strong>${cnt} bh</strong></div>`).join('')
        : '<div style="color:#64748b;">Tidak ada daya pelanggan kategori Box APP (53–197 kVA).</div>';
}

async function testCalculateKajian(){
    const pelanggans = getCustomerRowsPayload();
    const uraiRows = getUraiRowsPayload();
    if (!pelanggans.length && !uraiRows.length) {
        setKkfStatus('Isi minimal data pelanggan atau URAI untuk menjalankan Uji Hitung.', 'err');
        return;
    }

    setKkfStatus('Menghitung EDIR 0014 (BP/UJL), Rekap MDU, Trafo, & Box APP...', 'info');
    try {
        const res = await fetch(KK_ROUTES.simulate, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': KK_CSRF },
            body: JSON.stringify({
                ulp: document.getElementById('kkfUlp').value,
                tahun: document.getElementById('kkfTahun').value || '{{ date("Y") }}',
                jarak_gudang_km: parseFloat(document.getElementById('kkfJarak').value || '0') || 0,
                pelanggans: pelanggans,
                urai_rows: uraiRows
            })
        });
        const json = await res.json();
        if (!res.ok) {
            setKkfStatus(json.message || 'Gagal melakukan uji perhitungan.', 'err');
            return;
        }
        renderKkfCalcResult(json);
        setKkfStatus(`Uji perhitungan selesai: ${json.feasibility?.hasil_ringkas || 'OK'}`, 'ok');
    } catch (e) {
        console.error(e);
        setKkfStatus('Terjadi kesalahan jaringan saat menghitung kajian.', 'err');
    }
}

function onSelectSavedProject(id){
    if (!id) return;
    const sel = document.getElementById('kkfSavedProjectSelect');
    const opt = sel ? sel.options[sel.selectedIndex] : null;
    if (opt && opt.dataset.kode) {
        const inp = document.getElementById('kkfKodeLookup');
        if (inp) inp.value = opt.dataset.kode;
    }
    loadProjectIntoKkf(id);
}

function openProjectByInput(){
    const raw = (document.getElementById('kkfKodeLookup')?.value || '').trim().toLowerCase();
    if (!raw) {
        setKkfStatus('Masukkan ID / Kode Kajian atau pilih proyek dari dropdown.', 'err');
        return;
    }
    const sel = document.getElementById('kkfSavedProjectSelect');
    if (!sel) return;
    let matchedId = '';
    for (const opt of sel.options) {
        if (!opt.value) continue;
        const kode = (opt.dataset.kode || '').trim().toLowerCase();
        if (kode === raw || opt.value === raw || kode.includes(raw)) {
            matchedId = opt.value;
            break;
        }
    }
    if (!matchedId) {
        setKkfStatus(`Kode kajian "${raw}" tidak ditemukan di database.`, 'err');
        return;
    }
    sel.value = matchedId;
    loadProjectIntoKkf(matchedId);
}

async function loadProjectIntoKkf(id){
    setKkfStatus('Memuat data kajian dari database...', 'info');
    const res = await fetch(`${KK_ROUTES.showBase}/${id}`, { headers: { 'Accept': 'application/json' } });
    const json = await res.json();
    if (!res.ok || !json.data) {
        setKkfStatus('Gagal memuat detail kajian dari database.', 'err');
        alert('Gagal memuat detail kajian.');
        return;
    }
    const p = json.data;
    document.getElementById('kkfId').value = p.id;
    const selSaved = document.getElementById('kkfSavedProjectSelect');
    if (selSaved) selSaved.value = String(p.id);
    const kodeInp = document.getElementById('kkfKodeLookup');
    if (kodeInp) kodeInp.value = p.kode_kajian || '';

    document.getElementById('kkfUlp').value = p.ulp;
    document.getElementById('kkfNama').value = p.nama_pekerjaan;
    document.getElementById('kkfJarak').value = p.jarak_gudang_km ?? 0;
    document.getElementById('kkfTahun').value = p.tahun;
    document.getElementById('kkfCatatanUlp').value = p.catatan_ulp || '';
    document.getElementById('kkfCatatanPerencanaan').value = p.catatan_perencanaan || '';
    document.getElementById('kkfBadgeKode').textContent = p.kode_kajian;
    syncKkfExportButtons(p.id);

    setCustomerRows(p.pelanggans || []);

    const tbody = document.getElementById('uraiTableBody');
    tbody.innerHTML = '';
    const urais = p.urai_rows || [];
    const byRow = {};
    let maxRowNum = 0;
    urais.forEach((u, i) => {
        const rn = parseInt(u.row_number || (i + 1), 10) || (i + 1);
        byRow[rn] = u.row_data || {};
        if (rn > maxRowNum) maxRowNum = rn;
    });
    const totalToRender = Math.max(uraiState.perPage, parseInt(p.row_count || '0', 10) || 0, maxRowNum, urais.length);
    for (let i = 1; i <= totalToRender; i++) {
        const tr = document.createElement('tr');
        tr.innerHTML = createUraiRowHtml(i, byRow[i] || {});
        tbody.appendChild(tr);
    }
    uraiState.page = 1;
    applyUraiColumnGroup();
    renderUraiPagination();
    recalcUraiTotals();

    const calcCard = document.getElementById('kkfCalcResultCard');
    if (calcCard) calcCard.style.display = 'none';

    const uraiCount = urais.length;
    const plgCount = (p.pelanggans || []).length;
    setKkfStatus(`Kajian "${p.kode_kajian}" dimuat (${plgCount} slot pelanggan, ${uraiCount} baris URAI terisi, status: ${p.status}).`, 'ok');

    switchKkTab('tabKkf', document.querySelector('[data-tab="tabKkf"]'));
}

/* ==================== TAB 3: DAFTAR, PAGINATION (10,25,50,100), EXPORT & KUOTA ==================== */
function renderPagerButtons(containerEl, currentPage, totalPages, onPageFn){
    if (!containerEl) return;
    if (totalPages <= 1) {
        containerEl.innerHTML = '';
        return;
    }
    let html = `<button type="button" class="kk-page-btn" ${currentPage <= 1 ? 'disabled' : ''} data-p="${currentPage - 1}">&lsaquo;</button>`;
    const startP = Math.max(1, currentPage - 2);
    const endP = Math.min(totalPages, currentPage + 2);
    if (startP > 1) {
        html += `<button type="button" class="kk-page-btn" data-p="1">1</button>`;
        if (startP > 2) html += `<span style="padding:0 4px;">...</span>`;
    }
    for (let p = startP; p <= endP; p++) {
        html += `<button type="button" class="kk-page-btn ${p === currentPage ? 'active' : ''}" data-p="${p}">${p}</button>`;
    }
    if (endP < totalPages) {
        if (endP < totalPages - 1) html += `<span style="padding:0 4px;">...</span>`;
        html += `<button type="button" class="kk-page-btn" data-p="${totalPages}">${totalPages}</button>`;
    }
    html += `<button type="button" class="kk-page-btn" ${currentPage >= totalPages ? 'disabled' : ''} data-p="${currentPage + 1}">&rsaquo;</button>`;
    containerEl.innerHTML = html;
    containerEl.querySelectorAll('button[data-p]').forEach(b => {
        b.addEventListener('click', () => onPageFn(parseInt(b.dataset.p, 10)));
    });
}

function updateRecapExportUrls(){
    const params = new URLSearchParams();
    const q = (document.getElementById('filterSearch').value || '').trim();
    const yr = document.getElementById('filterYear').value;
    const ulp = document.getElementById('filterUlp').value;
    const st = document.getElementById('filterStatus').value;
    if (q) params.set('search', q);
    if (yr) params.set('tahun', yr);
    if (ulp) params.set('ulp', ulp);
    if (st) params.set('status', st);
    const qs = params.toString() ? '?' + params.toString() : '';
    const pdfBtn = document.getElementById('btnRecapExportPdf');
    const xlsBtn = document.getElementById('btnRecapExportExcel');
    if (pdfBtn) pdfBtn.href = `${KK_ROUTES.showBase}/export-pdf${qs}`;
    if (xlsBtn) xlsBtn.href = `${KK_ROUTES.showBase}/export-excel${qs}`;
}

function filterByUlpCard(ulpName){
    document.getElementById('filterUlp').value = ulpName;
    syncUlpCardSelection();
    filterProjectsTable(true);
}

function syncUlpCardSelection(){
    const activeUlp = document.getElementById('filterUlp').value;
    document.querySelectorAll('[data-ulp-card]').forEach(card => {
        card.classList.toggle('active', activeUlp !== '' && card.dataset.ulpCard === activeUlp);
    });
}

function filterProjectsTable(resetPage = false, pageOverride = null){
    if (resetPage) listPagerState.projectsPage = 1;
    if (pageOverride !== null) listPagerState.projectsPage = pageOverride;

    const q = (document.getElementById('filterSearch').value || '').toLowerCase().trim();
    const yr = document.getElementById('filterYear').value;
    const ulp = document.getElementById('filterUlp').value;
    const st = document.getElementById('filterStatus').value;
    const perPage = parseInt(document.getElementById('projectsPerPage').value || '10', 10);

    const allRows = Array.from(document.querySelectorAll('#projectsListTable tbody tr.project-data-row'));
    const matched = [];
    allRows.forEach(tr => {
        const text = (tr.textContent || '').toLowerCase();
        const mQ = !q || text.includes(q);
        const mY = !yr || tr.dataset.year === yr;
        const mU = !ulp || tr.dataset.ulp === ulp;
        const mS = !st || tr.dataset.status === st;
        if (mQ && mY && mU && mS) matched.push(tr);
        else tr.style.display = 'none';
    });

    const total = matched.length;
    const totalPages = Math.max(1, Math.ceil(total / perPage));
    if (listPagerState.projectsPage > totalPages) listPagerState.projectsPage = totalPages;

    const start = (listPagerState.projectsPage - 1) * perPage;
    const end = Math.min(total, start + perPage);
    matched.forEach((tr, idx) => {
        tr.style.display = (idx >= start && idx < end) ? '' : 'none';
    });

    const infoEl = document.getElementById('projectsPagerInfo');
    if (infoEl) {
        infoEl.textContent = total === 0
            ? 'Menampilkan 0 dari ' + allRows.length + ' kajian'
            : `Menampilkan ${start + 1}–${end} dari ${total} kajian`;
    }
    renderPagerButtons(
        document.getElementById('projectsPagerBtns'),
        listPagerState.projectsPage,
        totalPages,
        (p) => filterProjectsTable(false, p)
    );
    updateRecapExportUrls();
}

function applyQuotaPagination(pageNum = null){
    if (pageNum !== null) listPagerState.quotaPage = pageNum;
    const q = (document.getElementById('quotaSearch')?.value || '').toLowerCase().trim();
    const perPage = parseInt(document.getElementById('quotaPerPage')?.value || '10', 10);

    const allRows = Array.from(document.querySelectorAll('#quotaTable tbody tr.quota-data-row'));
    const matched = [];
    allRows.forEach(tr => {
        const text = (tr.textContent || '').toLowerCase();
        if (!q || text.includes(q)) matched.push(tr);
        else tr.style.display = 'none';
    });

    const total = matched.length;
    const totalPages = Math.max(1, Math.ceil(total / perPage));
    if (listPagerState.quotaPage > totalPages) listPagerState.quotaPage = totalPages;

    const start = (listPagerState.quotaPage - 1) * perPage;
    const end = Math.min(total, start + perPage);
    matched.forEach((tr, idx) => {
        tr.style.display = (idx >= start && idx < end) ? '' : 'none';
    });

    const infoEl = document.getElementById('quotaPagerInfo');
    if (infoEl) {
        infoEl.textContent = total === 0
            ? 'Menampilkan 0 vendor'
            : `Menampilkan ${start + 1}–${end} dari ${total} vendor`;
    }
    renderPagerButtons(
        document.getElementById('quotaPagerBtns'),
        listPagerState.quotaPage,
        totalPages,
        (p) => applyQuotaPagination(p)
    );
}

function openContractModal(id, kode, nama, status, rab, vendor, noKontrak, catatan){
    document.getElementById('cmProjectId').value = id;
    document.getElementById('cmSubtitle').textContent = `${kode} — ${nama}`;
    document.getElementById('cmStatus').value = ['dilengkapi_perencanaan', 'layak', 'tidak_layak', 'terkontrak'].includes(status) ? status : 'layak';
    document.getElementById('cmNilai').value = Math.round(rab || 0);
    document.getElementById('cmVendor').value = vendor || '';
    document.getElementById('cmNoKontrak').value = noKontrak || '';
    document.getElementById('cmCatatan').value = catatan || '';
    toggleCmVendorSection();
    document.getElementById('contractModal').classList.add('open');
}

function toggleCmVendorSection(){
    const isContract = document.getElementById('cmStatus').value === 'terkontrak';
    document.getElementById('cmVendorWrap').style.display = isContract ? 'block' : 'none';
    syncCmContractNo();
}

function syncCmContractNo(){
    const sel = document.getElementById('cmVendor');
    const opt = sel.options[sel.selectedIndex];
    const warn = document.getElementById('cmQuotaWarning');
    warn.style.display = 'none';
    if (!opt || !opt.value) return;
    if (!document.getElementById('cmNoKontrak').value && opt.dataset.no) {
        document.getElementById('cmNoKontrak').value = opt.dataset.no;
    }
    const nilai = parseFloat(document.getElementById('cmNilai').value || '0');
    const sisa = parseFloat(opt.dataset.sisa || '0');
    if (opt.dataset.habis === '1' || sisa <= 0) {
        warn.textContent = 'PERINGATAN: Kuota kontrak vendor ini SUDAH HABIS dan tidak dapat menerima kontrak baru!';
        warn.style.display = 'block';
    } else if (nilai > sisa) {
        warn.textContent = `Nilai RAB (${formatRp(nilai)}) melebihi sisa kuota vendor (${formatRp(sisa)}).`;
        warn.style.display = 'block';
    }
}

async function submitContractModal(){
    const id = document.getElementById('cmProjectId').value;
    const payload = {
        status: document.getElementById('cmStatus').value,
        nilai_kontrak_pakai: parseFloat(document.getElementById('cmNilai').value || '0'),
        estimasi_nilai_rab: parseFloat(document.getElementById('cmNilai').value || '0'),
        vendor_kontrak: document.getElementById('cmVendor').value,
        no_kontrak: document.getElementById('cmNoKontrak').value,
        catatan_perencanaan: document.getElementById('cmCatatan').value,
    };

    const res = await fetch(`${KK_ROUTES.showBase}/${id}/contract`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': KK_CSRF },
        body: JSON.stringify(payload)
    });
    const json = await res.json();
    if (!res.ok) {
        const msg = json.errors ? Object.values(json.errors).flat().join('\n') : (json.message || 'Gagal menyimpan kontrak.');
        alert(msg);
        return;
    }
    alert(json.message);
    window.location.reload();
}

function openQuotaModal(){
    document.getElementById('quotaModal').classList.add('open');
}

async function deleteProject(id, kode){
    if (!confirm(`Hapus data kajian ${kode}?`)) return;
    const res = await fetch(`${KK_ROUTES.showBase}/${id}`, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': KK_CSRF }
    });
    const json = await res.json();
    if (!res.ok) {
        alert(json.message || 'Gagal menghapus data.');
        return;
    }
    window.location.reload();
}

document.addEventListener('DOMContentLoaded', function(){
    syncSimDefaultDaya();
    setCustomerRows([]);
    addUraiRows(uraiState.perPage);
    bindUraiDirectPaste();
    const kodeInp = document.getElementById('kkfKodeLookup');
    if (kodeInp) {
        kodeInp.addEventListener('keydown', function(e){
            if (e.key === 'Enter') {
                e.preventDefault();
                openProjectByInput();
            }
        });
    }
    filterProjectsTable(true);
    applyQuotaPagination(1);
    runQuickSimulation(true);
});
</script>
