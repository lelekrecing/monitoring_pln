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
        Schema::create('uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_files');
            $table->string('file_path');
            $table->string('versi')->nullable();
            $table->integer('jumlah_sheet')->default(0);
            $table->string('jenis_monitoring')->nullable();
            $table->unsignedInteger('sheet_selesai')->default(0);
            $table->string('sheet_sedang_diproses')->nullable();
            $table->string('upload_by')->nullable();
            $table->string('status')->default('success');
            $table->text('pesan_error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
