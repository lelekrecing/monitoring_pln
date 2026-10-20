<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Akun | PLN UP3 Jambi</title>

    @vite('resources/css/admin/dashboard_admin.css')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* =====================================================
           TEMA DARK MODE & RESET UI
        ===================================================== */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #0f172a; color: #e2e8f0; display: flex; min-height: 100vh; overflow-x: hidden; }

        /* SIDEBAR */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: 250px; min-width: 250px; height: 100vh;
            background-color: #0f172a;
            border-right: 1px solid rgba(255,255,255,.08);
            display: flex; flex-direction: column; overflow-y: auto; z-index: 100;
        }
        .logo { padding: 20px; text-align: center; margin-bottom: 10px; }
        .sidebar-logo-pln { max-width: 70px; width: 100%; height: auto; object-fit: contain; margin: 0 auto; display: block; }
        .sidebar ul { list-style: none; padding: 0; margin: 0; flex: 0 0 auto; }
        .sidebar ul li { padding: 0; margin: 0; }
        .sidebar-link {
            display: block; width: 100%; padding: 15px 25px;
            color: #94a3b8; text-decoration: none; font-size: 15px;
            font-weight: 500; transition: .3s ease; border-left: 3px solid transparent;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: linear-gradient(90deg, rgba(14,165,233,.15) 0%, rgba(14,165,233,.05) 100%);
            color: #38bdf8; border-left-color: #0ea5e9;
        }
        .sidebar-link span { display: block; }
        .logout-menu { margin-top: auto; border-top: 1px solid rgba(255,255,255,.08); }
        .logout-menu a { color: #ef4444; }
        .logout-menu a:hover { background: rgba(239,68,68,.1); border-left-color: #ef4444; }

        /* LAYOUT */
        .wrapper { display: flex; width: 100%; }
        .content { flex: 1; padding: 40px; margin-left: 250px; min-width: 0; }
        .card { background-color: #1e293b; border-radius: 12px; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,.1); border: 1px solid rgba(255,255,255,.08); }

        /* HEADER */
        .header-title { margin-bottom: 25px; border-bottom: 1px solid rgba(255,255,255,.08); padding-bottom: 20px; }
        .header-title h2 { color: #f8fafc; font-size: 24px; font-weight: 600; }
        .header-title p { color: #94a3b8; font-size: 14px; margin-top: 5px; }

        /* ALERT */
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; font-size: 14px; }
        .alert-success { background-color: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.2); color: #4ade80; }
        .alert-error { background-color: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.2); color: #f87171; }

        /* TOOLBAR (PENCARIAN) */
        .toolbar { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        .toolbar input[type="text"] {
            flex: 1; min-width: 200px; max-width: 360px; padding: 10px 14px; border-radius: 6px;
            border: 1px solid rgba(255,255,255,.08); background: #0f172a; color: #f8fafc; font-size: 14px; outline: none; transition: .3s;
        }
        .toolbar input[type="text"]:focus { border-color: #0ea5e9; box-shadow: 0 0 0 2px rgba(14,165,233,.2); }
        .total-info { margin-left: auto; color: #94a3b8; font-size: 14px; }

        /* BUTTON */
        .btn-primary { background-color: #0ea5e9; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: .2s; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; text-decoration: none; }
        .btn-primary:hover { background-color: #0284c7; }
        .btn-secondary { background-color: transparent; color: #cbd5e1; border: 1px solid #475569; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 500; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; }
        .btn-secondary:hover { background-color: rgba(255,255,255,.05); color: #fff; }
        .btn-sm { padding: 6px 12px; font-size: 13px; }

        /* TABEL */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background-color: #0f172a; color: #94a3b8; font-weight: 600; font-size: 13px; text-transform: uppercase; padding: 15px; border-bottom: 2px solid rgba(255,255,255,.08); }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,.08); color: #cbd5e1; font-size: 14px; vertical-align: middle; }
        tr:hover { background-color: rgba(255,255,255,.02); }
        .empty { padding: 28px; text-align: center; color: #94a3b8; }

        /* ACTION BUTTONS */
        .action-btns { display: flex; gap: 6px; justify-content: flex-end; align-items: center; }
        .action-btns form { display: flex; }
        .btn-icon { background: transparent; border: none; cursor: pointer; padding: 6px; border-radius: 6px; transition: .2s; display: flex; align-items: center; justify-content: center; line-height: 0; }
        .btn-icon .material-icons { font-size: 20px; }
        .btn-reset { color: #38bdf8; }
        .btn-reset:hover { background-color: rgba(56,189,248,.12); }
        .btn-delete { color: #f87171; }
        .btn-delete:hover { background-color: rgba(239,68,68,.12); }
        .btn-disabled { color: #475569; cursor: not-allowed; }

        /* PAGINASI */
        .pagination { margin-top: 18px; display: flex; align-items: center; gap: 10px; color: #94a3b8; font-size: 14px; flex-wrap: wrap; }
        .pagination .disabled { opacity: .4; pointer-events: none; }

        /* MODAL */
        .modal { display: none; position: fixed; z-index: 1000; inset: 0; background-color: rgba(0,0,0,.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px; }
        .modal.active { display: flex; }
        .modal-content { background-color: #1e293b; border-radius: 12px; padding: 30px; width: 100%; max-width: 440px; border: 1px solid rgba(255,255,255,.08); box-shadow: 0 20px 25px -5px rgba(0,0,0,.2); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
        .modal-header h3 { color: #f8fafc; font-size: 18px; }
        .modal-sub { color: #94a3b8; font-size: 14px; margin-bottom: 20px; }
        .close-btn { background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 24px; transition: .2s; }
        .close-btn:hover { color: #f8fafc; }

        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; color: #cbd5e1; font-size: 13px; margin-bottom: 8px; font-weight: 500; }
        .form-control { width: 100%; padding: 12px 15px; background-color: #0f172a; border: 1px solid rgba(255,255,255,.08); border-radius: 6px; color: #f8fafc; font-size: 14px; transition: .3s; }
        .form-control:focus { outline: none; border-color: #0ea5e9; box-shadow: 0 0 0 2px rgba(14,165,233,.2); }
        .form-text { display: block; font-size: 11px; color: #64748b; margin-top: 5px; }
        .modal-footer { margin-top: 25px; display: flex; justify-content: flex-end; gap: 10px; }

        /* TOGGLE PASSWORD */
        .password-wrapper { position: relative; display: flex; align-items: center; }
        .password-toggle-icon { position: absolute; right: 15px; cursor: pointer; color: #64748b; user-select: none; transition: color .2s; }
        .password-toggle-icon:hover { color: #cbd5e1; }
        .password-wrapper .form-control { padding-right: 45px; }

        @media (max-width: 768px) {
            .sidebar { position: static; width: 100%; min-width: 0; height: auto; }
            body { flex-direction: column; }
            .content { margin-left: 0; padding: 20px; }
            .card { padding: 20px; }
            .total-info { margin-left: 0; width: 100%; }
        }
    </style>
</head>
<body>

<div class="wrapper">

    <!-- SIDEBAR ADMIN -->
    <aside class="sidebar">
        <div class="logo">
            <img src="{{ asset('images/Logo_PLN.png') }}" alt="Logo PLN" class="sidebar-logo-pln">
        </div>
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="sidebar-link"><span>Dashboard</span></a></li>
            <li><a href="{{ url('/input-data') }}" class="sidebar-link"><span>Upload Monitoring</span></a></li>
            <li><a href="{{ url('/riwayat-upload') }}" class="sidebar-link"><span>Riwayat Upload</span></a></li>
            <li><a href="{{ route('admin.standar-konstruksi.index') }}" class="sidebar-link"><span>Standar Konstruksi</span></a></li>
            @if (Route::has('admin.anggota.index'))
                <li><a href="{{ route('admin.anggota.index') }}" class="sidebar-link active"><span>Manajemen Akun</span></a></li>
            @endif
        </ul>
        <div class="logout-menu">
            <a href="{{ route('logout.admin') }}" class="sidebar-link">
                <span style="display:flex; align-items:center; gap:8px;"><i class="material-icons" style="font-size:18px;">logout</i> Logout</span>
            </a>
        </div>
    </aside>

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