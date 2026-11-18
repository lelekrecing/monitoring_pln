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
        gap:12px;
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

    .sheet-year-wrap{
        position:relative;
        display:flex;
        align-items:center;
        min-width:155px;
    }

    .sheet-year-wrap i.sy-icon{
        position:absolute;
        left:12px;
        color:#64748b;
        font-size:13px;
        pointer-events:none;
    }

    .sheet-year-wrap select{
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

    .sheet-year-wrap select:focus{
        background:#ffffff;
        border-color:#0d2137;
        box-shadow:0 0 0 3px rgba(13, 33, 55, 0.09);
    }

    .sheet-perpage-wrap,
    .tbl-entries-bar{
        display:inline-flex;
        align-items:center;
        gap:8px;
        font-size:13.5px;
        font-weight:600;
        color:#334155;
        font-family:'Inter', 'Segoe UI', Arial, sans-serif;
    }

    .sheet-perpage-wrap select,
    .tbl-entries-bar select{
        height:38px;
        min-width:74px;
        background:#ffffff;
        border:1px solid #cbd5e1;
        border-radius:8px;
        padding:0 10px;
        font-size:13.5px;
        font-weight:600;
        color:#0f172a;
        cursor:pointer;
        outline:none;
        transition:all .18s ease;
    }

    .sheet-perpage-wrap select:focus,
    .tbl-entries-bar select:focus{
        border-color:#0d2137;
        box-shadow:0 0 0 3px rgba(13, 33, 55, 0.09);
    }

    .tbl-entries-wrap{
        display:flex;
        align-items:center;
        justify-content:space-between;
        flex-wrap:wrap;
        gap:10px;
        padding:10px 14px;
        background:#ffffff;
        border:1px solid #cbd5e1;
        border-bottom:none;
        border-radius:12px 12px 0 0;
    }

    .tbl-entries-wrap + .table-box,
    .tbl-entries-wrap + .plg2026-table-box{
        border-top-left-radius:0 !important;
        border-top-right-radius:0 !important;
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
        .simerah-topbar, .simerah-sidebar, .sheet-filter-card, .tbl-entries-wrap, .header-actions {
            display:none !important;
        }
        .simerah-main { margin-left:0 !important; width:100% !important; }
    }
</style>

<div class="sheet-filter-card" id="sheetFilterBar">
    <div class="sheet-filter-top">
        <div class="sheet-filter-controls">
            <div class="sheet-perpage-wrap">
                <span>Show</span>
                <select id="sheetTablePerPage" aria-label="Show entries">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
            </div>

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
        </div>

        <div class="sheet-filter-meta">
            <button type="button" class="sheet-reset-btn" id="sheetFilterResetBtn">
                <i class="fa fa-rotate-left"></i>
                <span>Reset</span>
            </button>
        </div>
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
        var resetBtn    = document.getElementById('sheetFilterResetBtn');
        var noMatchEl   = document.getElementById('sheetNoMatchBanner');

        if (!searchInput || !yearSelect) return;

        var rawTables = Array.prototype.slice.call(document.querySelectorAll('.table-box table, .plg2026-table-box table')).filter(function(t){
            return !t.classList.contains('rpb-info-table');
        });

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

        var yearSet = {};
        var tableStates = [];

        rawTables.forEach(function(tbl, tblIdx){
            var tbody = tbl.querySelector('tbody');
            if (!tbody) return;

            var tableBox = tbl.closest('.table-box, .plg2026-table-box');
            var footer = null;
            if (tableBox && tableBox.nextElementSibling && tableBox.nextElementSibling.classList.contains('js-sheet-table-footer')) {
                footer = tableBox.nextElementSibling;
            }

            var blockYears = [];
            var yearSec = tbl.closest('.sheet-year-section');
            if (yearSec) {
                var bannerEl = yearSec.querySelector('.sheet-year-banner');
                if (bannerEl) {
                    extractYears(bannerEl.textContent || '').forEach(function(y){
                        if (blockYears.indexOf(y) === -1) blockYears.push(y);
                        yearSet[y] = true;
                    });
                }
            }

            var block = tbl.closest('.sheet-block');
            if (block) {
                var infoEls = block.querySelectorAll('.sheet-info, .sheet-section-title, .sheet-block-title, .sheet-info-box');
                infoEls.forEach(function(el){
                    extractYears(el.textContent || '').forEach(function(y){
                        if (blockYears.indexOf(y) === -1) blockYears.push(y);
                        yearSet[y] = true;
                    });
                });
                var prev = block.previousElementSibling;
                if (prev && (prev.classList.contains('sheet-section-title') || prev.classList.contains('sheet-block'))) {
                    var prevTitle = prev.querySelector('.sheet-section-title');
                    var titleSrc = prev.classList.contains('sheet-section-title') ? prev : prevTitle;
                    if (titleSrc && !prev.querySelector('table')) {
                        extractYears(titleSrc.textContent || '').forEach(function(y){
                            if (blockYears.indexOf(y) === -1) blockYears.push(y);
                            yearSet[y] = true;
                        });
                    }
                }
            }

            var dataRows = [];
            Array.prototype.forEach.call(tbody.rows, function(tr){
                if (isStructuralRow(tr)) return;
                var txt = (tr.textContent || '').replace(/\s+/g, ' ').trim();
                if (!txt) return;
                var years = extractYears(txt);
                blockYears.forEach(function(by){
                    if (years.indexOf(by) === -1) years.push(by);
                });
                years.forEach(function(y){ yearSet[y] = true; });
                tr.dataset.sfText  = txt.toLowerCase();
                tr.dataset.sfYears = years.join(',');
                dataRows.push(tr);
            });

            var state = {
                index: tblIdx,
                table: tbl,
                tbody: tbody,
                tableBox: tableBox,
                footer: footer,
                block: block,
                yearSection: yearSec,
                dataRows: dataRows,
                page: 1,
                perPage: 10,
                localPerPageSelect: null
            };

            // Jika ada lebih dari 1 tabel di halaman (mis. KHS JASA 2026 / 2025 / 2024 atau blok multi-tahun),
            // tampilkan juga kontrol Show [10/25/50/100] entries di atas masing-masing tabel.
            if (rawTables.length > 1 && tableBox && tableBox.parentNode) {
                var wrap = document.createElement('div');
                wrap.className = 'tbl-entries-wrap';
                wrap.innerHTML = '<div class="tbl-entries-bar"><span>Show</span>'
                    + '<select class="js-local-perpage" aria-label="Show entries">'
                    + '<option value="10" selected>10</option>'
                    + '<option value="25">25</option>'
                    + '<option value="50">50</option>'
                    + '<option value="100">100</option>'
                    + '</select><span>entries</span></div>';
                tableBox.parentNode.insertBefore(wrap, tableBox);
                var localSel = wrap.querySelector('.js-local-perpage');
                state.localPerPageSelect = localSel;
                state.entriesWrap = wrap;
                localSel.addEventListener('change', function(){
                    state.perPage = parseInt(this.value, 10) || 10;
                    state.page = 1;
                    renderTableState(state);
                });
            }

            tableStates.push(state);
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

        function renderPagerControls(containerEl, state, totalPages){
            if (!containerEl) return;
            if (totalPages <= 1) {
                containerEl.innerHTML = '';
                return;
            }
            var currentPage = state.page;
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
                    state.page = parseInt(this.getAttribute('data-p'), 10) || 1;
                    renderTableState(state);
                    if (state.tableBox) {
                        state.tableBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                });
            });
        }

        function syncTablePdfUrl(state, visibleRows){
            if (!state.footer) return;
            var pdfBtn = state.footer.querySelector('.js-table-pdf-btn');
            if (!pdfBtn) return;

            var base = pdfBtn.getAttribute('data-base-url');
            if (!base) return;

            var params = new URLSearchParams(window.location.search);
            var bIdx = pdfBtn.getAttribute('data-block-index');
            var bYear = pdfBtn.getAttribute('data-block-year') || '';
            var qVal = searchInput.value.trim();
            var yVal = yearSelect.value || bYear;

            if (bIdx !== null && bIdx !== '') {
                params.set('block', bIdx);
            } else {
                params.delete('block');
            }
            if (qVal) params.set('q', qVal); else params.delete('q');
            if (yVal) params.set('tahun', yVal); else params.delete('tahun');

            params.set('per_page', String(state.perPage));
            params.set('page', String(state.page));

            var rowNums = [];
            visibleRows.forEach(function(tr){
                var rn = tr.getAttribute('data-row');
                if (rn) rowNums.push(rn);
            });
            if (rowNums.length > 0) {
                params.set('rows', rowNums.join(','));
            } else {
                params.delete('rows');
            }

            var qs = params.toString();
            pdfBtn.href = base + (qs ? '?' + qs : '');
        }

        function renderTableState(state){
            var q = searchInput.value.trim().toLowerCase();
            var year = yearSelect.value;
            var hasFilter = q !== '' || year !== '';
            var perPage = state.perPage || 10;

            var matchedRows = state.dataRows.filter(function(tr){
                var rowText  = tr.dataset.sfText || '';
                var rowYears = tr.dataset.sfYears || '';
                var matchQ    = !q || rowText.indexOf(q) !== -1;
                var matchYear = !year || rowYears.split(',').indexOf(year) !== -1;
                return matchQ && matchYear;
            });

            var totalMatched = matchedRows.length;
            var totalPages = Math.max(1, Math.ceil(totalMatched / perPage));
            if (state.page > totalPages) state.page = totalPages;

            var start = (state.page - 1) * perPage;
            var end   = Math.min(totalMatched, start + perPage);
            var visibleSlice = matchedRows.slice(start, end);
            var visibleSet = new Set(visibleSlice);
            var isPaginatedSubset = totalMatched > visibleSlice.length;

            Array.prototype.forEach.call(state.tbody.rows, function(tr){
                if (isStructuralRow(tr)) {
                    tr.style.display = (hasFilter || isPaginatedSubset) && (tr.classList.contains('row-total') || tr.classList.contains('row-sisa') || tr.classList.contains('row-footer')) ? 'none' : '';
                    return;
                }
                tr.style.display = visibleSet.has(tr) ? '' : 'none';
            });

            if (hasFilter || isPaginatedSubset) {
                var sectionRows = state.tbody.querySelectorAll('tr.row-section, tr.row-section-major, tr.row-category, tr.row-subcategory');
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

            if (state.block) {
                state.block.style.display = (hasFilter && totalMatched === 0) ? 'none' : '';
            }
            if (state.yearSection) {
                state.yearSection.style.display = (hasFilter && totalMatched === 0) ? 'none' : '';
            }

            if (state.footer) {
                var pagerContainer = state.footer.querySelector('.js-table-pager-btns');
                renderPagerControls(pagerContainer, state, totalPages);
            }

            syncTablePdfUrl(state, visibleSlice);
            return totalMatched;
        }

        function applyAllTables(resetPage){
            var q = searchInput.value.trim();
            var year = yearSelect.value;
            var hasFilter = q !== '' || year !== '';
            var globalPerPage = perPageSel ? (parseInt(perPageSel.value, 10) || 10) : 10;

            if (clearBtn) clearBtn.classList.toggle('show', q !== '');
            if (resetBtn) resetBtn.classList.toggle('show', hasFilter);

            var grandTotalMatched = 0;
            var grandTotalAll = 0;

            tableStates.forEach(function(state){
                if (resetPage) {
                    state.page = 1;
                }
                grandTotalAll += state.dataRows.length;
                grandTotalMatched += renderTableState(state);
            });

            if (noMatchEl) {
                noMatchEl.classList.toggle('show', hasFilter && grandTotalMatched === 0 && grandTotalAll > 0);
            }
        }

        searchInput.addEventListener('input', function(){ applyAllTables(true); });
        yearSelect.addEventListener('change', function(){ applyAllTables(true); });
        if (perPageSel) {
            perPageSel.addEventListener('change', function(){
                var nextPerPage = parseInt(this.value, 10) || 10;
                tableStates.forEach(function(state){
                    state.perPage = nextPerPage;
                    state.page = 1;
                    if (state.localPerPageSelect) {
                        state.localPerPageSelect.value = String(nextPerPage);
                    }
                });
                applyAllTables(true);
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function(){
                searchInput.value = '';
                applyAllTables(true);
                searchInput.focus();
            });
        }

        if (resetBtn) {
            resetBtn.addEventListener('click', function(){
                searchInput.value = '';
                yearSelect.value = '';
                if (perPageSel) perPageSel.value = '10';
                tableStates.forEach(function(state){
                    state.perPage = 10;
                    state.page = 1;
                    if (state.localPerPageSelect) {
                        state.localPerPageSelect.value = '10';
                    }
                });
                applyAllTables(true);
            });
        }

        applyAllTables(true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSheetFilterBar);
    } else {
        initSheetFilterBar();
    }
})();
</script>
