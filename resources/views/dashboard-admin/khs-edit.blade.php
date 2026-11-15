<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit / Upload KHS | SIGINJAI PLN</title>

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
        .preview-box {
            margin-bottom: 12px; text-align: center; padding: 14px;
            border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc;
        }
        .preview-box img { width: 150px; border-radius: 8px; border: 1px solid #cbd5e1; }
        .preview-label { margin-top: 8px; font-size: 12px; font-weight: 700; color: #059669; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 22px; }
    </style>
</head>
<body>

@includeIf('partials.simerah-admin-topbar', ['topSubTitle' => 'UP3 JAMBI • EDIT DATA KHS'])

<div class="wrapper">
    @includeIf('partials.simerah-admin-sidebar', ['activeAdmin' => 'dashboard'])

    <main class="content">
        <div class="card form-card">
            <div class="header-title">
                <div>
                    <h2>Edit / Upload Data KHS</h2>
                    <small>Perbarui detail proyek KHS, gambar, atau file kontrak PDF.</small>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Batal</a>
            </div>

            <form action="{{ route('admin.khs.update', $khs->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Nama Proyek / KHS</label>
                    <input type="text" name="nama_proyek" class="form-control" value="{{ $khs->nama_proyek }}" required>
                </div>

                <div class="form-group">
                    <label>Link ArcGIS</label>
                    <input type="url" name="arcgis_link" class="form-control" value="{{ $khs->arcgis_link }}" placeholder="https://www.arcgis.com/...">
                </div>

                <div class="form-group">
                    <label>Upload Gambar Baru</label>
                    @if($khs->gambar)
                        <div class="preview-box">
                            <img src="{{ asset('storage/' . $khs->gambar) }}" alt="Preview Gambar">
                            <div class="preview-label">Gambar saat ini</div>
                        </div>
                    @endif
                    <input type="file" name="gambar" class="form-control" accept="image/*">
                    <small class="form-help">*Biarkan kosong jika tidak ingin mengubah gambar.</small>
                </div>

                <div class="form-group">
                    <label>Upload Kontrak PDF Baru</label>
                    @if($khs->kontrak_pdf)
                        <div class="preview-box">
                            <a href="{{ asset('storage/' . $khs->kontrak_pdf) }}" target="_blank" class="btn-secondary">
                                <i class="fa fa-file-pdf"></i> Buka PDF Saat Ini
                            </a>
                        </div>
                    @endif
                    <input type="file" name="kontrak_pdf" class="form-control" accept="application/pdf">
                    <small class="form-help">*Biarkan kosong jika tidak ingin mengubah file PDF.</small>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </main>
</div>

</body>
</html>
