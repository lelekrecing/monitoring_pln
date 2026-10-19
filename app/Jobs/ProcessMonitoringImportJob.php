<?php

namespace App\Jobs;

use App\Http\Controllers\InputDataController;
use App\Models\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

/**
 * Job ini menggantikan loop JavaScript yang sebelumnya ada di input-data.blade.php.
 * Seluruh proses (baca semua sheet -> staging -> finalize) sekarang berjalan di sini,
 * dijalankan oleh queue worker (proses PHP CLI terpisah) -- SEPENUHNYA LEPAS dari
 * tab browser yang memicunya. Kalau pengguna pindah halaman, tutup tab, atau bahkan
 * mematikan komputernya, job ini tetap berjalan sampai selesai di server.
 *
 * Progres (sheet ke berapa, status, error) dituliskan ke tabel `uploads` di setiap
 * langkah, supaya bisa dipantau dari halaman manapun lewat endpoint
 * InputDataController::progress() / GET /input-data/progress/{upload_id}.
 */
class ProcessMonitoringImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Batas waktu job ini boleh berjalan (detik). Diset longgar (1 jam) karena tidak
     * lagi terikat batas timeout request HTTP hosting -- ini proses CLI di background.
     */
    public $timeout = 3600;

    /**
     * Jangan retry otomatis kalau gagal. Kalau di-retry otomatis, sheet yang sudah
     * sempat staging dari percobaan sebelumnya bisa dobel/konflik. Lebih aman: gagal
     * sekali -> staging dibersihkan (lihat handle()) -> admin upload ulang dari awal.
     */
    public $tries = 1;

    public function __construct(
        public int $uploadId,
        public string $jenisMonitoring,
        public string $labelMonitoring
    ) {}

    public function handle(InputDataController $importer): void
    {
        $upload = Upload::find($this->uploadId);
        if (!$upload) return;

        try {
            $upload->update(['status' => 'processing']);

            $fullPath = storage_path('app/public/' . $upload->file_path);
            if (!file_exists($fullPath)) {
                throw new \Exception('File fisik tidak ditemukan di server.');
            }

            // Baca ulang daftar nama sheet (operasi ini sangat cepat, tidak makan RAM)
            $reader = IOFactory::createReaderForFile($fullPath);
            $sheetNames = $reader->listWorksheetNames($fullPath);

            foreach ($sheetNames as $index => $sheetName) {
                $upload->update(['sheet_sedang_diproses' => $sheetName]);

                // Method inti ini sama persis dengan yang dipakai endpoint HTTP manual --
                // lihat InputDataController::runProcessSheet(). Melempar Throwable kalau gagal.
                $importer->runProcessSheet(
                    $upload->id,
                    $upload->file_path,
                    $sheetName,
                    $index,
                    $this->jenisMonitoring
                );

                $upload->increment('sheet_selesai');
            }

            // Semua sheet sukses sebagai staging -> swap jadi aktif, hapus data lama.
            // runFinalize() juga otomatis set status upload jadi 'aktif'.
            $importer->runFinalize($upload->id, $this->jenisMonitoring);

        } catch (Throwable $e) {
            // Bersihkan sisa sheet staging supaya tidak menumpuk sebagai sampah,
            // lalu catat pesan error yang sesungguhnya supaya bisa dilihat pengguna
            // lewat widget progres (data lama TIDAK disentuh -- tetap aman & aktif).
            $importer->runCancel($upload->id);
            $upload->update([
                'status'                => 'gagal',
                'pesan_error'           => $e->getMessage(),
                'sheet_sedang_diproses' => null,
            ]);
        }
    }

    /**
     * Dipanggil otomatis oleh Laravel kalau job ini gagal total (mis. exception di luar
     * try/catch handle(), atau proses worker mati mendadak). Jaring pengaman terakhir
     * supaya status upload tidak nyangkut selamanya di 'processing'.
     */
    public function failed(Throwable $exception): void
    {
        $upload = Upload::find($this->uploadId);
        if (!$upload) return;

        $upload->update([
            'status'                => 'gagal',
            'pesan_error'           => $exception->getMessage(),
            'sheet_sedang_diproses' => null,
        ]);
    }
}
