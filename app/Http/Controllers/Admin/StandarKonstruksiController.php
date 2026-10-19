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
            ->orderBy('nomor')
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
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('standar-konstruksi', $namaFile, 'public');
            $fullPath = storage_path('app/public/' . $path);

            $reader = IOFactory::createReaderForFile($fullPath);
            $spreadsheet = $reader->load($fullPath);
            $worksheet = $spreadsheet->getSheet(0);

            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            if ($highestColumnIndex < 3) {
                throw new \Exception(
                    'File yang dipilih bukan file Standar Konstruksi. ' .
                    'Silakan pilih file Standar Konstruksi yang sesuai. ' .
                    '(Format kolom harus: No | Kontruksi SUTM 2026 | Link Gambar)'
                );
            }

            $headerNo = $this->normalisasiTeks((string) $worksheet->getCell('A1')->getValue());
            $headerNama = $this->normalisasiTeks((string) $worksheet->getCell('B1')->getValue());
            $headerLink = $this->normalisasiTeks((string) $worksheet->getCell('C1')->getValue());

            $headerValid =
                $headerNo === 'no' &&
                (str_contains($headerNama, 'sutm') && (str_contains($headerNama, 'konstruksi') || str_contains($headerNama, 'kontruksi'))) &&
                (str_contains($headerLink, 'link') && str_contains($headerLink, 'gambar'));

            if (!$headerValid) {
                throw new \Exception(
                    'File yang dipilih bukan file Standar Konstruksi. ' .
                    'Silakan pilih file Standar Konstruksi yang sesuai. ' .
                    '(Format kolom harus: No | Kontruksi SUTM 2026 | Link Gambar)'
                );
            }

            $data = [];
            $subBabKodeSaatIni = null;
            $subBabNamaSaatIni = null;
            $urutanKe = 0;

            for ($row = 2; $row <= $highestRow; $row++) {
                $no = trim((string) $worksheet->getCell('A' . $row)->getValue());
                $nama = trim((string) $worksheet->getCell('B' . $row)->getValue());
                $linkCell = $worksheet->getCell('C' . $row);
                $link = trim((string) $linkCell->getValue());

                $hyperlink = $linkCell->getHyperlink();
                if ($hyperlink && $hyperlink->getUrl() !== '') {
                    $link = trim((string) $hyperlink->getUrl());
                }

                if ($no === '' && $nama === '' && $link === '') {
                    continue;
                }

                if ($this->isBarisSubBab($no, $nama, $link)) {
                    $subBabKodeSaatIni = mb_strtoupper($no);
                    $subBabNamaSaatIni = $nama;
                    continue;
                }

                if ($nama === '') {
                    continue;
                }

                $urutanKe++;

                $data[] = [
                    'urutan'          => $urutanKe,
                    'nomor'           => is_numeric($no) ? (int) $no : ($row - 1), // KOREKSI: 'no' diubah menjadi 'nomor'
                    'nama_konstruksi' => $nama,
                    'link_gambar'     => $link,
                    'sub_bab_kode'    => $subBabKodeSaatIni,
                    'sub_bab_nama'    => $subBabNamaSaatIni,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }

            if (empty($data)) {
                throw new \Exception('Tidak ada data Standar Konstruksi yang ditemukan.');
            }

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

            $pesanSukses = 'Import berhasil. ' . count($data) . ' data Standar Konstruksi berhasil diperbarui' .
                ($jumlahSubBab > 0 ? ' (' . $jumlahSubBab . ' sub-bab).' : '.');

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => $pesanSukses]);
            }

            return redirect()->route('admin.standar-konstruksi.index')->with('success', $pesanSukses);

        } catch (\Throwable $e) {
            DB::rollBack();

            if (isset($path) && Storage::disk('public')->exists($path)) {
                try { Storage::disk('public')->delete($path); } catch (\Throwable $ignore) {}
            }

            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return redirect()->route('admin.standar-konstruksi.index')->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    private function normalisasiTeks(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;
        return trim($value);
    }

    private function isBarisSubBab(string $no, string $nama, string $link): bool
    {
        $kode = preg_replace('/[).:\-]+$/', '', $no) ?? $no;
        $kode = trim($kode);

        if ($kode === '' || $nama === '' || $link !== '') {
            return false;
        }

        if (is_numeric($kode)) {
            return false;
        }

        return (bool) preg_match('/^[A-Z]{1,4}[0-9]{0,3}$/', mb_strtoupper($kode));
    }
}