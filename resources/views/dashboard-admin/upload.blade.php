<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.admin-head')
    <title>Upload Excel Monitoring | Admin</title>
</head>
<body>

<div class="wrapper">
    @include('partials.admin-sidebar', ['active' => 'upload'])

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

                @include('partials.admin-alerts')

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