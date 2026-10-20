<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RiwayatUploadController extends Controller
{
    // Nilai kolom "status" di tabel uploads.
    // 'aktif' = sedang tampil di dashboard. Sesuaikan STATUS_ARSIP jika di
    // produksi nilainya berbeda (mis. 'nonaktif' / 'arsip').
    private const STATUS_AKTIF = 'aktif';
    private const STATUS_ARSIP = 'arsip';
    private const STATUS_GAGAL = 'gagal';

    public function index(Request $request)
    {
        $tampilkanSemua = $request->boolean('tampilkan_semua');

        $query = Upload::query();

        // Secara default sembunyikan upload kosong / gagal (tanpa data)
        $jumlahTersembunyi = 0;
        if (! $tampilkanSemua) {
            $jumlahTersembunyi = (clone $query)->where(function ($q) {
                $q->where('status', self::STATUS_GAGAL)
                  ->orWhereNull('jumlah_sheet')
                  ->orWhere('jumlah_sheet', 0);
            })->count();

            $query->where('status', '!=', self::STATUS_GAGAL)
                  ->where('jumlah_sheet', '>', 0);
        }

        $riwayat = $query->orderBy('created_at', 'desc')->get();

        return view('dashboard-admin.riwayat-upload', compact('riwayat', 'tampilkanSemua', 'jumlahTersembunyi'));
    }

    /** Jadikan satu upload sebagai tampilan aktif (per jenis monitoring). */
    public function aktifkan($id)
    {
        $upload = Upload::findOrFail($id);

        if ($upload->status === self::STATUS_GAGAL || (int) $upload->jumlah_sheet === 0) {
            return back()->with('error', 'Upload kosong/gagal tidak bisa dijadikan tampilan aktif.');
        }

        DB::transaction(function () use ($upload) {
            Upload::where('jenis_monitoring', $upload->jenis_monitoring)
                ->where('status', self::STATUS_AKTIF)
                ->update(['status' => self::STATUS_ARSIP]);

            $upload->status = self::STATUS_AKTIF;
            $upload->save();
        });

        return back()->with('success', 'Upload dijadikan tampilan aktif untuk ' . strtoupper($upload->jenis_monitoring) . '.');
    }

    /** Hapus riwayat upload beserta sheet terkait (jika ada kolom upload_id). */
    public function destroy($id)
    {
        $upload = Upload::findOrFail($id);

        DB::transaction(function () use ($upload) {
            if (Schema::hasTable('sheet') && Schema::hasColumn('sheet', 'upload_id')) {
                DB::table('sheet')->where('upload_id', $upload->id)->delete();
            }
            $upload->delete();
        });

        return back()->with('success', 'Riwayat upload berhasil dihapus.');
    }
}