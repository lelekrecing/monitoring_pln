<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom-kolom yang dibutuhkan agar progres import bisa dipantau dari
 * database (bukan dari memori JavaScript tab browser), sehingga tetap terlihat
 * walau pengguna berpindah halaman.
 *
 * CATATAN: sesuaikan nama kolom di sini kalau ternyata tabel `uploads` Anda sudah
 * punya kolom dengan nama serupa (mis. sudah ada `jenis_monitoring`) -- migration ini
 * ditulis dengan asumsi kolom-kolom berikut BELUM ada, berdasarkan skema yang terlihat
 * di InputDataController sebelumnya (nama_files, file_path, versi, jumlah_sheet,
 * upload_by, status, created_at).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uploads', function (Blueprint $table) {
            // Jenis monitoring (khs / tiang / pelanggan) -- disimpan di sini supaya
            // Job background tahu jenis apa yang sedang diproses tanpa perlu parameter
            // tambahan dari luar.
            $table->string('jenis_monitoring')->nullable()->after('jumlah_sheet');

            // Counter sheet yang sudah selesai diproses, untuk hitung persentase progres.
            $table->unsignedInteger('sheet_selesai')->default(0)->after('jenis_monitoring');

            // Nama sheet yang SEDANG diproses saat ini (ditampilkan di widget progres).
            $table->string('sheet_sedang_diproses')->nullable()->after('sheet_selesai');

            // Pesan error asli kalau proses gagal (ditampilkan di widget progres).
            $table->text('pesan_error')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('uploads', function (Blueprint $table) {
            $table->dropColumn([
                'jenis_monitoring',
                'sheet_selesai',
                'sheet_sedang_diproses',
                'pesan_error',
            ]);
        });
    }
};