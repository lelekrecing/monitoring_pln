<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Standar Konstruksi | SIGINJAI PLN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        .form-card { max-width: 720px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 7px; }
        .form-control { width: 100%; padding: 11px 14px; }
        .form-help { display: block; font-size: 12px; color: #64748b; margin-top: 6px; }
        .req { color: #dc2626; }
        .preview-box {
            margin-bottom: 12px; text-align: center; padding: 16px;
            border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc;
        }
        .preview-box img { max-width: 220px; border-radius: 8px; border: 1px solid #cbd5e1; }
        .preview-label { margin-top: 8px; font-size: 12px; font-weight: 700; color: #059669; }
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
                    <h2>Edit Standar Konstruksi</h2>
                    <small>Perbarui data nama konstruksi maupun tautan gambar referensi.</small>
                </div>
                <a href="{{ route('admin.standar-konstruksi.index') }}" class="btn-secondary">Batal</a>
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

            <form action="{{ route('admin.standar-konstruksi.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>No (urutan tampil)</label>
                    <input type="number" name="nomor" class="form-control" value="{{ old('nomor', $item->nomor) }}" min="0">
                </div>

                <div class="form-group">
                    <label>Kontruksi SUTM 2026 <span class="req">*</span></label>
                    <input type="text" name="nama_konstruksi" class="form-control" value="{{ old('nama_konstruksi', $item->nama_konstruksi) }}" required>
                    <small class="form-help">Nama akan tampil PERSIS seperti yang diketik, tidak diubah sistem.</small>
                </div>

                <div class="form-group">
                    <label>Link Gambar (Google Drive) <span class="req">*</span></label>
                    <div class="preview-box">
                        <img src="{{ $item->gambar_url }}" alt="Preview"
                             onerror="this.onerror=null; this.src='{{ $item->gambar_url_alt }}'; this.onerror=function(){ this.src='https://placehold.co/220x160?text=No+Image'; };">
                        <div class="preview-label">Gambar saat ini</div>
                    </div>
                    <input type="url" name="link_gambar" class="form-control" value="{{ old('link_gambar', $item->link_gambar) }}" placeholder="https://drive.google.com/file/d/.../view?usp=sharing" required>
                    <small class="form-help">Ganti dengan link Google Drive baru untuk mengganti gambar.</small>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.standar-konstruksi.index') }}" class="btn-secondary">Kembali</a>
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
