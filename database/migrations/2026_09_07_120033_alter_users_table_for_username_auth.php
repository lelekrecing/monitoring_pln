<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan username JIKA belum ada
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->after('name')->nullable();
            });
        }

        // 2. EKSEKUSI PAKSA: Isi semua username yang kosong agar tidak ditolak MySQL
        // (Diletakkan di luar IF agar pasti tereksekusi)
        DB::statement('UPDATE users SET username = CONCAT("user_", id) WHERE username IS NULL OR username = ""');

        // 3. Kunci username menjadi unik
        $indexes = DB::select("SHOW INDEX FROM users WHERE Key_name = 'users_username_unique'");
        if (empty($indexes)) {
            Schema::table('users', function (Blueprint $table) {
                $table->unique('username');
            });
        }

        // 4. Hapus email JIKA masih ada
        if (Schema::hasColumn('users', 'email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }
        
        // 5. Hapus verifikasi email JIKA masih ada
        if (Schema::hasColumn('users', 'email_verified_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('email_verified_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['username']);
                $table->dropColumn('username');
            });
        }
        
        if (!Schema::hasColumn('users', 'email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email')->unique()->after('name')->nullable();
            });
        }
    }
};