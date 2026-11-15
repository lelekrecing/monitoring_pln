<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data KHS | SIGINJAI PLN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        .form-card { max-width: 720px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 7px; }
        .form-control { width: 100%; padding: 11px 14px; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; }
    </style>
</head>
<body>

@includeIf('partials.simerah-admin-topbar', ['topSubTitle' => 'UP3 JAMBI • DATA KHS'])

<div class="wrapper">
    @includeIf('partials.simerah-admin-sidebar', ['activeAdmin' => 'dashboard'])

    <main class="content">
        <div class="card form-card">
            <div class="header-title">
                <div>
                    <h2>Tambah Data KHS Baru</h2>
                    <small>Tambahkan data proyek KHS, link ArcGIS, gambar, dan dokumen kontrak.</small>
                </div>
            </div>

            <form action="{{ route('admin.khs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Nama Proyek / KHS</label>
                    <input type="text" name="nama_proyek" class="form-control" placeholder="Masukkan nama proyek..." required>
                </div>
                <div class="form-group">
                    <label>Link ArcGIS (Opsional)</label>
                    <input type="url" name="arcgis_link" class="form-control" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label>Upload Gambar (Opsional saat ini)</label>
                    <input type="file" name="gambar" class="form-control" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Upload Kontrak PDF (Opsional saat ini)</label>
                    <input type="file" name="kontrak_pdf" class="form-control" accept="application/pdf">
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
