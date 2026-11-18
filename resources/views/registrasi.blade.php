<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi | SIGINJAI PLN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    @vite('resources/css/registrasi.css')
</head>
<body>

<div class="container">
    <div class="registrasi-container">

        <!-- BAGIAN KIRI (FOTO & BRANDING ASLI) -->
        <div class="left-panel">
            <img src="{{ asset('images/login.jpg') }}" class="bg-image" alt="PLN">
            <div class="overlay"></div>
            <div class="left-content">
                <div class="brand-badge">
                    <img src="{{ asset('images/logo-siginjai.png') }}" alt="Logo Tugu Keris Siginjai">
                </div>
                <div class="brand-logo">SIGINJAI</div>
                <p class="brand-tagline">
                    Sistem Gerbang Informasi Jasa , Anggaran &amp; Investasi
                </p>
                <p class="brand-org">PLN UP3 Jambi</p>
            </div>
        </div>

        <!-- BAGIAN KANAN -->
        <div class="right-panel">
            <div class="registrasi-box">
                <div class="login-header">
                    <h1>Daftar Akun Baru</h1>
                    <p class="login-subtitle">Buat akun pengguna untuk mengakses portal monitoring perencanaan UP3 Jambi.</p>
                </div>

                @if ($errors->any())
                    <div class="alert">
                        <i class="fa fa-circle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form action="{{ route('register.store') }}" method="POST">
                    @csrf

                    <div class="form-field">
                        <label for="namaInput">Nama Lengkap</label>
                        <div class="input-group">
                            <i class="fa fa-id-card input-icon"></i>
                            <input
                                id="namaInput"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Masukkan Nama Lengkap"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="usernameInput">Username</label>
                        <div class="input-group">
                            <i class="fa fa-user input-icon"></i>
                            <input
                                id="usernameInput"
                                type="text"
                                name="username"
                                value="{{ old('username') }}"
                                placeholder="Masukkan Username"
                                autocomplete="username"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="passwordInput">Password</label>
                        <div class="input-group">
                            <i class="fa fa-lock input-icon"></i>
                            <input
                                id="passwordInput"
                                type="password"
                                name="password"
                                placeholder="Masukkan Password"
                                autocomplete="new-password"
                                required
                            >
                            <button type="button" class="toggle-password" onclick="toggleRegPassword('passwordInput', this)" aria-label="Tampilkan atau sembunyikan password">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-register">
                        <i class="fa fa-user-plus"></i>
                        <span>Daftar Akun</span>
                    </button>
                </form>

                <div class="login-link">
                    Sudah memiliki akun? <a href="{{ route('login.view') }}">Masuk ke Sistem</a>
                </div>

                <div class="login-footer">
                    &copy; {{ date('Y') }} PT PLN (Persero) UP3 Jambi &bull; SIGINJAI
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function toggleRegPassword(inputId, btn){
    const input = document.getElementById(inputId);
    const icon = btn ? btn.querySelector('i') : null;
    if(!input) return;
    if(input.type === 'password'){
        input.type = 'text';
        if(icon){ icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
    } else {
        input.type = 'password';
        if(icon){ icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
    }
}
</script>
</body>
</html>
