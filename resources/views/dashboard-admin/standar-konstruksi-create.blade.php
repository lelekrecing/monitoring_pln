<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Standar Konstruksi | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{ background:#f4f7fb; }
        .form-card-header{ background:#0B5EA8; color:#fff; padding:16px 24px; border-radius:8px 8px 0 0; }
    </style>
</head>
<body class="d-flex align-items-center py-5 min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="form-card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tambah Standar Konstruksi (Manual)</h5>
                        <a href="{{ route('admin.standar-konstruksi.index') }}" class="btn btn-sm btn-light">Batal</a>
                    </div>
                    <div class="card-body p-4 p-md-5">

                        <div class="alert alert-secondary small">
                            Untuk tambah/perbarui data dalam jumlah banyak, gunakan <strong>Import Excel</strong> di halaman
                            <a href="{{ route('admin.standar-konstruksi.index') }}">Kelola Standar Konstruksi</a>.
                            Form ini hanya untuk menambah satu data secara cepat.
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.standar-konstruksi.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label>No (opsional, untuk urutan tampil)</label>
                                <input type="number" name="nomor" class="form-control" value="{{ old('nomor') }}" min="0">
                            </div>

                            <div class="mb-3">
                                <label>Kontruksi SUTM 2026 <span class="text-danger">*</span></label>
                                <input type="text" name="nama_konstruksi" class="form-control" value="{{ old('nama_konstruksi') }}" placeholder="mis. TM 10, TM KHUSUS TYPE 3" required>
                                <small class="text-muted">Nama akan tampil PERSIS seperti yang diketik, tidak diubah sistem.</small>
                            </div>

                            <div class="mb-4">
                                <label>Link Gambar (Google Drive) <span class="text-danger">*</span></label>
                                <input type="url" name="link_gambar" class="form-control" value="{{ old('link_gambar') }}" placeholder="https://drive.google.com/file/d/.../view?usp=sharing" required>
                                <small class="text-muted d-block mt-1">
                                    Tempel link "Share" dari Google Drive. Pastikan akses file sudah "Anyone with the link".
                                </small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>