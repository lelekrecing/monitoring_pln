<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KonstruksiStandar;
use App\Services\KajianExportService;
use Illuminate\Http\Request;
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

    public function exportPdf(Request $request, KajianExportService $exportService)
    {
        if (!Auth::check() && !session('admin')) {
            return redirect()->route('login.view');
        }

        $konstruksi = KonstruksiStandar::orderByRaw('urutan IS NULL, urutan asc')
            ->orderBy('no')
            ->orderBy('id')
            ->get();

        $file = $exportService->exportStandarKonstruksiPdf($konstruksi, [
            'q'        => trim((string) $request->query('q', '')),
            'tahun'    => trim((string) $request->query('tahun', '')),
            'per_page' => trim((string) $request->query('per_page', '')),
            'page'     => trim((string) $request->query('page', '')),
            'ids'      => trim((string) $request->query('ids', '')),
        ]);

        return response($file['content'], 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $file['filename'] . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}