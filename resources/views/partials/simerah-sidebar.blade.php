@php
    $sidebarMenuList = $sidebarMenu
        ?? app(\App\Http\Controllers\DashboardController::class)->buildSidebarMenu(request('tahun'));
    $activeNav     = $activeNav ?? '';
    $activeSheetId = isset($activeSheetId) ? (int) $activeSheetId : null;
    $yearQuerySuffix = request('tahun') ? ('?tahun=' . urlencode((string) request('tahun'))) : '';
@endphp

<style>
    .simerah-shell{
        display:flex;
        min-height:calc(100vh - 62px);
        align-items:stretch;
        width:100%;
    }

    .simerah-main{
        flex:1;
        min-width:0;
        padding:22px 26px 44px;
    }

    .simerah-main > .container,
    .simerah-main > .plg2026-container{
        width:100% !important;
        max-width:100% !important;
        margin:0 !important;
    }

    .simerah-sidebar{
        width:270px;
        flex-shrink:0;
        background:#ffffff;
        color:#334155;
        display:flex;
        flex-direction:column;
        padding:16px 12px 24px;
        border-right:1px solid #e2e8f0;
        position:sticky;
        top:62px;
        height:calc(100vh - 62px);
        overflow-y:auto;
        z-index:100;
        font-family:'Inter', 'Segoe UI', Arial, sans-serif;
    }

    .simerah-sidebar::-webkit-scrollbar{
        width:5px;
    }
    .simerah-sidebar::-webkit-scrollbar-thumb{
        background:#cbd5e1;
        border-radius:10px;
    }

    .simerah-sidebar-backdrop{
        display:none;
    }

    .simerah-sidebar .sidebar-section-label{
        font-size:10.5px;
        font-weight:700;
        letter-spacing:.08em;
        text-transform:uppercase;
        color:#94a3b8;
        padding:12px 12px 6px;
        margin-top:4px;
    }

    .simerah-sidebar .sidebar-search{
        position:relative;
        display:flex;
        align-items:center;
        background:#f8fafc;
        border:1px solid #e2e8f0;
        border-radius:10px;
        padding:0 12px;
        height:38px;
        margin-bottom:14px;
        transition:.18s;
    }

    .simerah-sidebar .sidebar-search:focus-within{
        background:#ffffff;
        border-color:#0d2137;
        box-shadow:0 0 0 3px rgba(13,33,55,.08);
    }

    .simerah-sidebar .sidebar-search-icon{
        color:#94a3b8;
        font-size:13px;
        margin-right:8px;
        flex-shrink:0;
    }

    .simerah-sidebar .sidebar-search input{
        width:100%;
        border:none;
        outline:none;
        background:transparent;
        font-size:13px;
        color:#0f172a;
        font-family:inherit;
    }

    .simerah-sidebar .sidebar-search input::placeholder{
        color:#94a3b8;
    }

    .simerah-sidebar .sidebar-search-results{
        display:none;
        position:absolute;
        top:calc(100% + 6px);
        left:0;
        right:0;
        background:#ffffff;
        border:1px solid #e2e8f0;
        border-radius:12px;
        box-shadow:0 14px 32px rgba(15,23,42,.14);
        max-height:360px;
        overflow-y:auto;
        z-index:220;
        padding:6px;
    }

    .simerah-sidebar .sidebar-search-results.show{
        display:block;
    }

    .simerah-sidebar .search-result-item{
        display:block;
        padding:9px 10px;
        border-radius:8px;
        text-decoration:none;
        color:#0f172a;
        border-bottom:1px solid #f1f5f9;
        transition:.12s;
    }

    .simerah-sidebar .search-result-item:last-child{
        border-bottom:none;
    }

    .simerah-sidebar .search-result-item:hover{
        background:#f1f5f9;
    }

    .simerah-sidebar .search-result-title{
        font-size:12.5px;
        font-weight:700;
        color:#0d2137;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:6px;
    }

    .simerah-sidebar .search-result-badge{
        font-size:10px;
        font-weight:600;
        background:#e2e8f0;
        color:#334155;
        padding:2px 6px;
        border-radius:5px;
        flex-shrink:0;
    }

    .simerah-sidebar .search-result-snippet{
        font-size:11.5px;
        color:#64748b;
        margin-top:2px;
        line-height:1.35;
        word-break:break-word;
    }

    .simerah-sidebar .search-result-empty{
        padding:14px 10px;
        text-align:center;
        font-size:12.5px;
        color:#64748b;
    }

    .simerah-sidebar .menu > a{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        padding:10px 12px;
        border-radius:10px;
        text-decoration:none;
        color:#334155;
        font-size:13.5px;
        font-weight:600;
        transition:.15s;
        margin-bottom:3px;
    }

    .simerah-sidebar .menu > a span{
        display:flex;
        align-items:center;
        gap:10px;
    }

    .simerah-sidebar .menu > a:hover{
        background:#f1f5f9;
        color:#0f172a;
    }

    .simerah-sidebar .menu > a.active-nav{
        background:#0d2137;
        color:#ffffff;
        box-shadow:0 4px 12px rgba(13,33,55,.18);
    }

    .simerah-sidebar .menu > a.active-nav i{
        color:#ffffff;
    }

    .simerah-sidebar .submenu,
    .simerah-sidebar .subsubmenu,
    .simerah-sidebar .subsubsubmenu,
    .simerah-sidebar .menu-lvl4,
    .simerah-sidebar .menu-lvl5{
        display:none;
        flex-direction:column;
        gap:2px;
    }

    .simerah-sidebar .submenu.open,
    .simerah-sidebar .subsubmenu.open,
    .simerah-sidebar .subsubsubmenu.open,
    .simerah-sidebar .menu-lvl4.open,
    .simerah-sidebar .menu-lvl5.open{
        display:flex;
    }

    .simerah-sidebar .submenu{
        margin:4px 0 8px;
        padding:6px;
        background:#f8fafc;
        border:1px solid #e2e8f0;
        border-radius:10px;
    }

    .simerah-sidebar .submenu a{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:8px;
        padding:7px 10px;
        border-radius:8px;
        text-decoration:none;
        color:#334155;
        font-size:12.5px;
        font-weight:500;
        line-height:1.35;
        transition:.14s;
    }

    .simerah-sidebar .submenu a:hover{
        background:#e2e8f0;
        color:#0f172a;
    }

    .simerah-sidebar .submenu a.active-sheet{
        background:#0d2137;
        color:#ffffff;
        font-weight:700;
        box-shadow:0 2px 8px rgba(13,33,55,.18);
    }

    .simerah-sidebar .subsubmenu{
        padding-left:10px;
        border-left:2px solid #cbd5e1;
        margin:2px 0 4px 8px;
    }

    .simerah-sidebar .subsubsubmenu{
        padding-left:10px;
        border-left:1.5px solid #cbd5e1;
        margin:2px 0 4px 8px;
    }

    .simerah-sidebar .menu-lvl4{
        padding-left:10px;
        border-left:1px dashed #cbd5e1;
        margin:2px 0 4px 8px;
    }

    .simerah-sidebar .menu-lvl5{
        padding-left:10px;
        border-left:1px dotted #cbd5e1;
        margin:2px 0 4px 8px;
    }

    .simerah-sidebar .menu-lvl4-item,
    .simerah-sidebar .menu-lvl5-item{
        font-size:12px !important;
    }

    .simerah-sidebar .menu-empty{
        padding:8px 10px;
        font-size:12px;
        color:#94a3b8;
    }

    .simerah-sidebar .fa-chevron-down{
        font-size:10.5px;
        transition:transform .18s ease;
    }

    .simerah-sidebar a.chevron-open .fa-chevron-down{
        transform:rotate(180deg);
    }

    @media (max-width: 768px){
        .simerah-shell{
            display:block;
        }
        .simerah-main{
            padding:16px 14px 36px;
        }
        .simerah-sidebar{
            position:fixed;
            top:62px;
            left:0;
            bottom:0;
            height:calc(100vh - 62px);
            transform:translateX(-105%);
            transition:transform .22s ease;
            box-shadow:6px 0 28px rgba(15,23,42,.18);
            z-index:260;
        }
        .simerah-sidebar.mobile-open{
            transform:translateX(0);
        }
        .simerah-sidebar-backdrop.show{
            display:block;
            position:fixed;
            inset:62px 0 0 0;
            background:rgba(15,23,42,.45);
            z-index:250;
        }
    }
</style>

<div class="simerah-sidebar-backdrop" id="simerahSidebarBackdrop"></div>

<aside class="sidebar simerah-sidebar" id="simerahSidebar">
    <div class="menu">
        <div class="sidebar-search">
            <span class="sidebar-search-icon"><i class="fa fa-magnifying-glass"></i></span>
            <input type="text" id="sheetSearchInput" placeholder="Telusuri Data Monitoring..." autocomplete="off">
            <div class="sidebar-search-results" id="sheetSearchResults"></div>
        </div>

        <a href="{{ route('dashboard') . $yearQuerySuffix }}" class="{{ $activeNav === 'dashboard' ? 'active-nav' : '' }}">
            <span><i class="fa fa-home"></i> Dashboard Utama</span>
        </a>

        <div class="sidebar-section-label">Modul Monitoring</div>

        <a href="#" id="pusatMonitoringTrigger" onclick="toggleSimerahMenu(); return false;">
            <span><i class="fa fa-folder"></i> Pusat Monitoring</span>
            <i class="fa fa-chevron-down" id="pusatMonitoringChevron"></i>
        </a>

        <div class="submenu" id="submenu">
            @foreach($sidebarMenuList as $monitoringLabel => $monitoringGroup)
                <a href="#" onclick="toggleSimerahSub('sub{{ Str::slug($monitoringLabel) }}', this); return false;">
                    <span>{{ $monitoringLabel }}</span>
                    <i class="fa fa-chevron-down"></i>
                </a>

                <div class="subsubmenu" id="sub{{ Str::slug($monitoringLabel) }}">
                    @if(($monitoringGroup['type'] ?? 'flat') === 'monitoring-khs')
                        @foreach($monitoringGroup['groups'] as $topLabel => $topGroup)
                            @if(($topGroup['type'] ?? 'flat') === 'nested')
                                <a href="#" onclick="toggleSimerahSub('sub{{ Str::slug($monitoringLabel.'-'.$topLabel) }}', this); return false;">
                                    <span>{{ $topLabel }}</span>
                                    <i class="fa fa-chevron-down"></i>
                                </a>

                                <div class="subsubsubmenu" id="sub{{ Str::slug($monitoringLabel.'-'.$topLabel) }}">
                                    @foreach($topGroup['children'] as $subLabel => $sheetsInSub)
                                        @php
                                            $flatSubSheets = [];
                                            foreach ((array) $sheetsInSub as $entry) {
                                                if (is_array($entry)) {
                                                    foreach ($entry as $nestedSheet) {
                                                        if (is_object($nestedSheet)) $flatSubSheets[] = $nestedSheet;
                                                    }
                                                } elseif (is_object($entry)) {
                                                    $flatSubSheets[] = $entry;
                                                }
                                            }
                                            $primarySubSheet = collect($flatSubSheets)->sortByDesc(fn ($s) => (int) ($s->tahun ?? 0))->first() ?? ($flatSubSheets[0] ?? null);
                                            $subSheetIds = array_map(fn ($s) => (int) $s->id, $flatSubSheets);
                                            $isSubActive = $activeSheetId !== null && in_array($activeSheetId, $subSheetIds, true);
                                            $displaySubLabel = count($flatSubSheets) === 1 && $primarySubSheet ? $primarySubSheet->nama_sheet : $subLabel;
                                        @endphp
                                        @if($primarySubSheet)
                                            <a href="{{ route('sheet.show', $primarySubSheet->id) . $yearQuerySuffix }}"
                                               class="menu-lvl4-item {{ $isSubActive ? 'active-sheet' : '' }}">
                                                {{ $displaySubLabel }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @elseif(in_array($topLabel, ['Reservasi', 'Rekap'], true))
                                <a href="#" onclick="toggleSimerahSub('sub{{ Str::slug($monitoringLabel.'-'.$topLabel) }}', this); return false;">
                                    <span>{{ $topLabel }}</span>
                                    <i class="fa fa-chevron-down"></i>
                                </a>

                                <div class="subsubsubmenu" id="sub{{ Str::slug($monitoringLabel.'-'.$topLabel) }}">
                                    @foreach($topGroup['items'] as $itemSheet)
                                        <a href="{{ route('sheet.show', $itemSheet->id) . $yearQuerySuffix }}"
                                           class="menu-lvl4-item {{ $activeSheetId === (int) $itemSheet->id ? 'active-sheet' : '' }}">
                                            {{ $itemSheet->nama_sheet }}
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                @php
                                    $groupItems = $topGroup['items'] ?? [];
                                    $primaryTopSheet = collect($groupItems)->sortByDesc(fn ($s) => (int) ($s->tahun ?? 0))->first() ?? ($groupItems[0] ?? null);
                                    $topSheetIds = array_map(fn ($s) => (int) $s->id, $groupItems);
                                    $isTopActive = $activeSheetId !== null && in_array($activeSheetId, $topSheetIds, true);
                                    $displayTopLabel = count($groupItems) === 1 && $primaryTopSheet ? $primaryTopSheet->nama_sheet : $topLabel;
                                @endphp
                                @if($primaryTopSheet)
                                    <a href="{{ route('sheet.show', $primaryTopSheet->id) . $yearQuerySuffix }}"
                                       class="{{ $isTopActive ? 'active-sheet' : '' }}">
                                        <span>{{ $displayTopLabel }}</span>
                                    </a>
                                @endif
                            @endif
                        @endforeach
                    @else
                        @forelse($monitoringGroup['items'] as $itemSheet)
                            <a href="{{ route('sheet.show', $itemSheet->id) . $yearQuerySuffix }}"
                               class="{{ $activeSheetId === (int) $itemSheet->id ? 'active-sheet' : '' }}">
                                {{ $itemSheet->nama_sheet }}
                            </a>
                        @empty
                            <div class="menu-empty">Belum ada data. Silakan upload file Excel-nya di halaman Admin.</div>
                        @endforelse
                    @endif
                </div>
            @endforeach
        </div>

        <div class="sidebar-section-label">Informasi &amp; Kajian</div>

        <a href="{{ route('standar-konstruksi.index') }}" class="{{ $activeNav === 'standar' ? 'active-nav' : '' }}">
            <span><i class="fa fa-images"></i> Standar Konstruksi</span>
        </a>

        <a href="{{ route('kajian-kelayakan.index') }}" class="{{ $activeNav === 'kajian' ? 'active-nav' : '' }}">
            <span><i class="fa fa-calculator"></i> Kajian Kelayakan Proyek</span>
        </a>
    </div>
</aside>

<script>
(function(){
    window.toggleSimerahMenu = function(){
        var submenu = document.getElementById('submenu');
        var trigger = document.getElementById('pusatMonitoringTrigger');
        if (!submenu) return;
        var isOpen = submenu.classList.toggle('open');
        if (trigger) trigger.classList.toggle('chevron-open', isOpen);
    };

    window.toggleSimerahSub = function(id, triggerEl){
        var el = document.getElementById(id);
        if (!el) return;
        var isOpen = el.classList.toggle('open');
        if (triggerEl) triggerEl.classList.toggle('chevron-open', isOpen);
    };

    function initSimerahSidebar(){
        var sidebar   = document.getElementById('simerahSidebar');
        var backdrop  = document.getElementById('simerahSidebarBackdrop');
        var toggleBtn = document.getElementById('mobileSidebarToggle');

        // Auto-buka hierarchy menu sampai sheet yang sedang aktif
        if (sidebar) {
            var activeLink = sidebar.querySelector('a.active-sheet');
            if (activeLink) {
                var parent = activeLink.parentElement;
                while (parent && parent !== sidebar) {
                    if (parent.classList.contains('submenu')
                        || parent.classList.contains('subsubmenu')
                        || parent.classList.contains('subsubsubmenu')
                        || parent.classList.contains('menu-lvl4')
                        || parent.classList.contains('menu-lvl5')) {
                        parent.classList.add('open');
                        var prevTrigger = parent.previousElementSibling;
                        if (prevTrigger && prevTrigger.tagName === 'A') {
                            prevTrigger.classList.add('chevron-open');
                        }
                    }
                    parent = parent.parentElement;
                }
                setTimeout(function(){
                    try { activeLink.scrollIntoView({ block: 'nearest' }); } catch(e) {}
                }, 40);
            }
        }

        // Mobile sidebar drawer
        if (toggleBtn && sidebar && backdrop) {
            toggleBtn.addEventListener('click', function(){
                sidebar.classList.toggle('mobile-open');
                backdrop.classList.toggle('show', sidebar.classList.contains('mobile-open'));
            });
            backdrop.addEventListener('click', function(){
                sidebar.classList.remove('mobile-open');
                backdrop.classList.remove('show');
            });
        }

        // Live AJAX search sidebar
        var searchInput   = document.getElementById('sheetSearchInput');
        var searchResults = document.getElementById('sheetSearchResults');
        if (!searchInput || !searchResults) return;

        var searchTimer = null;
        var activeController = null;
        var urlYear = new URLSearchParams(window.location.search).get('tahun') || '';
        var searchUrlBase = @json(route('search.sheet'));
        var sheetShowUrlBase = @json(url('/sheet'));

        function escapeHtml(str){
            return String(str || '').replace(/[&<>"']/g, function(m){
                return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];
            });
        }

        function runSearch(q){
            if (activeController && typeof activeController.abort === 'function') {
                activeController.abort();
            }
            activeController = new AbortController();

            var url = searchUrlBase + '?q=' + encodeURIComponent(q);
            if (urlYear) url += '&tahun=' + encodeURIComponent(urlYear);

            fetch(url, {
                signal: activeController.signal,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(r){ return r.json(); })
            .then(function(payload){
                var sheets = (payload && Array.isArray(payload.sheets)) ? payload.sheets : [];
                var rows   = (payload && Array.isArray(payload.data)) ? payload.data : [];

                if (sheets.length === 0 && rows.length === 0) {
                    searchResults.innerHTML = '<div class="search-result-empty">Tidak ditemukan data yang cocok.</div>';
                    searchResults.classList.add('show');
                    return;
                }

                var html = '';
                sheets.slice(0, 10).forEach(function(item){
                    var targetId = item.id || item.sheet_id;
                    var title    = item.nama_sheet || 'Sheet';
                    var kategori = item.kategori || 'Sheet';
                    var href     = sheetShowUrlBase + '/' + encodeURIComponent(targetId);
                    if (urlYear) href += '?tahun=' + encodeURIComponent(urlYear);

                    html += '<a class="search-result-item" href="' + escapeHtml(href) + '">'
                          + '<div class="search-result-title"><span>' + escapeHtml(title) + '</span>'
                          + '<span class="search-result-badge">' + escapeHtml(kategori) + '</span>'
                          + '</div>'
                          + '</a>';
                });

                rows.slice(0, 15).forEach(function(item){
                    var targetId = item.sheet_id || item.id;
                    var title    = item.nama_sheet || 'Sheet';
                    var snippet  = item.snippet || '';
                    var rowNo    = item.row_number ? ('Baris ' + item.row_number) : '';
                    var href     = sheetShowUrlBase + '/' + encodeURIComponent(targetId);
                    if (urlYear) href += '?tahun=' + encodeURIComponent(urlYear);
                    if (q) href += (href.indexOf('?') !== -1 ? '&' : '?') + 'q=' + encodeURIComponent(q);

                    html += '<a class="search-result-item" href="' + escapeHtml(href) + '">'
                          + '<div class="search-result-title"><span>' + escapeHtml(title) + '</span>'
                          + (rowNo ? '<span class="search-result-badge">' + escapeHtml(rowNo) + '</span>' : '')
                          + '</div>'
                          + (snippet ? '<div class="search-result-snippet">' + escapeHtml(snippet) + '</div>' : '')
                          + '</a>';
                });

                searchResults.innerHTML = html;
                searchResults.classList.add('show');
            })
            .catch(function(err){
                if (err && err.name === 'AbortError') return;
                searchResults.innerHTML = '<div class="search-result-empty">Gagal memuat hasil pencarian.</div>';
                searchResults.classList.add('show');
            });
        }

        searchInput.addEventListener('input', function(){
            var q = searchInput.value.trim();
            clearTimeout(searchTimer);
            if (q.length < 2) {
                searchResults.classList.remove('show');
                searchResults.innerHTML = '';
                return;
            }
            searchTimer = setTimeout(function(){ runSearch(q); }, 240);
        });

        document.addEventListener('click', function(e){
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.remove('show');
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSimerahSidebar);
    } else {
        initSimerahSidebar();
    }
})();
</script>
