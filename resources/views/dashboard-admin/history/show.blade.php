<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Upload - Riwayat</title>
    
    <!-- Tetap muat CSS bawaan untuk styling general jika ada -->
    @vite('resources/css/admin/dashboard_admin.css')
    
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* =====================================================
           TEMA DARK MODE & RESET UI (KONSISTEN)
        ===================================================== */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Instrument Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #0f172a; color: #e2e8f0; display: flex; min-height: 100vh; overflow-x: hidden; }
        
        /* LAYOUT UTAMA */
        .wrapper { display: flex; width: 100%; }
        /* Margin-left 250px penting agar konten tidak tertutup sidebar yang fixed */
        .content { flex: 1; padding: 40px; margin-left: 250px; } 
        
        /* =====================================================
           STYLE SIDEBAR (DISALIN DARI SUMBER 4 AGAR SAMA PERSIS)
        ===================================================== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 250px;
            min-width: 250px;
            height: 100vh;
            background-color: #0f172a; /* Tambahan: Pastikan background sidebar sama dengan body */
            border-right: 1px solid rgba(255,255,255,.08); /* Tambahan batas pinggir */
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .logo {
            padding: 20px;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .sidebar-logo-pln {
            max-width: 70px;
            width: 100%;
            height: auto;
            object-fit: contain;
            margin: 0 auto;
            display: block;
        }

        .sidebar ul {
            list-style: none; /* Hilangkan bullet point default */
            padding: 0;
            margin: 0;
            flex: 0 0 auto;
        }
        
        .sidebar ul li {
            padding: 0;
            margin: 0;
        }

        .sidebar-link {
            display: block;
            width: 100%;
            padding: 15px 25px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: .3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-link:hover, .sidebar-link.active {
            background: linear-gradient(
                90deg,
                rgba(14, 165, 233, 0.15) 0%,
                rgba(14, 165, 233, 0.05) 100%
            );
            color: #38bdf8;
            border-left-color: #0ea5e9;
        }

        .sidebar-link span {
            display: block;
        }

        .logout-menu {
            margin-top: auto;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        .logout-menu a {
            color: #ef4444;
        }

        .logout-menu a:hover {
            background: rgba(239, 68, 68, 0.1);
            border-left-color: #ef4444;
        }

        /* =====================================================
           CARD & CONTAINERS (KONTEN UTAMA)
        ===================================================== */
        .card { 
            background-color: #1e293b; border-radius: 12px; padding: 30px; 
            border: 1px solid rgba(255,255,255,.08); 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); 
        }
        
        /* HEADER & NAVIGATION */
        .back-link {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; margin-bottom: 24px;
            background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px; color: #94a3b8; text-decoration: none;
            font-size: 14px; font-weight: 500; transition: all 0.2s ease;
        }
        .back-link:hover { background: rgba(14, 165, 233, 0.1); color: #38bdf8; border-color: #0ea5e9; }
        .back-link .material-icons { font-size: 18px; }

        .header-title { margin-bottom: 25px; border-bottom: 1px solid rgba(255,255,255,.08); padding-bottom: 20px; }
        .header-title h2 { color: #f8fafc; font-size: 22px; font-weight: 600; line-height: 1.4; word-break: break-all; margin-bottom: 16px; }
        
        /* METADATA INFO BOX (Pengganti teks paragraf biasa) */
        .meta-box {
            display: flex; flex-wrap: wrap; gap: 24px;
            background: rgba(15, 23, 42, 0.5); border-radius: 8px; padding: 16px 20px;
            border: 1px solid rgba(255,255,255,0.04);
        }
        .meta-item { display: flex; flex-direction: column; gap: 4px; }
        .meta-label { font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; }
        .meta-value { font-size: 14px; color: #e2e8f0; font-weight: 500; }
        
        /* TABEL KONSISTEN */
        .table-container { 
            overflow-x: auto; margin-top: 30px; border-radius: 8px; 
            border: 1px solid rgba(255,255,255,.08); background-color: #0f172a; 
        }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { 
            background-color: rgba(255,255,255,0.02); color: #94a3b8; font-weight: 600; 
            font-size: 13px; text-transform: uppercase; padding: 16px 20px; 
            border-bottom: 1px solid rgba(255,255,255,.08); letter-spacing: 0.5px; 
        }
        td { padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,.04); color: #cbd5e1; font-size: 14px; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: rgba(255, 255, 255, 0.02); }

        /* BADGES */
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; display: inline-block; }
        .badge-live { background: rgba(34,197,94,.12); color: #4ade80; border: 1px solid rgba(34,197,94,.2); }
        .badge-arsip { background: rgba(148,163,184,.15); color: #94a3b8; border: 1px solid rgba(148,163,184,.2); }
        
        /* RESPONSIVE AJUSTMENTS */
        @media (max-width: 768px) {
            .content { margin-left: 0; padding: 20px; }
            .meta-box { flex-direction: column; gap: 12px; }
            .sidebar { transform: translateX(-100%); } /* Sembunyikan sidebar di mobile untuk sementara */
        }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- Memuat Sidebar Secara Dinamis (Pastikan file ini hanya berisi HTML tag <aside> sampai </aside>) -->
    @include('partials.admin-sidebar', ['active' => 'riwayat-upload'])

    <main class="content">
        <div class="card">
            <!-- Tombol Kembali UI/UX Friendly -->
            <a href="/riwayat-upload" class="back-link">
                <i class="material-icons">arrow_back</i>
                Kembali ke Riwayat Upload
            </a>

            <div class="header-title">
                <h2>{{ $upload->nama_files }}</h2>
                
                <!-- Meta Info Box: Rapi, scannable, dan profesional -->
                <div class="meta-box">
                    <div class="meta-item">
                        <span class="meta-label">Jenis Monitoring</span>
                        <span class="meta-value">{{ strtoupper($upload->jenis_monitoring ?? '-') }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Waktu Upload</span>
                        <span class="meta-value">{{ \Carbon\Carbon::parse($upload->created_at)->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Status Keseluruhan</span>
                        <span class="meta-value" style="text-transform: capitalize;">{{ $upload->status }}</span>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th width="35%">Nama Sheet</th>
                            <th width="25%">Kategori</th>
                            <th width="10%">Tahun</th>
                            <th width="15%" style="text-align: right;">Total Baris</th>
                            <th width="15%" style="text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sheets as $sheet)
                            <tr>
                                <td style="font-weight: 500; color: #f8fafc;">{{ $sheet->nama_sheet }}</td>
                                <td>{{ $sheet->kategori }}</td>
                                <td>{{ $sheet->tahun }}</td>
                                <!-- Rata kanan untuk kemudahan membaca nominal/angka -->
                                <td style="text-align: right; font-variant-numeric: tabular-nums;">{{ $sheet->total_rows }}</td>
                                <td style="text-align: center;">
                                    @if ($sheet->is_active)
                                        <span class="badge badge-live">Aktif</span>
                                    @else
                                        <span class="badge badge-arsip">Arsip</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: #64748b; padding: 50px 0;">
                                    <i class="material-icons" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px; display: block;">find_in_page</i>
                                    Tidak ada sheet ditemukan dalam dokumen ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</body>
</html>