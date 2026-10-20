<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('standar_konstruksis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('urutan')->nullable()->index();
            $table->unsignedInteger('no')->nullable()->index(); // Ubah 'nomor' menjadi 'no'
            $table->string('nama_konstruksi');
            $table->text('link_gambar');
            $table->string('sub_bab_kode', 20)->nullable()->index();
            $table->string('sub_bab_nama')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standar_konstruksis');
    }
};