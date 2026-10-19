<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\Sheet;
use App\Models\SheetData;
use App\Jobs\ProcessMonitoringImportJob;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class InputDataController extends Controller
{
    // =========================================================================
    // 1. ROUTING HANDLERS (FASE INISIASI)
    // =========================================================================

    public function storeKhs(Request $request) { return $this->initImport($request, 'khs', 'Monitoring KHS'); }
    public function storeTiang(Request $request) { return $this->initImport($request, 'tiang', 'Monitoring Tiang'); }
    public function storePelanggan(Request $request) { return $this->initImport($request, 'pelanggan', 'Monitoring Pelanggan'); }
    public function store(Request $request) { return $this->storeKhs($request); }

    // =========================================================================
    // 2. FASE INISIASI (Upload File, Validasi, lalu KIRIM ke Background Job)
    // =========================================================================
    // PENTING: Sejak revisi ini, controller TIDAK LAGI mengembalikan daftar sheet
    // untuk diproses satu-satu oleh JavaScript di browser. Sebaliknya, seluruh proses
    // (baca semua sheet -> staging -> finalize) didelegasikan ke ProcessMonitoringImportJob
    // yang berjalan di queue worker -- proses berjalan MANDIRI, TIDAK bergantung pada
    // tab browser tetap terbuka. Endpoint ini hanya bertugas menyimpan file & langsung balas.
    private function initImport(Request $request, string $jenisMonitoring, string $labelMonitoring)
    {
        $request->validate(['file_excel' => 'required|mimes:xlsx,xls|max:20480']);

        try {
            // 1. Simpan file
            $file = $request->file('file_excel');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $namaFile, 'public');
            $fullPath = storage_path('app/public/' . $path);

            // 2. BACA NAMA SHEET SAJA (Sangat cepat, 0.1 detik, tidak makan RAM)
            //    Ini masih dilakukan di request HTTP karena super cepat & dipakai untuk
            //    validasi jenis file sebelum kita commit apapun ke database.
            $reader = IOFactory::createReaderForFile($fullPath);
            $sheetNames = $reader->listWorksheetNames($fullPath);
            $sheetCount = count($sheetNames);

            // 3. Validasi Jenis File
            $this->validateJenisMonitoring($sheetNames, $jenisMonitoring, $labelMonitoring);

            // 4. Catat Upload dengan status 'queued'. Data lama TIDAK disentuh di sini,
            //    dan TIDAK ADA proses berat yang dikerjakan di request HTTP ini.
            //
            //    CATATAN UNTUK app/Models/Upload.php:
            //    Kolom baru berikut WAJIB ada di $fillable (atau pakai $guarded = []):
            //      - jenis_monitoring (string, nullable)
            //      - sheet_selesai (integer, default 0)
            //      - sheet_sedang_diproses (string, nullable)
            //      - pesan_error (text, nullable)
            //    Lihat migration terlampir: add_progress_columns_to_uploads_table.php
            $upload = Upload::create([
                'nama_files'             => $namaFile,
                'file_path'              => $path,
                'versi'                  => 'v1',
                'jumlah_sheet'           => $sheetCount,
                'sheet_selesai'          => 0,
                'sheet_sedang_diproses'  => null,
                'jenis_monitoring'       => $jenisMonitoring,
                'upload_by'              => 1,
                'created_at'             => now(),
                'status'                 => 'queued',
            ]);

            // 5. Kirim tugas ke background job. TIDAK ditunggu (async) -- request ini
            //    langsung balas ke browser begitu job berhasil di-dispatch ke antrean.
            ProcessMonitoringImportJob::dispatch($upload->id, $jenisMonitoring, $labelMonitoring);

            return response()->json([
                'success'          => true,
                'upload_id'        => $upload->id,
                'jenis_monitoring' => $jenisMonitoring,
                'jumlah_sheet'     => $sheetCount,
                'message'          => "Import {$sheetCount} sheet dimulai di background.",
            ]);

        } catch (Throwable $e) {
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // =========================================================================
    // 3. ENDPOINT POLLING PROGRES (Dipanggil widget progres global di semua halaman)
    // =========================================================================
    public function progress($uploadId)
    {
        $upload = Upload::find($uploadId);

        if (!$upload) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json([
            'upload_id'             => $upload->id,
            'status'                => $upload->status,                 // queued | processing | aktif | gagal
            'jenis_monitoring'      => $upload->jenis_monitoring,
            'jumlah_sheet'          => $upload->jumlah_sheet,
            'sheet_selesai'         => $upload->sheet_selesai,
            'sheet_sedang_diproses' => $upload->sheet_sedang_diproses,
            'pesan_error'           => $upload->pesan_error,
        ]);
    }

    // =========================================================================
    // 4. ENDPOINT HTTP MANUAL/DEBUG (opsional, tidak dipakai alur normal lagi)
    // =========================================================================
    // Endpoint ini DIPERTAHANKAN supaya kompatibel kalau ada kebutuhan memproses satu
    // sheet secara manual (mis. debugging), tapi alur upload normal TIDAK memanggil ini
    // lagi dari JavaScript -- semua sudah didelegasikan ke ProcessMonitoringImportJob.
    public function processSheet(Request $request)
    {
        $request->validate([
            'upload_id'        => 'required|integer',
            'file_path'        => 'required|string',
            'sheet_name'       => 'required|string',
            'sheet_index'      => 'required|integer',
            'jenis_monitoring' => 'required|string',
        ]);

        try {
            $this->runProcessSheet(
                (int) $request->upload_id,
                $request->file_path,
                $request->sheet_name,
                (int) $request->sheet_index,
                $request->jenis_monitoring
            );

            return response()->json(['success' => true, 'message' => "Sheet {$request->sheet_name} sukses diproses."]);

        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => "Gagal di Sheet {$request->sheet_name}: " . $e->getMessage()], 422);
        }
    }

    public function finalizeImport(Request $request)
    {
        $request->validate([
            'upload_id'        => 'required|integer',
            'jenis_monitoring' => 'required|string',
        ]);

        try {
            $this->runFinalize((int) $request->upload_id, $request->jenis_monitoring);
            return response()->json(['success' => true, 'message' => 'Finalisasi berhasil. Data lama diganti dengan data baru.']);
        } catch (Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal finalisasi: ' . $e->getMessage()], 422);
        }
    }

    public function cancelImport(Request $request)
    {
        $request->validate(['upload_id' => 'required|integer']);
        $this->runCancel((int) $request->upload_id);
        return response()->json(['success' => true, 'message' => 'Sisa proses staging dibersihkan.']);
    }

    // =========================================================================
    // 5. LOGIKA INTI (Dipakai bersama oleh endpoint HTTP di atas MAUPUN oleh
    //    ProcessMonitoringImportJob yang berjalan di queue worker/background)
    // =========================================================================

    /**
     * Proses satu sheet: baca dari file Excel, simpan sebagai Sheet + SheetData
     * berstatus STAGING (is_active = 0). Melempar Throwable kalau gagal -- pemanggil
     * (baik endpoint HTTP maupun Job) bertanggung jawab menangani kegagalannya.
     */
    public function runProcessSheet(int $uploadId, string $filePath, string $sheetName, int $sheetIndex, string $jenisMonitoring): void
    {
        // max_execution_time di-set 0 (tanpa batas) karena kode ini sekarang dijalankan
        // oleh queue worker (proses PHP CLI terpisah), BUKAN lagi oleh request HTTP milik
        // pengguna -- jadi tidak lagi terikat batas waktu request web server/hosting.
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        $fullPath = storage_path('app/public/' . $filePath);
        if (!file_exists($fullPath)) throw new \Exception("File fisik tidak ditemukan di server.");

        // ARSITEKTUR KUNCI: Hanya load 1 sheet yang diminta ke RAM!
        $reader = IOFactory::createReaderForFile($fullPath);
        $reader->setLoadSheetsOnly([$sheetName]);
        $spreadsheet = $reader->load($fullPath);
        $worksheet = $spreadsheet->getSheetByName($sheetName);

        if (!$worksheet) throw new \Exception("Sheet {$sheetName} tidak terbaca.");

        // PENTING: pakai getHighestDataRow()/getHighestDataColumn(), BUKAN getHighestRow()/getHighestColumn().
        // Versi "Data" hanya menghitung sel yang benar-benar berisi NILAI, sedangkan versi biasa
        // ikut menghitung sel yang cuma diberi FORMAT (border/warna/lebar kolom) tanpa isi.
        // Sheet seperti "RES. SPM" sering diformat jauh melampaui data aslinya (mis. sampai baris 5000
        // padahal data cuma 200 baris) -- itu yang bikin proses lambat lalu timeout di hosting shared.
        $highestRow = $worksheet->getHighestDataRow();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($worksheet->getHighestDataColumn());

        // Ekstrak data & Hyperlink
        $rows = $this->readWorksheetToArray($worksheet, $highestRow, $highestColumnIndex);
        foreach ($worksheet->getHyperlinkCollection() as $coordinate => $hyperlink) {
            $url = trim((string) $hyperlink->getUrl());
            if ($url === '' || str_starts_with($url, '#')) continue;
            [$colLetter, $rowNumber] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($coordinate);
            $colIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($colLetter) - 1;
            $rowIndex = $rowNumber - 1;
            $currentValue = trim((string) ($rows[$rowIndex][$colIndex] ?? ''));
            if (!preg_match('#^https?://#i', $currentValue)) {
                $rows[$rowIndex][$colIndex] = $url;
            }
        }

        // Penentuan Tahun & Kategori
        $tahun = null;
        if (preg_match('/(20\d{2})/', $sheetName, $match)) $tahun = $match[1];
        $kategori = $tahun ? trim(str_replace($tahun, '', $sheetName)) : $sheetName;

        DB::beginTransaction();
        try {
            $sheetModel = Sheet::create([
                'upload_id'        => $uploadId,
                'nama_sheet'       => $sheetName,
                'kategori'         => strtoupper($kategori),
                'jenis_monitoring' => $jenisMonitoring,
                'tahun'            => $tahun ?? date('Y'),
                'urutan'           => $sheetIndex + 1,
                'total_rows'       => count($rows),
                'is_active'        => 0, // STAGING: baru diaktifkan oleh runFinalize() setelah semua sheet sukses
                'created_at'       => now(),
            ]);

            $now = now();
            $bulkRows = [];
            foreach ($rows as $rowNumber => $row) {
                $bulkRows[] = [
                    'sheet_id'   => $sheetModel->id,
                    'row_number' => $rowNumber + 1,
                    'row_data'   => json_encode($row),
                    'created_at' => $now,
                ];
            }

            foreach (array_chunk($bulkRows, 500) as $chunk) {
                SheetData::insert($chunk);
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Pola swap: sheet baru sudah ada di DB berstatus staging (is_active=0) dari
     * runProcessSheet(). Di sini, dalam SATU transaksi: (a) ARSIPKAN sheet lama yang aktif
     * untuk jenis_monitoring ini, lalu (b) aktifkan sheet baru. Karena ini baru dipanggil
     * setelah SEMUA sheet (loop di Job) sukses, data lama TIDAK PERNAH hilang kalau ada
     * sheet yang gagal di tengah proses.
     *
     * REVISI (Fitur Histori Upload): Sheet & SheetData lama TIDAK LAGI dihapus permanen
     * saat data baru difinalisasi -- cukup dinonaktifkan (is_active=0), persis seperti
     * status staging. Bedanya, ini bukan sheet "yang sedang diproses", tapi arsip dari
     * upload sebelumnya yang PERNAH aktif. Baris `upload_id` pada tabel `sheet` dipakai
     * sebagai penanda kepemilikan, sehingga:
     *   - Halaman histori (lihat HistoryController) bisa menampilkan semua upload lama
     *     beserta sheet-sheet di dalamnya, walau sudah tidak aktif.
     *   - Admin bisa "mengaktifkan kembali" (rollback) sebuah upload lama kapan saja
     *     lewat HistoryController::activate(), tanpa perlu upload ulang filenya.
     *   - runCancel() tetap aman: ia HANYA menghapus sheet is_active=0 milik upload_id
     *     yang gagal (lihat scope `where('upload_id', $uploadId)` di sana), jadi arsip
     *     dari upload_id lain yang berbeda tidak pernah ikut terhapus oleh proses gagal.
     */
    public function runFinalize(int $uploadId, string $jenisMonitoring): void
    {
        DB::beginTransaction();
        try {
            // Pastikan memang ada sheet staging milik upload ini sebelum swap dieksekusi
            $staggingCount = Sheet::where('upload_id', $uploadId)->where('is_active', 0)->count();
            if ($staggingCount === 0) {
                throw new \Exception("Tidak ada sheet staging ditemukan untuk upload_id {$uploadId}. Finalisasi dibatalkan.");
            }

            // (a) ARSIPKAN semua sheet lain yang sedang aktif untuk jenis ini --
            //     BUKAN dihapus. Data & histori-nya tetap utuh di database, cuma
            //     berhenti tampil di dashboard karena is_active jadi 0.
            Sheet::where('jenis_monitoring', $jenisMonitoring)
                ->where('is_active', 1)
                ->where('upload_id', '!=', $uploadId)
                ->update(['is_active' => 0]);

            // (b) Aktifkan sheet baru (staging -> aktif)
            Sheet::where('upload_id', $uploadId)
                ->where('is_active', 0)
                ->update(['is_active' => 1]);

            Upload::where('id', $uploadId)->update([
                'status'                => 'aktif',
                'sheet_sedang_diproses' => null,
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Membersihkan sisa sheet staging (is_active=0) milik upload yang gagal, supaya
     * tidak menumpuk sebagai sampah di database. Data lama (is_active=1) tidak disentuh.
     * Sengaja TIDAK melempar exception -- ini best-effort cleanup, pemanggil (Job) yang
     * menuliskan pesan error asli ke kolom pesan_error.
     */
    public function runCancel(int $uploadId): void
    {
        try {
            $stagingSheetIds = Sheet::where('upload_id', $uploadId)->where('is_active', 0)->pluck('id');
            SheetData::whereIn('sheet_id', $stagingSheetIds)->delete();
            Sheet::whereIn('id', $stagingSheetIds)->delete();
        } catch (Throwable $e) {
            // Diamkan -- kegagalan cleanup tidak boleh menutupi pesan error asli
        }
    }

    // =========================================================================
    // 6. LOGIKA BANTUAN (HELPERS)
    // =========================================================================
    private function validateJenisMonitoring(array $sheetNames, string $jenisMonitoring, string $labelMonitoring): void
    {
        $kataKunciPerJenis = [
            'tiang'     => ['RPB', 'TA PER SPB', 'MAXIMA', 'WIKA', 'TOTAL VENDOR', 'REKAP KR', 'SPB'],
            'pelanggan' => ['PELANGGAN', 'PERMOHONAN'],
            'khs'       => ['KHS', 'RES', 'REKAP', 'AMR', 'GANTER', 'KR PBPD', 'COVER', 'PEMBESIAN', 'PENGIKAT', 'JOINTING', 'BKJ', 'DETAIL KR', 'KUOTA VENDOR'],
        ];

        $kataKunci = $kataKunciPerJenis[$jenisMonitoring] ?? null;
        if ($kataKunci === null) return;

        foreach ($sheetNames as $namaSheet) {
            $namaSheetUpper = mb_strtoupper(trim($namaSheet));
            foreach ($kataKunci as $kunci) {
                if (str_contains($namaSheetUpper, $kunci)) return;
            }
        }
        throw new \Exception("File BUKAN file {$labelMonitoring}. Silakan periksa kembali kecocokan file Anda.");
    }

    private function readWorksheetToArray(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet, int $highestRow, int $highestColumnIndex): array
    {
        $colLetters = [];
        for ($c = 1; $c <= $highestColumnIndex; $c++) $colLetters[$c] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);

        $rows = [];
        $emptyRowConsecutiveCount = 0;
        $maxEmptyRowsAllowed = 10;

        for ($r = 1; $r <= $highestRow; $r++) {
            $rowData = [];
            $isCompletelyEmpty = true;

            for ($c = 1; $c <= $highestColumnIndex; $c++) {
                $cell = $worksheet->getCell($colLetters[$c] . $r, false);
                if ($cell === null || $cell->getValue() === null) {
                    $rowData[] = null;
                    continue;
                }

                $rawValue = $cell->getValue();
                if ($rawValue instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                    $value = $rawValue->getPlainText();
                } elseif ($cell->isFormula()) {
                    $value = $cell->getOldCalculatedValue();
                    if ($value === null) {
                        try { $value = $cell->getCalculatedValue(); } catch (Throwable $e) { $value = $rawValue; }
                    }
                } else {
                    $value = $rawValue;
                }

                $style = $worksheet->getParentOrThrow()->getCellXfByIndex($cell->getXfIndex());
                $value = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::toFormattedString($value, $style->getNumberFormat()->getFormatCode() ?? \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_GENERAL);

                if (trim((string)$value) !== '') $isCompletelyEmpty = false;
                $rowData[] = $value;
            }

            if ($isCompletelyEmpty) {
                $emptyRowConsecutiveCount++;
                $rows[] = $rowData;
                if ($emptyRowConsecutiveCount >= $maxEmptyRowsAllowed) {
                    array_splice($rows, -$maxEmptyRowsAllowed);
                    break;
                }
            } else {
                $emptyRowConsecutiveCount = 0;
                $rows[] = $rowData;
            }
        }
        return $rows;
    }
}