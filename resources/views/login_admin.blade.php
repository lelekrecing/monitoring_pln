<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | SIGINJAI PLN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

    @vite('resources/css/login.css')
</head>
<body>

<div class="container">
    <div class="login-container">

        <!-- BAGIAN KIRI -->
        <div class="left-panel">
            <img src="{{ asset('images/login.jpg') }}" class="bg-image" alt="PLN">
            <div class="overlay"></div>
            <div class="left-content">
                <div class="brand-badge">
                    <img src="{{ asset('images/logo-siginjai.png') }}" alt="Logo Tugu Keris Siginjai">
                </div>
                <span class="brand-eyebrow">ADMIN PANEL</span>
                <div class="brand-logo">SIGINJAI</div>
                <p class="brand-tagline">
                    Sistem Gerbang Informasi Jasa , Anggaran &amp; Investasi
                </p>
                <p class="brand-org">PLN UP3 Jambi</p>
            </div>
        </div>

        <!-- BAGIAN KANAN -->
        <div class="right-panel">
            <div class="login-box">
                <div class="login-header">
                    <div class="login-pln-badge">
                        <img src="{{ asset('images/Logo_PLN.png') }}" alt="Logo PLN">
                        <span>Administrator • UP3 Jambi</span>
                    </div>
                    <h1>Login Admin</h1>
                    <p class="login-subtitle">Masuk menggunakan email administrator dan password Anda.</p>
                </div>

                @if ($errors->any())
                    <div class="alert">
                        <i class="fa fa-circle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form action="{{ route('admin.login') }}" method="POST">
                    @csrf

                    <div class="form-field">
                        <label for="adminEmailInput">Email Admin</label>
                        <div class="input-group">
                            <i class="fa fa-envelope input-icon"></i>
                            <input
                                id="adminEmailInput"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Masukkan Email Admin"
                                autocomplete="email"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-field">
                        <label for="adminPasswordInput">Password</label>
                        <div class="input-group">
                            <i class="fa fa-lock input-icon"></i>
                            <input
                                id="adminPasswordInput"
                                type="password"
                                name="password"
                                placeholder="Masukkan Password"
                                autocomplete="current-password"
                                required
                            >
                            <button type="button" class="toggle-password" onclick="togglePasswordVisibility('adminPasswordInput', this)" aria-label="Tampilkan atau sembunyikan password">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fa fa-shield-halved"></i>
                        <span>Masuk sebagai Admin</span>
                    </button>
                </form>

                <a href="{{ route('login.view') }}" class="btn-admin">
                    <i class="fa fa-arrow-left"></i>
                    <span>Kembali ke Login User</span>
                </a>

                <div class="login-footer">
                    &copy; {{ date('Y') }} PT PLN (Persero) UP3 Jambi &bull; SIGINJAI
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, btn) {
    var input = document.getElementById(inputId);
    if (!input) return;
    var icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) { icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
    } else {
        input.type = 'password';
        if (icon) { icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
    }
}
</script>

@includeIf('partials.login-loading')
@includeIf('partials.auth-popup')
</body>
</html>
