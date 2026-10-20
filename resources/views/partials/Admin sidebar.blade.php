{{--
    Sidebar admin bersama.
    Pemakaian: @include('partials.admin-sidebar', ['active' => 'dashboard'])
    Nilai $active: dashboard | upload | riwayat | standar | akun
--}}
@php $active = $active ?? ''; @endphp
<aside class="sidebar">
    <div class="logo">
        <img src="{{ asset('images/Logo_PLN.png') }}" alt="Logo PLN" class="sidebar-logo-pln">
    </div>

    <ul>
        <li><a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ $active === 'dashboard' ? 'active' : '' }}"><span>Dashboard</span></a></li>
        <li><a href="{{ url('/input-data') }}" class="sidebar-link {{ $active === 'upload' ? 'active' : '' }}"><span>Upload Monitoring</span></a></li>
        <li><a href="{{ url('/riwayat-upload') }}" class="sidebar-link {{ $active === 'riwayat' ? 'active' : '' }}"><span>Riwayat Upload</span></a></li>
        <li><a href="{{ route('admin.standar-konstruksi.index') }}" class="sidebar-link {{ $active === 'standar' ? 'active' : '' }}"><span>Standar Konstruksi</span></a></li>
        @if (Route::has('admin.anggota.index'))
            <li><a href="{{ route('admin.anggota.index') }}" class="sidebar-link {{ $active === 'akun' ? 'active' : '' }}"><span>Manajemen Akun</span></a></li>
        @endif
    </ul>

    <div class="logout-menu">
        <a href="{{ route('logout.admin') }}" class="sidebar-link">
            <span style="display:flex; align-items:center; gap:8px;"><i class="material-icons" style="font-size:18px;">logout</i> Logout</span>
        </a>
    </div>
</aside>