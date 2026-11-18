@php
    $authUser    = auth()->user();
    $userName    = $authUser->name ?? session('nama') ?? 'Pengguna PLN';
    $userRoleRaw = (string) ($authUser->role ?? (session()->has('admin') ? 'Administrator' : 'User Perencanaan'));
    $userRole    = strtolower($userRoleRaw) === 'user' ? 'User Perencanaan' : ucfirst($userRoleRaw);
    $userInitial = strtoupper(substr(trim($userName), 0, 1)) ?: 'P';
    $logoutHref  = session()->has('admin') && !$authUser ? route('logout.admin') : route('logout');
    $topSubTitle = $topSubTitle ?? 'UP3 JAMBI • MONITORING PERENCANAAN';
@endphp

<style>
    .simerah-topbar{
        position:sticky;
        top:0;
        z-index:120;
        height:62px;
        background:#0d2137;
        border-bottom:1px solid rgba(255,255,255,.08);
        padding:0 24px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:16px;
        font-family:'Inter', 'Segoe UI', Arial, sans-serif;
    }

    .simerah-topbar-left{
        display:flex;
        align-items:center;
        gap:12px;
        min-width:0;
    }

    .simerah-topbar-brand{
        display:flex;
        align-items:center;
        gap:10px;
        text-decoration:none;
        color:#ffffff;
    }

    .simerah-topbar-badge{
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

    .simerah-topbar-badge img{
        width:100%;
        height:100%;
        object-fit:contain;
    }

    .simerah-topbar-text{
        display:flex;
        flex-direction:column;
        line-height:1.15;
    }

    .simerah-topbar-name{
        font-size:14.5px;
        font-weight:800;
        letter-spacing:.04em;
        color:#ffffff;
    }

    .simerah-topbar-sub{
        font-size:10px;
        font-weight:500;
        color:#94a3b8;
        letter-spacing:.05em;
    }

    .simerah-topbar-right{
        display:flex;
        align-items:center;
        gap:12px;
        flex-shrink:0;
    }

    .simerah-user-pill{
        display:flex;
        align-items:center;
        gap:10px;
        background:rgba(255,255,255,.07);
        border:1px solid rgba(255,255,255,.12);
        border-radius:999px;
        padding:4px 14px 4px 5px;
    }

    .simerah-user-avatar{
        width:30px;
        height:30px;
        border-radius:50%;
        background:#e2e8f0;
        color:#0d2137;
        font-size:12.5px;
        font-weight:800;
        display:flex;
        align-items:center;
        justify-content:center;
        flex-shrink:0;
    }

    .simerah-user-info{
        display:flex;
        flex-direction:column;
        line-height:1.2;
    }

    .simerah-user-name{
        font-size:12.5px;
        font-weight:600;
        color:#f8fafc;
        max-width:170px;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .simerah-user-role{
        font-size:10.5px;
        color:#94a3b8;
    }

    .simerah-btn-keluar{
        display:inline-flex;
        align-items:center;
        gap:7px;
        background:#ffffff;
        color:#0f172a;
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

    .simerah-btn-keluar:hover{
        background:#f1f5f9;
        transform:translateY(-1px);
    }

    .simerah-menu-btn{
        display:none;
        align-items:center;
        justify-content:center;
        width:36px;
        height:36px;
        border-radius:9px;
        background:rgba(255,255,255,.1);
        border:1px solid rgba(255,255,255,.18);
        color:#ffffff;
        cursor:pointer;
        font-size:15px;
        flex-shrink:0;
    }

    @media (max-width: 768px){
        .simerah-menu-btn{ display:inline-flex; }
        .simerah-topbar{ padding:0 14px; }
        .simerah-user-info{ display:none; }
        .simerah-user-pill{ padding:4px; }
        .simerah-topbar-sub{ display:none; }
    }
</style>

<header class="simerah-topbar">
    <div class="simerah-topbar-left">
        @if(!empty($withSidebar))
        <button type="button" class="simerah-menu-btn" id="mobileSidebarToggle" aria-label="Buka menu navigasi">
            <i class="fa fa-bars"></i>
        </button>
        @endif
        <a href="{{ route('dashboard') }}" class="simerah-topbar-brand">
            <div class="simerah-topbar-badge">
                <img src="{{ asset('images/Logo_PLN.png') }}" alt="Logo PLN">
            </div>
            <div class="simerah-topbar-text">
                <span class="simerah-topbar-name">SIGINJAI</span>
                <span class="simerah-topbar-sub">Sistem Gerbang Informasi Jasa , Anggaran &amp; Investasi</span>
            </div>
        </a>
    </div>

    <div class="simerah-topbar-right">
        <div class="simerah-user-pill" title="{{ $userName }}">
            <div class="simerah-user-avatar">{{ $userInitial }}</div>
            <div class="simerah-user-info">
                <span class="simerah-user-name">{{ $userName }}</span>
                <span class="simerah-user-role">{{ $userRole }}</span>
            </div>
        </div>

        <a href="{{ $logoutHref }}" class="simerah-btn-keluar" title="Keluar dari SIGINJAI PLN">
            <i class="fa fa-right-from-bracket"></i>
            <span>Keluar</span>
        </a>
    </div>
</header>
