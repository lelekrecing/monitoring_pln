<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Standar Konstruksi | SIGINJAI PLN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        .error-box ul { margin-left: 20px; margin-top: 5px; }
        .upload-section-title { font-size: 16px; font-weight: 700; margin-top: 4px; margin-bottom: 5px; }
        .upload-section-desc { font-size: 13px; margin-bottom: 14px; }
        .upload-box {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s ease; text-align: center;
        }
        .upload-box .material-icons { font-size: 40px; margin-bottom: 10px; }
        .upload-box input[type="file"] { display: none; }
        .button-group { display: flex; gap: 10px; justify-content: flex-end; margin-top: 16px; flex-wrap: wrap; }
        .upload-progress-wrap { margin-top: 15px; }
        .upload-progress-track { width: 100%; height: 8px; border-radius: 999px; background: #e2e8f0; overflow: hidden; }
        .upload-progress-fill { height: 100%; width: 0%; background: linear-gradient(90deg, #0d2137, #0ea5e9); transition: width .2s ease; }
        .upload-progress-fill.is-processing { background: #f59e0b; }
        .upload-progress-fill.is-error { background: #ef4444; }
        .upload-progress-info { display: flex; justify-content: space-between; font-size: 12.5px; color: #475569; margin-top: 8px; font-weight: 600; }
        .js-upload-progress-result { margin-top: 12px; }
        .upload-cancel-notice { margin-top: 10px; font-size: 13px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; border-radius: 10px; padding: 10px 14px; }

        .section-divider { border: none; border-top: 1px solid #e2e8f0; margin: 28px 0 22px; }
        .data-toolbar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 16px; }
        .data-search { flex: 1; min-width: 220px; max-width: 380px; height: 40px; padding: 0 14px; border-radius: 10px; border: 1px solid #cbd5e1; background: #f8fafc; color: #0f172a; font-size: 13.5px; outline: none; }
        .data-search:focus { background: #fff; border-color: #0d2137; box-shadow: 0 0 0 3px rgba(13,33,55,.08); }
        .data-year { height: 40px; padding: 0 12px; border-radius: 10px; border: 1px solid #cbd5e1; background: #f8fafc; color: #0f172a; font-size: 13.5px; font-weight: 600; outline: none; }
        .total-info { margin-left: auto; font-size: 13px; font-weight: 600; color: #64748b; }
        .table-wrap { overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 12px; }
        .sub-bab-row td { background: #153459 !important; color: #ffffff !important; font-weight: 700 !important; font-size: 13px !important; }
        .link-gambar { color: #0284c7; font-weight: 600; text-decoration: underline; }
        .link-gambar:hover { color: #0d2137; }
        .action-links { display: flex; gap: 8px; justify-content: flex-end; align-items: center; }
        .btn-mini { padding: 6px 11px; border-radius: 8px; font-size: 12px; font-weight: 600; border: 1px solid #cbd5e1; background: #fff; color: #0f172a; text-decoration: none; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; }
        .btn-mini:hover { background: #f1f5f9; }
        .btn-mini.danger { color: #dc2626; border-color: #fecaca; background: #fef2f2; }
        .btn-mini.danger:hover { background: #fee2e2; }
    </style>
</head>

<body>

@includeIf('partials.simerah-admin-topbar', ['topSubTitle' => 'UP3 JAMBI • STANDAR KONSTRUKSI'])

<div class="wrapper">

    @includeIf('partials.simerah-admin-sidebar', ['activeAdmin' => 'standar'])

    <!-- =====================================================
         CONTENT
    ===================================================== -->
    <main class="content">

        <div class="card">

            <!-- =================================================
                 HEADER
            ================================================== -->
            <div class="header-title">
                <div>
                    <h2>
                        Upload Standar Konstruksi
                    </h2>
                    <small>
                        Upload file Excel Standar Konstruksi SUTM 2026
                    </small>
                </div>
                <a href="{{ route('admin.standar-konstruksi.create') }}" class="btn-secondary">
                    <i class="fa fa-plus"></i> Tambah Manual
                </a>
            </div>

            <!-- =================================================
                 ALERT SUCCESS
            ================================================== -->
            @if(session('success'))
                <div class="success-box">
                    {{ session('success') }}
                </div>
            @endif

            <!-- =================================================
                 ALERT ERROR
            ================================================== -->
            @if(session('error'))
                <div class="error-box">
                    {{ session('error') }}
                </div>
            @endif

            <!-- =================================================
                 VALIDATION ERROR
            ================================================== -->
            @if($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- =================================================
                 UPLOAD STANDAR KONSTRUKSI
            ================================================== -->
            <p class="upload-section-title">
                Upload Standar Konstruksi
            </p>

            <p class="upload-section-desc">
                File Excel Standar Konstruksi SUTM 2026
                (kolom: No, Kontruksi SUTM 2026, Link Gambar).
            </p>

            <form
                action="{{ route('admin.standar-konstruksi.import') }}"
                method="POST"
                enctype="multipart/form-data"
                class="js-upload-form"
                data-jenis="Standar Konstruksi"
            >
                @csrf

                <label class="upload-box">
                    <span class="material-icons">
                        upload_file
                    </span>

                    <p class="js-file-name">
                        Klik untuk memilih file Excel (.xlsx)
                        Standar Konstruksi
                    </p>

                    <input
                        type="file"
                        class="js-file-input"
                        name="file_excel"
                        accept=".xls,.xlsx"
                        required
                    >
                </label>

                <!-- PROGRESS -->
                <div
                    class="upload-progress-wrap js-upload-progress-wrap"
                    style="display:none;"
                >
                    <div class="upload-progress-track">
                        <div
                            class="upload-progress-fill js-upload-progress-fill"
                            style="width:0%;"
                        ></div>
                    </div>

                    <div class="upload-progress-info">
                        <span class="upload-progress-status js-upload-progress-status">
                            Menyiapkan file...
                        </span>

                        <span class="upload-progress-percent js-upload-progress-percent">
                            0%
                        </span>
                    </div>

                    <div class="js-upload-progress-result"></div>
                </div>

                <!-- BUTTON -->
                <div class="button-group">
                    <button
                        type="reset"
                        class="btn-cancel"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn-save"
                    >
                        Upload &amp; Import Standar Konstruksi
                    </button>
                </div>

                <div
                    class="upload-cancel-notice js-upload-cancel-notice"
                    style="display:none;"
                ></div>

            </form>

            @if(isset($konstruksi))
                <hr class="section-divider">

                <div class="data-toolbar">
                    <input type="text" id="adminStandarSearch" class="data-search" placeholder="Cari konstruksi, sub-bab, atau nomor..." autocomplete="off">
                    <select id="adminStandarYear" class="data-year" aria-label="Filter Tahun">
                        <option value="">Semua Tahun</option>
                    </select>
                    <select id="adminStandarPerPage" class="data-year" aria-label="Tampil Data">
                        <option value="10">Tampil: 10</option>
                        <option value="25" selected>Tampil: 25</option>
                        <option value="50">Tampil: 50</option>
                        <option value="100">Tampil: 100</option>
                        <option value="all">Tampil: Semua</option>
                    </select>
                    <span class="total-info" id="adminStandarCount">Total: {{ $konstruksi->count() }} item</span>
                    <a href="{{ route('admin.standar-konstruksi.export-pdf') }}" id="btnAdminStandarPdf" target="_blank" class="btn-mini" style="background:#b91c1c;color:#fff;border-color:#b91c1c;height:40px;padding:0 14px;border-radius:10px;font-size:13px;">
                        <i class="fa fa-file-pdf"></i> Cetak PDF
                    </a>
                </div>

                <div class="table-wrap">
                    <table id="adminStandarTable">
                        <thead>
                            <tr>
                                <th style="width:70px; text-align:center;">No</th>
                                <th>Konstruksi SUTM</th>
                                <th>Link Gambar</th>
                                <th style="text-align:right; width:160px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $subBabTampil = null; @endphp
                            @forelse($konstruksi as $item)
                                @if($item->sub_bab_nama && $item->sub_bab_nama !== $subBabTampil)
                                    @php $subBabTampil = $item->sub_bab_nama; @endphp
                                    <tr class="sub-bab-row">
                                        <td colspan="4">
                                            {{ $item->sub_bab_kode ? $item->sub_bab_kode . ' - ' . $item->sub_bab_nama : $item->sub_bab_nama }}
                                        </td>
                                    </tr>
                                @endif
                                <tr class="standar-item-row">
                                    <td style="text-align:center;">{{ $item->no }}</td>
                                    <td>{{ $item->nama_konstruksi }}</td>
                                    <td>
                                        @if($item->link_gambar)
                                            <a href="{{ $item->link_gambar }}" target="_blank" rel="noopener noreferrer" class="link-gambar">Lihat Gambar</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-links">
                                            <a href="{{ route('admin.standar-konstruksi.edit', $item->id) }}" class="btn-mini">
                                                <i class="fa fa-pen"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ route('admin.standar-konstruksi.destroy', $item->id) }}" onsubmit="return confirm('Hapus item konstruksi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-mini danger">
                                                    <i class="fa fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="empty">Belum ada data Standar Konstruksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;margin-top:14px;font-size:12.5px;color:#475569;" id="adminStandarPager">
                    <span id="adminStandarPagerInfo">Menampilkan 0 item</span>
                    <div style="display:flex;gap:5px;flex-wrap:wrap;" id="adminStandarPagerBtns"></div>
                </div>
            @endif

        </div>

    </main>

</div>

<!-- =========================================================
     JAVASCRIPT
     PROGRESS UPLOAD & FILTER TABEL
========================================================= -->
<script>
document
    .querySelectorAll('.js-file-input')
    .forEach(function(input) {
        input.addEventListener(
            'change',
            function() {
                const label =
                    this
                    .closest('.upload-box')
                    .querySelector('.js-file-name');

                if (this.files.length > 0) {
                    label.textContent =
                        this.files[0].name;
                }
            }
        );
    });

/* =========================================================
   PROGRESS BAR UNTUK SEMUA FORM
========================================================= */
document
    .querySelectorAll('.js-upload-form')
    .forEach(function(form) {
        const wrap =
            form.querySelector(
                '.js-upload-progress-wrap'
            );

        const fill =
            form.querySelector(
                '.js-upload-progress-fill'
            );

        const status =
            form.querySelector(
                '.js-upload-progress-status'
            );

        const percent =
            form.querySelector(
                '.js-upload-progress-percent'
            );

        const result =
            form.querySelector(
                '.js-upload-progress-result'
            );

        const fileInp =
            form.querySelector(
                '.js-file-input'
            );

        const btnSave =
            form.querySelector(
                '.btn-save'
            );

        const fileLabel =
            form.querySelector(
                '.js-file-name'
            );

        const cancelNotice =
            form.querySelector(
                '.js-upload-cancel-notice'
            );

        if (
            !wrap ||
            !fill ||
            !status ||
            !percent ||
            !result
        ) {
            return;
        }

        const labelDefaultText =
            fileLabel
                ? fileLabel.textContent
                : '';

        let xhrAktif = null;
        let displayedPct = 0;
        let animId = null;

        function setProgress(pct, teks) {
            fill.style.width = pct + '%';
            percent.textContent = pct + '%';
            if (teks) {
                status.textContent = teks;
            }
        }

        function animateProgressTo(targetPct, teks) {
            if (animId) {
                cancelAnimationFrame(animId);
            }

            const mulaiPct = displayedPct;
            const selisih = targetPct - mulaiPct;

            if (selisih <= 0) {
                displayedPct = targetPct;
                setProgress(targetPct, teks);
                return;
            }

            const waktuMulai = performance.now();
            const durasi = Math.min(1000, Math.max(300, selisih * 15));

            function langkah(sekarang) {
                const t = Math.min(1, (sekarang - waktuMulai) / durasi);
                const nilaiSekarang = Math.round(mulaiPct + selisih * t);

                if (nilaiSekarang !== displayedPct) {
                    displayedPct = nilaiSekarang;
                    setProgress(nilaiSekarang, teks);
                }

                if (t < 1) {
                    animId = requestAnimationFrame(langkah);
                } else {
                    animId = null;
                }
            }

            animId = requestAnimationFrame(langkah);
        }

        function tampilkanGagal(pesan) {
            if (animId) {
                cancelAnimationFrame(animId);
            }

            fill.classList.remove('is-processing');
            fill.classList.add('is-error');
            status.textContent = '❌ Upload/import gagal.';
            result.innerHTML = '<div class="error-box">' + pesan + '</div>';

            if (btnSave) {
                btnSave.disabled = false;
            }

            xhrAktif = null;
        }

        form.addEventListener('submit', function(e) {
            if (typeof FormData === 'undefined' || typeof XMLHttpRequest === 'undefined') {
                return;
            }

            e.preventDefault();

            result.innerHTML = '';
            wrap.style.display = '';
            fill.classList.remove('is-processing', 'is-error');

            if (btnSave) {
                btnSave.disabled = true;
            }

            if (cancelNotice) {
                cancelNotice.style.display = 'none';
                cancelNotice.innerHTML = '';
            }

            if (animId) {
                cancelAnimationFrame(animId);
            }

            displayedPct = 0;
            setProgress(0, 'Menyiapkan file...');

            const formData = new FormData(form);
            const xhr = new XMLHttpRequest();
            xhrAktif = xhr;

            xhr.open('POST', form.getAttribute('action'), true);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.upload.addEventListener('progress', function(ev) {
                if (!ev.lengthComputable) return;
                const pctUpload = Math.round((ev.loaded / ev.total) * 90);
                animateProgressTo(pctUpload, 'Sedang mengupload file...');
            });

            xhr.upload.addEventListener('load', function() {
                animateProgressTo(90, 'Sedang membaca & mengimpor data Excel...');
                fill.classList.add('is-processing');
            });

            xhr.onload = function() {
                if (xhrAktif !== xhr) return;
                fill.classList.remove('is-processing');

                let data = null;
                try {
                    data = JSON.parse(xhr.responseText);
                } catch(err) {
                    data = null;
                }

                if (xhr.status >= 200 && xhr.status < 300 && data && data.success) {
                    animateProgressTo(100, 'Upload dan import selesai.');
                    result.innerHTML = '<div class="success-box">✅ ' + data.message + '</div>';

                    if (btnSave) {
                        btnSave.disabled = false;
                    }
                    if (fileInp) {
                        fileInp.value = '';
                    }
                    xhrAktif = null;
                    return;
                }

                let pesan = 'Terjadi kesalahan saat upload/import.';
                if (data) {
                    if (data.message) pesan = data.message;
                    if (data.errors) {
                        const semuaPesan = Object.values(data.errors).flat();
                        if (semuaPesan.length) pesan = semuaPesan.join(' ');
                    }
                }
                tampilkanGagal(pesan);
            };

            xhr.onerror = function() {
                if (xhrAktif !== xhr) return;
                tampilkanGagal('Tidak dapat terhubung ke server. Periksa koneksi lalu coba lagi.');
            };

            xhr.send(formData);
        });

        form.addEventListener('reset', function() {
            if (xhrAktif) {
                try { xhrAktif.abort(); } catch(err) {}
                xhrAktif = null;
            }
            if (animId) cancelAnimationFrame(animId);
            displayedPct = 0;
            wrap.style.display = 'none';
            fill.classList.remove('is-processing', 'is-error');
            fill.style.width = '0%';
            percent.textContent = '0%';
            status.textContent = 'Menyiapkan file...';
            result.innerHTML = '';
            if (fileLabel) fileLabel.textContent = labelDefaultText;
            if (btnSave) btnSave.disabled = false;
            if (cancelNotice) {
                cancelNotice.style.display = 'none';
                cancelNotice.innerHTML = '';
            }
        });
    });

/* =========================================================
   FILTER PENCARIAN, TAHUN, TAMPIL 10/25/50/100 & PDF STANDAR KONSTRUKSI
========================================================= */
(function(){
    var tbl = document.getElementById('adminStandarTable');
    var searchInp = document.getElementById('adminStandarSearch');
    var yearSel = document.getElementById('adminStandarYear');
    var perPageSel = document.getElementById('adminStandarPerPage');
    var countEl = document.getElementById('adminStandarCount');
    var pagerInfo = document.getElementById('adminStandarPagerInfo');
    var pagerBtns = document.getElementById('adminStandarPagerBtns');
    var pdfBtn = document.getElementById('btnAdminStandarPdf');
    var pdfBaseUrl = {{ json_encode(route('admin.standar-konstruksi.export-pdf')) }};
    if (!tbl || !searchInp || !yearSel) return;

    var rows = Array.prototype.slice.call(tbl.querySelectorAll('tbody tr.standar-item-row'));
    var subBabRows = Array.prototype.slice.call(tbl.querySelectorAll('tbody tr.sub-bab-row'));
    var years = {};
    rows.forEach(function(r){
        var txt = (r.textContent || '').replace(/\s+/g, ' ').trim();
        r.dataset.search = txt.toLowerCase();
        var m = txt.match(/\b(20[1-3][0-9])\b/g) || [];
        r.dataset.years = m.join(',');
        m.forEach(function(y){ years[y] = true; });
    });

    Object.keys(years).sort(function(a,b){ return Number(b) - Number(a); }).forEach(function(y){
        var opt = document.createElement('option');
        opt.value = y;
        opt.textContent = 'Tahun ' + y;
        yearSel.appendChild(opt);
    });

    var currentPage = 1;

    function renderPager(totalPages){
        if (!pagerBtns) return;
        if (totalPages <= 1) {
            pagerBtns.innerHTML = '';
            return;
        }
        var html = '<button type="button" class="btn-mini" ' + (currentPage <= 1 ? 'disabled' : '') + ' data-p="' + (currentPage - 1) + '">&lsaquo;</button>';
        var s = Math.max(1, currentPage - 2);
        var e = Math.min(totalPages, currentPage + 2);
        if (s > 1) html += '<button type="button" class="btn-mini" data-p="1">1</button>' + (s > 2 ? '<span>...</span>' : '');
        for (var p = s; p <= e; p++) {
            var style = p === currentPage ? 'background:#0d2137;color:#fff;border-color:#0d2137;' : '';
            html += '<button type="button" class="btn-mini" style="' + style + '" data-p="' + p + '">' + p + '</button>';
        }
        if (e < totalPages) html += (e < totalPages - 1 ? '<span>...</span>' : '') + '<button type="button" class="btn-mini" data-p="' + totalPages + '">' + totalPages + '</button>';
        html += '<button type="button" class="btn-mini" ' + (currentPage >= totalPages ? 'disabled' : '') + ' data-p="' + (currentPage + 1) + '">&rsaquo;</button>';
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
        var q = searchInp.value.trim().toLowerCase();
        var y = yearSel.value;
        var perRaw = perPageSel ? perPageSel.value : '25';
        var perPage = perRaw === 'all' ? Number.MAX_SAFE_INTEGER : (parseInt(perRaw, 10) || 25);

        if (pdfBtn) {
            var params = new URLSearchParams();
            if (searchInp.value.trim()) params.set('q', searchInp.value.trim());
            if (y) params.set('tahun', y);
            pdfBtn.href = pdfBaseUrl + (params.toString() ? '?' + params.toString() : '');
        }

        var matched = rows.filter(function(r){
            var okQ = !q || (r.dataset.search || '').indexOf(q) !== -1;
            var okY = !y || (r.dataset.years || '').split(',').indexOf(y) !== -1;
            return okQ && okY;
        });

        var total = matched.length;
        var totalPages = perRaw === 'all' ? 1 : Math.max(1, Math.ceil(total / perPage));
        if (currentPage > totalPages) currentPage = totalPages;

        var start = (currentPage - 1) * perPage;
        var end = Math.min(total, start + perPage);
        var pageSet = new Set(perRaw === 'all' ? matched : matched.slice(start, end));

        rows.forEach(function(r){
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

        if (countEl) countEl.textContent = 'Total: ' + total + ' item';
        if (pagerInfo) {
            pagerInfo.textContent = total === 0
                ? 'Menampilkan 0 dari ' + rows.length + ' item'
                : 'Menampilkan ' + (start + 1) + '–' + end + ' dari ' + total + ' item';
        }
        renderPager(totalPages);
    }

    searchInp.addEventListener('input', function(){ applyFilter(true); });
    yearSel.addEventListener('change', function(){ applyFilter(true); });
    if (perPageSel) perPageSel.addEventListener('change', function(){ applyFilter(true); });
    applyFilter(true);
})();
</script>

@include('partials.upload-progress-widget')

</body>

</html>
