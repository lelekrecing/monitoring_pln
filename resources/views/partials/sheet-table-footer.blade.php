@once
<style>
    .sheet-table-footer{
        display:flex;
        align-items:center;
        justify-content:space-between;
        flex-wrap:wrap;
        gap:10px 16px;
        padding:10px 16px;
        margin-top:-2px;
        margin-bottom:18px;
        background:#e8edf3;
        border:1px solid #cbd5e1;
        border-top:2px solid #cbd5e1;
        border-radius:0 0 10px 10px;
        font-family:'Inter', 'Segoe UI', Arial, sans-serif;
    }

    .sheet-table-footer-left{
        display:flex;
        align-items:center;
        flex-wrap:wrap;
        gap:8px;
    }

    .sheet-table-footer-right{
        display:flex;
        align-items:center;
        flex-wrap:wrap;
        gap:12px;
        font-size:12.5px;
        font-weight:700;
        color:#1e293b;
    }

    .stf-label{
        font-size:12px;
        font-weight:600;
        color:#475569;
        display:inline-flex;
        align-items:center;
        gap:5px;
    }

    .stf-year-select{
        height:34px;
        padding:0 12px;
        border-radius:6px;
        border:1px solid #cbd5e1;
        background:#ffffff;
        color:#0d2137;
        font-size:12.5px;
        font-weight:600;
        cursor:pointer;
        outline:none;
        min-width:165px;
    }

    .stf-year-select:focus{
        border-color:#0d2137;
    }

    .stf-pdf-btn{
        display:inline-flex;
        align-items:center;
        gap:7px;
        height:34px;
        padding:0 14px;
        border-radius:6px;
        background:#dc2626;
        color:#ffffff !important;
        text-decoration:none;
        font-size:12.5px;
        font-weight:700;
        border:none;
        cursor:pointer;
        box-shadow:0 1px 2px rgba(220,38,38,.22);
        transition:background .15s ease, transform .12s ease;
    }

    .stf-pdf-btn:hover{
        background:#b91c1c;
    }

    .stf-badge{
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:4px 10px;
        border-radius:999px;
        background:#ffffff;
        border:1px solid #cbd5e1;
        color:#0d2137;
        font-size:12px;
        font-weight:700;
    }

    .stf-meta{
        color:#334155;
        font-size:12.5px;
        font-weight:600;
    }

    @media print {
        .sheet-table-footer { display:none !important; }
    }

    @media (max-width: 640px) {
        .sheet-table-footer {
            flex-direction:column;
            align-items:flex-start;
        }
    }
</style>
@endonce

@php
    $isBlockFooter = $isBlockFooter ?? false;
    $blockIndex    = $blockIndex ?? 0;
    $blockLabel    = trim((string) ($blockLabel ?? ''));
    $blockYear     = trim((string) ($blockYear ?? ''));
    $rowCount      = (int) ($rowCount ?? 0);
    $siblingList   = is_array($siblingYearSheets ?? null) ? $siblingYearSheets : [];
    $hasSiblings   = count($siblingList) > 1;
@endphp

@if(isset($sheet))
    @if($isBlockFooter)
        @php
            $blockPdfHref = route('sheet.export-pdf', $sheet->id) . '?block=' . $blockIndex . ($blockYear !== '' ? '&tahun=' . urlencode($blockYear) : '');
        @endphp
        <div class="sheet-table-footer sheet-table-footer-block" data-block-index="{{ $blockIndex }}">
            <div class="sheet-table-footer-left">
                <a href="{{ $blockPdfHref }}"
                   target="_blank"
                   class="stf-pdf-btn js-block-pdf-btn"
                   data-base-url="{{ route('sheet.export-pdf', $sheet->id) }}"
                   data-block-index="{{ $blockIndex }}"
                   data-block-year="{{ $blockYear }}">
                    <i class="fa fa-file-pdf"></i>
                    <span>Cetak PDF{{ $blockYear !== '' ? ' (' . $blockYear . ')' : '' }}</span>
                </a>
                @if($blockLabel !== '')
                    <span class="stf-badge">
                        <i class="fa fa-calendar-check"></i>
                        {{ $blockLabel }}
                    </span>
                @endif
            </div>
            <div class="sheet-table-footer-right">
                <span class="stf-meta">Total Data : {{ number_format($rowCount, 0, ',', '.') }} baris</span>
            </div>
        </div>
    @else
        <div class="sheet-table-footer sheet-table-footer-single" data-has-siblings="{{ $hasSiblings ? '1' : '0' }}">
            <div class="sheet-table-footer-left">
                <span class="stf-label">
                    <i class="fa fa-calendar-days"></i> Tahun PDF:
                </span>
                <select class="stf-year-select js-footer-pdf-year" aria-label="Pilih Tahun untuk Cetak PDF">
                    @if($hasSiblings)
                        @foreach($siblingList as $sib)
                            <option value="{{ route('sheet.export-pdf', $sib['id']) }}"
                                    data-year="{{ $sib['year'] ?? '' }}"
                                    data-is-sibling="1"
                                    @if(!empty($sib['is_current'])) selected @endif>
                                {{ $sib['nama_sheet'] }}
                            </option>
                        @endforeach
                    @else
                        <option value="{{ route('sheet.export-pdf', $sheet->id) }}" data-year="" selected>
                            Semua Tahun ({{ $sheet->nama_sheet }})
                        </option>
                    @endif
                </select>
                <a href="{{ route('sheet.export-pdf', $sheet->id) }}"
                   target="_blank"
                   class="stf-pdf-btn js-footer-pdf-btn"
                   data-default-base="{{ route('sheet.export-pdf', $sheet->id) }}">
                    <i class="fa fa-file-pdf"></i>
                    <span>Cetak PDF</span>
                </a>
            </div>
            <div class="sheet-table-footer-right">
                <span class="stf-meta js-footer-row-count">
                    {{ $rowCount > 0 ? 'Total Data : ' . number_format($rowCount, 0, ',', '.') . ' baris' : $sheet->nama_sheet }}
                </span>
            </div>
        </div>
    @endif
@endif

@once
<script>
(function(){
    function updateAllFooterPdfLinks(){
        var searchInput = document.getElementById('sheetTableSearchInput');
        var qVal = searchInput ? searchInput.value.trim() : '';

        // 1. Update blok multi-tabel PDF links agar ikut membawa kata pencarian aktif
        document.querySelectorAll('.js-block-pdf-btn').forEach(function(btn){
            var base = btn.getAttribute('data-base-url');
            if (!base) return;
            var params = new URLSearchParams(window.location.search);
            params.set('block', btn.getAttribute('data-block-index') || '0');
            var bYear = btn.getAttribute('data-block-year') || '';
            if (bYear) params.set('tahun', bYear);
            if (qVal) params.set('q', qVal); else params.delete('q');
            btn.href = base + '?' + params.toString();
        });

        // 2. Update single-table footer PDF link sesuai dropdown tahun + filter pencarian
        document.querySelectorAll('.sheet-table-footer-single').forEach(function(footer){
            var sel = footer.querySelector('.js-footer-pdf-year');
            var btn = footer.querySelector('.js-footer-pdf-btn');
            if (!sel || !btn) return;
            var opt = sel.options[sel.selectedIndex];
            var base = (opt && opt.value) ? opt.value : (btn.getAttribute('data-default-base') || '');
            var yearVal = opt ? (opt.getAttribute('data-year') || '') : '';
            var isSibling = opt && opt.getAttribute('data-is-sibling') === '1';

            var params = new URLSearchParams();
            if (!isSibling && yearVal) {
                params.set('tahun', yearVal);
            }
            if (qVal) {
                params.set('q', qVal);
            }
            var qs = params.toString();
            btn.href = base + (qs ? '?' + qs : '');
        });
    }

    function syncFooterYearsFromFilterBar(){
        var topYearSel = document.getElementById('sheetTableYearFilter');
        if (!topYearSel) return;

        document.querySelectorAll('.sheet-table-footer-single').forEach(function(footer){
            var hasSiblings = footer.getAttribute('data-has-siblings') === '1';
            var sel = footer.querySelector('.js-footer-pdf-year');
            if (!sel) return;

            if (!hasSiblings) {
                // Salin tahun terdeteksi dari #sheetTableYearFilter jika belum ada di footer select
                var existingYears = {};
                Array.prototype.forEach.call(sel.options, function(o){
                    var y = o.getAttribute('data-year') || '';
                    if (y) existingYears[y] = true;
                });
                var base = (footer.querySelector('.js-footer-pdf-btn') || {}).getAttribute('data-default-base') || '';
                Array.prototype.forEach.call(topYearSel.options, function(o){
                    if (o.value && !existingYears[o.value]) {
                        var opt = document.createElement('option');
                        opt.value = base;
                        opt.setAttribute('data-year', o.value);
                        opt.textContent = o.textContent;
                        sel.appendChild(opt);
                        existingYears[o.value] = true;
                    }
                });

                if (topYearSel.value) {
                    Array.prototype.forEach.call(sel.options, function(o, idx){
                        if ((o.getAttribute('data-year') || '') === topYearSel.value) {
                            sel.selectedIndex = idx;
                        }
                    });
                }
            }
        });

        updateAllFooterPdfLinks();
    }

    function bindFooterEvents(){
        syncFooterYearsFromFilterBar();

        var searchInput = document.getElementById('sheetTableSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', updateAllFooterPdfLinks);
        }

        var topYearSel = document.getElementById('sheetTableYearFilter');
        if (topYearSel) {
            topYearSel.addEventListener('change', function(){
                document.querySelectorAll('.sheet-table-footer-single[data-has-siblings="0"] .js-footer-pdf-year').forEach(function(sel){
                    Array.prototype.forEach.call(sel.options, function(o, idx){
                        if ((o.getAttribute('data-year') || '') === topYearSel.value) {
                            sel.selectedIndex = idx;
                        }
                    });
                });
                updateAllFooterPdfLinks();
            });
        }

        document.querySelectorAll('.js-footer-pdf-year').forEach(function(sel){
            sel.addEventListener('change', function(){
                var footer = sel.closest('.sheet-table-footer-single');
                var hasSiblings = footer && footer.getAttribute('data-has-siblings') === '1';
                var opt = sel.options[sel.selectedIndex];
                var yearVal = opt ? (opt.getAttribute('data-year') || '') : '';
                if (!hasSiblings && topYearSel) {
                    var foundTop = Array.prototype.some.call(topYearSel.options, function(o){ return o.value === yearVal; });
                    if (foundTop || yearVal === '') {
                        topYearSel.value = yearVal;
                        topYearSel.dispatchEvent(new Event('change'));
                    }
                }
                updateAllFooterPdfLinks();
            });
        });
    }

    document.addEventListener('sheetFilterReady', function(e){
        syncFooterYearsFromFilterBar();
        if (e && e.detail && typeof e.detail.totalMatched === 'number') {
            document.querySelectorAll('.sheet-table-footer-single .js-footer-row-count').forEach(function(el){
                el.textContent = 'Total Data : ' + e.detail.totalMatched + ' baris';
            });
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindFooterEvents);
    } else {
        bindFooterEvents();
    }
})();
</script>
@endonce
