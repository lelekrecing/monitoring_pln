<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Excel Monitoring | SIGINJAI PLN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        .form-narrow { max-width: 680px; }
        .upload-box {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            cursor: pointer; transition: all .2s ease; text-align: center;
        }
        .upload-box .material-icons { font-size: 40px; margin-bottom: 10px; }
        .upload-box input[type="file"] { display: none; }
        .button-group { display: flex; justify-content: flex-end; gap: 10px; margin-top: 16px; }
    </style>
</head>
<body>

@includeIf('partials.simerah-admin-topbar', ['topSubTitle' => 'UP3 JAMBI • UPLOAD MONITORING'])

<div class="wrapper">
    @includeIf('partials.simerah-admin-sidebar', ['activeAdmin' => 'upload'])

    <main class="content">
        <div class="card">

            <div class="header-title">
                <div>
                    <h2>Upload Excel Monitoring PLN</h2>
                    <small>Upload file Excel (.xls, .xlsx)</small>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Kembali</a>
            </div>

            <div class="form-narrow">

                @if(session('success'))
                    <div class="success-box">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="error-box">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="error-box">
                        <ul style="margin-left:18px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <label class="upload-box">
                        <span class="material-icons">cloud_upload</span>
                        <p class="js-file-name">Pilih Dokumen Excel (.xls, .xlsx)</p>
                        <input type="file" name="file_excel" class="js-file-input" accept=".xls,.xlsx" required>
                    </label>

                    <div class="button-group">
                        <button type="submit" class="btn-save">Upload Dokumen</button>
                    </div>
                </form>
            </div>

        </div>
    </main>
</div>

<script>
    document.querySelectorAll('.js-file-input').forEach(function (input) {
        input.addEventListener('change', function () {
            const label = this.closest('.upload-box').querySelector('.js-file-name');
            if (this.files.length > 0) { label.textContent = this.files[0].name; }
        });
    });
</script>

@includeIf('partials.upload-progress-widget')

</body>
</html>
