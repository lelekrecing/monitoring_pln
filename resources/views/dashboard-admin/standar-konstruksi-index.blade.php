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
</script>

@include('partials.upload-progress-widget')

</body>

</html>
