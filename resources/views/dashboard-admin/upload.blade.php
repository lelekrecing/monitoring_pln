<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- KOREKSI ARSITEKTUR: CSRF Token wajib untuk request AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Upload Excel Monitoring | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Memanggil CSS khusus upload -->
    @vite('resources/css/admin/upload.css')
</head>
<body class="d-flex align-items-center py-5 min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="form-card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Upload Excel Monitoring PLN</h5>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary text-white">Kembali</a>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        
                        <!-- Penambahan ID "uploadForm" untuk ditarget oleh JavaScript -->
                        <form id="uploadForm" action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Kotak Dashed Upload Area -->
                            <div class="upload-area mb-4">
                                <span class="upload-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="55" height="55" fill="currentColor" class="bi bi-cloud-arrow-up" viewBox="0 0 16 16">
                                      <path fill-rule="evenodd" d="M7.646 5.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 6.707V10.5a.5.5 0 0 1-1 0V6.707L6.354 7.854a.5.5 0 1 1-.708-.708z"/>
                                      <path d="M4.406 3.342A5.53 5.53 0 0 1 8 2c2.69 0 4.923 2 5.166 4.579C14.758 6.804 16 8.137 16 9.773 16 11.569 14.502 13 12.687 13H3.781C1.708 13 0 11.366 0 9.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383m.653.757c-.757.653-1.153 1.44-1.153 2.056v.448l-.445.049C2.064 6.805 1 7.952 1 9.318 1 10.785 2.23 12 3.781 12h8.906C13.98 12 15 10.988 15 9.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 4.825 10.328 3 8 3a4.53 4.53 0 0 0-2.941 1.1z"/>
                                    </svg>
                                </span>
                                <label class="d-block mt-3 mb-2 text-muted fw-bold">Pilih Dokumen Excel (.xls, .xlsx)</label>
                                <input type="file" name="file_excel" class="form-control" accept=".xls,.xlsx" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <!-- Penambahan ID "submitBtn" agar bisa dimanipulasi teksnya -->
                                <button type="submit" id="submitBtn" class="btn btn-primary btn-lg fw-bold">Upload Dokumen</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- UI Feedback & JAVASCRIPT ORCHESTRATOR -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('uploadForm');
            const submitBtn = document.getElementById('submitBtn');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            form.addEventListener('submit', async function (e) {
                // 1. Hentikan reload halaman konvensional
                e.preventDefault(); 
                
                const fileInput = form.querySelector('input[type="file"]');
                if (!fileInput.files.length) return;

                const originalBtnText = submitBtn.innerHTML;
                
                // 2. Kunci form agar tidak diklik dua kali oleh user
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Mengunggah dan Membaca File...';

                const formData = new FormData(form);

                try {
                    // ==========================================
                    // FASE 1: INISIASI UPLOAD (Mengirim file fisik)
                    // ==========================================
                    let initResponse = await fetch(form.action, {
                        method: 'POST',
                        headers: { 
                            'X-Requested-With': 'XMLHttpRequest', // Identitas AJAX untuk Laravel
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    let initData = await initResponse.json();

                    if (!initResponse.ok) {
                        throw new Error(initData.message || 'Gagal menginisiasi file.');
                    }

                    // Menangkap variabel penting dari respon Backend
                    const sheets = initData.sheets;
                    const uploadId = initData.upload_id;
                    const filePath = initData.file_path;
                    const jenisMonitoring = initData.jenis_monitoring;
                    const totalSheets = sheets.length;

                    // ==========================================
                    // FASE 2: ORKESTRASI PEMROSESAN PER SHEET
                    // ==========================================
                    for (let i = 0; i < totalSheets; i++) {
                        let sheetName = sheets[i];
                        let currentProgress = i + 1;
                        
                        // Update UI Real-time
                        submitBtn.innerHTML = `Memproses Sheet [${sheetName}] (${currentProgress}/${totalSheets})...`;
                        
                        let processResponse = await fetch("{{ route('input-data.process-sheet') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                upload_id: uploadId,
                                file_path: filePath,
                                sheet_name: sheetName,
                                sheet_index: i,
                                jenis_monitoring: jenisMonitoring
                            })
                        });

                        let processData = await processResponse.json();
                        
                        if (!processResponse.ok) {
                            throw new Error(`Terjadi kesalahan pada Sheet "${sheetName}": ${processData.message}`);
                        }
                    }

                    // ==========================================
                    // FASE 3: SELESAI SEMPURNA
                    // ==========================================
                    submitBtn.innerHTML = 'Selesai!';
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses 100%',
                        text: `Upload selesai. ${totalSheets} sheet berhasil diekstrak dan masuk ke database.`,
                        allowOutsideClick: false,
                        confirmButtonText: 'Kembali ke Dashboard'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('admin.dashboard') }}";
                        }
                    });

                } catch (error) {
                    // Error Handling
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Terhenti',
                        text: error.message,
                        confirmButtonColor: '#d33'
                    });
                    
                    // Kembalikan tombol seperti semula agar user bisa mencoba lagi
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            });
        });
    </script>
</body>
</html>