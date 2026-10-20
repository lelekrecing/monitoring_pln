<?php

namespace Database\Seeders;

use App\Models\KonstruksiStandar;
use Illuminate\Database\Seeder;

class KonstruksiStandarSeeder extends Seeder
{
    /**
     * ==========================================================
     * Seed data awal Standar Konstruksi SUTM 2026.
     *
     * SUMBER DATA: Google Spreadsheet "Standar Kontruksi" (Sheet1,
     * kolom A=No, B=Kontruksi SUTM 2026, C=Link Gambar), dibaca
     * dan disalin PERSIS - nama konstruksi TIDAK diubah/dipersingkat,
     * link gambar adalah link Google Drive asli.
     *
     * CATATAN PENTING:
     * Seeder ini hanya untuk pengisian data AWAL / fallback lokal.
     * Sumber kebenaran (source of truth) yang sesungguhnya adalah
     * Excel/Google Spreadsheet - setiap kali admin melakukan Import
     * Excel lewat halaman Admin > Standar Konstruksi, seluruh isi
     * tabel ini akan DIGANTI dengan data terbaru dari file yang
     * diupload (lihat Admin\StandarKonstruksiController::import()).
     * ==========================================================
     */
    public function run(): void
    {
        $data = [
            ['no' => 1,  'nama_konstruksi' => 'TM KHUSUS TYPE 3', 'link_gambar' => 'https://drive.google.com/file/d/118nMslLkkgLK8uCQjwQu5KbQ0S8bWs2M/view?usp=sharing'],
            ['no' => 2,  'nama_konstruksi' => 'TM KHUSUS TYPE 2', 'link_gambar' => 'https://drive.google.com/file/d/1OTBxFQ73lX4KQ3fZ508F7TBrJr7zH6OD/view?usp=sharing'],
            ['no' => 3,  'nama_konstruksi' => 'TM KHUSUS TYPE 1', 'link_gambar' => 'https://drive.google.com/file/d/1H8PcR5HmbNEOHxgPYrXy-Nw-xA_uoGqt/view?usp=sharing'],
            ['no' => 4,  'nama_konstruksi' => 'TM 10X', 'link_gambar' => 'https://drive.google.com/file/d/1HGDfJxeZNYaIb9y3MPAgUNHwGCKwoPvX/view?usp=sharing'],
            ['no' => 5,  'nama_konstruksi' => 'TM 10', 'link_gambar' => 'https://drive.google.com/file/d/1WDYCfAaTDrXjaWsxtpbVHoqmV3PlPiSg/view?usp=sharing'],
            ['no' => 6,  'nama_konstruksi' => 'TM 8X', 'link_gambar' => 'https://drive.google.com/file/d/1SYLBz6qsieFPt1Aruu67VoaAe1FD9kzo/view?usp=sharing'],
            ['no' => 7,  'nama_konstruksi' => 'TM 8', 'link_gambar' => 'https://drive.google.com/file/d/1p3rZusHUDY_5jS1kjQDfwnDyV2awPpqI/view?usp=sharing'],
            ['no' => 8,  'nama_konstruksi' => 'TM 5', 'link_gambar' => 'https://drive.google.com/file/d/1HCD5lvdr7dBTQzFv0jEtQXKIvoyrx486/view?usp=sharing'],
            ['no' => 9,  'nama_konstruksi' => 'TM 4X', 'link_gambar' => 'https://drive.google.com/file/d/1Y81iHlMNkKYT-y5GqZcrfYtZzWo97Vwn/view?usp=sharing'],
            ['no' => 10, 'nama_konstruksi' => 'TM 4', 'link_gambar' => 'https://drive.google.com/file/d/1BBTXGWj8giP9NQdRQtHCf8BEeIAqxsq_/view?usp=sharing'],
            ['no' => 11, 'nama_konstruksi' => 'TM 3', 'link_gambar' => 'https://drive.google.com/file/d/1tP4xlh1zxWKQfuiwQ85JcE0uOollAqRp/view?usp=sharing'],
            ['no' => 12, 'nama_konstruksi' => 'TM 1, 2-1', 'link_gambar' => 'https://drive.google.com/file/d/1UG6sqkOE__SJlcSnJipyGrJJejK0x-cC/view?usp=sharing'],
            ['no' => 13, 'nama_konstruksi' => 'TM 1, 0-3', 'link_gambar' => 'https://drive.google.com/file/d/1pgv8T2QJfLO4tnhDVQv2Ix1Ld0Hfv3ku/view?usp=sharing'],
            ['no' => 14, 'nama_konstruksi' => 'TM 1', 'link_gambar' => 'https://drive.google.com/file/d/17jSctTBzsZ6j9NgO3bYqS5eACNoMpPOr/view?usp=sharing'],
            ['no' => 15, 'nama_konstruksi' => 'TERMINAL LUG (AL)', 'link_gambar' => 'https://drive.google.com/file/d/1UsOUUre4CjfWO0gaJLO_sAwaLchXf7sN/view?usp=sharing'],
            ['no' => 16, 'nama_konstruksi' => 'TERMINAL LUG (AL) - 2 HOLES', 'link_gambar' => 'https://drive.google.com/file/d/13yGhW7UQ49k2WExWjAish21SysRJ9gJi/view?usp=sharing'],
            ['no' => 17, 'nama_konstruksi' => 'SUPPORT GSW (GROUND STEEL WIRE)/PENUMPU', 'link_gambar' => 'https://drive.google.com/file/d/1gXVCuyxyTSuGsd-hQzWlvIPS_HUhXNLD/view?usp=sharing'],
            ['no' => 18, 'nama_konstruksi' => 'SUPPORT GSW (GROUND STEEL WIRE)/PENEGANG', 'link_gambar' => 'https://drive.google.com/file/d/1jPZVq5zzMnO_NxlWJcgsZWlqW8035mYJ/view?usp=sharing'],
            ['no' => 19, 'nama_konstruksi' => 'STRUT POLE', 'link_gambar' => 'https://drive.google.com/file/d/1nJTf0Jb7lnxDKhJyGMDstHL_KEMGc2kX/view?usp=sharing'],
            ['no' => 20, 'nama_konstruksi' => 'PENGHALANG/RANJAU PANJAT', 'link_gambar' => 'https://drive.google.com/file/d/1tq7vM7Igu3obYTyr1P2hR5cjz62i6QF-/view?usp=sharing'],
            ['no' => 21, 'nama_konstruksi' => 'KONTRUKSI PEMASANGAN TIANG DOUBLE 12 M', 'link_gambar' => 'https://drive.google.com/file/d/1P-N3zHh9sS7cuivHFVoffux-QVcVIK-3/view?usp=sharing'],
            ['no' => 22, 'nama_konstruksi' => 'JARAK SUTM DENGAN TANAM TUMBUH', 'link_gambar' => 'https://drive.google.com/file/d/1UJMq2ixBVfWdY8SdLc9qTRAV23P1ZHX4/view?usp=sharing'],
            ['no' => 23, 'nama_konstruksi' => 'HORIZONTAL GUY WIRE (HGW-TM)', 'link_gambar' => 'https://drive.google.com/file/d/1tNiv9unTXBE0yXglUU2BikY-_kvTrsjC/view?usp=sharing'],
            ['no' => 24, 'nama_konstruksi' => 'GUY WIRE BAND', 'link_gambar' => 'https://drive.google.com/file/d/1bDjB7R0oxlbClihGXqKTi82qyNWB6dEZ/view?usp=sharing'],
            ['no' => 25, 'nama_konstruksi' => 'GUY WIRE (GW-TM)', 'link_gambar' => 'https://drive.google.com/file/d/1GbUiBOKrwJitdPCoraeTPtPSr0RO9iBO/view?usp=sharing'],
            ['no' => 26, 'nama_konstruksi' => 'GUARD - NET', 'link_gambar' => 'https://drive.google.com/file/d/1sKSdD3shJlQ4oHPQyZipQCk2pH93eJgk/view?usp=sharing'],
            ['no' => 27, 'nama_konstruksi' => 'DOUBLE ARM BOLT, NUT AND WASHER', 'link_gambar' => 'https://drive.google.com/file/d/1mPXTh6pdMEh0FgM6csZYZsaju-tGPEH5/view?usp=sharing'],
            ['no' => 28, 'nama_konstruksi' => 'CROSS ARM TYPE 2000 UNP 10', 'link_gambar' => 'https://drive.google.com/file/d/1q-8M1ClAxcs2h-X1w78rkDXxdfE8W-Km/view?usp=sharing'],
            ['no' => 29, 'nama_konstruksi' => 'COMPRESSION CONNECTOR (CCO) TIPE G', 'link_gambar' => 'https://drive.google.com/file/d/1txDsVSUGL7pSobEhLDj29gMag0vifot4/view?usp=sharing'],
            ['no' => 30, 'nama_konstruksi' => 'CLAMP BEUGEL', 'link_gambar' => 'https://drive.google.com/file/d/1otzA_tofc23PLE7kINnzZ_8kQTnbr-Yk/view?usp=sharing'],
            ['no' => 31, 'nama_konstruksi' => 'BOLT, NUT AND WASHER', 'link_gambar' => 'https://drive.google.com/file/d/1_GluwdRzWnlzJr1N6_NL1WglUmA3Jwp7/view?usp=sharing'],
            ['no' => 32, 'nama_konstruksi' => 'BAYONET TM', 'link_gambar' => 'https://drive.google.com/file/d/1gyGsrWRU578O4MuPsrkTyOFUjIvNKtKb/view?usp=sharing'],
        ];

        foreach ($data as $row) {
            KonstruksiStandar::create($row);
        }
    }
}