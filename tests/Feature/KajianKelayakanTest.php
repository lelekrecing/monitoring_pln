<?php

namespace Tests\Feature;

use App\Models\KajianProyek;
use App\Models\KontrakKuota;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KajianKelayakanTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_and_admin_views_render_cleanly(): void
    {
        $user = User::create([
            'name'     => 'ULP Kota Baru',
            'username' => 'ulp_kotabaru',
            'password' => Hash::make('secret123'),
            'role'     => 'user',
        ]);

        $this->actingAs($user)
            ->get(route('kajian-kelayakan.index'))
            ->assertOk()
            ->assertSee('Kajian Kelayakan Proyek')
            ->assertSee('Simulasi Cepat Pelanggan Baru');

        $this->withSession(['admin' => (object) ['name' => 'Admin Perencanaan', 'username' => 'admin']])
            ->get(route('admin.kajian-kelayakan.index'))
            ->assertOk()
            ->assertSee('Kajian Kelayakan Proyek');

        $this->withSession(['admin' => (object) ['name' => 'Admin Perencanaan', 'username' => 'admin']])
            ->get(route('admin.standar-konstruksi.index'))
            ->assertOk()
            ->assertSee('Standar Konstruksi');

        $this->get('/registrasi')->assertOk()->assertSee('Daftar Akun Baru');
        $this->get('/update-data')->assertOk()->assertSee('Update Data Monitoring');
    }

    public function test_simulation_kkf_store_and_quota_exhaustion_blocking(): void
    {
        $user = User::create([
            'name'     => 'User Perencanaan',
            'username' => 'perencanaan_test',
            'password' => Hash::make('secret123'),
            'role'     => 'user',
        ]);

        // 1. Simulasi cepat pelanggan baru (1 trafo 250 kVA untuk PB B2T 197 kVA)
        $simRes = $this->actingAs($user)->postJson(route('kajian-kelayakan.simulate'), [
            'tahun'               => '2026',
            'jarak_gudang_km'     => 10,
            'total_pelanggan'     => 1,
            'total_daya_kva'      => 197,
            'jumlah_trafo'        => 1,
            'kapasitas_trafo_kva' => 250,
            'estimasi_sutm_km'    => 0.2,
            'estimasi_sutr_km'    => 0.2,
            'jumlah_tiang'        => 8,
        ]);

        $simRes->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('feasibility.layak_otomatis', true);

        // 2. Simpan kajian KKF lengkap beserta URAI rows (TM-3 => BA=1, BD=0; Tr.2 => DS=1)
        $storeRes = $this->actingAs($user)->postJson(route('kajian-kelayakan.store'), [
            'mode'            => 'kajian',
            'status'          => 'diajukan',
            'ulp'             => 'Kota Baru',
            'nama_pekerjaan'  => 'PB Pabrik Sawit Jaya',
            'jarak_gudang_km' => 12,
            'tahun'           => '2026',
            'pelanggans'      => [
                [
                    'jenis_permohonan' => 'PB',
                    'kelompok_tarif'   => 'B2T / 197000 VA',
                    'jumlah_pelanggan' => 1,
                    'daya_sebelum_kva' => 0,
                    'daya_sesudah_kva' => 197,
                ],
            ],
            'urai_rows' => [
                [
                    'AJ'     => 'T01',
                    'AL'     => 'TM-3',
                    'AM'     => 'AAACS 3x150 mm2',
                    'VOL150' => '0.25',
                    'BG'     => '160',
                    'CANTOL' => '2JRS - 2JRS',
                    'DP'     => 'Tr.2',
                    'VOLJTR' => '0.2',
                ],
            ],
        ]);

        $storeRes->assertOk()
            ->assertJsonPath('data.total_pelanggan', 1)
            ->assertJsonPath('data.total_daya_kva', 197)
            ->assertJsonPath('data.jumlah_trafo', 1);

        $proyekId1 = $storeRes->json('data.id');
        $proyek1 = KajianProyek::with('uraiRows')->findOrFail($proyekId1);
        $this->assertEquals('1', $proyek1->uraiRows[0]->row_data['BA']);
        $this->assertEquals('0', $proyek1->uraiRows[0]->row_data['BD']);
        $this->assertEquals('1', $proyek1->uraiRows[0]->row_data['DS']);

        // 3. Buat kajian ke-2
        $storeRes2 = $this->actingAs($user)->postJson(route('kajian-kelayakan.store'), [
            'mode'            => 'kajian',
            'status'          => 'diajukan',
            'ulp'             => 'Telanai Pura',
            'nama_pekerjaan'  => 'PB Gudang Logistik',
            'jarak_gudang_km' => 8,
            'tahun'           => '2026',
            'pelanggans'      => [
                [
                    'jenis_permohonan' => 'PB',
                    'kelompok_tarif'   => 'B2T / 131000 VA',
                    'jumlah_pelanggan' => 1,
                    'daya_sebelum_kva' => 0,
                    'daya_sesudah_kva' => 131,
                ],
            ],
            'jumlah_trafo'        => 1,
            'kapasitas_trafo_kva' => 160,
        ]);
        $storeRes2->assertOk();
        $proyekId2 = $storeRes2->json('data.id');

        // 4. Buat Master Kuota Kontrak: batas Rp 500 juta & maksimal 1 pekerjaan
        KontrakKuota::create([
            'vendor'               => 'PT Vendor Energi Jambi',
            'no_kontrak'           => '001.PJ/DAN/2026',
            'jenis_pekerjaan'      => 'Pembangunan Jaringan & PB/PD',
            'tahun'                => '2026',
            'kuota_awal_rp'        => 500000000,
            'terpakai_manual_rp'   => 0,
            'kuota_pekerjaan_maks' => 1,
            'status_aktif'         => true,
        ]);

        // Kontrak pertama berhasil memakai kuota
        $contract1 = $this->actingAs($user)->postJson(route('kajian-kelayakan.contract', $proyekId1), [
            'status'              => 'terkontrak',
            'vendor_kontrak'      => 'PT Vendor Energi Jambi',
            'nilai_kontrak_pakai' => 300000000,
            'estimasi_nilai_rab'  => 300000000,
        ]);
        $contract1->assertOk()->assertJsonPath('data.status', 'terkontrak');

        // Kontrak kedua HARUS DITOLAK (422) karena kuota pekerjaan vendor sudah habis (1/1)
        $contract2 = $this->actingAs($user)->postJson(route('kajian-kelayakan.contract', $proyekId2), [
            'status'              => 'terkontrak',
            'vendor_kontrak'      => 'PT Vendor Energi Jambi',
            'nilai_kontrak_pakai' => 100000000,
            'estimasi_nilai_rab'  => 100000000,
        ]);
        $contract2->assertStatus(422);
        $this->assertEquals('diajukan', KajianProyek::findOrFail($proyekId2)->status);
    }
}
