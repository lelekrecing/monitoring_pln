<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Upload Monitoring Excel</title>

    @vite('resources/css/input-data.css')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* =====================================================
           TEMA DARK MODE & RESET UI (SINKRONISASI STANDAR)
        ===================================================== */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #0f172a; color: #e2e8f0; display: flex; min-height: 100vh; overflow-x: hidden; }

        /* SIDEBAR FIXED & PRESISI */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: 250px; min-width: 250px; height: 100vh;
            background-color: #0f172a;
            border-right: 1px solid rgba(255,255,255,.08);
            display: flex; flex-direction: column; overflow-y: auto; z-index: 100;
        }
        .logo { padding: 20px; text-align: center; margin-bottom: 10px; }
        .sidebar-logo-pln { max-width: 70px; width: 100%; height: auto; object-fit: contain; margin: 0 auto; display: block; }

        /* RESET PADDING UL & LI AGAR HOVER FULL WIDTH */
        .sidebar ul { list-style: none; padding: 0; margin: 0; flex: 0 0 auto; }
        .sidebar ul li { padding: 0; margin: 0; }
        .sidebar-link {
            display: block; width: 100%; padding: 15px 25px;
            color: #94a3b8; text-decoration: none; font-size: 15px;
            font-weight: 500; transition: .3s ease; border-left: 3px solid transparent;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: linear-gradient(90deg, rgba(14, 165, 233, 0.15) 0%, rgba(14, 165, 233, 0.05) 100%);
            color: #38bdf8; border-left-color: #0ea5e9;
        }
        .sidebar-link span { display: block; }

        .logout-menu { margin-top: auto; border-top: 1px solid rgba(255,255,255,.08); }
        .logout-menu a { color: #ef4444; }
        .logout-menu a:hover { background: rgba(239, 68, 68, 0.1); border-left-color: #ef4444; }

        /* LAYOUT UTAMA */
        .wrapper { display: flex; width: 100%; }
        .content { flex: 1; padding: 40px; margin-left: 250px; }
        .card { background-color: #1e293b; border-radius: 12px; padding: 30px; border: 1px solid rgba(255,255,255,.08); }

        /* HEADER DASHBOARD */
        .header-title { margin-bottom: 25px; border-bottom: 1px solid rgba(255,255,255,.08); padding-bottom: 20px; }
        .header-title h2 { color: #f8fafc; font-size: 24px; font-weight: 600; margin-bottom: 5px; }
        .header-title small { color: #94a3b8; font-size: 14px; display: block; }

        /* ALERT MESSAGES */
        .success-box { background-color: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2); color: #4ade80; padding: 15px; margin-bottom: 20px; border-radius: 8px; font-size: 14px; }
        .error-box { background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #f87171; padding: 15px; margin-bottom: 20px; border-radius: 8px; font-size: 14px; }
        .error-box ul { margin-left: 20px; margin-top: 5px; }

        /* UPLOAD SECTION UI */
        .upload-section-title { font-size: 16px; font-weight: 600; color: #f8fafc; margin-top: 30px; margin-bottom: 5px; }
        .upload-section-title:first-of-type { margin-top: 0; }
        .upload-section-desc { font-size: 13px; color: #94a3b8; margin-bottom: 15px; }
        .upload-divider { border: none; border-top: 1px solid rgba(255,255,255,.08); margin: 30px 0; }

        .upload-box {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 40px 20px; background-color: #0f172a; border: 2px dashed rgba(255,255,255,.15);
            border-radius: 8px; cursor: pointer; transition: all 0.3s ease; text-align: center;
        }
        .upload-box:hover { border-color: #0ea5e9; background-color: rgba(14, 165, 233, 0.05); }
        .upload-box .material-icons { font-size: 40px; color: #94a3b8; margin-bottom: 10px; transition: color 0.3s ease; }
        .upload-box:hover .material-icons { color: #38bdf8; }
        .upload-box p { color: #cbd5e1; font-size: 14px; font-weight: 500; }
        .upload-box input[type="file"] { display: none; }

        /* BUTTONS */
        .button-group { display: flex; gap: 10px; justify-content: flex-end; margin-top: 15px; }
        .btn-save { background-color: #0ea5e9; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; font-size: 14px; }
        .btn-save:hover { background-color: #0284c7; }
        .btn-cancel { background-color: transparent; color: #cbd5e1; border: 1px solid #475569; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 14px; transition: 0.2s; }
        .btn-cancel:hover { background-color: rgba(255,255,255,0.05); color: white; }

        /* UI/UX: Indikator Proses Lokal */
        .is-processing { background-color: #f59e0b !important; cursor: not-allowed !important; opacity: 0.9; }
        .is-error { background-color: #ef4444 !important; }
        .form-locked { opacity: 0.5; pointer-events: none; }
        .spinner-inline {
            display: inline-block; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid #fff; border-radius: 50%; animation: spin 1s linear infinite;
            margin-right: 8px; vertical-align: middle;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* UI/UX: Progress Bar per sheet */
        .upload-progress-wrap { display: none; margin-top: 15px; }
        .upload-progress-wrap.active { display: block; }
        .upload-progress-track { width: 100%; height: 8px; border-radius: 999px; background: rgba(255,255,255,0.08); overflow: hidden; }
        .upload-progress-bar { height: 100%; width: 0%; background: linear-gradient(90deg, #0ea5e9, #38bdf8); transition: width .25s ease; }
        .upload-progress-bar.is-error { background: #ef4444; }
        .upload-progress-text { font-size: 13px; color: #94a3b8; margin-top: 8px; font-weight: 500; }

        /* RESPONSIF */
        @media (max-width: 768px) {
            .sidebar { position: static; width: 100%; min-width: 0; height: auto; }
            body { flex-direction: column; }
            .content { margin-left: 0; padding: 20px; }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <!-- SIDEBAR ADMIN -->
    <aside class="sidebar">
        <div class="logo">
            <img src="{{ asset('images/Logo_PLN.png') }}" alt="Logo PLN" class="sidebar-logo-pln">
        </div>
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="sidebar-link"><span>Dashboard</span></a></li>
            <li class="active"><a href="{{ url('/input-data') }}" class="sidebar-link active"><span>Upload Monitoring</span></a></li>
            <li><a href="{{ url('/riwayat-upload') }}" class="sidebar-link"><span>Riwayat Upload</span></a></li>
            <li><a href="{{ route('admin.standar-konstruksi.index') }}" class="sidebar-link"><span>Standar Konstruksi</span></a></li>
            @if (Route::has('admin.anggota.index'))
                <li><a href="{{ route('admin.anggota.index') }}" class="sidebar-link"><span>Manajemen Akun</span></a></li>
            @endif
        </ul>
        <div class="logout-menu">
            <a href="{{ route('logout.admin') }}" class="sidebar-link">
                <span style="display:flex; align-items:center; gap:8px;">
                    <i class="material-icons" style="font-size: 18px;">logout</i> Logout
                </span>
            </a>
        </div>
    </aside>

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
                background: '#1e293b',
                color: '#fff'
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
                cancelButtonColor: '#334155',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Kembali',
                background: '#1e293b',
                color: '#fff'
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
                                background: '#1e293b',
                                color: '#fff'
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