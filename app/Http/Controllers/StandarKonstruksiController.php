<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KonstruksiStandar;
use Illuminate\Support\Facades\Auth;

class StandarKonstruksiController extends Controller
{
    public function index()
    {
        // Pengecekan login ganda (selain dari Middleware)
        if (!Auth::check()) {
            return redirect()->route('login.view');
        }

        // Mengambil data Standar Konstruksi dari database
        $konstruksi = KonstruksiStandar::orderByRaw('urutan IS NULL, urutan asc')
            ->orderBy('no')
            ->orderBy('id')
            ->get();

        // Mengarahkan ke file view resources/views/standar-konstruksi.blade.php
        return view(
            'standar-konstruksi',
            compact('konstruksi')
        );
    }
}