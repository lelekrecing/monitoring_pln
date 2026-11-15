<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Standar Konstruksi | SIGINJAI PLN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        .form-card { max-width: 720px; }
        .info-note {
            background: #f1f5f9; border: 1px solid #cbd5e1; border-left: 4px solid #0d2137;
            border-radius: 10px; padding: 12px 15px; font-size: 13px; color: #334155; margin-bottom: 20px;
        }
        .info-note a { color: #0d2137; font-weight: 700; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 7px; }
        .form-control { width: 100%; padding: 11px 14px; }
        .form-help { display: block; font-size: 12px; color: #64748b; margin-top: 6px; }
        .req { color: #dc2626; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; }
    </style>
</head>
<body>

@includeIf('partials.simerah-admin-topbar', ['topSubTitle' => 'UP3 JAMBI • STANDAR KONSTRUKSI'])

<div class="wrapper">
    @includeIf('partials.simerah-admin-sidebar', ['activeAdmin' => 'standar'])

    <main class="content">
        <div class="card form-card">
            <div class="header-title">
                <div>
                    <h2>Tambah Standar Konstruksi (Manual)</h2>
                    <small>Tambahkan satu item Standar Konstruksi SUTM secara cepat.</small>
                </div>
                <a href="{{ route('admin.standar-konstruksi.index') }}" class="btn-secondary">Batal</a>
            </div>

            <div class="info-note">
                Untuk tambah/perbarui data dalam jumlah banyak, gunakan <strong>Import Excel</strong> di halaman
                <a href="{{ route('admin.standar-konstruksi.index') }}">Kelola Standar Konstruksi</a>.
                Form ini hanya untuk menambah satu data secara cepat.
            </div>

            @if ($errors->any())
                <div class="error-box">
                    <ul style="margin-left:18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.standar-konstruksi.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>No (opsional, untuk urutan tampil)</label>
                    <input type="number" name="nomor" class="form-control" value="{{ old('nomor') }}" min="0">
                </div>

                <div class="form-group">
                    <label>Kontruksi SUTM 2026 <span class="req">*</span></label>
                    <input type="text" name="nama_konstruksi" class="form-control" value="{{ old('nama_konstruksi') }}" placeholder="mis. TM 10, TM KHUSUS TYPE 3" required>
                    <small class="form-help">Nama akan tampil PERSIS seperti yang diketik, tidak diubah sistem.</small>
                </div>

                <div class="form-group">
                    <label>Link Gambar (Google Drive) <span class="req">*</span></label>
                    <input type="url" name="link_gambar" class="form-control" value="{{ old('link_gambar') }}" placeholder="https://drive.google.com/file/d/.../view?usp=sharing" required>
                    <small class="form-help">Tempel link "Share" dari Google Drive. Pastikan akses file sudah "Anyone with the link".</small>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.standar-konstruksi.index') }}" class="btn-secondary">Kembali</a>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
