<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KonstruksiStandar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class StandarKonstruksiController extends Controller
{
    private function checkAdmin()
    {
        if (!session()->has('admin')) {
            return redirect()->route('login.view');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $konstruksi = KonstruksiStandar::orderByRaw('urutan IS NULL, urutan asc')
            ->orderBy('no')
            ->orderBy('id')
            ->get();

        return view(
            'dashboard-admin.standar-konstruksi-index',
            compact('konstruksi')
        );
    }

    public function import(Request $request)
    {
        if ($redirect = $this->checkAdmin()) {
            return $redirect;
        }

        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls|max:20480',
        ], [
            'file_excel.required' => 'Silakan pilih file Excel Standar Konstruksi.',
            'file_excel.mimes' => 'File harus berupa Excel (.xlsx atau .xls).',
        ]);

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        set_time_limit(0);

        $file = $request->file('file_excel');

        DB::beginTransaction();

        try {
            /*
             * Simpan file Excel sebagai arsip.
             * Gambar TIDAK disimpan ke server.
             */
            $namaFile = time() . '_' . $file->getClientOriginalName();

            $path = $file->storeAs(
                'standar-konstruksi',
                $namaFile,
                'public'
            );

            $fullPath = storage_path('app/public/' . $path);

            /*
             * Baca Excel
             */
            $reader = IOFactory::createReaderForFile($fullPath);
            $spreadsheet = $reader->load($fullPath);

            /*
             * Ambil Sheet pertama.
             */
            $worksheet = $spreadsheet->getSheet(0);

            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $highestColumnIndex =
                Coordinate::columnIndexFromString($highestColumn);

            /*
             * Pastikan minimal ada 3 kolom.
             */
            if ($highestColumnIndex < 3) {
                throw new \Exception(
                    'File yang dipilih bukan file Standar Konstruksi. ' .
                    'Silakan pilih file Standar Konstruksi yang sesuai. ' .
                    '(Format kolom harus: No | Kontruksi SUTM 2026 | Link Gambar)'
                );
            }

            /*
             * Validasi header.
             *
             * Dibuat fleksibel (bukan pencocokan string persis) supaya
             * variasi kecil seperti spasi ganda, huruf besar/kecil,
             * atau typo "Kontruksi"/"Konstruksi" tetap dianggap valid,
             * tapi file yang strukturnya benar-benar berbeda tetap
             * ditolak.
             */
            $headerNo = $this->normalisasiTeks(
                (string) $worksheet->getCell('A1')->getValue()
            );

            $headerNama = $this->normalisasiTeks(
                (string) $worksheet->getCell('B1')->getValue()
            );

            $headerLink = $this->normalisasiTeks(
                (string) $worksheet->getCell('C1')->getValue()
            );

            $headerValid =
                $headerNo === 'no' &&
                (
                    str_contains($headerNama, 'sutm') &&
                    (
                        str_contains($headerNama, 'konstruksi') ||
                        str_contains($headerNama, 'kontruksi')
                    )
                ) &&
                (
                    str_contains($headerLink, 'link') &&
                    str_contains($headerLink, 'gambar')
                );

            if (!$headerValid) {
                throw new \Exception(
                    'File yang dipilih bukan file Standar Konstruksi. ' .
                    'Silakan pilih file Standar Konstruksi yang sesuai. ' .
                    '(Format kolom harus: No | Kontruksi SUTM 2026 | Link Gambar)'
                );
            }

            $data = [];

            /*
             * Kode & nama sub-bab yang sedang berjalan.
             *
             * Excel Standar Konstruksi berisi sub-bab dinamis di
             * tengah data, misalnya:
             *   A | SUTM
             *   ...data SUTM...
             *   B | NO MDU
             *   ...data NO MDU...
             *
             * Baris seperti "A | SUTM" BUKAN data konstruksi biasa,
             * melainkan judul sub-bab (lihat isBarisSubBab()). Setiap
             * baris data sesudahnya otomatis mengikuti sub-bab
             * terakhir yang ditemukan, sampai baris sub-bab berikutnya
             * muncul. Ini berlaku untuk sub-bab APAPUN (A, B, C, D,
             * ... atau kode lain) tanpa perlu hardcode di kode ini,
             * sehingga penambahan sub-bab baru di masa depan tidak
             * memerlukan perubahan kode.
             */
            $subBabKodeSaatIni = null;
            $subBabNamaSaatIni = null;

            /*
             * Penghitung urutan asli baris dari Excel.
             *
             * "No" pada Excel RESTART dari 1 di setiap sub-bab, jadi
             * tidak bisa dipakai untuk menjaga urutan tampil secara
             * keseluruhan (lihat migration add_urutan_...). Counter
             * ini naik terus untuk setiap baris DATA (bukan baris
             * sub-bab) yang berhasil dibaca, mengikuti persis urutan
             * baris di file - baris sub-bab A beserta isinya duluan,
             * baru sub-bab B beserta isinya, dst.
             */
            $urutanKe = 0;

            /*
             * Baca data mulai baris 2.
             */
            for ($row = 2; $row <= $highestRow; $row++) {

                $no = trim(
                    (string) $worksheet->getCell('A' . $row)->getValue()
                );

                $nama = trim(
                    (string) $worksheet->getCell('B' . $row)->getValue()
                );

                $linkCell = $worksheet->getCell('C' . $row);

                $link = trim(
                    (string) $linkCell->getValue()
                );

                /*
                 * Kalau link dipasang sebagai hyperlink Excel,
                 * ambil URL aslinya.
                 */
                $hyperlink = $linkCell->getHyperlink();

                if (
                    $hyperlink &&
                    $hyperlink->getUrl() !== ''
                ) {
                    $link = trim(
                        (string) $hyperlink->getUrl()
                    );
                }

                /*
                 * Lewati baris yang benar-benar kosong (No, Nama,
                 * dan Link semuanya kosong).
                 */
                if ($no === '' && $nama === '' && $link === '') {
                    continue;
                }

                /*
                 * Baris SUB-BAB (mis. "A | SUTM", "B | NO MDU",
                 * atau sub-bab baru apapun seperti "C | ...").
                 * Simpan sebagai sub-bab yang sedang berjalan, lalu
                 * lanjut ke baris berikutnya - baris ini sendiri
                 * bukan data konstruksi.
                 */
                if ($this->isBarisSubBab($no, $nama, $link)) {
                    $subBabKodeSaatIni = mb_strtoupper($no);
                    $subBabNamaSaatIni = $nama;

                    continue;
                }

                /*
                 * Lewati baris kosong (tidak ada nama konstruksi).
                 */
                if ($nama === '') {
                    continue;
                }

                $urutanKe++;

                $data[] = [
                    'urutan' => $urutanKe,

                    'no' => is_numeric($no)
                        ? (int) $no
                        : ($row - 1),

                    'nama_konstruksi' => $nama,

                    'link_gambar' => $link,

                    'sub_bab_kode' => $subBabKodeSaatIni,

                    'sub_bab_nama' => $subBabNamaSaatIni,

                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (empty($data)) {
                throw new \Exception(
                    'Tidak ada data Standar Konstruksi yang ditemukan.'
                );
            }

            /*
             * Excel adalah sumber data terbaru.
             * Data lama diganti seluruhnya.
             */
            KonstruksiStandar::query()->delete();

            foreach (array_chunk($data, 500) as $chunk) {
                KonstruksiStandar::insert($chunk);
            }

            DB::commit();

            $jumlahSubBab = collect($data)
                ->pluck('sub_bab_kode')
                ->filter(fn ($kode) => $kode !== null && $kode !== '')
                ->unique()
                ->count();

            $pesanSukses =
                'Import berhasil. ' .
                count($data) .
                ' data Standar Konstruksi berhasil diperbarui' .
                ($jumlahSubBab > 0
                    ? ' (' . $jumlahSubBab . ' sub-bab).'
                    : '.');

            // Kalau request datang dari progress bar upload (AJAX,
            // mengirim header Accept: application/json), balas dengan
            // JSON supaya JS bisa menampilkan status "berhasil" tanpa
            // reload halaman - sama seperti pola upload Monitoring.
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $pesanSukses,
                ]);
            }

            return redirect()
                ->route('admin.standar-konstruksi.index')
                ->with('success', $pesanSukses);

        } catch (\Throwable $e) {

            DB::rollBack();

            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()
                ->route('admin.standar-konstruksi.index')
                ->with(
                    'error',
                    'Import gagal: ' . $e->getMessage()
                );
        }
    }

    /**
     * Normalisasi teks untuk keperluan validasi header supaya
     * tidak kaku terhadap perbedaan huruf besar/kecil maupun
     * spasi berlebih (mis. "  No  " tetap dianggap sama dengan
     * "No").
     */
    private function normalisasiTeks(string $value): string
    {
        $value = mb_strtolower(trim($value));

        // Rapikan spasi ganda/tab/newline menjadi satu spasi.
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return trim($value);
    }

    /**
     * Deteksi apakah sebuah baris adalah baris SUB-BAB
     * (mis. "A | SUTM", "B | NO MDU"), bukan baris data
     * konstruksi biasa.
     *
     * Ciri-ciri baris sub-bab pada Excel Standar Konstruksi:
     *  - Kolom "No" berisi kode singkat berupa huruf saja atau
     *    huruf diikuti angka (A, B, C, ..., AA, A1, dst) - BUKAN
     *    angka urut biasa seperti 1, 2, 3.
     *  - Kolom "Kontruksi SUTM 2026" (nama) berisi judul sub-bab
     *    dan tidak boleh kosong.
     *  - Kolom "Link Gambar" kosong, karena baris judul sub-bab
     *    tidak punya gambar sendiri.
     *
     * Pola ini SENGAJA tidak dibatasi hanya untuk "A" dan "B" -
     * kode apapun yang mengikuti pola huruf (+ opsional angka)
     * akan otomatis dikenali sebagai sub-bab baru, sehingga
     * penambahan sub-bab C, D, E, dst di masa depan tidak
     * memerlukan perubahan kode ini.
     */
    private function isBarisSubBab(string $no, string $nama, string $link): bool
    {
        // Buang tanda baca penutup yang kadang ikut ditulis,
        // mis. "A." atau "A)" tetap dikenali sebagai kode "A".
        $kode = preg_replace('/[).:\-]+$/', '', $no) ?? $no;
        $kode = trim($kode);

        if ($kode === '' || $nama === '' || $link !== '') {
            return false;
        }

        // Kode angka murni (1, 2, 10, ...) bukan sub-bab -
        // itu baris data biasa.
        if (is_numeric($kode)) {
            return false;
        }

        // Kode sub-bab: 1-4 huruf, opsional diikuti sampai 3 angka.
        // Cocok untuk "A", "B", "C", "AA", "A1", atau angka romawi
        // seperti "I", "II", "III".
        return (bool) preg_match('/^[A-Z]{1,4}[0-9]{0,3}$/', mb_strtoupper($kode));
    }
}