{{--
    Widget progres upload global.

    CARA PAKAI:
    Sisipkan baris berikut sebelum </body> di layout utama Anda (mis.
    resources/views/layouts/app.blade.php), atau kalau belum ada layout terpadu,
    tempel di setiap file blade utama (dashboard.blade.php, dashboard-admin.blade.php,
    standar-konstruksi/index.blade.php, input-data.blade.php, dst):

        @include('partials.upload-progress-widget')

    Simpan file ini di: resources/views/partials/upload-progress-widget.blade.php

    Widget ini TIDAK butuh apapun dikirim dari controller -- ia murni JS yang polling
    endpoint GET /input-data/progress/{upload_id} memakai upload_id yang tersimpan di
    localStorage browser. Karena polling ke database (bukan memori tab), status tetap
    akurat walau Anda pindah halaman, refresh, atau buka tab baru.
--}}
@if (Auth::check() || session()->has('admin'))
<div id="upload-progress-widget" class="upload-progress-widget" style="display:none;">
    <div class="upload-progress-widget__header">
        <span class="upload-progress-widget__title" id="upw-title">Mengimpor data...</span>
        <button type="button" id="upw-dismiss" class="upload-progress-widget__dismiss" aria-label="Tutup">&times;</button>
    </div>
    <div class="upload-progress-widget__bar-wrap">
        <div class="upload-progress-widget__bar" id="upw-bar" style="width:0%"></div>
    </div>
    <div class="upload-progress-widget__detail" id="upw-detail">Menyiapkan...</div>
</div>

<style>
    .upload-progress-widget {
        position: fixed;
        right: 20px;
        bottom: 20px;
        width: 320px;
        max-width: calc(100vw - 40px);
        background: #1e293b;
        color: #fff;
        border-radius: 10px;
        padding: 14px 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
        z-index: 9999;
        font-size: 13px;
        font-family: inherit;
    }
    .upload-progress-widget__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .upload-progress-widget__title { font-weight: 600; }
    .upload-progress-widget__dismiss {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 18px;
        cursor: pointer;
        line-height: 1;
        padding: 0 4px;
    }
    .upload-progress-widget__dismiss:hover { color: #fff; }
    .upload-progress-widget__bar-wrap {
        background: #334155;
        border-radius: 6px;
        height: 8px;
        overflow: hidden;
        margin-bottom: 6px;
    }
    .upload-progress-widget__bar {
        background: #3b82f6;
        height: 100%;
        width: 0%;
        transition: width .3s ease;
    }
    .upload-progress-widget__bar.is-error { background: #ef4444; }
    .upload-progress-widget__detail { color: #94a3b8; }
</style>

<script>
(function () {
    const STORAGE_KEY = 'active_upload_id';
    const PROGRESS_URL_BASE = "{{ url('/input-data/progress') }}";

    const widget     = document.getElementById('upload-progress-widget');
    const barEl       = document.getElementById('upw-bar');
    const titleEl      = document.getElementById('upw-title');
    const detailEl     = document.getElementById('upw-detail');
    const dismissBtn   = document.getElementById('upw-dismiss');

    let pollTimer = null;

    function stopPolling() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = null;
    }

    function hideWidget() {
        widget.style.display = 'none';
        stopPolling();
    }

    dismissBtn.addEventListener('click', function () {
        localStorage.removeItem(STORAGE_KEY);
        hideWidget();
    });

    async function pollOnce(uploadId) {
        try {
            const res = await fetch(`${PROGRESS_URL_BASE}/${uploadId}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!res.ok) {
                // upload_id tidak valid / sudah dihapus -- berhenti memantau
                localStorage.removeItem(STORAGE_KEY);
                hideWidget();
                return;
            }

            const data = await res.json();
            const total = data.jumlah_sheet || 0;
            const done  = data.sheet_selesai || 0;
            const percent = total > 0 ? Math.round((done / total) * 100) : 0;

            widget.style.display = 'block';
            barEl.classList.toggle('is-error', data.status === 'gagal');

            if (data.status === 'queued') {
                titleEl.textContent = 'Menunggu antrean...';
                detailEl.textContent = 'Import akan segera dimulai oleh server.';
                barEl.style.width = '3%';

            } else if (data.status === 'processing') {
                titleEl.textContent = `Mengimpor ${(data.jenis_monitoring || '').toUpperCase()}...`;
                detailEl.textContent = data.sheet_sedang_diproses
                    ? `Sheet ${done + 1}/${total}: "${data.sheet_sedang_diproses}"`
                    : `${done}/${total} sheet selesai`;
                barEl.style.width = percent + '%';

            } else if (data.status === 'aktif') {
                titleEl.textContent = 'Import selesai!';
                detailEl.textContent = `${total} sheet berhasil diimport. Data sudah aktif.`;
                barEl.style.width = '100%';
                stopPolling();
                localStorage.removeItem(STORAGE_KEY);
                setTimeout(hideWidget, 5000);

            } else if (data.status === 'gagal') {
                titleEl.textContent = 'Import gagal';
                detailEl.textContent = data.pesan_error || 'Terjadi kesalahan saat memproses. Data lama tetap aman/aktif.';
                barEl.style.width = '100%';
                stopPolling();
                localStorage.removeItem(STORAGE_KEY);
            }

        } catch (e) {
            // Koneksi sempat gagal -- diamkan, akan dicoba lagi di polling berikutnya
        }
    }

    // Kalau ada upload yang sedang berjalan (dari halaman/tab manapun sebelumnya),
    // langsung tampilkan begitu halaman ini dimuat -- inilah yang membuat progres
    // "tetap terlihat" walau pengguna sudah berpindah halaman.
    const activeId = localStorage.getItem(STORAGE_KEY);
    if (activeId) {
        pollOnce(activeId);
        pollTimer = setInterval(() => pollOnce(activeId), 2500);
    }

    // Dipanggil dari halaman input-data.blade.php begitu upload baru dimulai.
    window.startUploadProgressWidget = function (uploadId) {
        localStorage.setItem(STORAGE_KEY, uploadId);
        stopPolling();
        pollOnce(uploadId);
        pollTimer = setInterval(() => pollOnce(uploadId), 2500);
    };
})();
</script>
@endif
