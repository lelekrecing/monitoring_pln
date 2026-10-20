<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | PLN UP3 Jambi</title>

    <!-- CSS custom melalui Vite -->
    @vite('resources/css/admin/dashboard_admin.css')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* =====================================================
           TEMA DARK MODE & RESET UI (SINKRON DENGAN STANDAR KONSTRUKSI)
           Tanpa Bootstrap - layout memakai CSS Grid / Flexbox
        ===================================================== */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #0f172a; color: #e2e8f0; display: flex; min-height: 100vh; overflow-x: hidden; }

        /* SIDEBAR FIXED */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: 250px; min-width: 250px; height: 100vh;
            background-color: #0f172a;
            border-right: 1px solid rgba(255,255,255,.08);
            display: flex; flex-direction: column; overflow-y: auto; z-index: 100;
        }
        .logo { padding: 20px; text-align: center; margin-bottom: 10px; }
        .sidebar-logo-pln { max-width: 70px; width: 100%; height: auto; object-fit: contain; margin: 0 auto; display: block; }

        /* RESET UL & LI AGAR HOVER FULL WIDTH */
        .sidebar ul { list-style: none; padding: 0; margin: 0; flex: 0 0 auto; }
        .sidebar ul li { padding: 0; margin: 0; }
        .sidebar-link {
            display: block; width: 100%; padding: 15px 25px;
            color: #94a3b8; text-decoration: none; font-size: 15px;
            font-weight: 500; transition: .3s ease; border-left: 3px solid transparent;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: linear-gradient(90deg, rgba(14, 165, 233, 0.15) 0%, rgba(14, 165, 233, 0.05) 100%);
            color: #38bdf8; border-left-color: #0ea5e9;
        }
        .sidebar-link span { display: block; }

        .logout-menu { margin-top: auto; border-top: 1px solid rgba(255,255,255,.08); }
        .logout-menu a { color: #ef4444; }
        .logout-menu a:hover { background: rgba(239, 68, 68, 0.1); border-left-color: #ef4444; }

        /* LAYOUT UTAMA */
        .wrapper { display: flex; width: 100%; }
        .content { flex: 1; padding: 40px; margin-left: 250px; }

        /* HEADER */
        .header-title { margin-bottom: 30px; }
        .header-title h2 { color: #f8fafc; font-size: 26px; font-weight: 600; margin-bottom: 8px; }
        .header-title p { color: #94a3b8; font-size: 15px; }

        /* KARTU STATISTIK (CSS GRID) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }
        .stat-card {
            background-color: #1e293b;
            border-radius: 12px;
            padding: 30px 25px;
            border: 1px solid rgba(255,255,255,.08);
            border-left: 4px solid #0ea5e9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
            border-color: rgba(14, 165, 233, 0.3);
            border-left-color: #0ea5e9;
        }
        .stat-card .stat-label {
            color: #94a3b8; font-size: 14px; font-weight: 500;
            margin-bottom: 12px; text-transform: uppercase; letter-spacing: .5px;
        }
        .stat-card .stat-value { color: #f8fafc; font-size: 32px; font-weight: 700; line-height: 1.2; }
        .stat-card .stat-value.text-md { font-size: 22px; } /* khusus tanggal agar tidak terpotong */

        /* RESPONSIF */
        @media (max-width: 768px) {
            .sidebar { position: static; width: 100%; min-width: 0; height: auto; }
            body { flex-direction: column; }
            .content { margin-left: 0; padding: 20px; }
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
            <li class="active"><a href="{{ route('admin.dashboard') }}" class="sidebar-link active"><span>Dashboard</span></a></li>
            <li><a href="{{ url('/input-data') }}" class="sidebar-link"><span>Upload Monitoring</span></a></li>
            <li><a href="{{ url('/riwayat-upload') }}" class="sidebar-link"><span>Riwayat Upload</span></a></li>
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

    <!-- CONTENT DASHBOARD -->
    <main class="content">

        <div class="header-title">
            <h2>Selamat Datang, Administrator</h2>
            <p>Kelola sistem dan data monitoring melalui panel ini.</p>
        </div>

        <!-- 3 KARTU STATISTIK -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Data Monitoring</div>
                <div class="stat-value">{{ number_format($totalDataMonitoring, 0, ',', '.') }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Total Vendor</div>
                <div class="stat-value">{{ number_format($totalVendor, 0, ',', '.') }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Update Terakhir</div>
                <div class="stat-value text-md">
                    {{--
                        $lastUpdated berasal dari Upload::max('created_at') (aksi upload/import
                        terakhir yang berhasil - lihat DashboardController::adminIndex()),
                        tersimpan UTC karena config('app.timezone') = UTC. Di-parse eksplisit
                        sebagai UTC lalu dikonversi ke Asia/Jakarta (WIB) saat ditampilkan.
                        Jika belum ada upload berhasil, tampil "Belum ada update".
                    --}}
                    {{ $lastUpdated ? \Illuminate\Support\Carbon::parse($lastUpdated, 'UTC')->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : 'Belum ada update' }}
                </div>
            </div>
        </div>

    </main>
</div>

@includeIf('partials.upload-progress-widget')

</body>
</html>