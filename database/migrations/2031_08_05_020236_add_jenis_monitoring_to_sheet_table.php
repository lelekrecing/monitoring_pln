<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        // PENDEKATAN DEFENSIF: Cek apakah kolom 'jenis_monitoring' SUDAH ADA.
        // Jika belum ada, baru jalankan perintah alter table.
        // Jika sudah ada (seperti kasus di database Anda saat ini), lewati diam-diam.
        if (!Schema::hasColumn('sheet', 'jenis_monitoring')) {
            Schema::table('sheet', function (Blueprint $table) {
                $table->string('jenis_monitoring', 30)
                      ->default('khs')
                      ->after('kategori');
            });
        }
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        // PENDEKATAN DEFENSIF: Pastikan kolom ada sebelum mencoba menghapusnya.
        if (Schema::hasColumn('sheet', 'jenis_monitoring')) {
            Schema::table('sheet', function (Blueprint $table) {
                $table->dropColumn('jenis_monitoring');
            });
        }
    }
};