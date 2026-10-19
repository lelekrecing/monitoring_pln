<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom `urutan` pada tabel `standar_konstruksis`.
     *
     * MASALAH: kolom "No" pada Excel Standar Konstruksi RESTART dari 1
     * di setiap sub-bab, misalnya:
     *   A | SUTM
     *     1 | TM 1        <- No = 1
     *     2 | TM 1 2-1     <- No = 2
     *     ...
     *   B | NO MDU
     *     1 | CROSS ARM... <- No = 1 (restart lagi)
     *     2 | TERMINAL LUG...
     *
     * Karena "No" tidak unik secara keseluruhan (No=1 muncul di setiap
     * sub-bab), sorting berdasarkan "No" saja akan MENGACAK urutan
     * asli (item No=1 dari sub-bab B akan tercampur dekat No=1 dari
     * sub-bab A).
     *
     * Kolom `urutan` menyimpan posisi baris SEBENARNYA sesuai urutan
     * baca dari file Excel (baris sub-bab A duluan lalu isinya, baru
     * baris sub-bab B lalu isinya, dst - persis seperti di Excel).
     * Data ditampilkan berdasarkan `urutan`, BUKAN berdasarkan "No",
     * sehingga urutan tampil selalu sama persis dengan urutan di file
     * yang diimport, walau nomornya sendiri berulang per sub-bab.
     */
    public function up(): void
    {
        Schema::table('standar_konstruksis', function (Blueprint $table) {
            $table->unsignedInteger('urutan')->nullable()->after('id');

            $table->index('urutan');
        });
    }

    public function down(): void
    {
        Schema::table('standar_konstruksis', function (Blueprint $table) {
            $table->dropIndex(['urutan']);
            $table->dropColumn('urutan');
        });
    }
};