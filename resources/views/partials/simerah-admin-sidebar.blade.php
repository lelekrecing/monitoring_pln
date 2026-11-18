@php
    $activeAdmin = $activeAdmin ?? '';
@endphp

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<aside class="sidebar" id="adminSidebar">
    <ul>
        <li>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ $activeAdmin === 'dashboard' ? 'active' : '' }}">
                <span><i class="fa fa-home"></i> Dashboard Utama</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-section-label">Modul Monitoring</div>
    <ul>
        <li>
            <a href="{{ url('/input-data') }}" class="sidebar-link {{ $activeAdmin === 'upload' ? 'active' : '' }}">
                <span><i class="fa fa-cloud-arrow-up"></i> Upload Monitoring</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.standar-konstruksi.index') }}" class="sidebar-link {{ $activeAdmin === 'standar' ? 'active' : '' }}">
                <span><i class="fa fa-images"></i> Upload Standar Konstruksi</span>
            </a>
        </li>
    </ul>

    <div class="sidebar-section-label">Informasi &amp; Kajian</div>
    <ul>
        <li>
            <a href="{{ route('admin.kajian-kelayakan.index') }}" class="sidebar-link {{ $activeAdmin === 'kajian' ? 'active' : '' }}">
                <span><i class="fa fa-calculator"></i> Kajian Kelayakan Proyek</span>
            </a>
        </li>
        <li>
            <a href="{{ url('/riwayat-upload') }}" class="sidebar-link {{ $activeAdmin === 'riwayat' ? 'active' : '' }}">
                <span><i class="fa fa-clock-rotate-left"></i> Riwayat Upload</span>
            </a>
        </li>
        @if (Route::has('admin.anggota.index'))
            <li>
                <a href="{{ route('admin.anggota.index') }}" class="sidebar-link {{ $activeAdmin === 'akun' ? 'active' : '' }}">
                    <span><i class="fa fa-users-gear"></i> Manajemen Akun</span>
                </a>
            </li>
        @endif
    </ul>
</aside>

<script>
(function(){
    function initAdminDrawer(){
        var toggleBtn = document.getElementById('mobileSidebarToggle');
        var sidebar   = document.getElementById('adminSidebar');
        var backdrop  = document.getElementById('sidebarBackdrop');
        if (!toggleBtn || !sidebar || !backdrop) return;

        toggleBtn.addEventListener('click', function(){
            sidebar.classList.toggle('mobile-open');
            backdrop.classList.toggle('show', sidebar.classList.contains('mobile-open'));
        });

        backdrop.addEventListener('click', function(){
            sidebar.classList.remove('mobile-open');
            backdrop.classList.remove('show');
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminDrawer);
    } else {
        initAdminDrawer();
    }
})();
</script>
