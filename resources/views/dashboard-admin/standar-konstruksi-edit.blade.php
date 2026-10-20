<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Standar Konstruksi | Admin</title>
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
                        <h5 class="mb-0">Edit Standar Konstruksi</h5>
                        <a href="{{ route('admin.standar-konstruksi.index') }}" class="btn btn-sm btn-light">Batal</a>
                    </div>
                    <div class="card-body p-4 p-md-5">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.standar-konstruksi.update', $item->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label>No (urutan tampil)</label>
                                <input type="number" name="nomor" class="form-control" value="{{ old('nomor', $item->nomor) }}" min="0">
                            </div>

                            <div class="mb-3">
                                <label>Kontruksi SUTM 2026 <span class="text-danger">*</span></label>
                                <input type="text" name="nama_konstruksi" class="form-control" value="{{ old('nama_konstruksi', $item->nama_konstruksi) }}" required>
                                <small class="text-muted">Nama akan tampil PERSIS seperti yang diketik, tidak diubah sistem.</small>
                            </div>

                            <div class="mb-4">
                                <label>Link Gambar (Google Drive) <span class="text-danger">*</span></label>
                                <div class="mb-3 text-center p-3 border rounded bg-light">
                                    <img src="{{ $item->gambar_url }}" class="img-thumbnail" style="max-width:220px;" alt="Preview"
                                         onerror="this.onerror=null; this.src='{{ $item->gambar_url_alt }}'; this.onerror=function(){ this.src='https://placehold.co/220x160?text=No+Image'; };">
                                    <div class="mt-2"><small class="text-success fw-bold">Gambar saat ini</small></div>
                                </div>
                                <input type="url" name="link_gambar" class="form-control" value="{{ old('link_gambar', $item->link_gambar) }}" placeholder="https://drive.google.com/file/d/.../view?usp=sharing" required>
                                <small class="text-muted d-block mt-1">Ganti dengan link Google Drive baru untuk mengganti gambar.</small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>