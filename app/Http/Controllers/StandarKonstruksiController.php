<?php

namespace App\Http\Controllers;

use App\Models\KonstruksiStandar;
use Illuminate\Support\Facades\Auth;

class StandarKonstruksiController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $konstruksi = KonstruksiStandar::orderByRaw('urutan IS NULL, urutan asc')
            ->orderBy('nomor')
            ->orderBy('id')
            ->get();

        return view(
            'standar-konstruksi',
            compact('konstruksi')
        );
    }
}