<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan kolom username HANYA JIKA belum ada
        if (!Schema::hasColumn('admins', 'username')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->string('username')->after('nama')->nullable();
            });
            
            // 2. SALIN email lama menjadi username sementara agar Admin tidak kehilangan akses
            DB::statement('UPDATE admins SET username = email');
        }

        // 3. Hapus kolom email
        if (Schema::hasColumn('admins', 'email')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('admins', 'email')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->string('email')->after('nama')->nullable();
            });
            DB::statement('UPDATE admins SET email = username');
        }
        
        if (Schema::hasColumn('admins', 'username')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->dropColumn('username');
            });
        }
    }
};