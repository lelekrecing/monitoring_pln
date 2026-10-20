<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Upload | PLN UP3 Jambi</title>

    @vite('resources/css/admin/dashboard_admin.css')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #0f172a; color: #e2e8f0; display: flex; min-height: 100vh; overflow-x: hidden; }

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

        .wrapper { display: flex; width: 100%; }
        .content { flex: 1; padding: 40px; margin-left: 250px; min-width: 0; }
        .card { background-color: #1e293b; border-radius: 12px; padding: 30px; border: 1px solid rgba(255,255,255,.08); }

        .header-title { margin-bottom: 25px; border-bottom: 1px solid rgba(255,255,255,.08); padding-bottom: 20px; }
        .header-title h2 { color: #f8fafc; font-size: 24px; font-weight: 600; margin-bottom: 5px; }
        .header-title small { color: #94a3b8; font-size: 14px; display: block; }

        .success-box { background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.2); color: #4ade80; padding: 14px; margin-bottom: 20px; border-radius: 8px; font-size: 14px; }
        .error-box { background: rgba(239,68,68,.1); border: 1px solid rgba(239,68,68,.2); color: #f87171; padding: 14px; margin-bottom: 20px; border-radius: 8px; font-size: 14px; }

        .filter-bar { display: flex; gap: 10px; align-items: center; margin-bottom: 20px; flex-wrap: wrap; }
        .chip {
            padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; text-decoration: none;
            color: #38bdf8; background: rgba(14,165,233,.08); border: 1px solid #0ea5e9;
        }
        .chip-info { font-weight: 500; }
        .chip-info a { color: #38bdf8; text-decoration: underline; }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            text-align: left; color: #cbd5e1; font-size: 13px; text-transform: uppercase; letter-spacing: .5px;
            padding: 16px 20px; background: #0f172a; white-space: nowrap; font-weight: 600;
        }
        thead th:last-child { text-align: right; }
        tbody td { padding: 18px 20px; border-top: 1px solid rgba(255,255,255,.06); font-size: 15px; vertical-align: middle; }
        .file-pill {
            display: inline-block; padding: 10px 20px; border-radius: 6px; background: #0f172a;
            border: 1px solid rgba(255,255,255,.1); font-size: 14px; color: #cbd5e1; font-weight: 500; word-break: break-all;
        }
        .badge { display: inline-block; padding: 6px 14px; border-radius: 999px; font-size: 13px; font-weight: 700; text-transform: uppercase; }
        .badge-aktif { background: rgba(34,197,94,.12); color: #4ade80; border: 1px solid rgba(34,197,94,.4); }
        .badge-arsip { background: rgba(148,163,184,.12); color: #94a3b8; border: 1px solid rgba(148,163,184,.3); }
        .badge-gagal { background: rgba(239,68,68,.12); color: #f87171; border: 1px solid rgba(239,68,68,.4); }
        .badge-proses { background: rgba(245,158,11,.12); color: #fbbf24; border: 1px solid rgba(245,158,11,.4); }

        .aksi { display: flex; justify-content: flex-end; gap: 14px; }
        .aksi form { display: inline; }
        .icon-btn { background: none; border: 0; cursor: pointer; padding: 4px; line-height: 1; color: #64748b; transition: .2s; }
        .icon-btn .material-icons { font-size: 24px; }
        .icon-btn.activate:hover { color: #4ade80; }
        .icon-btn.delete:hover { color: #f87171; }
        .icon-btn:disabled { cursor: default; color: #475569; opacity: .8; }
        .empty { padding: 30px; text-align: center; color: #94a3b8; }

        @media (max-width: 768px) {
            .sidebar { position: static; width: 100%; min-width: 0; height: auto; }
            body { flex-direction: column; }
            .content { margin-left: 0; padding: 20px; }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <aside class="sidebar">
        <div class="logo">
            <img src="{{ asset('images/Logo_PLN.png') }}" alt="Logo PLN" class="sidebar-logo-pln">
        </div>
        <ul>
            <li><a href="{{ route('admin.dashboard') }}" class="sidebar-link"><span>Dashboard</span></a></li>
            <li><a href="{{ url('/input-data') }}" class="sidebar-link"><span>Upload Monitoring</span></a></li>
            <li class="active"><a href="{{ url('/riwayat-upload') }}" class="sidebar-link active"><span>Riwayat Upload</span></a></li>
            <li><a href="{{ route('admin.standar-konstruksi.index') }}" class="sidebar-link"><span>Standar Konstruksi</span></a></li>
            @if (Route::has('admin.anggota.index'))
                <li><a href="{{ route('admin.anggota.index') }}" class="sidebar-link"><span>Manajemen Akun</span></a></li>
            @endif
        </ul>
        <div class="logout-menu">
            <a href="{{ route('logout.admin') }}" class="sidebar-link">
                <span style="display:flex; align-items:center; gap:8px;"><i class="material-icons" style="font-size:18px;">logout</i> Logout</span>
            </a>
        </div>
    </aside>

    <main class="content">
        <div class="card">

            <div class="header-title">
                <h2>Riwayat Upload</h2>
                <small>Semua data yang pernah diupload tetap tersimpan di sini. Pilih salah satu untuk dijadikan tampilan aktif di dashboard, kapan saja.</small>
            </div>

            @if (session('success'))
                <div class="success-box">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="error-box">{{ session('error') }}</div>
            @endif

            <div class="filter-bar">
                <a href="{{ url('/riwayat-upload') }}" class="chip">Semua</a>
                @if (! $tampilkanSemua && $jumlahTersembunyi > 0)
                    <span class="chip chip-info">
                        &bull; Upload kosong/gagal (tanpa data) sedang disembunyikan.
                        <a href="{{ url('/riwayat-upload') }}?tampilkan_semua=1">Tampilkan semua</a>
                    </span>
                @endif
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Nama File</th>
                            <th>Jenis</th>
                            <th>Diupload</th>
                            <th>Progres</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($riwayat as $u)
                        @php
                            $namaFile = $u->nama_file ?? $u->file_name ?? $u->nama_asli
                                ?? (! empty($u->file_path) ? basename($u->file_path) : '-');
                            $aktif = $u->status === 'aktif';
                            $gagal = $u->status === 'gagal';
                            $kosong = (int) $u->jumlah_sheet === 0;
                        @endphp
                        <tr>
                            <td><span class="file-pill">{{ $namaFile }}</span></td>
                            <td>{{ strtoupper($u->jenis_monitoring ?? '-') }}</td>
                            <td>{{ $u->created_at ? \Illuminate\Support\Carbon::parse($u->created_at, 'UTC')->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y, H:i') : '-' }}</td>
                            <td>{{ (int) $u->sheet_selesai }}/{{ (int) $u->jumlah_sheet }} sheet</td>
                            <td>
                                @if ($aktif)
                                    <span class="badge badge-aktif">Sedang tampil</span>
                                @elseif ($gagal)
                                    <span class="badge badge-gagal" title="{{ $u->pesan_error }}">Gagal</span>
                                @elseif ($u->status === 'arsip')
                                    <span class="badge badge-arsip">Arsip</span>
                                @else
                                    <span class="badge badge-proses">{{ $u->status ?? 'Diproses' }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="aksi">
                                    <form method="POST" action="{{ route('admin.riwayat-upload.aktifkan', $u->id) }}"
                                          onsubmit="return confirm('Jadikan upload ini sebagai tampilan aktif di dashboard?')">
                                        @csrf
                                        <button type="submit" class="icon-btn activate"
                                                title="{{ $aktif ? 'Sedang tampil' : 'Jadikan tampilan aktif' }}"
                                                {{ ($aktif || $gagal || $kosong) ? 'disabled' : '' }}>
                                            <i class="material-icons">check_circle</i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.riwayat-upload.destroy', $u->id) }}"
                                          onsubmit="return confirm('Hapus riwayat upload ini beserta datanya? Tindakan ini tidak bisa dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-btn delete" title="Hapus">
                                            <i class="material-icons">delete</i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">Belum ada riwayat upload.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</div>

@includeIf('partials.upload-progress-widget')

</body>
</html>