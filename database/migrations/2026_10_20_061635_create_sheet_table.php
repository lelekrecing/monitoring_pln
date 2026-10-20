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
        Schema::create('sheet', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('upload_id')->index('sheet_upload_id_foreign');
            $table->string('nama_sheet');
            $table->string('kategori');
            $table->string('jenis_monitoring')->default('khs');
            $table->year('tahun');
            $table->integer('urutan')->default(0);
            $table->integer('total_rows')->default(0);
            $table->longText('merge_cells')->nullable();
            $table->integer('highest_row')->nullable();
            $table->integer('highest_column')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sheet');
    }
};
