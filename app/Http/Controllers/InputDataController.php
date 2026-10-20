<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\Sheet;
use App\Models\SheetData;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;

class InputDataController extends Controller
{
    // ==========================
    // TAMBAHAN: Tiga pintu masuk upload terpisah sesuai jenis monitoring.
    // Semuanya memanggil logika import yang SAMA (importExcel), supaya
    // proses baca Excel & simpan ke database tetap satu sumber kebenaran
    // (tidak dobel kode, lebih gampang dirawat) tapi tetap terpisah
    // datanya per jenis monitoring.
    // ==========================

    public function storeKhs(Request $request)
    {
        return $this->importExcel($request, 'khs', 'Monitoring KHS');
    }

    public function storeTiang(Request $request)
    {
        return $this->importExcel($request, 'tiang', 'Monitoring Tiang');
    }

    public function storePelanggan(Request $request)
    {
        return $this->importExcel($request, 'pelanggan', 'Monitoring Pelanggan');
    }

    // Dipertahankan supaya route/kode lama yang masih memanggil store()
    // langsung tetap berfungsi seperti sebelumnya (upload KHS).
    public function store(Request $request)
    {
        return $this->storeKhs($request);
    }

    // ==========================
    // Proses import Excel (dipakai bersama oleh KHS/Tiang/Pelanggan)
    // ==========================
    private function importExcel(Request $request, string $jenisMonitoring, string $labelMonitoring)
    {
        // ===========================
        // Naikkan batas memori & waktu eksekusi
        // khusus untuk request ini saja.
        // ===========================
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        set_time_limit(0);

        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls|max:20480'
        ]);

        DB::beginTransaction();

        try {
            // ===========================
            // Upload File
            // ===========================
            $file = $request->file('file_excel');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $namaFile, 'public');

            $fullPath = storage_path('app/public/' . $path);

            // ===========================
            // Baca Excel
            // ===========================
            // setReadDataOnly(true) sengaja TIDAK dipakai supaya style
            // (Number Format tanggal/angka/Rp) dan merge cell ikut terbaca.
            // Keamanan terhadap error formula tetap terjaga karena
            // readWorksheetToArray() memakai getOldCalculatedValue().
            $reader = IOFactory::createReaderForFile($fullPath);
            $spreadsheet = $reader->load($fullPath);

            $sheetCount = $spreadsheet->getSheetCount();

            // ===========================
            // Validasi kecocokan file dengan jenis monitoring yang dipilih
            // (dicek dari NAMA SHEET di dalam file), SEBELUM data lama
            // dihapus & data baru disimpan.
            // ===========================
            $this->validateJenisMonitoring($spreadsheet, $jenisMonitoring, $labelMonitoring);

            // ===========================
            // Simpan Upload
            // ===========================
            $upload = Upload::create([
                'nama_files'   => $namaFile,
                'file_path'    => $path,
                'versi'        => 'v1',
                'jumlah_sheet' => $sheetCount,
                'upload_by'    => 1,
                'created_at'   => now(),
                'status'       => 'aktif'
            ]);

            // ===========================
            // Hapus Data Sheet & SheetData Lama
            // HANYA untuk jenis monitoring yang sama.
            // Riwayat di tabel "uploads" tetap disimpan.
            // ===========================
            $oldSheetIds = Sheet::where('jenis_monitoring', $jenisMonitoring)->pluck('id');
            SheetData::whereIn('sheet_id', $oldSheetIds)->delete();
            Sheet::where('jenis_monitoring', $jenisMonitoring)->delete();

            // ===========================
            // FIX: Cari tahun cadangan dari sheet mana pun di file ini.
            // Dipakai kalau suatu sheet (mis. "TOTAL VENDOR", "REKAP KR")
            // namanya tidak mengandung angka tahun, supaya kolom "tahun"
            // (NOT NULL) tidak pernah diisi null.
            // ===========================
            $tahunFile = null;

            foreach ($spreadsheet->getSheetNames() as $namaSheetCek) {
                if (preg_match('/(20\d{2})/', $namaSheetCek, $m)) {
                    $tahunFile = $m[1];
                    break;
                }
            }

            // ===========================
            // Loop Semua Sheet
            // ===========================
            foreach ($spreadsheet->getWorksheetIterator() as $index => $worksheet) {

                $sheetName = trim($worksheet->getTitle());
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                $highestColumnIndex =
                    \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                // Baca isi sheet. Sel berformula memakai nilai hasil
                // terakhir yang disimpan Excel (bukan teks formula mentah).
                $rows = $this->readWorksheetToArray($worksheet, $highestRow, $highestColumnIndex);

                // ===========================
                // Tangkap URL dari Hyperlink Excel
                // Sel yang tampil sebagai teks biasa tapi sebenarnya
                // berisi link (Insert Link) diganti dengan URL aslinya.
                // ===========================
                foreach ($worksheet->getHyperlinkCollection() as $coordinate => $hyperlink) {
                    $url = trim((string) $hyperlink->getUrl());

                    if ($url === '' || str_starts_with($url, '#')) {
                        continue; // lewati link internal antar-sel/sheet
                    }

                    [$colLetter, $rowNumber] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::coordinateFromString($coordinate);
                    $colIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($colLetter) - 1;
                    $rowIndex = $rowNumber - 1;

                    $currentValue = trim((string) ($rows[$rowIndex][$colIndex] ?? ''));

                    if (!preg_match('#^https?://#i', $currentValue)) {
                        $rows[$rowIndex][$colIndex] = $url;
                    }
                }

                // ===========================
                // Ambil Tahun (FIX: dengan fallback bertingkat)
                // 1. Dari nama sheet
                // 2. Dari sheet lain di file yang sama
                // 3. Dari input form (name="tahun"), kalau ada
                // 4. Tahun sekarang
                // ===========================
                $tahunDariNama = null;

                if (preg_match('/(20\d{2})/', $sheetName, $match)) {
                    $tahunDariNama = $match[1];
                }

                $tahun = $tahunDariNama
                    ?? $tahunFile
                    ?? $request->input('tahun')
                    ?? now()->year;

                // ===========================
                // Ambil Kategori
                // Tahun hanya dibuang dari nama kalau memang berasal dari
                // nama sheet itu sendiri (bukan hasil fallback).
                // ===========================
                $kategori = $sheetName;

                if ($tahunDariNama) {
                    $kategori = trim(str_replace($tahunDariNama, '', $sheetName));
                }

                // ===========================
                // Simpan Sheet
                // ===========================
                $sheetModel = Sheet::create([
                    'upload_id'        => $upload->id,
                    'nama_sheet'       => $sheetName,
                    'kategori'         => strtoupper($kategori),
                    'jenis_monitoring' => $jenisMonitoring,
                    'tahun'            => $tahun,
                    'urutan'           => $index + 1,
                    'total_rows'       => count($rows),
                    'is_active'        => 1,
                    'created_at'       => now(),
                ]);

                // ===========================
                // Simpan Isi Sheet (bulk insert)
                // ===========================
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
            }

            DB::commit();

            // Request AJAX (progress bar upload) dibalas JSON.
            // Form submit biasa tetap redirect + flash session.
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Upload ' . $labelMonitoring . ' berhasil. Total Sheet : ' . $sheetCount,
                ]);
            }

            return redirect()
                ->back()
                ->with('success', 'Upload ' . $labelMonitoring . ' berhasil. Total Sheet : ' . $sheetCount);

        } catch (\Exception $e) {

            DB::rollBack();

            // Kalau gagal, hapus file fisik yang sempat ke-upload supaya
            // tidak menumpuk jadi sampah di server.
            if (isset($path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                try {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                } catch (\Throwable $ignore) {
                    // Diabaikan - kegagalan hapus file tidak boleh
                    // menutupi pesan error asli di bawah.
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }

    // ==========================
    // Validasi kecocokan isi file Excel dengan jenis monitoring
    // yang dipilih saat upload (KHS/Tiang/Pelanggan). Dicek dari NAMA
    // SHEET di dalam file, berdasarkan daftar kata kunci per jenis.
    // Kalau TIDAK ADA satupun nama sheet yang cocok, upload ditolak.
    // ==========================
    private function validateJenisMonitoring(
        \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet,
        string $jenisMonitoring,
        string $labelMonitoring
    ): void {
        $kataKunciPerJenis = [
            // Sesuai daftar sheet Monitoring Tiang di Sidebar.
            'tiang' => ['RPB', 'TA PER SPB', 'MAXIMA', 'WIKA', 'TOTAL VENDOR', 'REKAP KR', 'SPB'],

            // Sesuai daftar sheet Monitoring Pelanggan di Sidebar.
            // "PERMOHONAN" supaya file sumber asli "Monitoring Permohonan"
            // tetap diterima.
            'pelanggan' => ['PELANGGAN', 'PERMOHONAN'],

            // Sesuai pengelompokan sheet Monitoring KHS di
            // DashboardController::buildKhsMenu().
            'khs' => [
                'KHS', 'RES', 'REKAP', 'AMR', 'GANTER', 'KR PBPD', 'COVER',
                'PEMBESIAN', 'PENGIKAT', 'JOINTING', 'BKJ', 'DETAIL KR',
                'KUOTA VENDOR',
            ],
        ];

        $kataKunci = $kataKunciPerJenis[$jenisMonitoring] ?? null;

        // Jenis monitoring yang belum dikenal tidak divalidasi.
        if ($kataKunci === null) {
            return;
        }

        $namaSheetAsli = $spreadsheet->getSheetNames();

        foreach ($namaSheetAsli as $namaSheet) {
            $namaSheetUpper = mb_strtoupper(trim($namaSheet));

            foreach ($kataKunci as $kunci) {
                if (str_contains($namaSheetUpper, $kunci)) {
                    return; // Ada sheet yang cocok -> file valid, lolos.
                }
            }
        }

        // Tidak ada satupun sheet yang cocok -> tolak upload.
        throw new \Exception(
            'File yang diupload sepertinya BUKAN file ' . $labelMonitoring . '. ' .
            'Nama sheet di dalam file Anda: "' . implode('", "', $namaSheetAsli) . '". ' .
            'Silakan periksa kembali dan upload file Excel yang sesuai untuk ' . $labelMonitoring . '.'
        );
    }

    // ==========================
    // Pengganti $worksheet->toArray() bawaan PhpSpreadsheet, supaya SEL
    // BERFORMULA menampilkan HASIL formulanya, bukan teks formula mentah.
    // Untuk sel berformula dipakai getOldCalculatedValue() (nilai cache
    // yang disimpan Excel), tanpa menjalankan mesin kalkulasi
    // PhpSpreadsheet.
    // ==========================
    private function readWorksheetToArray(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet,
        int $highestRow,
        int $highestColumnIndex
    ): array {
        $colLetters = [];
        for ($c = 1; $c <= $highestColumnIndex; $c++) {
            $colLetters[$c] = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
        }

        $rows = [];

        for ($r = 1; $r <= $highestRow; $r++) {
            $rowData = [];

            for ($c = 1; $c <= $highestColumnIndex; $c++) {
                // createIfNotExists = false, supaya sel kosong tidak ikut
                // dibuat di memori.
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

                    // Fallback: hanya kepakai kalau file belum pernah
                    // menyimpan nilai cache. Dibungkus try/catch supaya
                    // formula rumit tidak menggagalkan seluruh import.
                    if ($value === null) {
                        try {
                            $value = $cell->getCalculatedValue();
                        } catch (\Throwable $e) {
                            $value = $rawValue;
                        }
                    }
                } else {
                    $value = $rawValue;
                }

                // Terapkan format angka bawaan sel ke nilai akhirnya.
                $style = $worksheet->getParentOrThrow()->getCellXfByIndex($cell->getXfIndex());
                $value = \PhpOffice\PhpSpreadsheet\Style\NumberFormat::toFormattedString(
                    $value,
                    $style->getNumberFormat()->getFormatCode() ?? \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_GENERAL
                );

                $rowData[] = $value;
            }

            $rows[] = $rowData;
        }

        return $rows;
    }
}