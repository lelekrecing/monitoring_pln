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
        margin-bottom:22px;
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
        gap:10px;
    }

    .sheet-table-footer-right{
        display:flex;
        align-items:center;
        flex-wrap:wrap;
        gap:6px;
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
@endphp

@if(isset($sheet))
    @php
        $pdfBaseUrl = route('sheet.export-pdf', $sheet->id);
        $initialHref = $pdfBaseUrl . ($isBlockFooter ? ('?block=' . $blockIndex . ($blockYear !== '' ? '&tahun=' . urlencode($blockYear) : '')) : '');
        $btnTitle = $blockYear !== ''
            ? ('Cetak PDF (' . $blockYear . ')')
            : ('Cetak PDF');
    @endphp
    <div class="sheet-table-footer js-sheet-table-footer"
         data-sheet-id="{{ $sheet->id }}"
         data-block-index="{{ $isBlockFooter ? $blockIndex : '' }}"
         data-block-year="{{ $blockYear }}">
        <div class="sheet-table-footer-left">
            <a href="{{ $initialHref }}"
               target="_blank"
               class="stf-pdf-btn js-table-pdf-btn"
               data-base-url="{{ $pdfBaseUrl }}"
               data-block-index="{{ $isBlockFooter ? $blockIndex : '' }}"
               data-block-year="{{ $blockYear }}">
                <i class="fa fa-file-pdf"></i>
                <span>{{ $btnTitle }}</span>
            </a>
            @if($isBlockFooter && $blockLabel !== '')
                <span class="stf-badge">
                    <i class="fa fa-calendar-check"></i>
                    {{ $blockLabel }}
                </span>
            @endif
        </div>
        <div class="sheet-table-footer-right">
            <div class="sheet-pager-controls js-table-pager-btns"></div>
        </div>
    </div>
@endif
