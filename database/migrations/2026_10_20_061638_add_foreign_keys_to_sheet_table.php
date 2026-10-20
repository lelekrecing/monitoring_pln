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
        Schema::table('sheet', function (Blueprint $table) {
            $table->foreign(['upload_id'])->references(['id'])->on('uploads')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sheet', function (Blueprint $table) {
            $table->dropForeign('sheet_upload_id_foreign');
        });
    }
};
