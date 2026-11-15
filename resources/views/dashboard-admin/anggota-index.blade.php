<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Akun | SIGINJAI PLN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        .toolbar { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        .toolbar input[type="text"] { flex: 1; min-width: 220px; max-width: 380px; padding: 10px 14px; }
        .toolbar select { padding: 10px 12px; height: 40px; }
        .total-info { margin-left: auto; color: #64748b; font-size: 13.5px; font-weight: 500; }
        .btn-sm { padding: 7px 13px !important; font-size: 12.5px !important; }
        .table-container { overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 12px; }
        .empty { padding: 28px; text-align: center; color: #64748b; }
        .action-btns { display: flex; gap: 6px; justify-content: flex-end; align-items: center; }
        .action-btns form { display: flex; }
        .btn-icon { background: transparent; border: none; cursor: pointer; padding: 6px; border-radius: 8px; transition: .18s; display: flex; align-items: center; justify-content: center; line-height: 0; }
        .btn-icon .material-icons { font-size: 20px; }
        .btn-reset { color: #0284c7; }
        .btn-reset:hover { background-color: #e0f2fe; }
        .btn-delete { color: #dc2626; }
        .btn-delete:hover { background-color: #fee2e2; }
        .btn-disabled { color: #94a3b8; cursor: not-allowed; }
        .pagination { margin-top: 18px; display: flex; align-items: center; gap: 10px; color: #64748b; font-size: 13.5px; flex-wrap: wrap; }
        .pagination .disabled { opacity: .45; pointer-events: none; }
        .modal { display: none; position: fixed; z-index: 1000; inset: 0; background-color: rgba(15,23,42,.55); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px; }
        .modal.active { display: flex; }
        .modal-content { width: 100%; max-width: 440px; padding: 28px; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
        .modal-header h3 { font-size: 18px; font-weight: 700; }
        .modal-sub { font-size: 13.5px; margin-bottom: 18px; }
        .close-btn { background: none; border: none; color: #64748b; cursor: pointer; font-size: 24px; transition: .15s; }
        .close-btn:hover { color: #0f172a; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 13px; margin-bottom: 7px; font-weight: 600; }
        .form-control { width: 100%; padding: 11px 14px; }
        .form-text { display: block; font-size: 11px; margin-top: 5px; }
        .modal-footer { margin-top: 22px; display: flex; justify-content: flex-end; gap: 10px; }
        .password-wrapper { position: relative; display: flex; align-items: center; }
        .password-toggle-icon { position: absolute; right: 14px; cursor: pointer; color: #64748b; user-select: none; }
        .password-wrapper .form-control { padding-right: 45px; }
    </style>
</head>
<body>

@includeIf('partials.simerah-admin-topbar', ['topSubTitle' => 'UP3 JAMBI • MANAJEMEN AKUN'])

<div class="wrapper">
    @includeIf('partials.simerah-admin-sidebar', ['activeAdmin' => 'akun'])

    <!-- CONTENT -->
    <main class="content">
        <div class="card">

            <div class="header-title">
                <h2>Manajemen Akun</h2>
                <p>Daftar akun pengguna yang terdaftar di sistem, reset sandi, dan hapus akun.</p>
            </div>

            <!-- MESSAGES -->
            @if (session('success'))
                <div class="alert alert-success"><strong>Berhasil!</strong> {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-error"><strong>Gagal Sistem!</strong> {{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-error"><strong>Gagal Validasi!</strong> {{ $errors->first() }}</div>
            @endif

            <!-- PENCARIAN -->
            <form method="GET" action="{{ route('admin.anggota.index') }}" class="toolbar">
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama / email...">
                <button type="submit" class="btn-primary">
                    <i class="material-icons" style="font-size:18px;">search</i> Cari
                </button>
                @if ($q !== '')
                    <a href="{{ route('admin.anggota.index') }}" class="btn-secondary">Reset</a>
                @endif
                <span class="total-info">Total: {{ number_format($anggota->total(), 0, ',', '.') }} akun</span>
            </form>

            <!-- TABEL ANGGOTA -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Terdaftar</th>
                            <th style="text-align: right; width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($anggota as $i => $u)
                        @php $namaUser = $u->nama ?? $u->name ?? '-'; @endphp
                        <tr>
                            <td>{{ $anggota->firstItem() + $i }}</td>
                            <td>{{ $namaUser }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->created_at ? \Illuminate\Support\Carbon::parse($u->created_at, 'UTC')->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="btn-icon btn-reset" title="Reset Password"
                                            onclick="bukaReset('{{ route('admin.anggota.reset-password', $u->id) }}', @js($namaUser))">
                                        <i class="material-icons">lock_reset</i>
                                    </button>

                                    @if ($u->id === auth()->id())
                                        <button type="button" class="btn-icon btn-disabled" title="Tidak dapat menghapus akun sendiri" disabled>
                                            <i class="material-icons">delete_forever</i>
                                        </button>
                                    @else
                                        <form method="POST" action="{{ route('admin.anggota.destroy', $u->id) }}"
                                              onsubmit="return confirm('Hapus akun ' + @js($namaUser) + '? Tindakan ini tidak bisa dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon btn-delete" title="Hapus Akun">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty">{{ $q !== '' ? 'Tidak ada akun yang cocok dengan pencarian.' : 'Belum ada akun.' }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- PAGINASI -->
            @if ($anggota->hasPages())
                <div class="pagination">
                    <a href="{{ $anggota->previousPageUrl() ?? '#' }}" class="btn-secondary btn-sm {{ $anggota->onFirstPage() ? 'disabled' : '' }}">&laquo; Sebelumnya</a>
                    <span>Halaman {{ $anggota->currentPage() }} dari {{ $anggota->lastPage() }}</span>
                    <a href="{{ $anggota->nextPageUrl() ?? '#' }}" class="btn-secondary btn-sm {{ $anggota->hasMorePages() ? '' : 'disabled' }}">Berikutnya &raquo;</a>
                </div>
            @endif

        </div>
    </main>
</div>

<!-- MODAL RESET PASSWORD -->
<div id="modalReset" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Reset Password</h3>
            <button type="button" class="close-btn" onclick="tutupReset()">&times;</button>
        </div>
        <p class="modal-sub" id="resetNama"></p>
        <form method="POST" id="formReset" action="">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="pw1">Password baru (min. 6 karakter)</label>
                <div class="password-wrapper">
                    <input type="password" name="password" id="pw1" class="form-control" required minlength="6" autocomplete="new-password">
                    <i class="material-icons password-toggle-icon" onclick="togglePasswordVisibility('pw1', this)">visibility_off</i>
                </div>
            </div>
            <div class="form-group">
                <label for="pw2">Konfirmasi password</label>
                <div class="password-wrapper">
                    <input type="password" name="password_confirmation" id="pw2" class="form-control" required minlength="6" autocomplete="new-password">
                    <i class="material-icons password-toggle-icon" onclick="togglePasswordVisibility('pw2', this)">visibility_off</i>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="tutupReset()">Batal</button>
                <button type="submit" class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const modalReset = document.getElementById('modalReset');
    const formReset  = document.getElementById('formReset');

    function bukaReset(url, nama) {
        formReset.action = url;
        formReset.reset();

        // Kembalikan kedua input ke mode tersembunyi
        ['pw1', 'pw2'].forEach(function (id) {
            const el = document.getElementById(id);
            el.type = 'password';
            el.nextElementSibling.textContent = 'visibility_off';
        });

        document.getElementById('resetNama').textContent = 'Akun: ' + nama;
        modalReset.classList.add('active');
        document.getElementById('pw1').focus();
    }

    function tutupReset() { modalReset.classList.remove('active'); }

    function togglePasswordVisibility(inputId, iconElement) {
        const input = document.getElementById(inputId);
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        iconElement.textContent = show ? 'visibility' : 'visibility_off';
    }

    // Tutup modal saat klik area luar atau tekan Escape
    modalReset.addEventListener('click', function (e) { if (e.target === modalReset) tutupReset(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') tutupReset(); });
</script>

@includeIf('partials.upload-progress-widget')

</body>
</html>