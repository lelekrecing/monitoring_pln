@php
    $adminName    = session('nama') ?? 'Administrator';
    $adminInitial = strtoupper(substr(trim($adminName), 0, 1)) ?: 'A';
    $adminSub     = $topSubTitle ?? 'UP3 JAMBI • PANEL ADMINISTRATOR';
@endphp

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
        color:#fff;
    }

    .topbar-brand-sub{
        font-size:10px;
        font-weight:500;
        color:#94a3b8;
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
        max-width:170px;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .topbar-user-role{
        font-size:10.5px;
        color:#94a3b8;
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
    }

    /* SHELL & SIDEBAR PUTIH */
    .wrapper{
        display:flex;
        min-height:calc(100vh - 62px);
        width:100%;
    }

    .sidebar{
        width:265px;
        min-width:265px;
        flex-shrink:0;
        height:calc(100vh - 62px);
        position:sticky;
        top:62px;
        align-self:flex-start;
        overflow-y:auto;
        background:#ffffff;
        border-right:1px solid var(--slate-200);
        padding:16px 0 28px;
        z-index:100;
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
        border-left:none;
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

    /* MAIN CONTENT & CARD PUTIH SIMERAH KOJA */
    .content{
        flex:1;
        min-width:0;
        margin-left:0 !important;
        padding:26px 32px 48px !important;
    }

    .card{
        background:#ffffff !important;
        border:1px solid var(--slate-200) !important;
        border-radius:16px !important;
        padding:26px 28px !important;
        box-shadow:0 4px 16px rgba(15, 23, 42, 0.04) !important;
        color:var(--slate-900) !important;
    }

    .header-title{
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:16px;
        flex-wrap:wrap;
        margin-bottom:22px !important;
        padding:22px 26px !important;
        border-radius:14px !important;
        background:linear-gradient(135deg, #0d2137 0%, #153459 100%) !important;
        color:#ffffff !important;
        border-bottom:none !important;
        box-shadow:0 10px 24px -8px rgba(13, 33, 55, 0.25);
    }

    .header-title h2,
    .header-title h1{
        color:#ffffff !important;
        font-size:21px !important;
        font-weight:700 !important;
        letter-spacing:-0.01em;
        margin-bottom:4px !important;
    }

    .header-title p,
    .header-title small{
        color:#cbd5e1 !important;
        font-size:13px !important;
        display:block;
    }

    .alert-success,
    .success-box{
        background:#ecfdf5 !important;
        border:1px solid #a7f3d0 !important;
        border-left:4px solid #10b981 !important;
        color:#065f46 !important;
        padding:13px 16px !important;
        border-radius:10px !important;
        font-size:13.5px !important;
        margin-bottom:18px !important;
    }

    .alert-error,
    .error-box{
        background:#fef2f2 !important;
        border:1px solid #fecaca !important;
        border-left:4px solid #dc2626 !important;
        color:#991b1b !important;
        padding:13px 16px !important;
        border-radius:10px !important;
        font-size:13.5px !important;
        margin-bottom:18px !important;
    }

    .btn-primary,
    .btn-save{
        background:#0d2137 !important;
        color:#ffffff !important;
        border:1px solid #0d2137 !important;
        padding:10px 18px !important;
        border-radius:10px !important;
        font-size:13.5px !important;
        font-weight:600 !important;
        cursor:pointer;
        text-decoration:none;
        display:inline-flex;
        align-items:center;
        gap:8px;
        transition:.18s ease;
    }

    .btn-primary:hover,
    .btn-save:hover{
        background:#153459 !important;
        transform:translateY(-1px);
    }

    .btn-secondary,
    .btn-cancel{
        background:#ffffff !important;
        color:#334155 !important;
        border:1px solid #cbd5e1 !important;
        padding:10px 18px !important;
        border-radius:10px !important;
        font-size:13.5px !important;
        font-weight:600 !important;
        cursor:pointer;
        text-decoration:none;
        display:inline-flex;
        align-items:center;
        gap:8px;
        transition:.18s ease;
    }

    .btn-secondary:hover,
    .btn-cancel:hover{
        background:#f1f5f9 !important;
        color:#0f172a !important;
    }

    .toolbar input[type="text"],
    .toolbar select,
    .form-control{
        background:#f8fafc !important;
        color:#0f172a !important;
        border:1px solid #cbd5e1 !important;
        border-radius:10px !important;
        font-size:13.5px !important;
    }

    .toolbar input[type="text"]:focus,
    .toolbar select:focus,
    .form-control:focus{
        background:#ffffff !important;
        border-color:#0d2137 !important;
        box-shadow:0 0 0 3px rgba(13,33,55,.08) !important;
        outline:none !important;
    }

    table{
        width:100%;
        border-collapse:collapse;
    }

    table thead th{
        background:#0d2137 !important;
        color:#ffffff !important;
        font-size:12px !important;
        font-weight:600 !important;
        text-transform:uppercase;
        letter-spacing:.04em;
        padding:13px 15px !important;
        border-bottom:none !important;
    }

    table thead th:first-child{ border-top-left-radius:10px; }
    table thead th:last-child{ border-top-right-radius:10px; }

    table tbody td{
        padding:13px 15px !important;
        border-bottom:1px solid #e2e8f0 !important;
        color:#334155 !important;
        font-size:13.5px !important;
    }

    table tbody tr:hover{
        background:#f8fafc !important;
    }

    .upload-box{
        background:#f8fafc !important;
        border:2px dashed #cbd5e1 !important;
        border-radius:14px !important;
        padding:32px 20px !important;
    }

    .upload-box:hover{
        border-color:#0d2137 !important;
        background:#f1f5f9 !important;
    }

    .upload-box .material-icons{
        color:#0d2137 !important;
    }

    .upload-box p{
        color:#334155 !important;
        font-weight:600 !important;
    }

    .upload-section-title{
        color:#0f172a !important;
        font-weight:700 !important;
    }

    .upload-section-desc{
        color:#64748b !important;
    }

    .upload-divider{
        border-top:1px solid #e2e8f0 !important;
    }

    .modal-content{
        background:#ffffff !important;
        color:#0f172a !important;
        border:1px solid #e2e8f0 !important;
        border-radius:16px !important;
        box-shadow:0 24px 50px rgba(15,23,42,.22) !important;
    }

    .modal-header h3{
        color:#0f172a !important;
    }

    .modal-sub,
    .form-group label,
    .form-text{
        color:#475569 !important;
    }

    .total-info,
    .empty,
    .pagination{
        color:#64748b !important;
    }

    @media (max-width: 900px){
        .topbar{ padding:0 16px; }
        .topbar-menu-btn{ display:inline-flex; }
        .topbar-user-info,
        .topbar-brand-sub{ display:none; }
        .topbar-user-pill{ padding:4px; }
        .sidebar{
            position:fixed;
            left:0;
            top:62px;
            bottom:0;
            height:calc(100vh - 62px);
            z-index:220;
            transform:translateX(-105%);
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
            z-index:210;
        }
        .content{
            padding:18px 16px 40px !important;
        }
    }
</style>

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
