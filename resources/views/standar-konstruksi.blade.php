<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Standar Konstruksi SUTM 2026 | SIGINJAI PLN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        *{ margin:0; padding:0; box-sizing:border-box; font-family:'Inter', 'Segoe UI', Arial, sans-serif; }
        body{ background:#f8fafc; color:#0f172a; }
        .container{ width:min(1680px, 96%); margin:22px auto 44px; }
        .page-header{
            background:linear-gradient(135deg, #0d2137 0%, #153459 100%);
            color:white; padding:22px 26px; border-radius:16px;
            margin-bottom:16px; display:flex; justify-content:space-between;
            align-items:center; gap:15px; flex-wrap:wrap;
            box-shadow:0 12px 28px -8px rgba(13, 33, 55, 0.25);
            border:1px solid rgba(255,255,255,.08);
        }
        .page-header h2{ font-size:23px; font-weight:700; line-height:1.3; }
        .page-header p{ font-size:13px; color:#cbd5e1; margin-top:3px; }
        .header-actions{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
        .header-btn{
            display:inline-flex; align-items:center; justify-content:center; gap:6px;
            height:38px; padding:0 16px; background:#ffffff; color:#0f172a;
            border-radius:10px; text-decoration:none; font-size:13px;
            font-weight:600; white-space:nowrap; border:none; cursor:pointer;
            box-shadow:0 1px 3px rgba(0,0,0,.15);
            transition: background .2s ease, transform .2s ease;
        }
        .header-btn:hover{ background:#f1f5f9; color:#0d2137; transform:translateY(-1px); }
        .header-btn.pdf-btn{ background:#dc2626; color:#ffffff; }
        .header-btn.pdf-btn:hover{ background:#b91c1c; color:#ffffff; }

        .toolbar-card{
            background:#ffffff; border:1px solid #e2e8f0; border-radius:14px;
            padding:14px 18px; margin-bottom:16px; box-shadow:0 1px 3px rgba(15, 23, 42, 0.05);
            display:flex; gap:10px; align-items:center; flex-wrap:wrap;
        }
        .toolbar-search{
            flex:1; min-width:220px; height:40px; padding:0 15px; border:1px solid #cbd5e1;
            border-radius:10px; font-size:13.5px; background:#f8fafc; outline:none; color:#0f172a;
        }
        .toolbar-search:focus{ background:#ffffff; border-color:#0d2137; box-shadow:0 0 0 3px rgba(13,33,55,.09); }
        .toolbar-select{
            height:40px; padding:0 12px; border:1px solid #cbd5e1; border-radius:10px;
            font-size:13px; font-weight:600; background:#f8fafc; color:#0f172a; outline:none; cursor:pointer;
        }
        .toolbar-count{
            font-size:12.5px; font-weight:600; color:#475569; background:#f1f5f9;
            padding:7px 12px; border-radius:999px; border:1px solid #e2e8f0;
        }
        .btn-pdf-toolbar{
            display:inline-flex; align-items:center; gap:6px; height:40px; padding:0 15px;
            border-radius:10px; background:#b91c1c; color:#ffffff; text-decoration:none;
            font-size:13px; font-weight:700; white-space:nowrap; transition:.15s;
        }
        .btn-pdf-toolbar:hover{ background:#991b1b; color:#ffffff; }

        .table-box{
            background:#fff; border-radius:14px; overflow-x:auto; box-shadow: 0 2px 10px rgba(15,23,42,.06); border:1px solid #cbd5e1;
        }
        table{ border-collapse:collapse; width:100%; min-width:850px; }
        thead th{
            background:#0d2137; color:#f8fafc; padding:11px 12px; border:1px solid #1e3a5f; text-align:center; font-weight:700; font-size:12.5px; vertical-align:middle;
        }
        tbody td{ padding:10px 12px; border:1px solid #e2e8f0; font-size:13px; vertical-align:middle; color:#1e293b; }
        tbody tr:nth-child(even){ background:#f8fafc; }
        tbody tr:hover{ background:#eff6ff; }
        tr.sub-bab-row td.sub-bab-title{
            background:#153459; color:#ffffff; font-weight:700; font-size:13px; letter-spacing:.3px; padding:10px 14px; border:1px solid #1e3a5f;
        }
        tr.sub-bab-row:hover{ background:transparent; }
        .col-no{ width:70px; text-align:center; }
        .col-nama{ min-width:350px; }
        .col-link{ min-width:400px; }
        .link-gambar{ color:#0563C1; text-decoration:underline; cursor:pointer; font-weight:600; }
        .link-gambar:hover{ color:#003f7f; }
        .empty{ text-align:center; padding:30px; color:#777; }

        .pager-card{
            background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:12px 18px; margin-top:14px;
            display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;
            font-size:12.5px; color:#475569; box-shadow:0 1px 3px rgba(15,23,42,.04);
        }
        .pager-btns{ display:flex; gap:5px; align-items:center; flex-wrap:wrap; }
        .pg-btn{
            min-width:32px; height:32px; padding:0 9px; border-radius:8px; border:1px solid #cbd5e1;
            background:#fff; color:#334155; font-size:12.5px; font-weight:600; cursor:pointer;
        }
        .pg-btn:hover:not(:disabled){ background:#f1f5f9; }
        .pg-btn.active{ background:#0d2137; color:#fff; border-color:#0d2137; }
        .pg-btn:disabled{ opacity:.45; cursor:not-allowed; }

        @media print {
            .simerah-topbar, .simerah-sidebar, .header-actions, .toolbar-card, .pager-card { display:none !important; }
            .simerah-main { margin-left:0 !important; width:100% !important; }
        }
        @media(max-width:700px){
            .container{ width:96%; margin-top:12px; }
            .page-header{ flex-direction:column; align-items:flex-start; padding:16px 18px; }
            .page-header h2{ font-size:20px; }
            .header-actions{ width:100%; justify-content:flex-end; }
        }
    </style>
</head>

<body>

@includeIf('partials.simerah-topbar', ['topSubTitle' => 'UP3 JAMBI • STANDAR KONSTRUKSI', 'withSidebar' => true])

<div class="simerah-shell">
    @includeIf('partials.simerah-sidebar', ['activeNav' => 'standar'])
    <div class="simerah-main">

<div class="container">
    <div class="page-header">
        <div>
            <h2>Standar Konstruksi SUTM 2026</h2>
            <p>Dokumentasi Standar Konstruksi Jaringan Tegangan Menengah PLN UP3 Jambi</p>
        </div>
    </div>

    <div class="toolbar-card">
        <div style="display:inline-flex;align-items:center;gap:8px;font-size:13.5px;font-weight:600;color:#334155;">
            <span>Show</span>
            <select id="userStandarPerPage" class="toolbar-select" aria-label="Show entries" style="min-width:74px;">
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span>entries</span>
        </div>
        <input type="text" id="searchKonstruksi" class="toolbar-search" placeholder="Cari nama konstruksi, sub-bab, atau nomor..." autocomplete="off">
        <select id="userStandarYear" class="toolbar-select" aria-label="Filter Tahun">
            <option value="">Semua Tahun</option>
        </select>
        <a href="{{ route('standar-konstruksi.export-pdf') }}" id="btnUserStandarPdf" target="_blank" class="btn-pdf-toolbar">
            <i class="fa fa-file-pdf"></i> Cetak PDF
        </a>
    </div>

    <div class="table-box">
        <table id="tabelKonstruksi">
            <thead>
                <tr>
                    <th class="col-no">NO</th>
                    <th class="col-nama">KONTRUKSI SUTM 2026</th>
                    <th class="col-link">LINK GAMBAR</th>
                </tr>
            </thead>
            <tbody>
                @php $subBabTampilSaatIni = null; @endphp
                @forelse($konstruksi as $item)
                    @if($item->sub_bab_nama && $item->sub_bab_nama !== $subBabTampilSaatIni)
                        @php $subBabTampilSaatIni = $item->sub_bab_nama; @endphp
                        <tr class="sub-bab-row">
                            <td colspan="3" class="sub-bab-title">
                                {{ $item->sub_bab_kode ? $item->sub_bab_kode . ' - ' . $item->sub_bab_nama : $item->sub_bab_nama }}
                            </td>
                        </tr>
                    @endif
                    <tr class="standar-item-row" data-id="{{ $item->id }}">
                        <td class="col-no">{{ $item->no }}</td>
                        <td class="col-nama">{{ $item->nama_konstruksi }}</td>
                        <td class="col-link">
                            @if($item->link_gambar)
                                <a href="{{ $item->link_gambar }}" target="_blank" rel="noopener noreferrer" class="link-gambar">Lihat Gambar</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty">Belum ada data Standar Konstruksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pager-card" id="userStandarPager" style="justify-content:flex-end;">
        <div class="pager-btns" id="userStandarPagerBtns"></div>
    </div>
</div>

<script>
(function(){
    var searchInput = document.getElementById('searchKonstruksi');
    var yearSelect  = document.getElementById('userStandarYear');
    var perPageSel  = document.getElementById('userStandarPerPage');
    var pagerBtns   = document.getElementById('userStandarPagerBtns');
    var pdfLinks    = [document.getElementById('btnUserStandarPdf')];
    var pdfBaseUrl  = @json(route('standar-konstruksi.export-pdf'));

    var tbody = document.querySelector('#tabelKonstruksi tbody');
    if (!tbody) return;

    var itemRows = Array.prototype.slice.call(tbody.querySelectorAll('tr.standar-item-row'));
    var subBabRows = Array.prototype.slice.call(tbody.querySelectorAll('tr.sub-bab-row'));
    var yearSet = {};

    itemRows.forEach(function(r){
        var txt = (r.textContent || '').replace(/\s+/g, ' ').trim();
        r.dataset.search = txt.toLowerCase();
        var m = txt.match(/\b(20[1-3][0-9])\b/g) || [];
        r.dataset.years = m.join(',');
        m.forEach(function(y){ yearSet[y] = true; });
    });

    Object.keys(yearSet).sort(function(a,b){ return Number(b) - Number(a); }).forEach(function(y){
        var opt = document.createElement('option');
        opt.value = y;
        opt.textContent = 'Tahun ' + y;
        yearSelect.appendChild(opt);
    });

    var currentPage = 1;

    function syncPdfUrls(visibleRows, perPage){
        var params = new URLSearchParams();
        if (searchInput.value.trim()) params.set('q', searchInput.value.trim());
        if (yearSelect.value) params.set('tahun', yearSelect.value);
        params.set('per_page', String(perPage));
        params.set('page', String(currentPage));
        var ids = [];
        (visibleRows || []).forEach(function(r){
            if (r.dataset.id) ids.push(r.dataset.id);
        });
        if (ids.length > 0) params.set('ids', ids.join(','));
        var url = pdfBaseUrl + (params.toString() ? '?' + params.toString() : '');
        pdfLinks.forEach(function(a){ if (a) a.href = url; });
    }

    function renderPager(totalPages){
        if (totalPages <= 1) {
            pagerBtns.innerHTML = '';
            return;
        }
        var html = '<button type="button" class="pg-btn" ' + (currentPage <= 1 ? 'disabled' : '') + ' data-p="' + (currentPage - 1) + '">&lsaquo;</button>';
        var s = Math.max(1, currentPage - 2);
        var e = Math.min(totalPages, currentPage + 2);
        if (s > 1) html += '<button type="button" class="pg-btn" data-p="1">1</button>' + (s > 2 ? '<span>...</span>' : '');
        for (var p = s; p <= e; p++) {
            html += '<button type="button" class="pg-btn ' + (p === currentPage ? 'active' : '') + '" data-p="' + p + '">' + p + '</button>';
        }
        if (e < totalPages) html += (e < totalPages - 1 ? '<span>...</span>' : '') + '<button type="button" class="pg-btn" data-p="' + totalPages + '">' + totalPages + '</button>';
        html += '<button type="button" class="pg-btn" ' + (currentPage >= totalPages ? 'disabled' : '') + ' data-p="' + (currentPage + 1) + '">&rsaquo;</button>';
        pagerBtns.innerHTML = html;
        pagerBtns.querySelectorAll('button[data-p]').forEach(function(b){
            b.addEventListener('click', function(){
                currentPage = parseInt(this.dataset.p, 10);
                applyFilter(false);
            });
        });
    }

    function applyFilter(resetPage){
        if (resetPage) currentPage = 1;
        var q = searchInput.value.trim().toLowerCase();
        var y = yearSelect.value;
        var perPage = parseInt(perPageSel.value, 10) || 10;

        var matched = itemRows.filter(function(r){
            var okQ = !q || (r.dataset.search || '').indexOf(q) !== -1;
            var okY = !y || (r.dataset.years || '').split(',').indexOf(y) !== -1;
            return okQ && okY;
        });

        var total = matched.length;
        var totalPages = Math.max(1, Math.ceil(total / perPage));
        if (currentPage > totalPages) currentPage = totalPages;

        var start = (currentPage - 1) * perPage;
        var end = Math.min(total, start + perPage);
        var visibleSlice = matched.slice(start, end);
        var pageSet = new Set(visibleSlice);

        itemRows.forEach(function(r){
            r.style.display = pageSet.has(r) ? '' : 'none';
        });

        subBabRows.forEach(function(sb){
            var hasVisible = false;
            var nxt = sb.nextElementSibling;
            while (nxt && !nxt.classList.contains('sub-bab-row')) {
                if (nxt.classList.contains('standar-item-row') && nxt.style.display !== 'none') {
                    hasVisible = true;
                    break;
                }
                nxt = nxt.nextElementSibling;
            }
            sb.style.display = hasVisible ? '' : 'none';
        });

        syncPdfUrls(visibleSlice, perPage);
        renderPager(totalPages);
    }

    searchInput.addEventListener('input', function(){ applyFilter(true); });
    yearSelect.addEventListener('change', function(){ applyFilter(true); });
    perPageSel.addEventListener('change', function(){ applyFilter(true); });
    applyFilter(true);
})();

const btnBack = document.getElementById('btnBackHistory');
if(btnBack){
    btnBack.addEventListener('click', function(event){
        if(window.history.length > 1 && document.referrer){
            event.preventDefault();
            window.history.back();
        }
    });
}
</script>
    </div>
</div>
</body>
</html>
