<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Upload Monitoring Excel | SIGINJAI PLN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        .error-box ul { margin-left: 20px; margin-top: 5px; }
        .upload-section-title { font-size: 16px; font-weight: 700; margin-top: 26px; margin-bottom: 5px; }
        .upload-section-title:first-of-type { margin-top: 0; }
        .upload-section-desc { font-size: 13px; margin-bottom: 14px; }
        .upload-divider { border: none; margin: 28px 0; }
        .upload-box {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s ease; text-align: center;
        }
        .upload-box .material-icons { font-size: 40px; margin-bottom: 10px; }
        .upload-box input[type="file"] { display: none; }
        .button-group { display: flex; gap: 10px; justify-content: flex-end; margin-top: 15px; }
        .is-processing { background-color: #f59e0b !important; border-color: #f59e0b !important; cursor: not-allowed !important; opacity: 0.92; }
        .is-error { background-color: #ef4444 !important; border-color: #ef4444 !important; }
        .form-locked { opacity: 0.55; pointer-events: none; }
        .spinner-inline {
            display: inline-block; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid #fff; border-radius: 50%; animation: spin 1s linear infinite;
            margin-right: 8px; vertical-align: middle;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .upload-progress-wrap { display: none; margin-top: 15px; }
        .upload-progress-wrap.active { display: block; }
        .upload-progress-track { width: 100%; height: 8px; border-radius: 999px; background: #e2e8f0; overflow: hidden; }
        .upload-progress-bar { height: 100%; width: 0%; background: linear-gradient(90deg, #0d2137, #0ea5e9); transition: width .25s ease; }
        .upload-progress-bar.is-error { background: #ef4444; }
        .upload-progress-text { font-size: 13px; color: #475569; margin-top: 8px; font-weight: 500; }
    </style>
</head>
<body>

@includeIf('partials.simerah-admin-topbar', ['topSubTitle' => 'UP3 JAMBI • UPLOAD MONITORING'])

<div class="wrapper">
    @includeIf('partials.simerah-admin-sidebar', ['activeAdmin' => 'upload'])

    <!-- CONTENT -->
    <main class="content">
        <div class="card">

            <div class="header-title">
                <h2>Upload Monitoring Excel</h2>
                <small>Upload file Excel sesuai jenis monitoring (KHS, Tiang, atau Pelanggan)</small>
            </div>

            <!-- MESSAGES -->
            @if(session('success'))
                <div class="success-box">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="error-box">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 1. UPLOAD MONITORING KHS -->
            <p class="upload-section-title">1. Upload Monitoring KHS</p>
            <p class="upload-section-desc">File Excel Monitoring KHS (KHS Jasa, KHS Cover, Reservasi, Rekap, dsb).</p>
            <form action="{{ route('input-data.store') }}" method="POST" enctype="multipart/form-data" class="js-upload-form" id="formKHS">
                @csrf
                <label class="upload-box js-upload-box">
                    <span class="material-icons">upload_file</span>
                    <p class="js-file-name">Klik untuk memilih file Excel (.xlsx) Monitoring KHS</p>
                    <input type="file" class="js-file-input" name="file_excel" accept=".xls,.xlsx" required>
                </label>

                <div class="upload-progress-wrap js-progress-wrap">
                    <div class="upload-progress-track">
                        <div class="upload-progress-bar js-progress-bar"></div>
                    </div>
                    <div class="upload-progress-text js-progress-text">Menunggu...</div>
                </div>

                <div class="button-group">
                    <button type="button" class="btn-cancel js-btn-cancel">Batal</button>
                    <button type="submit" class="btn-save">Upload &amp; Import KHS</button>
                </div>
            </form>

            <hr class="upload-divider">

            <!-- 2. UPLOAD MONITORING TIANG -->
            <p class="upload-section-title">2. Upload Monitoring Tiang</p>
            <p class="upload-section-desc">File Excel Monitoring Tiang (Total Vendor, Rekap KR, RPB per SPB, TA per SPB, Maxima per SPB, WIKA per SPB, dsb).</p>
            <form action="{{ route('input-data.store.tiang') }}" method="POST" enctype="multipart/form-data" class="js-upload-form" id="formTiang">
                @csrf
                <label class="upload-box js-upload-box">
                    <span class="material-icons">upload_file</span>
                    <p class="js-file-name">Klik untuk memilih file Excel (.xlsx) Monitoring Tiang</p>
                    <input type="file" class="js-file-input" name="file_excel" accept=".xls,.xlsx" required>
                </label>

                <div class="upload-progress-wrap js-progress-wrap">
                    <div class="upload-progress-track">
                        <div class="upload-progress-bar js-progress-bar"></div>
                    </div>
                    <div class="upload-progress-text js-progress-text">Menunggu...</div>
                </div>

                <div class="button-group">
                    <button type="button" class="btn-cancel js-btn-cancel">Batal</button>
                    <button type="submit" class="btn-save">Upload &amp; Import Tiang</button>
                </div>
            </form>

            <hr class="upload-divider">

            <!-- 3. UPLOAD MONITORING PELANGGAN -->
            <p class="upload-section-title">3. Upload Monitoring Pelanggan</p>
            <p class="upload-section-desc">Data Pelanggan 2026.</p>
            <form action="{{ route('input-data.store.pelanggan') }}" method="POST" enctype="multipart/form-data" class="js-upload-form" id="formPelanggan">
                @csrf
                <label class="upload-box js-upload-box">
                    <span class="material-icons">upload_file</span>
                    <p class="js-file-name">Klik untuk memilih file Excel (.xlsx) Monitoring Pelanggan</p>
                    <input type="file" class="js-file-input" name="file_excel" accept=".xls,.xlsx" required>
                </label>

                <div class="upload-progress-wrap js-progress-wrap">
                    <div class="upload-progress-track">
                        <div class="upload-progress-bar js-progress-bar"></div>
                    </div>
                    <div class="upload-progress-text js-progress-text">Menunggu...</div>
                </div>

                <div class="button-group">
                    <button type="button" class="btn-cancel js-btn-cancel">Batal</button>
                    <button type="submit" class="btn-save">Upload &amp; Import Pelanggan</button>
                </div>
            </form>

        </div>
    </main>
</div>

<!-- =======================================================
     SCRIPT (LOGIKA UPLOAD TIDAK DIUBAH)
======================================================== -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const PROGRESS_URL_BASE = "{{ url('/input-data/progress') }}";
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const POLL_INTERVAL_MS = 1500;

    document.querySelectorAll('.js-upload-form').forEach(function (form) {
        const fileInput     = form.querySelector('.js-file-input');
        const fileLabel     = form.querySelector('.js-file-name');
        const btnSave       = form.querySelector('.btn-save');
        const btnCancel     = form.querySelector('.js-btn-cancel');
        const uploadBox     = form.querySelector('.js-upload-box');
        const progressWrap  = form.querySelector('.js-progress-wrap');
        const progressBar   = form.querySelector('.js-progress-bar');
        const progressText  = form.querySelector('.js-progress-text');

        const defaultText = "Klik untuk memilih file Excel (.xlsx)";
        let isSubmitting = false;

        function lockForm() {
            btnSave.disabled = true;
            btnSave.classList.remove('is-error');
            btnSave.classList.add('is-processing');
            btnSave.innerHTML = "<span class='spinner-inline'></span> Sedang Mengunggah &amp; Mengekstrak...";
            btnCancel.disabled = true;
            btnCancel.style.opacity = '0.5';
            uploadBox.classList.add('form-locked');
            progressWrap.classList.add('active');
            progressBar.classList.remove('is-error');
            progressBar.style.width = '0%';
            progressText.textContent = 'Mengunggah file...';
        }

        function unlockForm(success) {
            btnSave.disabled = false;
            btnSave.classList.remove('is-processing');
            btnSave.innerHTML = form.querySelector('input[type=file]').closest('form').dataset.originalLabel || btnSave.dataset.originalLabel;
            btnCancel.disabled = false;
            btnCancel.style.opacity = '1';
            uploadBox.classList.remove('form-locked');
            isSubmitting = false;
            if (success) {
                progressWrap.classList.remove('active');
            }
        }

        function setProgress(percent, text, isError) {
            progressBar.style.width = Math.max(0, Math.min(100, percent)) + '%';
            progressBar.classList.toggle('is-error', !!isError);
            progressText.textContent = text;
        }

        function showFatalError(message) {
            btnSave.classList.remove('is-processing');
            btnSave.classList.add('is-error');
            btnSave.innerHTML = 'Gagal, Coba Lagi';
            setProgress(100, message, true);
            btnCancel.disabled = false;
            btnCancel.style.opacity = '1';
            uploadBox.classList.remove('form-locked');
            isSubmitting = false;

            Swal.fire({
                title: 'Upload Gagal',
                text: message,
                icon: 'error',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#0d2137',
                background: '#ffffff',
                color: '#0f172a'
            });
        }

        btnSave.dataset.originalLabel = btnSave.innerHTML;

        fileInput.addEventListener('change', function () {
            fileLabel.textContent = this.files.length > 0 ? this.files[0].name : defaultText;
        });

        btnCancel.addEventListener('click', function (e) {
            e.preventDefault();
            if (fileInput.files.length === 0) return;

            Swal.fire({
                title: 'Batalkan Pilihan File?',
                text: "File yang sudah dipilih akan dihapus dari antrean.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Kembali',
                background: '#ffffff',
                color: '#0f172a'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.reset();
                    fileLabel.textContent = defaultText;
                    progressWrap.classList.remove('active');
                }
            });
        });

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            if (isSubmitting) return;
            if (fileInput.files.length === 0) return;

            isSubmitting = true;
            lockForm();

            let pollTimer = null;

            try {
                const formData = new FormData(form);

                const initResponse = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const initContentType = initResponse.headers.get('content-type') || '';
                if (!initContentType.includes('application/json')) {
                    throw new Error('Server tidak membalas JSON saat mengunggah file (kemungkinan error 500). Periksa log Laravel.');
                }

                const initData = await initResponse.json();

                if (!initResponse.ok || !initData.success) {
                    throw new Error(initData.message || 'Upload gagal.');
                }

                const uploadId = initData.upload_id;
                const jenisMonitoring = initData.jenis_monitoring;
                const totalSheetsAwal = initData.jumlah_sheet || 1;

                setProgress(5, initData.message || `Import ${totalSheetsAwal} sheet dimulai di background...`);

                pollTimer = setInterval(async function () {
                    try {
                        const progressResponse = await fetch(`${PROGRESS_URL_BASE}/${uploadId}`, {
                            headers: { 'Accept': 'application/json' }
                        });

                        if (!progressResponse.ok) return;

                        const p = await progressResponse.json();

                        if (p.status === 'not_found') {
                            clearInterval(pollTimer);
                            throw new Error('Data upload tidak ditemukan saat memantau progres.');
                        }

                        const total = p.jumlah_sheet || totalSheetsAwal;
                        const percent = 5 + Math.round(((p.sheet_selesai || 0) / total) * 90);

                        if (p.status === 'gagal') {
                            clearInterval(pollTimer);
                            throw new Error(p.pesan_error || 'Import gagal diproses di background.');
                        }

                        if (p.status === 'aktif') {
                            clearInterval(pollTimer);
                            setProgress(100, 'Semua sheet berhasil diproses!');
                            unlockForm(true);

                            await Swal.fire({
                                title: 'Upload Berhasil',
                                text: `Data ${(p.jenis_monitoring || jenisMonitoring || '').toUpperCase()} berhasil diimport (${total} sheet).`,
                                icon: 'success',
                                timer: 2200,
                                showConfirmButton: false,
                                background: '#ffffff',
                                color: '#0f172a'
                            });

                            window.location.reload();
                            return;
                        }

                        setProgress(
                            percent,
                            p.sheet_sedang_diproses
                                ? `Memproses sheet "${p.sheet_sedang_diproses}" (${p.sheet_selesai}/${total})...`
                                : `Menunggu giliran diproses di antrean (0/${total})...`
                        );

                    } catch (pollErr) {
                        clearInterval(pollTimer);
                        showFatalError(pollErr.message || 'Terjadi kesalahan saat memantau progres.');
                    }
                }, POLL_INTERVAL_MS);

            } catch (err) {
                if (pollTimer) clearInterval(pollTimer);
                showFatalError(err.message || 'Terjadi kesalahan tak terduga saat upload.');
            }
        });
    });
});
</script>

@includeIf('partials.upload-progress-widget')
</body>
</html>