<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | SIGINJAI PLN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    <style>
        :root{
            --navy-950: #0a1929;
            --navy-900: #0d2137;
            --navy-800: #153459;
            --pln-blue: #0B5EA8;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-700: #334155;
            --slate-900: #0f172a;
        }

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Inter', 'Segoe UI', Arial, sans-serif;
        }

        body{
            background:var(--slate-50);
            color:var(--slate-900);
            min-height:100vh;
            overflow-x:hidden;
        }

        /* ================= TOP NAVBAR (GAYA SIMERAH KOJA) ================= */

        .topbar{
            position:sticky;
            top:0;
            z-index:120;
            height:62px;
            background:var(--navy-900);
            border-bottom:1px solid rgba(255,255,255,.08);
            padding:0 24px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
        }

        .topbar-left{
            display:flex;
            align-items:center;
            gap:12px;
            min-width:0;
        }

        .topbar-menu-btn{
            display:none;
            width:36px;
            height:36px;
            border-radius:8px;
            border:1px solid rgba(255,255,255,.15);
            background:rgba(255,255,255,.07);
            color:#fff;
            cursor:pointer;
            align-items:center;
            justify-content:center;
            font-size:15px;
            transition:.2s;
        }

        .topbar-menu-btn:hover{
            background:rgba(255,255,255,.14);
        }

        .topbar-brand{
            display:flex;
            align-items:center;
            gap:10px;
            text-decoration:none;
            color:#fff;
        }

        .topbar-brand-badge{
            width:34px;
            height:34px;
            border-radius:9px;
            background:rgba(255,255,255,.1);
            border:1px solid rgba(255,255,255,.14);
            display:flex;
            align-items:center;
            justify-content:center;
            padding:4px;
            flex-shrink:0;
        }

        .topbar-brand-badge img{
            width:100%;
            height:100%;
            object-fit:contain;
        }

        .topbar-brand-text{
            display:flex;
            flex-direction:column;
            line-height:1.15;
        }

        .topbar-brand-name{
            font-size:14.5px;
            font-weight:800;
            letter-spacing:.04em;
            color:#ffffff;
        }

        .topbar-brand-sub{
            font-size:10px;
            font-weight:500;
            color:var(--slate-400);
            letter-spacing:.05em;
        }

        .topbar-right{
            display:flex;
            align-items:center;
            gap:12px;
            flex-shrink:0;
        }

        .topbar-user-pill{
            display:flex;
            align-items:center;
            gap:10px;
            background:rgba(255,255,255,.07);
            border:1px solid rgba(255,255,255,.12);
            border-radius:999px;
            padding:4px 14px 4px 5px;
        }

        .topbar-user-avatar{
            width:30px;
            height:30px;
            border-radius:50%;
            background:#e2e8f0;
            color:var(--navy-900);
            font-size:12.5px;
            font-weight:800;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-shrink:0;
        }

        .topbar-user-info{
            display:flex;
            flex-direction:column;
            line-height:1.2;
        }

        .topbar-user-name{
            font-size:12.5px;
            font-weight:600;
            color:#f8fafc;
            max-width:160px;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }

        .topbar-user-role{
            font-size:10.5px;
            color:var(--slate-400);
        }

        .btn-keluar{
            display:inline-flex;
            align-items:center;
            gap:7px;
            background:#ffffff;
            color:var(--slate-900);
            border:1px solid rgba(255,255,255,.2);
            border-radius:10px;
            padding:8px 15px;
            font-size:13px;
            font-weight:600;
            text-decoration:none;
            cursor:pointer;
            transition:all .18s ease;
            box-shadow:0 1px 3px rgba(0,0,0,.15);
        }

        .btn-keluar:hover{
            background:#f1f5f9;
            transform:translateY(-1px);
            box-shadow:0 4px 10px rgba(0,0,0,.2);
        }

        .btn-keluar i{
            font-size:13px;
            color:var(--slate-700);
        }

        /* ================= LAYOUT & SIDEBAR PUTIH ================= */

        .wrapper{
            display:flex;
            min-height:calc(100vh - 62px);
        }

        .sidebar{
            width:265px;
            flex-shrink:0;
            height:calc(100vh - 62px);
            position:sticky;
            top:62px;
            align-self:flex-start;
            overflow-y:auto;
            background:#ffffff;
            border-right:1px solid var(--slate-200);
            padding:16px 0 28px;
        }

        .sidebar-backdrop{
            display:none;
        }

        .sidebar-section-label{
            padding:16px 20px 6px;
            font-size:10.5px;
            font-weight:700;
            letter-spacing:.08em;
            text-transform:uppercase;
            color:var(--slate-400);
        }

        .sidebar ul{
            list-style:none;
            padding:0;
            margin:0;
        }

        .sidebar-link{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin:2px 12px;
            padding:10px 14px;
            border-radius:10px;
            color:var(--slate-700);
            text-decoration:none;
            font-size:13.5px;
            font-weight:500;
            transition:all .16s ease;
        }

        .sidebar-link span{
            display:inline-flex;
            align-items:center;
            gap:10px;
        }

        .sidebar-link span i{
            width:16px;
            text-align:center;
            font-size:13.5px;
            color:var(--slate-500);
        }

        .sidebar-link:hover{
            background:var(--slate-100);
            color:var(--navy-900);
        }

        .sidebar-link:hover span i{
            color:var(--navy-900);
        }

        .sidebar-link.active{
            background:var(--navy-900);
            color:#ffffff;
            font-weight:600;
            box-shadow:0 4px 12px rgba(13, 33, 55, 0.16);
        }

        .sidebar-link.active span i{
            color:#ffffff;
        }

        .sidebar-link.logout-danger{
            color:#b91c1c;
        }

        .sidebar-link.logout-danger span i{
            color:#dc2626;
        }

        .sidebar-link.logout-danger:hover{
            background:#fef2f2;
            color:#991b1b;
        }

        /* ================= MAIN CONTENT ================= */

        .content{
            flex:1;
            min-width:0;
            padding:28px 34px 48px;
        }

        .page-header{
            margin-bottom:20px;
        }

        .page-header h1{
            font-size:26px;
            font-weight:700;
            color:var(--slate-900);
            letter-spacing:-0.02em;
            margin-bottom:4px;
        }

        .page-header p{
            font-size:13.5px;
            color:var(--slate-500);
        }

        /* HERO BANNER NAVY */
        .welcome-banner{
            background:linear-gradient(135deg, #0d2137 0%, #153459 100%);
            color:#ffffff;
            padding:26px 28px;
            border-radius:16px;
            box-shadow:0 12px 28px -8px rgba(13, 33, 55, 0.28);
            border:1px solid rgba(255,255,255,.08);
            margin-bottom:26px;
        }

        .hero-date-pill{
            display:inline-flex;
            align-items:center;
            gap:8px;
            background:rgba(255,255,255,.1);
            border:1px solid rgba(255,255,255,.14);
            border-radius:999px;
            padding:5px 12px;
            font-size:12px;
            font-weight:500;
            color:#e2e8f0;
            margin-bottom:14px;
        }

        .welcome-banner h2{
            font-size:25px;
            font-weight:700;
            letter-spacing:-0.02em;
            color:#ffffff;
            margin-bottom:6px;
        }

        .welcome-banner p{
            font-size:13.5px;
            color:#cbd5e1;
            line-height:1.5;
        }

        .hero-modules{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(195px, 1fr));
            gap:12px;
            margin-top:22px;
        }

        .hero-module-card{
            background:rgba(255,255,255,.07);
            border:1px solid rgba(255,255,255,.11);
            border-radius:12px;
            padding:13px 14px;
            display:flex;
            align-items:flex-start;
            gap:12px;
            color:#ffffff;
            text-decoration:none;
            transition:all .18s ease;
        }

        .hero-module-card:hover{
            background:rgba(255,255,255,.13);
            border-color:rgba(255,255,255,.22);
            transform:translateY(-2px);
        }

        .hero-module-icon{
            width:34px;
            height:34px;
            border-radius:9px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:14px;
            color:#ffffff;
            flex-shrink:0;
        }

        .hi-blue{ background:#2563eb; }
        .hi-emerald{ background:#10b981; }
        .hi-amber{ background:#d97706; }
        .hi-purple{ background:#7c3aed; }

        .hero-module-body{
            min-width:0;
            flex:1;
        }

        .hero-module-title{
            font-size:13px;
            font-weight:600;
            color:#f8fafc;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:6px;
        }

        .hero-module-title i{
            font-size:10px;
            color:var(--slate-400);
        }

        .hero-module-desc{
            font-size:11.5px;
            color:#94a3b8;
            margin-top:3px;
            line-height:1.35;
        }

        /* SECTION HEADER */
        .section-bar{
            display:flex;
            align-items:center;
            justify-content:space-between;
            flex-wrap:wrap;
            gap:10px;
            margin-bottom:14px;
        }

        .section-bar-left{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .section-bar-icon{
            width:30px;
            height:30px;
            border-radius:8px;
            background:#ffffff;
            border:1px solid var(--slate-200);
            display:flex;
            align-items:center;
            justify-content:center;
            color:var(--navy-900);
            font-size:13px;
        }

        .section-bar-title{
            font-size:15px;
            font-weight:700;
            color:var(--slate-900);
        }

        .section-bar-meta{
            font-size:12px;
            color:var(--slate-500);
        }

        /* KARTU STATISTIK GAYA SIMERAH KOJA */
        .stats-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(260px, 1fr));
            gap:16px;
        }

        .stat-card{
            background:#ffffff;
            border-radius:14px;
            padding:20px 22px;
            border:1px solid var(--slate-200);
            box-shadow:0 1px 3px rgba(15, 23, 42, 0.05);
            transition:all .18s ease;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            gap:16px;
        }

        .stat-card:hover{
            transform:translateY(-2px);
            border-color:var(--slate-300);
            box-shadow:0 10px 24px -8px rgba(15, 23, 42, 0.12);
        }

        .stat-card-top{
            display:flex;
            align-items:center;
            justify-content:space-between;
        }

        .stat-icon{
            width:38px;
            height:38px;
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:15px;
        }

        .si-navy{ background:#eff6ff; color:#1d4ed8; }
        .si-emerald{ background:#ecfdf5; color:#059669; }
        .si-amber{ background:#fffbeb; color:#d97706; }

        .stat-badge{
            font-size:11px;
            font-weight:600;
            color:var(--slate-500);
            background:var(--slate-100);
            padding:4px 9px;
            border-radius:999px;
        }

        .stat-card .stat-value{
            color:var(--slate-900);
            font-size:30px;
            font-weight:800;
            letter-spacing:-0.02em;
            line-height:1.15;
            margin-bottom:4px;
        }

        .stat-card .stat-value.text-md{
            font-size:20px;
            font-weight:700;
        }

        .stat-card .stat-label{
            color:var(--slate-700);
            font-size:13.5px;
            font-weight:600;
        }

        .stat-card .stat-sub{
            color:var(--slate-400);
            font-size:12px;
            margin-top:2px;
        }

        /* RESPONSIF */
        @media (max-width: 960px){
            .topbar{ padding:0 16px; }
            .topbar-menu-btn{ display:inline-flex; }
            .topbar-user-info{ display:none; }
            .topbar-user-pill{ padding:4px; }
            .sidebar{
                position:fixed;
                top:62px;
                left:0;
                bottom:0;
                height:calc(100vh - 62px);
                z-index:115;
                transform:translateX(-100%);
                transition:transform .25s ease;
                box-shadow:0 16px 32px rgba(15, 23, 42, 0.2);
            }
            .sidebar.mobile-open{
                transform:translateX(0);
            }
            .sidebar-backdrop.show{
                display:block;
                position:fixed;
                inset:62px 0 0 0;
                background:rgba(15, 23, 42, 0.45);
                z-index:110;
            }
            .content{
                padding:20px 18px 40px;
            }
        }

        @media (max-width: 560px){
            .welcome-banner{ padding:20px; }
            .welcome-banner h2{ font-size:20px; }
        }
    </style>
</head>
<body>

@php
    $adminName    = session('nama') ?? 'Administrator';
    $adminInitial = strtoupper(substr(trim($adminName), 0, 1)) ?: 'A';

    $nowWib   = new \DateTime('now', new \DateTimeZone('Asia/Jakarta'));
    $hariMap  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulanMap = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $tanggalIndo = $hariMap[(int) $nowWib->format('w')] . ', ' . $nowWib->format('j') . ' ' . $bulanMap[(int) $nowWib->format('n')] . ' ' . $nowWib->format('Y');

    $jam = (int) $nowWib->format('G');
    if ($jam >= 4 && $jam < 11) {
        $salamWaktu = 'pagi';
    } elseif ($jam >= 11 && $jam < 15) {
        $salamWaktu = 'siang';
    } elseif ($jam >= 15 && $jam < 18) {
        $salamWaktu = 'sore';
    } else {
        $salamWaktu = 'malam';
    }
@endphp

<header class="topbar">
    <div class="topbar-left">
        <button type="button" class="topbar-menu-btn" id="mobileSidebarToggle" aria-label="Buka menu navigasi">
            <i class="fa fa-bars"></i>
        </button>

        <a href="{{ route('admin.dashboard') }}" class="topbar-brand">
            <div class="topbar-brand-badge">
                <img src="{{ asset('images/Logo_PLN.png') }}" alt="Logo PLN">
            </div>
            <div class="topbar-brand-text">
                <span class="topbar-brand-name">SIGINJAI</span>
                <span class="topbar-brand-sub">Sistem Gerbang Informasi Jasa , Anggaran &amp; Investasi</span>
            </div>
        </a>
    </div>

    <div class="topbar-right">
        <div class="topbar-user-pill" title="{{ $adminName }}">
            <div class="topbar-user-avatar">{{ $adminInitial }}</div>
            <div class="topbar-user-info">
                <span class="topbar-user-name">{{ $adminName }}</span>
                <span class="topbar-user-role">Administrator</span>
            </div>
        </div>

        <a href="{{ route('logout.admin') }}" class="btn-keluar" title="Keluar dari Panel Admin">
            <i class="fa fa-right-from-bracket"></i>
            <span>Keluar</span>
        </a>
    </div>
</header>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<div class="wrapper">
    <!-- SIDEBAR ADMIN -->
    <aside class="sidebar" id="adminSidebar">
        <ul>
            <li>
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link active">
                    <span><i class="fa fa-home"></i> Dashboard Utama</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Modul Monitoring</div>
        <ul>
            <li>
                <a href="{{ url('/input-data') }}" class="sidebar-link">
                    <span><i class="fa fa-cloud-arrow-up"></i> Upload Monitoring</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.standar-konstruksi.index') }}" class="sidebar-link">
                    <span><i class="fa fa-images"></i> Upload Standar Konstruksi</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-section-label">Informasi &amp; Kajian</div>
        <ul>
            <li>
                <a href="{{ route('admin.kajian-kelayakan.index') }}" class="sidebar-link">
                    <span><i class="fa fa-calculator"></i> Kajian Kelayakan Proyek</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/riwayat-upload') }}" class="sidebar-link">
                    <span><i class="fa fa-clock-rotate-left"></i> Riwayat Upload</span>
                </a>
            </li>
            @if (Route::has('admin.anggota.index'))
                <li>
                    <a href="{{ route('admin.anggota.index') }}" class="sidebar-link">
                        <span><i class="fa fa-users-gear"></i> Manajemen Akun</span>
                    </a>
                </li>
            @endif
        </ul>
    </aside>

    <!-- CONTENT DASHBOARD -->
    <main class="content">

        <div class="page-header">
            <h1>Ruang Kerja Administrator</h1>
            <p>Kelola data monitoring, riwayat unggahan Excel, standar konstruksi, dan akun pengguna UP3 Jambi.</p>
        </div>

        <div class="welcome-banner">
            <div class="hero-date-pill">
                <i class="fa fa-calendar-days"></i>
                <span>{{ $tanggalIndo }}</span>
            </div>

            <h2>Selamat {{ $salamWaktu }}, {{ $adminName }}.</h2>
            <p>Anda memiliki kendali penuh untuk memantau dan memperbarui seluruh data sistem monitoring SIGINJAI.</p>

            <div class="hero-modules">
                <a href="{{ url('/input-data') }}" class="hero-module-card">
                    <div class="hero-module-icon hi-blue">
                        <i class="fa fa-cloud-arrow-up"></i>
                    </div>
                    <div class="hero-module-body">
                        <div class="hero-module-title">
                            <span>Upload Monitoring</span>
                            <i class="fa fa-arrow-right"></i>
                        </div>
                        <div class="hero-module-desc">Impor file Excel sheet KHS, Tiang, &amp; Pelanggan</div>
                    </div>
                </a>

                <a href="{{ route('admin.standar-konstruksi.index') }}" class="hero-module-card">
                    <div class="hero-module-icon hi-amber">
                        <i class="fa fa-images"></i>
                    </div>
                    <div class="hero-module-body">
                        <div class="hero-module-title">
                            <span>Upload Standar Konstruksi</span>
                            <i class="fa fa-arrow-right"></i>
                        </div>
                        <div class="hero-module-desc">Unggah &amp; kelola katalog panduan gambar teknis</div>
                    </div>
                </a>

                <a href="{{ route('admin.kajian-kelayakan.index') }}" class="hero-module-card">
                    <div class="hero-module-icon hi-emerald">
                        <i class="fa fa-calculator"></i>
                    </div>
                    <div class="hero-module-body">
                        <div class="hero-module-title">
                            <span>Kajian Kelayakan Proyek</span>
                            <i class="fa fa-arrow-right"></i>
                        </div>
                        <div class="hero-module-desc">Lengkapi kebutuhan URAI, kelayakan &amp; kuota kontrak</div>
                    </div>
                </a>

                <a href="{{ url('/riwayat-upload') }}" class="hero-module-card">
                    <div class="hero-module-icon hi-emerald">
                        <i class="fa fa-clock-rotate-left"></i>
                    </div>
                    <div class="hero-module-body">
                        <div class="hero-module-title">
                            <span>Riwayat Upload</span>
                            <i class="fa fa-arrow-right"></i>
                        </div>
                        <div class="hero-module-desc">Lacak log unggahan &amp; kelola dataset aktif</div>
                    </div>
                </a>

                @if (Route::has('admin.anggota.index'))
                    <a href="{{ route('admin.anggota.index') }}" class="hero-module-card">
                        <div class="hero-module-icon hi-purple">
                            <i class="fa fa-users-gear"></i>
                        </div>
                        <div class="hero-module-body">
                            <div class="hero-module-title">
                                <span>Manajemen Akun</span>
                                <i class="fa fa-arrow-right"></i>
                            </div>
                            <div class="hero-module-desc">Atur akun pengguna dan hak akses portal</div>
                        </div>
                    </a>
                @endif
            </div>
        </div>

        <div class="section-bar">
            <div class="section-bar-left">
                <div class="section-bar-icon"><i class="fa fa-database"></i></div>
                <div class="section-bar-title">Ringkasan Data Sistem</div>
            </div>
            <div class="section-bar-meta">Data langsung dari database aktif</div>
        </div>

        <!-- 3 KARTU STATISTIK -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-top">
                    <div class="stat-icon si-navy"><i class="fa fa-table-list"></i></div>
                    <span class="stat-badge">Sheet Data</span>
                </div>
                <div>
                    <div class="stat-value">{{ number_format($totalDataMonitoring, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Data Monitoring</div>
                    <div class="stat-sub">Baris data KHS, Tiang &amp; Pelanggan tersimpan</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-top">
                    <div class="stat-icon si-emerald"><i class="fa fa-building"></i></div>
                    <span class="stat-badge">DETAIL KR 2026</span>
                </div>
                <div>
                    <div class="stat-value">{{ number_format($totalVendor, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Vendor</div>
                    <div class="stat-sub">Vendor pelaksana aktif terdata pada sheet</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-top">
                    <div class="stat-icon si-amber"><i class="fa fa-clock"></i></div>
                    <span class="stat-badge">WIB</span>
                </div>
                <div>
                    {{--
                        $lastUpdated berasal dari Upload::max('created_at') (aksi upload/import
                        terakhir yang berhasil - lihat DashboardController::adminIndex()),
                        tersimpan UTC karena config('app.timezone') = UTC. Di-parse eksplisit
                        sebagai UTC lalu dikonversi ke Asia/Jakarta (WIB) saat ditampilkan.
                        Jika belum ada upload berhasil, tampil "Belum ada update".
                    --}}
                    <div class="stat-value text-md">
                        {{ $lastUpdated ? \Illuminate\Support\Carbon::parse($lastUpdated, 'UTC')->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') : 'Belum ada update' }}
                    </div>
                    <div class="stat-label">Update Terakhir</div>
                    <div class="stat-sub">Waktu impor data Excel terakhir yang berhasil</div>
                </div>
            </div>
        </div>

    </main>
</div>

<script>
(function(){
    var toggleBtn = document.getElementById('mobileSidebarToggle');
    var sidebar   = document.getElementById('adminSidebar');
    var backdrop  = document.getElementById('sidebarBackdrop');

    if (!toggleBtn || !sidebar || !backdrop) return;

    toggleBtn.addEventListener('click', function(){
        var isOpen = sidebar.classList.toggle('mobile-open');
        backdrop.classList.toggle('show', isOpen);
    });

    backdrop.addEventListener('click', function(){
        sidebar.classList.remove('mobile-open');
        backdrop.classList.remove('show');
    });
})();
</script>

@includeIf('partials.upload-progress-widget')
@includeIf('partials.auth-popup')

</body>
</html>
