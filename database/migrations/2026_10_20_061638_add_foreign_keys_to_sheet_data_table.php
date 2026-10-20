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
        Schema::table('sheet_data', function (Blueprint $table) {
            $table->foreign(['sheet_id'])->references(['id'])->on('sheet')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sheet_data', function (Blueprint $table) {
            $table->dropForeign('sheet_data_sheet_id_foreign');
        });
    }
};
