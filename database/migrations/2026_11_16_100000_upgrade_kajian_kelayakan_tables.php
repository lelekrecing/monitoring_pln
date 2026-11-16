<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kajian_proyeks', function (Blueprint $table) {
            if (!Schema::hasColumn('kajian_proyeks', 'row_count')) {
                $table->unsignedInteger('row_count')->default(25)->after('jarak_gudang_km');
            }
            if (!Schema::hasColumn('kajian_proyeks', 'hasil_isolator')) {
                $table->json('hasil_isolator')->nullable()->after('kebutuhan_jaringan');
            }
            if (!Schema::hasColumn('kajian_proyeks', 'analisa_kkf')) {
                $table->json('analisa_kkf')->nullable()->after('hasil_isolator');
            }
        });

        Schema::table('kajian_urai_rows', function (Blueprint $table) {
            if (!Schema::hasColumn('kajian_urai_rows', 'updated_by')) {
                $table->string('updated_by', 150)->nullable()->after('row_data');
            }
        });

        if (!Schema::hasTable('kajian_params')) {
            Schema::create('kajian_params', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kajian_proyek_id')->constrained('kajian_proyeks')->cascadeOnDelete();
                $table->string('parameter', 80);
                $table->text('value')->nullable();
                $table->timestamps();

                $table->unique(['kajian_proyek_id', 'parameter']);
            });
        }

        if (!Schema::hasTable('kajian_tarif_masters')) {
            Schema::create('kajian_tarif_masters', function (Blueprint $table) {
                $table->id();
                $table->string('kode_tarif', 100)->unique();
                $table->string('kategori', 5)->default('R');
                $table->string('tarif_ap2t', 120)->nullable();
                $table->decimal('default_daya_kva', 12, 2)->default(0);
                $table->decimal('jam_nyala_bulan', 8, 2)->default(0);
                $table->decimal('bp_satuan_rp', 14, 2)->default(0);
                $table->string('ket_bp', 20)->default('fix');
                $table->decimal('ujl_satuan_rp', 14, 2)->default(0);
                $table->string('ket_ujl', 20)->default('per VA');
                $table->decimal('tarif_kwh_rp', 12, 2)->default(0);
                $table->string('ket_tarif', 20)->default('per kWh');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kajian_tarif_masters');
        Schema::dropIfExists('kajian_params');

        Schema::table('kajian_urai_rows', function (Blueprint $table) {
            if (Schema::hasColumn('kajian_urai_rows', 'updated_by')) {
                $table->dropColumn('updated_by');
            }
        });

        Schema::table('kajian_proyeks', function (Blueprint $table) {
            foreach (['row_count', 'hasil_isolator', 'analisa_kkf'] as $col) {
                if (Schema::hasColumn('kajian_proyeks', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
