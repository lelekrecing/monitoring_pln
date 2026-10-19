<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AnggotaController extends Controller
{
    public function index()
    {
        $anggota = User::orderBy('created_at', 'desc')->get();
        // Path sudah benar ke folder Anda
        return view('dashboard-admin.anggota', compact('anggota'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username|regex:/^[a-z0-9_]+$/',
            'password' => 'required|string|min:6',
        ], [
            'username.regex'   => 'Username hanya boleh berisi huruf kecil, angka, dan garis bawah (_), tanpa spasi.',
            'username.unique'  => 'Username ini sudah digunakan, silakan pilih yang lain.'
        ]);

        try {
            User::create([
                'name'     => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role'     => $request->role ?? 'user',
            ]);
            return redirect()->back()->with('success', 'Akun anggota berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error Create Anggota: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.');
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|regex:/^[a-z0-9_]+$/|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
        ]);

        try {
            $user->name = $request->name;
            $user->username = $request->username;
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            if ($request->filled('role')) {
                $user->role = $request->role;
            }

            $user->save();
            return redirect()->back()->with('success', 'Data anggota berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error Update Anggota: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->username === 'admin_pln' || $user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Tindakan Ditolak: Tidak dapat menghapus akun Super Admin atau Anda sendiri.');
        }

        try {
            $user->delete();
            return redirect()->back()->with('success', 'Akun anggota berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error Delete Anggota: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus anggota.');
        }
    }
}