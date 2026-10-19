<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom sub-bab pada tabel `standar_konstruksis`.
     *
     * Excel Standar Konstruksi punya sub-bab dinamis di tengah data,
     * misalnya:
     *   A | SUTM
     *   ...data SUTM...
     *   B | NO MDU
     *   ...data NO MDU...
     *
     * Baris "A | SUTM" dan "B | NO MDU" BUKAN data konstruksi biasa,
     * melainkan judul sub-bab. Setiap baris data yang mengikutinya
     * disimpan dengan menyimpan kode & nama sub-bab tersebut, supaya
     * relasi data -> sub-bab tetap tersimpan di database walau nanti
     * ditambah sub-bab baru (C, D, E, dst) tanpa perlu migration lagi.
     *
     * Kolom ini nullable supaya data lama (tanpa sub-bab) tetap valid.
     */
    public function up(): void
    {
        Schema::table('standar_konstruksis', function (Blueprint $table) {
            $table->string('sub_bab_kode', 20)->nullable()->after('link_gambar');
            $table->string('sub_bab_nama')->nullable()->after('sub_bab_kode');

            $table->index('sub_bab_kode');
        });
    }

    public function down(): void
    {
        Schema::table('standar_konstruksis', function (Blueprint $table) {
            $table->dropIndex(['sub_bab_kode']);
            $table->dropColumn(['sub_bab_kode', 'sub_bab_nama']);
        });
    }
};