<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * DINONAKTIFKAN (no-op).
     *
     * Migration ini SEBELUMNYA membuat tabel `konstruksi_standars`,
     * tapi tabel tersebut TIDAK dipakai - database sudah memiliki
     * tabel `standar_konstruksis` yang dipakai oleh fitur "Standar
     * Konstruksi" (lihat App\Models\KonstruksiStandar::$table).
     *
     * File migration ini SENGAJA dibiarkan ada (bukan dihapus) supaya
     * riwayat migration tetap konsisten, tapi up()/down() dikosongkan
     * supaya `php artisan migrate` TIDAK membuat tabel duplikat yang
     * tidak diperlukan dan TIDAK menyentuh tabel `standar_konstruksis`
     * yang sudah ada beserta isinya.
     */
    public function up(): void
    {
        // Sengaja dikosongkan - lihat catatan di atas.
    }

    public function down(): void
    {
        // Sengaja dikosongkan - lihat catatan di atas.
    }
};