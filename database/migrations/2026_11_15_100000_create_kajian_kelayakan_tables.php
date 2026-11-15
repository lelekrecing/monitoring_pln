<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kajian_proyeks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kajian', 100)->unique();
            $table->string('mode', 20)->default('kajian'); // simulasi | kajian
            $table->string('ulp', 100);
            $table->string('nama_pekerjaan', 255);
            $table->decimal('jarak_gudang_km', 10, 2)->default(0);
            $table->string('tahun', 10)->default('2026');

            // Ringkasan Beban & Pelanggan
            $table->integer('total_pelanggan')->default(0);
            $table->decimal('total_daya_kva', 14, 2)->default(0);

            // Ringkasan Kebutuhan Jaringan (diisi ULP untuk simulasi / Perencanaan untuk KKF)
            $table->integer('jumlah_trafo')->default(0);
            $table->decimal('kapasitas_trafo_kva', 10, 2)->default(0);
            $table->decimal('estimasi_sutm_km', 10, 2)->default(0);
            $table->decimal('estimasi_sutr_km', 10, 2)->default(0);
            $table->integer('jumlah_tiang')->default(0);

            // Hasil Analisis Kelayakan & RAB
            $table->decimal('estimasi_nilai_rab', 18, 2)->default(0);
            $table->decimal('estimasi_pendapatan_tahun', 18, 2)->default(0);
            $table->decimal('payback_tahun', 8, 2)->nullable();
            $table->boolean('layak_otomatis')->default(false);
            $table->string('hasil_ringkas', 120)->nullable();
            $table->string('status', 40)->default('draft'); // draft | diajukan | dilengkapi_perencanaan | layak | tidak_layak | terkontrak

            $table->text('catatan_ulp')->nullable();
            $table->text('catatan_perencanaan')->nullable();
            $table->json('kebutuhan_jaringan')->nullable();

            // Penugasan Kontrak & Kuota
            $table->string('vendor_kontrak', 200)->nullable();
            $table->string('no_kontrak', 120)->nullable();
            $table->decimal('nilai_kontrak_pakai', 18, 2)->default(0);
            $table->timestamp('terkontrak_at')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('created_by_name', 150)->nullable();
            $table->string('created_by_role', 30)->default('ulp');
            $table->string('updated_by_name', 150)->nullable();
            $table->timestamps();

            $table->index(['ulp', 'tahun']);
            $table->index('status');
            $table->index('vendor_kontrak');
        });

        Schema::create('kajian_pelanggans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kajian_proyek_id')->constrained('kajian_proyeks')->cascadeOnDelete();
            $table->unsignedTinyInteger('slot')->default(1);
            $table->string('jenis_permohonan', 10)->default('PB'); // PB | PD
            $table->string('kategori_tarif', 5)->nullable(); // R | S | B | I | P
            $table->string('kelompok_tarif', 80);
            $table->integer('jumlah_pelanggan')->default(1);
            $table->decimal('daya_sebelum_kva', 12, 2)->default(0);
            $table->decimal('daya_sesudah_kva', 12, 2)->default(0);
            $table->decimal('penambahan_daya_kva', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('kajian_urai_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kajian_proyek_id')->constrained('kajian_proyeks')->cascadeOnDelete();
            $table->unsignedInteger('row_number');
            $table->json('row_data');
            $table->timestamps();

            $table->index(['kajian_proyek_id', 'row_number']);
        });

        Schema::create('kontrak_kuotas', function (Blueprint $table) {
            $table->id();
            $table->string('vendor', 200);
            $table->string('no_kontrak', 120)->nullable();
            $table->string('jenis_pekerjaan', 150)->default('Pembangunan Jaringan & PB/PD');
            $table->string('tahun', 10)->default('2026');
            $table->decimal('kuota_awal_rp', 18, 2)->default(0);
            $table->decimal('terpakai_manual_rp', 18, 2)->default(0);
            $table->integer('kuota_pekerjaan_maks')->default(0); // 0 = batas berdasarkan nilai Rp saja
            $table->boolean('status_aktif')->default(true);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['vendor', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kontrak_kuotas');
        Schema::dropIfExists('kajian_urai_rows');
        Schema::dropIfExists('kajian_pelanggans');
        Schema::dropIfExists('kajian_proyeks');
    }
};
