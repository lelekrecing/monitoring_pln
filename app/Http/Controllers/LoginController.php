<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        // 2. Autentikasi Single-Table (Standar Enterprise)
        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();
            
            $user = Auth::user();

            // 3. Cek Role (RBAC - Role Based Access Control)
            if ($user->role === 'admin') {
                /*
                ===========================================================
                INJEKSI KOMPATIBILITAS (ARCHITECTURE BRIDGE)
                Agar middleware 'AdminAuthController' lama Anda tidak rusak, 
                kita tetap mendaftarkan sesi 'admin' secara manual di sini.
                ===========================================================
                */
                session([
                    'admin' => $user->id,
                    'nama'  => $user->name
                ]);

                return redirect()->route('admin.dashboard');
            }

            // Jika role == 'user', arahkan ke dashboard biasa
            return redirect()->route('dashboard');
        }

        // 4. Jika gagal, tolak dengan pesan error
        return back()->withErrors([
            'username' => 'Username atau Password salah.'
        ])->withInput();
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