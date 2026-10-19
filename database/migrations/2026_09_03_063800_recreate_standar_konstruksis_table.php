<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel `standar_konstruksis` yang sudah ada sebelumnya punya
     * struktur kolom yang TIDAK sesuai untuk fitur Standar Konstruksi
     * (kolom lamanya: judul, kategori, keterangan, file_path, file_name,
     * file_ext, file_size, diupload_oleh — pola upload dokumen generik,
     * dan tidak dipakai oleh Model/Controller/fitur manapun lain di
     * project ini).
     *
     * Migration ini membuat ULANG tabel tersebut dengan struktur yang
     * benar-benar dipakai oleh App\Models\KonstruksiStandar dan fitur
     * Standar Konstruksi (sumber data Excel: A=No, B=Kontruksi SUTM 2026,
     * C=Link Gambar Google Drive).
     */
    public function up(): void
    {
        Schema::dropIfExists('standar_konstruksis');

        Schema::create('standar_konstruksis', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nomor')->nullable();
            $table->string('nama_konstruksi');
            $table->text('link_gambar');
            $table->timestamps();

            $table->index('nomor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('standar_konstruksis');
    }
};