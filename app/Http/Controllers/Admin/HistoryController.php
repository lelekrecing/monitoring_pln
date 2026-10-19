<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sheet;
use App\Models\SheetData;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Fitur Riwayat Upload.
 *
 * Sejak InputDataController::runFinalize() diubah untuk MENGARSIPKAN (bukan menghapus)
 * sheet lama, setiap upload yang pernah sukses tetap punya jejak lengkap di tabel
 * `sheet` + `sheet_data` -- meski sudah tidak aktif (is_active=0). Controller ini
 * memanfaatkan itu untuk:
 *
 *   1. index()   -> Daftar semua upload (histori), per jenis_monitoring, dengan
 *                    penanda upload mana yang SEDANG aktif ditampilkan di dashboard.
 *   2. show()    -> Detail sheet-sheet apa saja yang ada di dalam satu upload tertentu.
 *   3. activate()-> "Rollback" / ganti tampilan dashboard supaya memakai data dari
 *                    upload lama yang dipilih, tanpa perlu upload ulang filenya.
 *                    Upload yang sebelumnya aktif otomatis diarsipkan (bukan dihapus),
 *                    jadi aksi ini aman dibolak-balik kapan saja.
 *   4. destroy()  -> Hapus PERMANEN sebuah upload beserta seluruh sheet & sheet_data
 *                    miliknya (dan file Excel fisiknya). Ini kebalikan dari filosofi
 *                    "arsipkan, jangan hapus" di atas -- dipakai admin saat SUDAH YAKIN
 *                    sebuah upload lama tidak dibutuhkan lagi sama sekali (mis. percobaan
 *                    gagal yang menumpuk, atau arsip lama yang memang mau dibuang untuk
 *                    menghemat ruang tabel `sheet_data`). Upload yang SEDANG aktif tidak
 *                    boleh dihapus lewat sini -- admin harus mengaktifkan upload lain
 *                    dulu, baru yang lama boleh dihapus.
 */
class HistoryController extends Controller
{
    /**
     * Pengaman: fitur ini bisa MENGUBAH data mana yang tampil di dashboard publik
     * (lewat activate()), jadi wajib admin yang sudah login. Memakai pola yang sama
     * dengan AdminAuthController::login() -- session('admin') diisi saat login admin
     * berhasil, bukan lewat middleware route (proyek ini belum memakai grup middleware
     * untuk rute admin, jadi pengecekan dilakukan manual di sini).
     */
    private function pastikanAdmin(): void
    {
        if (!session()->has('admin')) {
            abort(403, 'Halaman ini khusus admin. Silakan login sebagai admin terlebih dahulu.');
        }
    }

    /**
     * GET /riwayat-upload
     * GET /riwayat-upload?jenis_monitoring=khs
     * GET /riwayat-upload?tampilkan_semua=1   -> ikut tampilkan upload gagal/kosong (default disembunyikan)
     */
    public function index(Request $request)
    {
        $this->pastikanAdmin();

        $jenisMonitoring = $request->query('jenis_monitoring'); // khs | tiang | pelanggan | null (semua)
        $tampilkanSemua  = $request->boolean('tampilkan_semua'); // default false

        $query = Upload::query()->orderByDesc('created_at');
        if ($jenisMonitoring) {
            $query->where('jenis_monitoring', $jenisMonitoring);
        }

        $uploads = $query->get()->map(function (Upload $upload) {
            $totalSheetAktif = Sheet::where('upload_id', $upload->id)
                ->where('is_active', 1)
                ->count();

            $totalSheetArsip = Sheet::where('upload_id', $upload->id)
                ->where('is_active', 0)
                ->count();

            return [
                'id'                => $upload->id,
                'nama_files'        => $upload->nama_files,
                'jenis_monitoring'  => $upload->jenis_monitoring,
                'status'            => $upload->status, // queued | processing | aktif | gagal
                'pesan_error'       => $upload->pesan_error,
                'jumlah_sheet'      => $upload->jumlah_sheet,
                'sheet_selesai'     => $upload->sheet_selesai,
                'created_at'        => $upload->created_at,
                'total_sheet_data'  => $totalSheetAktif + $totalSheetArsip,
                // PERBAIKAN: sebelumnya syarat ini juga mengecek $upload->status === 'aktif'.
                // Kolom `status` TIDAK bisa diandalkan untuk upload lama (bisa "kotor"/tidak
                // konsisten dari versi kode sebelum fitur arsip ini ada). Sumber kebenaran yang
                // sebenarnya adalah data sheet itu sendiri: kalau upload ini punya sheet yang
                // SEDANG aktif, berarti dialah yang tampil di dashboard -- titik, terlepas dari
                // apa isi kolom status-nya.
                'sedang_tampil'     => $totalSheetAktif > 0,
                // Bisa diaktifkan kalau: (a) bukan yang sedang tampil, (b) punya sheet arsip
                // untuk direstore, dan (c) jenis_monitoring-nya jelas (bukan null) -- karena
                // proses swap perlu tahu jenis apa yang mau digantikan di dashboard.
                'bisa_diaktifkan'   => $totalSheetAktif === 0
                                        && $totalSheetArsip > 0
                                        && !empty($upload->jenis_monitoring),
                // Tidak boleh hapus upload yang SEDANG aktif ditampilkan -- admin harus
                // pindah ke upload lain dulu (lewat Aktifkan) sebelum yang ini boleh dibuang.
                'bisa_dihapus'      => $totalSheetAktif === 0,
            ];
        });

        if (!$tampilkanSemua) {
            // Sembunyikan upload yang benar-benar tidak punya jejak data sama sekali
            // (percobaan gagal/batal -- 0 sheet aktif maupun arsip). Ini murni noise,
            // tidak ada yang bisa dilihat atau diaktifkan dari baris seperti ini.
            $uploads = $uploads->filter(fn (array $u) => $u['total_sheet_data'] > 0)->values();
        }

        return view('dashboard-admin.history.index', [
            'uploads'          => $uploads,
            'jenisMonitoring'  => $jenisMonitoring,
            'tampilkanSemua'   => $tampilkanSemua,
        ]);
    }

    /**
     * GET /riwayat-upload/{uploadId}
     * Detail: sheet apa saja yang ada di dalam satu upload tertentu (aktif ataupun arsip).
     */
    public function show(int $uploadId)
    {
        $this->pastikanAdmin();

        $upload = Upload::findOrFail($uploadId);

        $sheets = Sheet::where('upload_id', $uploadId)
            ->orderBy('urutan')
            ->get(['id', 'nama_sheet', 'kategori', 'tahun', 'total_rows', 'is_active']);

        return view('dashboard-admin.history.show', compact('upload', 'sheets'));
    }

    /**
     * POST /riwayat-upload/{uploadId}/aktifkan
     *
     * Menjadikan upload lama ini sebagai tampilan aktif dashboard untuk jenis_monitoring-nya.
     * Upload yang sebelumnya aktif akan diarsipkan (is_active=0), BUKAN dihapus -- jadi
     * bisa diaktifkan lagi kapan pun lewat halaman ini juga. Tindakan ini instan (tidak
     * perlu baca ulang file Excel / queue worker), karena datanya sudah ada di database.
     */
    public function activate(int $uploadId)
    {
        $this->pastikanAdmin();

        $upload = Upload::findOrFail($uploadId);

        if (empty($upload->jenis_monitoring)) {
            return back()->withErrors([
                'history' => "Upload '{$upload->nama_files}' tidak memiliki jenis_monitoring yang jelas, sistem tidak tahu dashboard mana yang harus digantikan. Tidak bisa diaktifkan secara otomatis.",
            ]);
        }

        $totalSheetArsip = Sheet::where('upload_id', $uploadId)->where('is_active', 0)->count();
        if ($totalSheetArsip === 0) {
            return back()->withErrors([
                'history' => "Tidak ditemukan data sheet (arsip) untuk upload ini. Mungkin memang belum pernah selesai diproses, atau sudah dihapus manual dari database.",
            ]);
        }

        DB::beginTransaction();
        try {
            // Arsipkan seluruh sheet aktif LAIN untuk jenis_monitoring yang sama.
            // Sengaja pakai where() biasa (bukan whereNull) karena kita sudah pastikan
            // di atas $upload->jenis_monitoring tidak kosong.
            Sheet::where('jenis_monitoring', $upload->jenis_monitoring)
                ->where('is_active', 1)
                ->where('upload_id', '!=', $uploadId)
                ->update(['is_active' => 0]);

            // Aktifkan kembali seluruh sheet milik upload yang dipilih (arsip -> aktif)
            Sheet::where('upload_id', $uploadId)->update(['is_active' => 1]);

            // Selaraskan kolom status juga -- upload lama yang selama ini mungkin
            // masih tersimpan dengan status "kotor" (lihat catatan di index()) dirapikan
            // di sini supaya konsisten ke depannya.
            $upload->update(['status' => 'aktif']);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['history' => 'Gagal mengaktifkan data: ' . $e->getMessage()]);
        }

        return back()->with('success', "Dashboard sekarang menampilkan data dari upload '{$upload->nama_files}' ({$upload->created_at}).");
    }

    /**
     * DELETE /riwayat-upload/{uploadId}
     *
     * Hapus PERMANEN satu upload: baris di tabel `uploads`, semua baris `sheet` miliknya
     * (aktif maupun arsip), semua `sheet_data` di bawah sheet-sheet itu, dan file Excel
     * fisiknya di storage. TIDAK BISA DIBATALKAN -- makanya di sisi tampilan (Blade) aksi
     * ini wajib pakai konfirmasi eksplisit sebelum form dikirim.
     *
     * Upload yang sedang aktif ditampilkan di dashboard SENGAJA diblokir dari sini, supaya
     * dashboard publik tidak pernah tiba-tiba kehilangan datanya karena aksi hapus. Kalau
     * admin memang mau membuang upload yang saat ini aktif, dia harus mengaktifkan upload
     * lain dulu lewat activate() -- baru yang lama ini boleh dihapus.
     */
    public function destroy(Request $request, int $uploadId)
    {
    $this->pastikanAdmin();

    $upload = Upload::findOrFail($uploadId);
    $force  = $request->boolean('force'); // BARU: izinkan hapus paksa

    $totalSheetAktif = Sheet::where('upload_id', $uploadId)->where('is_active', 1)->count();

    if ($totalSheetAktif > 0 && !$force) {
        return back()->withErrors([
            'history' => "Upload '{$upload->nama_files}' sedang AKTIF ditampilkan di dashboard. Aktifkan upload lain terlebih dahulu, atau gunakan tombol \"Hapus Paksa\" jika Anda yakin.",
        ]);
    }

    $wasSedangTampil = $totalSheetAktif > 0;

    DB::beginTransaction();
    try {
        $sheetIds = Sheet::where('upload_id', $uploadId)->pluck('id');
        SheetData::whereIn('sheet_id', $sheetIds)->delete();
        Sheet::whereIn('id', $sheetIds)->delete();

        $namaFileUntukPesan = $upload->nama_files;
        $filePath           = $upload->file_path;

        $upload->delete();

        DB::commit();
    } catch (Throwable $e) {
        DB::rollBack();
        return back()->withErrors(['history' => 'Gagal menghapus data: ' . $e->getMessage()]);
    }

    try {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    } catch (Throwable $e) {
        // diamkan
    }

    $pesan = "Upload '{$namaFileUntukPesan}' beserta seluruh data sheet-nya berhasil dihapus permanen.";
    if ($wasSedangTampil) {
        $pesan .= ' PERHATIAN: upload ini tadinya sedang tampil di dashboard — dashboard untuk jenis monitoring terkait sekarang tidak menampilkan data sampai Anda mengaktifkan upload lain dari halaman ini.';
    }

    return back()->with('success', $pesan);
    }
}