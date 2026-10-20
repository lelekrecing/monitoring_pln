<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SIGINJAI</title>

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
                <div class="brand-logo">SIGINJAI</div>
                <p class="brand-tagline">
                    Sistem Gerbang Informasi Jasa , Anggaran & Investasi
                </p>
                <p class="brand-org">PLN UP3 Jambi</p>
            </div>
        </div>

        <!-- BAGIAN KANAN -->
        <div class="right-panel">
            <div class="login-box">
                <h1>Log In</h1>

                @if ($errors->any())
                    <div class="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <!-- KOREKSI: Ubah label dan input menjadi Username -->
                    <label>Username :</label>
                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        placeholder="Masukkan Username"
                        required
                    >

                    <label>Password :</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Masukkan Password"
                        required
                    >

                    <button type="submit" class="btn-login">
                        Log in
                    </button>
                </form>

                <!-- KOREKSI: Tautan Registrasi telah dibumihanguskan dari sini -->

            </div>
        </div>
    </div>
</div>

</body>
</html>