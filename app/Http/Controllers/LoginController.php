<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Login dari form utama (username).
     * - role = 'admin' -> masuk Dashboard Admin
     * - role = 'user'  -> masuk Dashboard User
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (strtolower((string) $user->role) === 'admin') {
                // Akun admin: pakai pola session('admin') yang sama dengan
                // DashboardController::adminIndex(), lalu lepas login "user".
                Auth::logout();

                session([
                    'admin' => $user->id,
                    'nama'  => $user->name,
                ]);

                return redirect()->route('admin.dashboard');
            }

            // Akun user biasa: bersihkan sisa session admin
            session()->forget(['admin', 'nama']);

            return redirect()->route('dashboard');
        }

        return back()
            ->withErrors(['username' => 'Username atau Password salah.'])
            ->onlyInput('username');
    }

    /**
     * Login ADMIN lewat halaman /admin (tabel admins, pakai email)
     */
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            Auth::logout();

            $request->session()->regenerate();

            session([
                'admin' => $admin->id,
                'nama'  => $admin->nama,
            ]);

            return redirect()->route('admin.dashboard');
        }

        return back()
            ->withErrors(['email' => 'Email atau Password salah.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        session()->forget(['admin', 'nama']);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}