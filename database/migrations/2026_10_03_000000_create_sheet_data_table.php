<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sheet_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sheet_id')->constrained('sheet')->cascadeOnDelete();
            $table->integer('row_number');
            $table->longText('row_data');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sheet_data');
    }
};