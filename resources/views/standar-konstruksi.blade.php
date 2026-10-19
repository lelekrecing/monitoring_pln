<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Standar Konstruksi SUTM 2026</title>

    <style>

        /* =====================================================
           RESET
        ===================================================== */

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Calibri, Arial, sans-serif;
        }


        /* =====================================================
           BODY
        ===================================================== */

        body{
            background:#f5f7fb;
        }


        /* =====================================================
           CONTAINER
        ===================================================== */

        .container{
            width:98%;
            margin:20px auto 40px;
        }


        /* =====================================================
           HEADER
        ===================================================== */

        .page-header{
            background:#0B5EA8;

            color:white;

            padding:18px 25px;

            border-radius:10px;

            margin-bottom:16px;

            display:flex;

            justify-content:space-between;

            align-items:center;

            gap:15px;

            flex-wrap:wrap;
        }


        .page-header h2{
            font-size:26px;

            line-height:1.3;
        }


        .page-header p{
            font-size:14px;

            opacity:.9;

            margin-top:3px;
        }


        /* =====================================================
           HEADER ACTIONS
        ===================================================== */

        .header-actions{
            display:flex;

            align-items:center;

            gap:10px;
        }


        /* =====================================================
           HEADER BUTTON
        ===================================================== */

        .header-btn{

            display:inline-flex;

            align-items:center;

            justify-content:center;

            height:38px;

            padding:0 16px;

            background:#ffffff;

            color:#0B5EA8;

            border-radius:8px;

            text-decoration:none;

            font-size:13px;

            font-weight:600;

            white-space:nowrap;

            border:none;

            cursor:pointer;

            transition:
                background .2s ease,
                transform .2s ease;
        }


        .header-btn:hover{

            background:#f1f5f9;

            color:#0B5EA8;

            transform:translateY(-1px);
        }


        /* =====================================================
           DASHBOARD BUTTON
        ===================================================== */

        .header-btn.dashboard{

            padding-left:16px;

            padding-right:16px;
        }


        /* =====================================================
           SEARCH BOX
        ===================================================== */

        .search-box{

            background:white;

            padding:14px;

            border-radius:10px;

            margin-bottom:16px;

            border:1px solid #dfe5ec;
        }


        .search-box input{

            width:100%;

            padding:12px 15px;

            border:1px solid #ccd5df;

            border-radius:8px;

            font-size:14px;

            outline:none;
        }


        .search-box input:focus{

            border-color:#0B5EA8;

            box-shadow:
                0 0 0 3px
                rgba(11,94,168,.1);
        }


        /* =====================================================
           TABLE BOX
        ===================================================== */

        .table-box{

            background:#fff;

            border-radius:12px;

            overflow-x:auto;

            box-shadow:
                0 4px 15px
                rgba(0,0,0,.12);

            border:1px solid #000;
        }


        /* =====================================================
           TABLE
        ===================================================== */

        table{

            border-collapse:collapse;

            width:100%;

            min-width:850px;
        }


        /* =====================================================
           TABLE HEADER
        ===================================================== */

        thead th{

            background:#00B0F0;

            color:#fff;

            padding:10px;

            border:1px solid #000;

            text-align:center;

            font-weight:bold;

            font-size:13px;

            vertical-align:middle;
        }


        /* =====================================================
           TABLE BODY
        ===================================================== */

        tbody td{

            padding:10px;

            border:1px solid #000;

            font-size:13px;

            vertical-align:middle;
        }


        tbody tr:nth-child(even){

            background:#f5f9fc;
        }


        tbody tr:hover{

            background:#eef7fc;
        }


        /* =====================================================
           BARIS JUDUL SUB-BAB (mis. "A - SUTM")
        ===================================================== */

        tr.sub-bab-row td.sub-bab-title{

            background:#0B5EA8;

            color:#ffffff;

            font-weight:700;

            font-size:13px;

            letter-spacing:.3px;

            padding:10px 14px;

            border:1px solid #000;
        }


        tr.sub-bab-row:hover{

            background:transparent;
        }


        /* =====================================================
           COLUMN NO
        ===================================================== */

        .col-no{

            width:70px;

            text-align:center;
        }


        /* =====================================================
           COLUMN NAMA
        ===================================================== */

        .col-nama{

            min-width:350px;
        }


        /* =====================================================
           COLUMN LINK
        ===================================================== */

        .col-link{

            min-width:400px;
        }


        /* =====================================================
           LINK GAMBAR
        ===================================================== */

        .link-gambar{

            color:#0563C1;

            text-decoration:underline;

            cursor:pointer;

            font-weight:600;
        }


        .link-gambar:hover{

            color:#003f7f;
        }


        /* =====================================================
           EMPTY DATA
        ===================================================== */

        .empty{

            text-align:center;

            padding:30px;

            color:#777;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media(max-width:700px){

            .container{

                width:96%;

                margin-top:12px;
            }


            .page-header{

                flex-direction:column;

                align-items:flex-start;

                padding:16px 18px;
            }


            .page-header h2{

                font-size:22px;
            }


            .page-header p{

                font-size:13px;
            }


            .header-actions{

                width:100%;

                justify-content:flex-end;
            }


            .header-btn{

                height:38px;

                padding:0 14px;

                font-size:12px;
            }

        }

    </style>

</head>


<body>

<div class="container">


    {{-- =====================================================
         HEADER
    ===================================================== --}}

    <div class="page-header">


        {{-- JUDUL --}}

        <div>

            <h2>
                Standar Konstruksi SUTM 2026
            </h2>

            <p>
                Dokumentasi Standar Konstruksi Jaringan Tegangan Menengah
            </p>

        </div>


        {{-- =================================================
             TOMBOL HEADER
        ================================================== --}}

        <div class="header-actions">


            {{-- KEMBALI --}}

            <a
                href="{{ url()->previous() }}"
                class="header-btn"
                id="btnBackHistory"
            >
                ← Kembali
            </a>


            {{-- DASHBOARD USER --}}

            <a
                href="{{ route('dashboard') }}"
                class="header-btn dashboard"
            >
                 Dashboard
            </a>


        </div>

    </div>


    {{-- =====================================================
         SEARCH
    ===================================================== --}}

    <div class="search-box">

        <input
            type="text"
            id="searchKonstruksi"
            placeholder="Cari nama konstruksi..."
            autocomplete="off"
        >

    </div>


    {{-- =====================================================
         TABLE
    ===================================================== --}}

    <div class="table-box">

        <table id="tabelKonstruksi">


            {{-- TABLE HEADER --}}

            <thead>

                <tr>

                    <th class="col-no">
                        NO
                    </th>

                    <th class="col-nama">
                        KONTRUKSI SUTM 2026
                    </th>

                    <th class="col-link">
                        LINK GAMBAR
                    </th>

                </tr>

            </thead>


            {{-- TABLE BODY --}}

            <tbody>

                @php
                    $subBabTampilSaatIni = null;
                @endphp

                @forelse($konstruksi as $item)


                    {{-- JUDUL SUB-BAB (mis. "A - SUTM", "B - NO MDU"),
                         hanya ditampilkan sekali setiap sub-bab berganti,
                         mengikuti urutan asli dari file Excel. --}}

                    @if($item->sub_bab_nama && $item->sub_bab_nama !== $subBabTampilSaatIni)

                        @php
                            $subBabTampilSaatIni = $item->sub_bab_nama;
                        @endphp

                        <tr class="sub-bab-row">

                            <td colspan="3" class="sub-bab-title">

                                {{ $item->sub_bab_kode
                                    ? $item->sub_bab_kode . ' - ' . $item->sub_bab_nama
                                    : $item->sub_bab_nama }}

                            </td>

                        </tr>

                    @endif


                    <tr>


                        {{-- NO --}}

                        <td class="col-no">

                            {{ $item->no }}

                        </td>


                        {{-- NAMA KONSTRUKSI --}}

                        <td class="col-nama">

                            {{ $item->nama_konstruksi }}

                        </td>


                        {{-- LINK GAMBAR --}}

                        <td class="col-link">


                            @if($item->link_gambar)

                                <a
                                    href="{{ $item->link_gambar }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="link-gambar"
                                >
                                    Lihat Gambar
                                </a>

                            @else

                                -

                            @endif


                        </td>


                    </tr>


                @empty


                    <tr>

                        <td
                            colspan="3"
                            class="empty"
                        >
                            Belum ada data Standar Konstruksi.
                        </td>

                    </tr>


                @endforelse

            </tbody>

        </table>

    </div>

</div>


{{-- =========================================================
     JAVASCRIPT SEARCH
========================================================= --}}

<script>


/* =========================================================
   SEARCH ELEMENT
========================================================= */

const searchInput =
    document.getElementById(
        'searchKonstruksi'
    );


const rows =
    document.querySelectorAll(
        '#tabelKonstruksi tbody tr'
    );


/* =========================================================
   SEARCH DATA
========================================================= */

searchInput.addEventListener(
    'input',
    function(){

        const keyword =
            this.value
                .toLowerCase()
                .trim();


        rows.forEach(
            function(row){

                if (row.classList.contains('sub-bab-row')) {
                    return;
                }

                const nama =
                    row.children[1]
                        ? row.children[1]
                            .textContent
                            .toLowerCase()
                        : '';


                row.style.display =
                    nama.includes(keyword)
                        ? ''
                        : 'none';

            }
        );

    }
);


/* =========================================================
   BUTTON KEMBALI
========================================================= */

const btnBack =
    document.getElementById(
        'btnBackHistory'
    );


if(btnBack){

    btnBack.addEventListener(
        'click',
        function(event){

            /*
             * Jika ada halaman sebelumnya,
             * kembali menggunakan browser history.
             */

            if(
                window.history.length > 1 &&
                document.referrer
            ){

                event.preventDefault();

                window.history.back();

            }

        }
    );

}


</script>


</body>

</html>