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
        Schema::table('sheet', function (Blueprint $table) {
            // PENDEKATAN DEFENSIF: Cek masing-masing kolom secara independen
            if (!Schema::hasColumn('sheet', 'merge_cells')) {
                $table->longText('merge_cells')->nullable()->after('total_rows');
            }

            if (!Schema::hasColumn('sheet', 'highest_row')) {
                $table->integer('highest_row')->nullable()->after('merge_cells');
            }

            if (!Schema::hasColumn('sheet', 'highest_column')) {
                $table->integer('highest_column')->nullable()->after('highest_row');
            }
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('sheet', function (Blueprint $table) {
            // PENDEKATAN DEFENSIF: Kumpulkan kolom yang eksis sebelum melakukan drop
            $columnsToDrop = [];

            if (Schema::hasColumn('sheet', 'merge_cells')) {
                $columnsToDrop[] = 'merge_cells';
            }
            if (Schema::hasColumn('sheet', 'highest_row')) {
                $columnsToDrop[] = 'highest_row';
            }
            if (Schema::hasColumn('sheet', 'highest_column')) {
                $columnsToDrop[] = 'highest_column';
            }

            // Eksekusi drop hanya jika array tidak kosong
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};