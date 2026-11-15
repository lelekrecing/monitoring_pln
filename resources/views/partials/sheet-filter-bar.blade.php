<style>
    .sheet-filter-card{
        background:#ffffff;
        border:1px solid #e2e8f0;
        border-radius:14px;
        padding:14px 18px;
        margin-bottom:16px;
        box-shadow:0 1px 3px rgba(15, 23, 42, 0.05);
        display:flex;
        flex-direction:column;
        gap:12px;
        font-family:'Inter', 'Segoe UI', Arial, sans-serif;
    }

    .sheet-filter-top{
        display:flex;
        align-items:center;
        justify-content:space-between;
        flex-wrap:wrap;
        gap:10px;
    }

    .sheet-filter-controls{
        display:flex;
        align-items:center;
        flex-wrap:wrap;
        gap:10px;
        flex:1;
        min-width:260px;
    }

    .sheet-search-wrap{
        position:relative;
        flex:1;
        min-width:220px;
        display:flex;
        align-items:center;
    }

    .sheet-search-wrap i.sf-icon{
        position:absolute;
        left:13px;
        color:#94a3b8;
        font-size:13.5px;
        pointer-events:none;
    }

    .sheet-search-wrap input{
        width:100%;
        height:40px;
        background:#f8fafc;
        border:1px solid #cbd5e1;
        border-radius:10px;
        padding:0 36px 0 38px;
        font-size:13.5px;
        color:#0f172a;
        outline:none;
        transition:all .18s ease;
    }

    .sheet-search-wrap input:focus{
        background:#ffffff;
        border-color:#0d2137;
        box-shadow:0 0 0 3px rgba(13, 33, 55, 0.09);
    }

    .sheet-search-clear{
        position:absolute;
        right:8px;
        width:24px;
        height:24px;
        border:none;
        border-radius:50%;
        background:#e2e8f0;
        color:#475569;
        cursor:pointer;
        display:none;
        align-items:center;
        justify-content:center;
        font-size:12px;
    }

    .sheet-search-clear.show{
        display:inline-flex;
    }

    .sheet-year-wrap, .sheet-perpage-wrap{
        position:relative;
        display:flex;
        align-items:center;
        min-width:155px;
    }

    .sheet-perpage-wrap{
        min-width:145px;
    }

    .sheet-year-wrap i.sy-icon,
    .sheet-perpage-wrap i.sp-icon{
        position:absolute;
        left:12px;
        color:#64748b;
        font-size:13px;
        pointer-events:none;
    }

    .sheet-year-wrap select,
    .sheet-perpage-wrap select{
        width:100%;
        height:40px;
        background:#f8fafc;
        border:1px solid #cbd5e1;
        border-radius:10px;
        padding:0 12px 0 34px;
        font-size:13px;
        font-weight:600;
        color:#0f172a;
        cursor:pointer;
        outline:none;
        transition:all .18s ease;
    }

    .sheet-year-wrap select:focus,
    .sheet-perpage-wrap select:focus{
        background:#ffffff;
        border-color:#0d2137;
        box-shadow:0 0 0 3px rgba(13, 33, 55, 0.09);
    }

    .sheet-filter-meta{
        display:flex;
        align-items:center;
        gap:8px;
        flex-wrap:wrap;
        flex-shrink:0;
    }

    .sheet-count-pill{
        display:inline-flex;
        align-items:center;
        gap:6px;
        background:#f1f5f9;
        color:#334155;
        border:1px solid #e2e8f0;
        border-radius:999px;
        padding:7px 12px;
        font-size:12px;
        font-weight:600;
    }

    .sheet-pdf-btn{
        display:inline-flex;
        align-items:center;
        gap:6px;
        height:38px;
        padding:0 14px;
        border-radius:10px;
        background:#b91c1c;
        color:#ffffff;
        font-size:12.5px;
        font-weight:700;
        text-decoration:none;
        border:none;
        cursor:pointer;
        box-shadow:0 2px 6px rgba(185, 28, 28, .22);
        transition:.15s ease;
        white-space:nowrap;
    }

    .sheet-pdf-btn:hover{
        background:#991b1b;
        color:#ffffff;
        transform:translateY(-1px);
    }

    .sheet-reset-btn{
        display:none;
        align-items:center;
        gap:5px;
        height:38px;
        padding:0 12px;
        border-radius:10px;
        border:1px solid #cbd5e1;
        background:#ffffff;
        color:#0d2137;
        font-size:12.5px;
        font-weight:600;
        cursor:pointer;
        transition:.15s;
    }

    .sheet-reset-btn.show{
        display:inline-flex;
    }

    .sheet-reset-btn:hover{
        background:#f1f5f9;
    }

    .sheet-pager-bar{
        display:flex;
        align-items:center;
        justify-content:space-between;
        flex-wrap:wrap;
        gap:10px;
        padding-top:10px;
        border-top:1px solid #f1f5f9;
        font-size:12.5px;
        color:#475569;
    }

    .sheet-pager-bottom{
        background:#ffffff;
        border:1px solid #e2e8f0;
        border-radius:14px;
        padding:12px 18px;
        margin-top:16px;
        box-shadow:0 1px 3px rgba(15, 23, 42, 0.05);
        display:flex;
        align-items:center;
        justify-content:space-between;
        flex-wrap:wrap;
        gap:10px;
        font-size:12.5px;
        color:#475569;
        font-family:'Inter', 'Segoe UI', Arial, sans-serif;
    }

    .sheet-pager-controls{
        display:flex;
        align-items:center;
        gap:5px;
        flex-wrap:wrap;
    }

    .sheet-pg-btn{
        min-width:32px;
        height:32px;
        padding:0 9px;
        border-radius:8px;
        border:1px solid #cbd5e1;
        background:#ffffff;
        color:#334155;
        font-size:12.5px;
        font-weight:600;
        cursor:pointer;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        transition:.12s ease;
    }

    .sheet-pg-btn:hover:not(:disabled){
        background:#f1f5f9;
        border-color:#94a3b8;
    }

    .sheet-pg-btn.active{
        background:#0d2137;
        color:#ffffff;
        border-color:#0d2137;
    }

    .sheet-pg-btn:disabled{
        opacity:.45;
        cursor:not-allowed;
    }

    .sheet-no-match-banner{
        display:none;
        background:#ffffff;
        border:1px dashed #cbd5e1;
        border-radius:14px;
        padding:28px 20px;
        text-align:center;
        color:#64748b;
        font-size:14px;
        margin-bottom:16px;
    }

    .sheet-no-match-banner.show{
        display:block;
    }

    @media print {
        .simerah-topbar, .simerah-sidebar, .sheet-filter-card, .sheet-pager-bottom, .header-actions {
            display:none !important;
        }
        .simerah-main { margin-left:0 !important; width:100% !important; }
    }
</style>

<div class="sheet-filter-card" id="sheetFilterBar">
    <div class="sheet-filter-top">
        <div class="sheet-filter-controls">
            <div class="sheet-search-wrap">
                <i class="fa fa-magnifying-glass sf-icon"></i>
                <input type="text" id="sheetTableSearchInput" placeholder="Cari data pada sheet ini (Penyedia, No KR, Paket, Lokasi, Nama...)" autocomplete="off">
                <button type="button" class="sheet-search-clear" id="sheetTableSearchClear" title="Hapus pencarian">
                    <i class="fa fa-xmark"></i>
                </button>
            </div>

            <div class="sheet-year-wrap">
                <i class="fa fa-calendar-days sy-icon"></i>
                <select id="sheetTableYearFilter" aria-label="Filter Tahun">
                    <option value="">Semua Tahun</option>
                </select>
            </div>

            <div class="sheet-perpage-wrap">
                <i class="fa fa-list-ol sp-icon"></i>
                <select id="sheetTablePerPage" aria-label="Jumlah Baris per Halaman">
                    <option value="10">Tampil: 10</option>
                    <option value="25" selected>Tampil: 25</option>
                    <option value="50">Tampil: 50</option>
                    <option value="100">Tampil: 100</option>
                    <option value="all">Tampil: Semua</option>
                </select>
            </div>
        </div>

        <div class="sheet-filter-meta">
            <span class="sheet-count-pill" id="sheetFilterCount">
                <i class="fa fa-list-check"></i>
                <span id="sheetFilterCountText">Memuat data...</span>
            </span>
            @if(isset($sheet))
                <a href="{{ route('sheet.export-pdf', $sheet->id) }}" id="btnSheetExportPdf" target="_blank" class="sheet-pdf-btn" data-base-url="{{ route('sheet.export-pdf', $sheet->id) }}">
                    <i class="fa fa-file-pdf"></i>
                    <span>Cetak PDF</span>
                </a>
            @endif
            <button type="button" class="sheet-reset-btn" id="sheetFilterResetBtn">
                <i class="fa fa-rotate-left"></i>
                <span>Reset</span>
            </button>
        </div>
    </div>

    <div class="sheet-pager-bar" id="sheetTopPager" style="display:none;">
        <span id="sheetTopPagerInfo">Menampilkan 0 baris</span>
        <div class="sheet-pager-controls" id="sheetTopPagerBtns"></div>
    </div>
</div>

<div class="sheet-no-match-banner" id="sheetNoMatchBanner">
    <i class="fa fa-magnifying-glass" style="font-size:20px; color:#94a3b8; display:block; margin-bottom:8px;"></i>
    Tidak ada baris data yang cocok dengan kata kunci atau tahun yang dipilih.
</div>

<script>
(function(){
    function initSheetFilterBar(){
        var searchInput = document.getElementById('sheetTableSearchInput');
        var clearBtn    = document.getElementById('sheetTableSearchClear');
        var yearSelect  = document.getElementById('sheetTableYearFilter');
        var perPageSel  = document.getElementById('sheetTablePerPage');
        var countText   = document.getElementById('sheetFilterCountText');
        var resetBtn    = document.getElementById('sheetFilterResetBtn');
        var pdfBtn      = document.getElementById('btnSheetExportPdf');
        var noMatchEl   = document.getElementById('sheetNoMatchBanner');
        var topPager    = document.getElementById('sheetTopPager');
        var topPagerInfo= document.getElementById('sheetTopPagerInfo');
        var topPagerBtns= document.getElementById('sheetTopPagerBtns');

        if (!searchInput || !yearSelect) return;

        var tables = Array.prototype.slice.call(document.querySelectorAll('.table-box table, .plg2026-table-box table')).filter(function(t){
            return !t.classList.contains('rpb-info-table');
        });

        // Buat panel paginasi bawah di akhir container
        var container = document.querySelector('.container, .plg2026-container');
        var bottomPager = null;
        var bottomPagerInfo = null;
        var bottomPagerBtns = null;
        if (container) {
            bottomPager = document.createElement('div');
            bottomPager.className = 'sheet-pager-bottom';
            bottomPager.style.display = 'none';
            bottomPager.innerHTML = '<span id="sheetBottomPagerInfo"></span><div class="sheet-pager-controls" id="sheetBottomPagerBtns"></div>';
            container.appendChild(bottomPager);
            bottomPagerInfo = bottomPager.querySelector('#sheetBottomPagerInfo');
            bottomPagerBtns = bottomPager.querySelector('#sheetBottomPagerBtns');
        }

        function isStructuralRow(tr){
            return tr.classList.contains('row-section')
                || tr.classList.contains('row-section-major')
                || tr.classList.contains('row-category')
                || tr.classList.contains('row-subcategory')
                || tr.classList.contains('row-total')
                || tr.classList.contains('row-sisa')
                || tr.classList.contains('row-footer');
        }

        function extractYears(text){
            var matches = text.match(/\b(20[1-3][0-9])\b/g);
            if (!matches) return [];
            var uniq = [];
            matches.forEach(function(y){
                if (uniq.indexOf(y) === -1) uniq.push(y);
            });
            return uniq;
        }

        var allDataRows = [];
        var yearSet = {};

        tables.forEach(function(tbl){
            var tbody = tbl.querySelector('tbody');
            if (!tbody) return;
            Array.prototype.forEach.call(tbody.rows, function(tr){
                if (isStructuralRow(tr)) return;
                var txt = (tr.textContent || '').replace(/\s+/g, ' ').trim();
                if (!txt) return;
                var years = extractYears(txt);
                years.forEach(function(y){ yearSet[y] = true; });
                tr.dataset.sfText  = txt.toLowerCase();
                tr.dataset.sfYears = years.join(',');
                allDataRows.push(tr);
            });
        });

        var headerTitle = document.querySelector('.page-header h2, .plg2026-page-header h2');
        if (headerTitle) {
            extractYears(headerTitle.textContent || '').forEach(function(y){
                yearSet[y] = true;
            });
        }

        var sortedYears = Object.keys(yearSet).sort(function(a, b){ return Number(b) - Number(a); });
        sortedYears.forEach(function(y){
            var opt = document.createElement('option');
            opt.value = y;
            opt.textContent = 'Tahun ' + y;
            yearSelect.appendChild(opt);
        });

        var urlParams = new URLSearchParams(window.location.search);
        var initQ = urlParams.get('q') || '';
        var initYear = urlParams.get('tahun') || '';
        if (initQ) searchInput.value = initQ;
        if (initYear && yearSet[initYear]) yearSelect.value = initYear;

        var currentPage = 1;

        function syncPdfUrl(){
            if (!pdfBtn) return;
            var base = pdfBtn.getAttribute('data-base-url');
            var params = new URLSearchParams(window.location.search);
            var qVal = searchInput.value.trim();
            var yVal = yearSelect.value;
            if (qVal) params.set('q', qVal); else params.delete('q');
            if (yVal) params.set('tahun', yVal); else params.delete('tahun');
            var qs = params.toString();
            pdfBtn.href = base + (qs ? '?' + qs : '');
        }

        function renderPagerControls(containerEl, totalPages){
            if (!containerEl) return;
            if (totalPages <= 1) {
                containerEl.innerHTML = '';
                return;
            }
            var html = '<button type="button" class="sheet-pg-btn" ' + (currentPage <= 1 ? 'disabled' : '') + ' data-p="' + (currentPage - 1) + '">&lsaquo;</button>';
            var startP = Math.max(1, currentPage - 2);
            var endP = Math.min(totalPages, currentPage + 2);
            if (startP > 1) {
                html += '<button type="button" class="sheet-pg-btn" data-p="1">1</button>';
                if (startP > 2) html += '<span style="padding:0 4px;">...</span>';
            }
            for (var p = startP; p <= endP; p++) {
                html += '<button type="button" class="sheet-pg-btn ' + (p === currentPage ? 'active' : '') + '" data-p="' + p + '">' + p + '</button>';
            }
            if (endP < totalPages) {
                if (endP < totalPages - 1) html += '<span style="padding:0 4px;">...</span>';
                html += '<button type="button" class="sheet-pg-btn" data-p="' + totalPages + '">' + totalPages + '</button>';
            }
            html += '<button type="button" class="sheet-pg-btn" ' + (currentPage >= totalPages ? 'disabled' : '') + ' data-p="' + (currentPage + 1) + '">&rsaquo;</button>';
            containerEl.innerHTML = html;
            containerEl.querySelectorAll('button[data-p]').forEach(function(btn){
                btn.addEventListener('click', function(){
                    currentPage = parseInt(this.getAttribute('data-p'), 10);
                    applyFilter(false);
                    var card = document.getElementById('sheetFilterBar');
                    if (card) card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                });
            });
        }

        function applyFilter(resetPage){
            if (resetPage) currentPage = 1;
            var q       = searchInput.value.trim().toLowerCase();
            var year    = yearSelect.value;
            var perRaw  = perPageSel ? perPageSel.value : 'all';
            var perPage = perRaw === 'all' ? Number.MAX_SAFE_INTEGER : (parseInt(perRaw, 10) || 25);
            var hasFilter = q !== '' || year !== '';

            if (clearBtn) clearBtn.classList.toggle('show', q !== '');
            if (resetBtn) resetBtn.classList.toggle('show', hasFilter);
            syncPdfUrl();

            var matchedRows = [];
            allDataRows.forEach(function(tr){
                var rowText  = tr.dataset.sfText || '';
                var rowYears = tr.dataset.sfYears || '';
                var matchQ    = !q || rowText.indexOf(q) !== -1;
                var matchYear = !year || rowYears.split(',').indexOf(year) !== -1;
                if (matchQ && matchYear) matchedRows.push(tr);
            });

            var totalMatched = matchedRows.length;
            var totalPages = perRaw === 'all' ? 1 : Math.max(1, Math.ceil(totalMatched / perPage));
            if (currentPage > totalPages) currentPage = totalPages;

            var start = (currentPage - 1) * perPage;
            var end   = Math.min(totalMatched, start + perPage);

            var visibleSet = new Set(perRaw === 'all' ? matchedRows : matchedRows.slice(start, end));
            var isPaginatedSubset = perRaw !== 'all' && totalPages > 1;

            tables.forEach(function(tbl){
                var tbody = tbl.querySelector('tbody');
                if (!tbody) return;
                var tblVisibleData = 0;

                Array.prototype.forEach.call(tbody.rows, function(tr){
                    if (isStructuralRow(tr)) {
                        tr.style.display = (hasFilter || isPaginatedSubset) && (tr.classList.contains('row-total') || tr.classList.contains('row-sisa') || tr.classList.contains('row-footer')) ? 'none' : '';
                        return;
                    }
                    if (visibleSet.has(tr)) {
                        tr.style.display = '';
                        tblVisibleData++;
                    } else {
                        tr.style.display = 'none';
                    }
                });

                if (hasFilter || isPaginatedSubset) {
                    var sectionRows = tbody.querySelectorAll('tr.row-section, tr.row-section-major, tr.row-category, tr.row-subcategory');
                    sectionRows.forEach(function(secRow){
                        var hasVisibleChild = false;
                        var next = secRow.nextElementSibling;
                        while (next) {
                            if (next.classList.contains('row-section') || next.classList.contains('row-section-major') || next.classList.contains('row-category')) {
                                break;
                            }
                            if (!isStructuralRow(next) && next.style.display !== 'none') {
                                hasVisibleChild = true;
                                break;
                            }
                            next = next.nextElementSibling;
                        }
                        secRow.style.display = hasVisibleChild ? '' : 'none';
                    });
                }

                var block = tbl.closest('.sheet-block');
                if (block) {
                    block.style.display = ((hasFilter || isPaginatedSubset) && tblVisibleData === 0) ? 'none' : '';
                }
            });

            var totalAllFormatted     = allDataRows.length.toLocaleString('id-ID');
            var totalMatchedFormatted = totalMatched.toLocaleString('id-ID');

            if (countText) {
                countText.textContent = hasFilter
                    ? ('Cocok ' + totalMatchedFormatted + ' dari ' + totalAllFormatted + ' baris')
                    : ('Total ' + totalAllFormatted + ' baris data');
            }

            var pagerText = totalMatched === 0
                ? 'Menampilkan 0 baris'
                : ('Menampilkan baris ' + (start + 1).toLocaleString('id-ID') + '–' + end.toLocaleString('id-ID') + ' dari ' + totalMatchedFormatted + ' baris');

            var showPager = totalMatched > 0 && (perRaw !== 'all' || totalPages > 1);
            if (topPager) {
                topPager.style.display = showPager ? 'flex' : 'none';
                if (topPagerInfo) topPagerInfo.textContent = pagerText;
                renderPagerControls(topPagerBtns, totalPages);
            }
            if (bottomPager) {
                bottomPager.style.display = (showPager && totalPages > 1) ? 'flex' : 'none';
                if (bottomPagerInfo) bottomPagerInfo.textContent = pagerText;
                renderPagerControls(bottomPagerBtns, totalPages);
            }

            if (noMatchEl) {
                noMatchEl.classList.toggle('show', hasFilter && totalMatched === 0 && allDataRows.length > 0);
            }
        }

        searchInput.addEventListener('input', function(){ applyFilter(true); });
        yearSelect.addEventListener('change', function(){ applyFilter(true); });
        if (perPageSel) {
            perPageSel.addEventListener('change', function(){ applyFilter(true); });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function(){
                searchInput.value = '';
                applyFilter(true);
                searchInput.focus();
            });
        }

        if (resetBtn) {
            resetBtn.addEventListener('click', function(){
                searchInput.value = '';
                yearSelect.value = '';
                if (perPageSel) perPageSel.value = '25';
                applyFilter(true);
            });
        }

        applyFilter(true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSheetFilterBar);
    } else {
        initSheetFilterBar();
    }
})();
</script>
