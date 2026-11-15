<?php

namespace App\Services;

use App\Models\KajianProyek;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class KajianExportService
{
    public function __construct(private readonly KajianKelayakanService $kajianService)
    {
    }

    /**
     * Build downloadable Excel (.xlsx) untuk satu kajian proyek KKO-KKF (3 sheet).
     */
    public function exportSingleExcel(KajianProyek $proyek, array $vendorQuotas = []): array
    {
        $proyek->loadMissing(['pelanggans', 'uraiRows']);
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('SIGINJAI PLN UP3 Jambi')
            ->setTitle('KKO-KKF ' . $proyek->kode_kajian);

        $kelLabel = $proyek->layak_otomatis ? 'LAYAK / DITERIMA' : 'BELUM LAYAK';
        $paybackText = $proyek->payback_tahun !== null ? round((float) $proyek->payback_tahun, 2) . ' tahun' : '-';

        // ============================================================
        // SHEET 1: Ringkasan KKO-KKF
        // ============================================================
        $s1 = $spreadsheet->getActiveSheet();
        $s1->setTitle('Ringkasan KKO-KKF');

        $this->styleBanner($s1, 'A1:D1', 'PT PLN (PERSERO) UP3 JAMBI - KAJIAN KELAYAKAN PROYEK (KKO - KKF)');
        $s1->setCellValue('A2', 'Dicetak dari SIGINJAI PLN pada ' . now()->format('d/m/Y H:i') . ' WIB');
        $s1->mergeCells('A2:D2');
        $s1->getStyle('A2')->getFont()->setSize(9)->setItalic(true)->getColor()->setRGB('475569');

        $summaryRows = [
            ['Kode Kajian', $proyek->kode_kajian, 'Tahun Anggaran', (string) $proyek->tahun],
            ['Nama Pekerjaan / Proyek', $proyek->nama_pekerjaan, 'ULP Pemohon', $proyek->ulp ?: '-'],
            ['Mode Kajian', strtoupper((string) $proyek->mode), 'Jarak dari Gudang', round((float) $proyek->jarak_gudang_km, 2) . ' km'],
            ['Status Proses', strtoupper(str_replace('_', ' ', (string) $proyek->status)), 'Keputusan Kelayakan', $kelLabel . ($proyek->hasil_ringkas ? ' (' . $proyek->hasil_ringkas . ')' : '')],
            ['Total Jumlah Pelanggan', (int) $proyek->total_pelanggan . ' plg', 'Total Penambahan Daya', round((float) $proyek->total_daya_kva, 2) . ' kVA'],
            ['Jumlah / Kapasitas Trafo', (int) $proyek->jumlah_trafo . ' bh (' . round((float) $proyek->kapasitas_trafo_kva, 0) . ' kVA)', 'Jumlah Tiang Terhitung', (int) $proyek->jumlah_tiang . ' btg'],
            ['Panjang SUTM / SUTR', round((float) $proyek->estimasi_sutm_km, 3) . ' km / ' . round((float) $proyek->estimasi_sutr_km, 3) . ' km', 'Payback Period', $paybackText],
            ['Estimasi Nilai RAB (Rp)', (float) $proyek->estimasi_nilai_rab, 'Est. Pendapatan / Tahun (Rp)', (float) $proyek->estimasi_pendapatan_tahun],
            ['Vendor Kontrak', $proyek->vendor_kontrak ?: '-', 'Nilai Kontrak Pakai (Rp)', (float) $proyek->nilai_kontrak_pakai],
            ['No. Kontrak / SPK', $proyek->no_kontrak ?: '-', 'Tanggal Terkontrak', $proyek->terkontrak_at ? $proyek->terkontrak_at->format('d/m/Y H:i') : '-'],
            ['Catatan ULP', $proyek->catatan_ulp ?: '-', 'Catatan Perencanaan', $proyek->catatan_perencanaan ?: '-'],
        ];

        $this->styleHeaderRow($s1, 'A4:D4');
        $s1->fromArray([['PARAMETER KAJIAN', 'NILAI / URAIAN', 'PARAMETER KAJIAN', 'NILAI / URAIAN']], null, 'A4');
        $rowIdx = 5;
        foreach ($summaryRows as $item) {
            $s1->fromArray([$item], null, 'A' . $rowIdx);
            $s1->getStyle('A' . $rowIdx)->getFont()->setBold(true);
            $s1->getStyle('C' . $rowIdx)->getFont()->setBold(true);
            $s1->getStyle("A{$rowIdx}:D{$rowIdx}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($rowIdx % 2 === 0 ? 'F8FAFC' : 'FFFFFF');
            foreach (['B', 'D'] as $col) {
                $val = $s1->getCell($col . $rowIdx)->getValue();
                if (is_float($val)) {
                    $s1->getStyle($col . $rowIdx)->getNumberFormat()->setFormatCode('#,##0');
                }
            }
            $rowIdx++;
        }
        $this->applyBorders($s1, 'A4:D' . ($rowIdx - 1));
        foreach (['A' => 28, 'B' => 38, 'C' => 28, 'D' => 38] as $col => $w) {
            $s1->getColumnDimension($col)->setWidth($w);
        }

        // ============================================================
        // SHEET 2: Data Pelanggan PB-PD
        // ============================================================
        $s2 = $spreadsheet->createSheet();
        $s2->setTitle('Data Pelanggan PB-PD');
        $this->styleBanner($s2, 'A1:I1', 'DATA PELANGGAN PB / PD - ' . $proyek->kode_kajian . ' (' . $proyek->nama_pekerjaan . ')');

        $pelHeaders = [
            'Slot', 'Permohonan', 'Kategori', 'Tarif & Daya Tujuan',
            'Jml Pelanggan', 'Daya Sebelum (kVA)', 'Daya Sesudah (kVA)',
            'Selisih / Pelanggan (kVA)', 'Total Tambah Daya (kVA)'
        ];
        $s2->fromArray([$pelHeaders], null, 'A3');
        $this->styleHeaderRow($s2, 'A3:I3');

        $r2 = 4;
        foreach ($proyek->pelanggans as $pel) {
            $dayaSblm = (float) $pel->daya_sebelum_kva;
            $dayaSsdh = (float) $pel->daya_sesudah_kva;
            $delta = max(0, $dayaSsdh - $dayaSblm);
            $s2->fromArray([[
                (int) $pel->slot,
                strtoupper((string) $pel->jenis_permohonan),
                (string) ($pel->kategori_tarif ?: '-'),
                (string) $pel->kelompok_tarif,
                (int) $pel->jumlah_pelanggan,
                $dayaSblm,
                $dayaSsdh,
                round($delta, 2),
                (float) $pel->penambahan_daya_kva,
            ]], null, 'A' . $r2);
            $r2++;
        }
        if ($r2 === 4) {
            $s2->setCellValue('A4', 'Belum ada data pelanggan.');
            $s2->mergeCells('A4:I4');
            $r2 = 5;
        } else {
            $s2->fromArray([[
                '', 'TOTAL', '', '',
                (int) $proyek->total_pelanggan,
                '', '', '',
                round((float) $proyek->total_daya_kva, 2),
            ]], null, 'A' . $r2);
            $s2->getStyle("A{$r2}:I{$r2}")->getFont()->setBold(true);
            $s2->getStyle("A{$r2}:I{$r2}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EFF6FF');
            $r2++;
        }
        $this->applyBorders($s2, 'A3:I' . ($r2 - 1));
        foreach (range('A', 'I') as $col) {
            $s2->getColumnDimension($col)->setAutoSize(true);
        }

        // ============================================================
        // SHEET 3: Input URAI 23 Kolom
        // ============================================================
        $s3 = $spreadsheet->createSheet();
        $s3->setTitle('Input URAI 23 Kolom');
        $colCount = count(KajianKelayakanService::URAI_COLUMNS) + 1;
        $lastColLetter = Coordinate::stringFromColumnIndex($colCount);

        $this->styleBanner($s3, "A1:{$lastColLetter}1", 'INPUT URAI KONSTRUKSI (23 KOLOM) - ' . $proyek->kode_kajian);
        $uraiHeaderLabels = ['Baris'];
        foreach (KajianKelayakanService::URAI_COLUMNS as $colKey) {
            $meta = KajianKelayakanService::URAI_COLUMN_META[$colKey] ?? ['label' => $colKey];
            $uraiHeaderLabels[] = $colKey . ' (' . $meta['label'] . ')';
        }

        $s3->fromArray([$uraiHeaderLabels], null, 'A3');
        $this->styleHeaderRow($s3, "A3:{$lastColLetter}3");

        $r3 = 4;
        foreach ($proyek->uraiRows as $row) {
            $payload = $row->row_data ?? [];
            $line = [(int) $row->row_number];
            foreach (KajianKelayakanService::URAI_COLUMNS as $colKey) {
                $line[] = $payload[$colKey] ?? '';
            }
            $s3->fromArray([$line], null, 'A' . $r3);
            $r3++;
        }
        if ($r3 === 4) {
            $s3->setCellValue('A4', 'Belum ada baris Input URAI terisi.');
            $s3->mergeCells("A4:{$lastColLetter}4");
            $r3 = 5;
        }
        $this->applyBorders($s3, "A3:{$lastColLetter}" . ($r3 - 1));
        for ($i = 1; $i <= $colCount; $i++) {
            $c = Coordinate::stringFromColumnIndex($i);
            $s3->getColumnDimension($c)->setWidth($i === 1 ? 8 : 17);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return [
            'content'  => $this->renderSpreadsheetToBytes($spreadsheet),
            'filename' => 'KKO-KKF_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $proyek->kode_kajian) . '.xlsx',
        ];
    }

    /**
     * Build downloadable Rekap Excel (.xlsx) daftar kajian + kuota kontrak vendor.
     */
    public function exportRecapExcel(Collection|array $projects, array $vendorQuotas = [], array $filters = []): array
    {
        $projects = collect($projects);
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('SIGINJAI PLN UP3 Jambi')
            ->setTitle('Rekap Kajian Kelayakan Proyek');

        $s1 = $spreadsheet->getActiveSheet();
        $s1->setTitle('Rekap Kajian Kelayakan');

        $filterNote = [];
        if (!empty($filters['tahun'])) {
            $filterNote[] = 'Tahun: ' . $filters['tahun'];
        }
        if (!empty($filters['ulp'])) {
            $filterNote[] = 'ULP: ' . $filters['ulp'];
        }
        if (!empty($filters['status'])) {
            $filterNote[] = 'Status: ' . strtoupper((string) $filters['status']);
        }
        if (!empty($filters['search'])) {
            $filterNote[] = 'Cari: "' . $filters['search'] . '"';
        }

        $this->styleBanner($s1, 'A1:N1', 'REKAPITULASI KAJIAN KELAYAKAN PROYEK (KKO-KKF) - PLN UP3 JAMBI');
        $s1->setCellValue('A2', 'Dicetak: ' . now()->format('d/m/Y H:i') . ' WIB' . ($filterNote ? ' | Filter: ' . implode(', ', $filterNote) : ' | Semua Data'));
        $s1->mergeCells('A2:N2');
        $s1->getStyle('A2')->getFont()->setSize(9)->setItalic(true)->getColor()->setRGB('475569');

        $headers = [
            'No', 'Kode Kajian', 'Mode', 'Nama Pekerjaan', 'ULP', 'Tahun',
            'Status', 'Kelayakan', 'Jml Pelanggan', 'Total Daya (kVA)',
            'Estimasi RAB (Rp)', 'Est. Pendapatan / Thn (Rp)', 'Payback (Th)', 'Vendor Kontrak'
        ];
        $s1->fromArray([$headers], null, 'A4');
        $this->styleHeaderRow($s1, 'A4:N4');

        $r = 5;
        $totPlg = 0;
        $totDaya = 0.0;
        $totRab = 0.0;
        $totPend = 0.0;

        foreach ($projects->values() as $idx => $p) {
            $totPlg += (int) $p->total_pelanggan;
            $totDaya += (float) $p->total_daya_kva;
            $totRab += (float) $p->estimasi_nilai_rab;
            $totPend += (float) $p->estimasi_pendapatan_tahun;

            $s1->fromArray([[
                $idx + 1,
                $p->kode_kajian,
                strtoupper((string) $p->mode),
                $p->nama_pekerjaan,
                $p->ulp ?: '-',
                (string) $p->tahun,
                strtoupper(str_replace('_', ' ', (string) $p->status)),
                $p->layak_otomatis ? 'LAYAK' : 'BELUM LAYAK',
                (int) $p->total_pelanggan,
                round((float) $p->total_daya_kva, 2),
                (float) $p->estimasi_nilai_rab,
                (float) $p->estimasi_pendapatan_tahun,
                $p->payback_tahun !== null ? round((float) $p->payback_tahun, 2) : '-',
                $p->vendor_kontrak ?: '-',
            ]], null, 'A' . $r);

            $s1->getStyle("K{$r}:L{$r}")->getNumberFormat()->setFormatCode('#,##0');
            $r++;
        }

        if ($r === 5) {
            $s1->setCellValue('A5', 'Tidak ada data kajian sesuai filter.');
            $s1->mergeCells('A5:N5');
            $r = 6;
        } else {
            $s1->fromArray([[
                '', 'TOTAL (' . $projects->count() . ' kajian)', '', '', '', '', '', '',
                $totPlg, round($totDaya, 2), $totRab, $totPend, '', ''
            ]], null, 'A' . $r);
            $s1->getStyle("A{$r}:N{$r}")->getFont()->setBold(true);
            $s1->getStyle("A{$r}:N{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EFF6FF');
            $s1->getStyle("K{$r}:L{$r}")->getNumberFormat()->setFormatCode('#,##0');
            $r++;
        }

        $this->applyBorders($s1, 'A4:N' . ($r - 1));
        foreach (range('A', 'N') as $col) {
            $s1->getColumnDimension($col)->setAutoSize(true);
        }

        // ============================================================
        // SHEET 2: Kuota Kontrak Vendor
        // ============================================================
        $s2 = $spreadsheet->createSheet();
        $s2->setTitle('Kuota Kontrak Vendor');
        $this->styleBanner($s2, 'A1:J1', 'MONITORING KUOTA KONTRAK VENDOR & PEKERJAAN - PLN UP3 JAMBI');

        $qHeaders = [
            'No', 'Nama Vendor / KHS', 'No. Kontrak', 'Tahun', 'Kuota Awal (Rp)',
            'Terpakai Total (Rp)', 'Sisa Kuota (Rp)', 'Kuota Pekerjaan', 'Jml Terkontrak', 'Status Kuota'
        ];
        $s2->fromArray([$qHeaders], null, 'A3');
        $this->styleHeaderRow($s2, 'A3:J3');

        $r2 = 4;
        foreach ($vendorQuotas as $idx => $q) {
            $terpakai = (float) ($q['realisasi_sheet_rp'] ?? 0) + (float) ($q['terpakai_kajian_rp'] ?? 0);
            $s2->fromArray([[
                $idx + 1,
                $q['vendor'] ?? '-',
                $q['no_kontrak'] ?: '-',
                $q['tahun'] ?? '-',
                (float) ($q['kuota_awal_rp'] ?? 0),
                $terpakai,
                (float) ($q['sisa_kuota_rp'] ?? 0),
                (int) ($q['kuota_pekerjaan_maks'] ?? 0),
                (int) ($q['jumlah_terkontrak'] ?? 0),
                !empty($q['kuota_habis']) ? 'KUOTA HABIS' : (empty($q['status_aktif']) ? 'NONAKTIF' : 'TERSEDIA'),
            ]], null, 'A' . $r2);
            $s2->getStyle("E{$r2}:G{$r2}")->getNumberFormat()->setFormatCode('#,##0');
            $r2++;
        }
        if ($r2 === 4) {
            $s2->setCellValue('A4', 'Belum ada data kuota vendor.');
            $s2->mergeCells('A4:J4');
            $r2 = 5;
        }
        $this->applyBorders($s2, 'A3:J' . ($r2 - 1));
        foreach (range('A', 'J') as $col) {
            $s2->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        return [
            'content'  => $this->renderSpreadsheetToBytes($spreadsheet),
            'filename' => 'Rekap_Kajian_Kelayakan_PLN_UP3_Jambi_' . now()->format('Ymd_His') . '.xlsx',
        ];
    }

    /**
     * Build downloadable PDF (pure PHP %PDF-1.4 A4 Portrait) untuk 1 kajian KKO-KKF.
     */
    public function exportSinglePdf(KajianProyek $proyek, array $vendorQuotas = []): array
    {
        $proyek->loadMissing(['pelanggans', 'uraiRows']);
        $pdf = new PurePhpPdfBuilder('P');

        $pdf->drawHeader(
            'PT PLN (PERSERO) UP3 JAMBI',
            'KAJIAN KELAYAKAN PROYEK (KKO - KKF) • ' . $proyek->kode_kajian,
            'Dicetak: ' . now()->format('d/m/Y H:i') . ' WIB'
        );

        $kelStatus = $proyek->layak_otomatis ? 'LAYAK / DITERIMA' : 'BELUM LAYAK';
        $kelColor = $proyek->layak_otomatis ? [16, 185, 129] : [225, 29, 72];
        $pdf->drawInfoBanner(
            $proyek->nama_pekerjaan,
            'ULP: ' . ($proyek->ulp ?: '-') . ' | Tahun: ' . $proyek->tahun . ' | Mode: ' . strtoupper((string) $proyek->mode),
            $kelStatus . ' (' . strtoupper(str_replace('_', ' ', (string) $proyek->status)) . ')',
            $kelColor
        );

        $pdf->drawSectionTitle('1. RINGKASAN IDENTITAS & ANALISA KELAYAKAN');
        $paybackText = $proyek->payback_tahun !== null ? round((float) $proyek->payback_tahun, 2) . ' tahun' : '-';
        $kvPairs = [
            ['Kode Kajian', $proyek->kode_kajian, 'Tahun Anggaran', (string) $proyek->tahun],
            ['ULP Pemohon', $proyek->ulp ?: '-', 'Jarak Gudang', round((float) $proyek->jarak_gudang_km, 2) . ' km'],
            ['Total Pelanggan', (int) $proyek->total_pelanggan . ' plg', 'Total Tambah Daya', round((float) $proyek->total_daya_kva, 2) . ' kVA'],
            ['Estimasi Nilai RAB', $this->rp((float) $proyek->estimasi_nilai_rab), 'Est. Pendapatan / Thn', $this->rp((float) $proyek->estimasi_pendapatan_tahun)],
            ['Payback Period', $paybackText, 'Panjang SUTM / SUTR', round((float) $proyek->estimasi_sutm_km, 3) . ' / ' . round((float) $proyek->estimasi_sutr_km, 3) . ' km'],
            ['Trafo & Tiang', (int) $proyek->jumlah_trafo . ' bh (' . round((float) $proyek->kapasitas_trafo_kva, 0) . ' kVA) • ' . (int) $proyek->jumlah_tiang . ' btg', 'Vendor Kontrak', $proyek->vendor_kontrak ?: '-'],
        ];
        $pdf->drawKeyValueGrid($kvPairs);

        if (!empty($proyek->hasil_ringkas) || !empty($proyek->catatan_ulp)) {
            $pdf->drawNoteBox('Catatan: ' . trim(($proyek->hasil_ringkas ?: '') . ($proyek->catatan_ulp ? ' | ULP: ' . $proyek->catatan_ulp : '')));
        }

        $pdf->drawSectionTitle('2. DATA PELANGGAN PB / PD');
        $pelRows = [];
        foreach ($proyek->pelanggans as $pel) {
            $pelRows[] = [
                (string) (int) $pel->slot,
                strtoupper((string) $pel->jenis_permohonan),
                (string) $pel->kelompok_tarif,
                number_format((float) $pel->daya_sebelum_kva, 2, ',', '.'),
                number_format((float) $pel->daya_sesudah_kva, 2, ',', '.'),
                (string) (int) $pel->jumlah_pelanggan,
                number_format((float) $pel->penambahan_daya_kva, 2, ',', '.'),
            ];
        }
        if (empty($pelRows)) {
            $pelRows[] = ['-', '-', 'Belum ada data pelanggan tercatat', '0', '0', '0', '0'];
        }
        $pdf->drawTable(
            ['Slot', 'Jenis', 'Tarif & Daya Tujuan', 'Sblm (kVA)', 'Ssdh (kVA)', 'Plg', 'Tambah Daya (kVA)'],
            [30, 42, 185, 65, 65, 38, 102],
            $pelRows,
            ['C', 'C', 'L', 'R', 'R', 'R', 'R']
        );

        $pdf->drawSectionTitle('3. RINGKASAN INPUT URAI KONSTRUKSI (' . $proyek->uraiRows->count() . ' BARIS)');
        $uraiRows = [];
        foreach ($proyek->uraiRows->take(45) as $row) {
            $d = $row->row_data ?? [];
            $uraiRows[] = [
                (string) $row->row_number,
                (string) ($d['AJ'] ?? '-'),
                (string) ($d['AL'] ?? '-'),
                (string) ($d['VOL150'] ?? '0'),
                (string) ($d['BA'] ?? '0'),
                (string) ($d['BG'] ?? '-'),
                (string) (($d['CANTOL'] ?? '') !== '' ? 'C:' . $d['CANTOL'] : (($d['PORTAL'] ?? '') !== '' ? 'P:' . $d['PORTAL'] : '-')),
                (string) ($d['DP'] ?? '-'),
                (string) ($d['VOLJTR'] ?? '0'),
                (string) ($d['DS'] ?? '0'),
            ];
        }
        if (empty($uraiRows)) {
            $uraiRows[] = ['-', '-', 'Belum ada baris URAI terisi', '0', '0', '-', '-', '-', '0', '0'];
        }
        $pdf->drawTable(
            ['Brs', 'AJ (TM)', 'AL (Konst)', 'Vol150', 'BA', 'BG (kVA)', 'Trafo', 'DP (Konst)', 'VolJTR', 'DS'],
            [26, 76, 56, 38, 28, 42, 74, 56, 40, 28],
            $uraiRows,
            ['C', 'L', 'L', 'R', 'R', 'R', 'L', 'L', 'R', 'R']
        );

        if ($proyek->uraiRows->count() > 45) {
            $pdf->drawNoteBox('Catatan: ' . ($proyek->uraiRows->count() - 45) . ' baris URAI lanjutan tersedia lengkap pada file Export Excel (.xlsx).');
        }

        return [
            'content'  => $pdf->output(),
            'filename' => 'KKO-KKF_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $proyek->kode_kajian) . '.pdf',
        ];
    }

    /**
     * Build downloadable Rekap PDF (A4 Landscape) daftar kajian + kuota vendor.
     */
    public function exportRecapPdf(Collection|array $projects, array $vendorQuotas = [], array $filters = []): array
    {
        $projects = collect($projects);
        $pdf = new PurePhpPdfBuilder('L');

        $filterSummary = [];
        if (!empty($filters['tahun'])) {
            $filterSummary[] = 'Tahun ' . $filters['tahun'];
        }
        if (!empty($filters['ulp'])) {
            $filterSummary[] = 'ULP ' . $filters['ulp'];
        }
        if (!empty($filters['status'])) {
            $filterSummary[] = 'Status ' . strtoupper((string) $filters['status']);
        }
        $sub = 'REKAP KAJIAN KELAYAKAN PROYEK & KUOTA KONTRAK' . ($filterSummary ? ' (' . implode(' | ', $filterSummary) . ')' : '');

        $pdf->drawHeader('PT PLN (PERSERO) UP3 JAMBI', $sub, 'Dicetak: ' . now()->format('d/m/Y H:i') . ' WIB');

        $totPlg = (int) $projects->sum('total_pelanggan');
        $totDaya = (float) $projects->sum('total_daya_kva');
        $totRab = (float) $projects->sum('estimasi_nilai_rab');
        $totPend = (float) $projects->sum('estimasi_pendapatan_tahun');

        $pdf->drawInfoBanner(
            'Total: ' . $projects->count() . ' Kajian • ' . number_format($totPlg, 0, ',', '.') . ' Pelanggan • ' . number_format($totDaya, 1, ',', '.') . ' kVA',
            'Total Estimasi RAB: ' . $this->rp($totRab) . ' | Est. Pendapatan Tahunan: ' . $this->rp($totPend),
            count($vendorQuotas) . ' VENDOR TERDAFTAR',
            [12, 45, 72]
        );

        $pdf->drawSectionTitle('1. DAFTAR KAJIAN KELAYAKAN PROYEK (KKO - KKF)');
        $projRows = [];
        foreach ($projects->values() as $idx => $p) {
            $projRows[] = [
                (string) ($idx + 1),
                (string) $p->kode_kajian,
                (string) $p->nama_pekerjaan,
                (string) ($p->ulp ?: '-'),
                (string) $p->tahun,
                strtoupper(str_replace('_', ' ', (string) $p->status)),
                $p->layak_otomatis ? 'LAYAK' : 'BELUM LAYAK',
                (string) (int) $p->total_pelanggan,
                number_format((float) $p->total_daya_kva, 1, ',', '.'),
                $this->rp((float) $p->estimasi_nilai_rab),
                (string) ($p->vendor_kontrak ?: '-'),
            ];
        }
        if (empty($projRows)) {
            $projRows[] = ['-', '-', 'Tidak ada data kajian sesuai filter', '-', '-', '-', '-', '0', '0', 'Rp 0', '-'];
        }
        $pdf->drawTable(
            ['No', 'Kode', 'Nama Pekerjaan', 'ULP', 'Thn', 'Status', 'Kelayakan', 'Plg', 'kVA', 'Est. RAB', 'Vendor Kontrak'],
            [24, 78, 162, 84, 32, 68, 60, 30, 44, 88, 99],
            $projRows,
            ['C', 'L', 'L', 'L', 'C', 'C', 'C', 'R', 'R', 'R', 'L']
        );

        $pdf->drawSectionTitle('2. MONITORING KUOTA KONTRAK VENDOR');
        $qRows = [];
        foreach ($vendorQuotas as $idx => $q) {
            $terpakai = (float) ($q['realisasi_sheet_rp'] ?? 0) + (float) ($q['terpakai_kajian_rp'] ?? 0);
            $jobs = ((int) ($q['jumlah_terkontrak'] ?? 0)) . ' / ' . (((int) ($q['kuota_pekerjaan_maks'] ?? 0)) > 0 ? (int) $q['kuota_pekerjaan_maks'] : 'Unl');
            $qRows[] = [
                (string) ($idx + 1),
                (string) ($q['vendor'] ?? '-'),
                (string) ($q['no_kontrak'] ?: '-'),
                (string) ($q['tahun'] ?? '-'),
                $this->rp((float) ($q['kuota_awal_rp'] ?? 0)),
                $this->rp($terpakai),
                $this->rp((float) ($q['sisa_kuota_rp'] ?? 0)),
                $jobs,
                !empty($q['kuota_habis']) ? 'HABIS' : (empty($q['status_aktif']) ? 'NONAKTIF' : 'TERSEDIA'),
            ];
        }
        if (empty($qRows)) {
            $qRows[] = ['-', 'Belum ada kuota vendor terdaftar', '-', '-', 'Rp 0', 'Rp 0', 'Rp 0', '0/0', '-'];
        }
        $pdf->drawTable(
            ['No', 'Nama Vendor', 'No. Kontrak', 'Thn', 'Kuota Awal', 'Terpakai', 'Sisa Kuota', 'Pekerjaan', 'Status'],
            [24, 185, 115, 36, 95, 95, 95, 58, 67],
            $qRows,
            ['C', 'L', 'L', 'C', 'R', 'R', 'R', 'C', 'C']
        );

        return [
            'content'  => $pdf->output(),
            'filename' => 'Rekap_Kajian_Kelayakan_PLN_UP3_Jambi_' . now()->format('Ymd_His') . '.pdf',
        ];
    }

    /**
     * Build downloadable PDF (A4 Landscape) untuk sheet Pusat Monitoring (menghormati filter q/tahun/dll).
     */
    public function exportSheetPdf(object $sheet, Collection|array $rows, array $filters = []): array
    {
        $rows = collect($rows);
        $q = mb_strtolower(trim((string) ($filters['q'] ?? '')));
        $tahun = trim((string) ($filters['tahun'] ?? ''));

        $extraTerms = [];
        foreach (['vendor', 'pelanggan', 'kr', 'paket', 'lokasi'] as $fk) {
            $fv = trim((string) ($filters[$fk] ?? ''));
            if ($fv !== '') {
                $extraTerms[] = mb_strtolower($fv);
            }
        }

        $decodedRows = [];
        foreach ($rows as $row) {
            $cells = is_array($row->row_data ?? null)
                ? $row->row_data
                : (json_decode((string) ($row->row_data ?? '[]'), true) ?: []);
            if (!is_array($cells)) {
                continue;
            }
            $cleanCells = array_map(fn ($v) => trim((string) ($v ?? '')), $cells);
            $hasAny = false;
            foreach ($cleanCells as $cv) {
                if ($cv !== '') {
                    $hasAny = true;
                    break;
                }
            }
            if (!$hasAny) {
                continue;
            }
            $decodedRows[] = $cleanCells;
        }

        // Cari baris header utama (baris pertama yang punya >= 3 sel terisi & bukan sekadar judul tunggal)
        $headerIdx = 0;
        foreach ($decodedRows as $idx => $cells) {
            $nonEmpty = count(array_filter($cells, fn ($v) => $v !== ''));
            if ($nonEmpty >= 3) {
                $headerIdx = $idx;
                break;
            }
        }

        $headerCells = $decodedRows[$headerIdx] ?? [];
        $dataCandidateRows = array_slice($decodedRows, $headerIdx + 1);

        // Tentukan indeks kolom yang terisi & maksimal 10 kolom agar muat rapi pada A4 Landscape
        $maxColCount = count($headerCells);
        foreach ($dataCandidateRows as $r) {
            $maxColCount = max($maxColCount, count($r));
        }
        $activeCols = [];
        for ($c = 0; $c < $maxColCount; $c++) {
            $colHasVal = trim((string) ($headerCells[$c] ?? '')) !== '';
            if (!$colHasVal) {
                foreach (array_slice($dataCandidateRows, 0, 30) as $sample) {
                    if (trim((string) ($sample[$c] ?? '')) !== '') {
                        $colHasVal = true;
                        break;
                    }
                }
            }
            if ($colHasVal) {
                $activeCols[] = $c;
            }
            if (count($activeCols) >= 10) {
                break;
            }
        }
        if (empty($activeCols)) {
            $activeCols = [0];
        }

        $tableHeaders = ['No'];
        foreach ($activeCols as $cIdx => $colIndex) {
            $lbl = trim((string) ($headerCells[$colIndex] ?? ''));
            $tableHeaders[] = $lbl !== '' ? $lbl : ('Kol ' . ($cIdx + 1));
        }

        $filteredData = [];
        $lineNo = 1;
        foreach ($dataCandidateRows as $cells) {
            $joined = implode(' ', $cells);
            $lower = mb_strtolower($joined);

            if ($q !== '' && !str_contains($lower, $q)) {
                continue;
            }
            if ($tahun !== '' && !str_contains($joined, $tahun)) {
                continue;
            }
            $okExtra = true;
            foreach ($extraTerms as $term) {
                if (!str_contains($lower, $term)) {
                    $okExtra = false;
                    break;
                }
            }
            if (!$okExtra) {
                continue;
            }

            $outRow = [(string) $lineNo++];
            foreach ($activeCols as $colIndex) {
                $outRow[] = (string) ($cells[$colIndex] ?? '');
            }
            $filteredData[] = $outRow;
        }

        $totalMatched = count($filteredData);
        $truncated = false;
        if ($totalMatched > 400) {
            $filteredData = array_slice($filteredData, 0, 400);
            $truncated = true;
        }
        if (empty($filteredData)) {
            $emptyRow = array_fill(0, count($tableHeaders), '-');
            $emptyRow[1] = 'Tidak ada data yang cocok dengan filter';
            $filteredData[] = $emptyRow;
        }

        $usableW = 773.0;
        $noW = 26.0;
        $otherCount = max(1, count($tableHeaders) - 1);
        $colW = ($usableW - $noW) / $otherCount;
        $widths = [$noW];
        $aligns = ['C'];
        for ($i = 0; $i < $otherCount; $i++) {
            $widths[] = $colW;
            $aligns[] = 'L';
        }

        $pdf = new PurePhpPdfBuilder('L');
        $filterNote = [];
        if ($q !== '') {
            $filterNote[] = 'Cari: "' . $filters['q'] . '"';
        }
        if ($tahun !== '') {
            $filterNote[] = 'Tahun: ' . $tahun;
        }
        foreach ($extraTerms as $t) {
            $filterNote[] = $t;
        }

        $sheetName = (string) ($sheet->nama_sheet ?? 'Sheet Monitoring');
        $pdf->drawHeader(
            'PT PLN (PERSERO) UP3 JAMBI • PUSAT MONITORING',
            $sheetName . ($filterNote ? ' (' . implode(' | ', $filterNote) . ')' : ''),
            'Dicetak: ' . now()->format('d/m/Y H:i') . ' WIB'
        );

        $pdf->drawInfoBanner(
            'Sheet: ' . $sheetName,
            'Menampilkan ' . number_format($totalMatched, 0, ',', '.') . ' baris data' . ($filterNote ? ' (terfilter)' : ' (seluruh data)'),
            $totalMatched . ' BARIS',
            [13, 33, 55]
        );

        $pdf->drawTable($tableHeaders, $widths, $filteredData, $aligns);

        if ($truncated) {
            $pdf->drawNoteBox('Catatan: PDF menampilkan 400 baris pertama agar ukuran dokumen tetap ringkas. Gunakan pencarian/filter Tahun untuk menyaring data spesifik.');
        }

        return [
            'content'  => $pdf->output(),
            'filename' => 'Monitoring_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $sheetName) . '_' . now()->format('Ymd_His') . '.pdf',
        ];
    }

    /**
     * Build downloadable PDF (A4 Portrait) untuk data Standar Konstruksi (User & Admin).
     */
    public function exportStandarKonstruksiPdf(Collection|array $items, array $filters = []): array
    {
        $items = collect($items);
        $q = mb_strtolower(trim((string) ($filters['q'] ?? '')));
        $tahun = trim((string) ($filters['tahun'] ?? ''));

        $filtered = $items->filter(function ($item) use ($q, $tahun) {
            $hay = mb_strtolower(trim(implode(' ', [
                (string) ($item->no ?? ''),
                (string) ($item->sub_bab_kode ?? ''),
                (string) ($item->sub_bab_nama ?? ''),
                (string) ($item->nama_konstruksi ?? ''),
                (string) ($item->link_gambar ?? ''),
            ])));
            if ($q !== '' && !str_contains($hay, $q)) {
                return false;
            }
            if ($tahun !== '' && !str_contains($hay, $tahun)) {
                return false;
            }
            return true;
        })->values();

        $pdf = new PurePhpPdfBuilder('P');
        $filterNote = [];
        if ($q !== '') {
            $filterNote[] = 'Cari: "' . $filters['q'] . '"';
        }
        if ($tahun !== '') {
            $filterNote[] = 'Tahun ' . $tahun;
        }

        $pdf->drawHeader(
            'PT PLN (PERSERO) UP3 JAMBI • REFERENSI TEKNIS',
            'STANDAR KONSTRUKSI SUTM 2026' . ($filterNote ? ' (' . implode(' | ', $filterNote) . ')' : ''),
            'Dicetak: ' . now()->format('d/m/Y H:i') . ' WIB'
        );

        $pdf->drawInfoBanner(
            'Daftar Standar Konstruksi Jaringan Tegangan Menengah',
            'Sumber: Database Standar Konstruksi SUTM UP3 Jambi',
            $filtered->count() . ' ITEM',
            [13, 33, 55]
        );

        $rows = [];
        foreach ($filtered as $item) {
            $subBab = $item->sub_bab_nama
                ? ($item->sub_bab_kode ? $item->sub_bab_kode . ' - ' . $item->sub_bab_nama : $item->sub_bab_nama)
                : '-';
            $rows[] = [
                (string) ($item->no ?? '-'),
                (string) $subBab,
                (string) ($item->nama_konstruksi ?? '-'),
                (string) ($item->link_gambar ?: '-'),
            ];
        }
        if (empty($rows)) {
            $rows[] = ['-', '-', 'Belum ada data Standar Konstruksi sesuai filter.', '-'];
        }

        $pdf->drawTable(
            ['No', 'Sub-Bab / Kelompok', 'Konstruksi SUTM', 'Link Gambar'],
            [36, 130, 210, 151],
            $rows,
            ['C', 'L', 'L', 'L']
        );

        return [
            'content'  => $pdf->output(),
            'filename' => 'Standar_Konstruksi_PLN_UP3_Jambi_' . now()->format('Ymd_His') . '.pdf',
        ];
    }

    private function renderSpreadsheetToBytes(Spreadsheet $spreadsheet): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'kjn_xlsx_');
        $writer = new Xlsx($spreadsheet);
        $writer->save($tmp);
        $bytes = (string) file_get_contents($tmp);
        @unlink($tmp);
        return $bytes;
    }

    private function styleBanner($sheet, string $range, string $text): void
    {
        $firstCell = explode(':', $range)[0];
        $sheet->setCellValue($firstCell, $text);
        $sheet->mergeCells($range);
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0C2D48']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);
    }

    private function styleHeaderRow($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
        ]);
    }

    private function applyBorders($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'],
                ],
            ],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
    }

    private function rp(float $val): string
    {
        return 'Rp ' . number_format($val, 0, ',', '.');
    }
}

/**
 * Pure-PHP A4 %PDF-1.4 writer (Helvetica Type1) — zero external dependencies.
 */
class PurePhpPdfBuilder
{
    private float $pageWidth;
    private float $pageHeight;
    private float $margin = 34.0;
    private float $y;
    private array $pages = [];
    private int $currentPage = -1;

    public function __construct(private readonly string $orientation = 'P')
    {
        if ($orientation === 'L') {
            $this->pageWidth = 841.89;
            $this->pageHeight = 595.28;
        } else {
            $this->pageWidth = 595.28;
            $this->pageHeight = 841.89;
        }
        $this->addPage();
    }

    public function addPage(): void
    {
        $this->pages[] = '';
        $this->currentPage = count($this->pages) - 1;
        $this->y = $this->pageHeight - $this->margin;
    }

    private function append(string $cmd): void
    {
        $this->pages[$this->currentPage] .= $cmd . "\n";
    }

    private function ensureSpace(float $neededHeight): void
    {
        if (($this->y - $neededHeight) < ($this->margin + 22)) {
            $this->addPage();
        }
    }

    public function drawHeader(string $org, string $title, string $rightMeta): void
    {
        $w = $this->pageWidth - ($this->margin * 2);
        $h = 46.0;
        $top = $this->y;
        $bottom = $top - $h;

        $this->fillRect($this->margin, $bottom, $w, $h, [12, 45, 72]);
        $this->fillRect($this->margin, $bottom, $w, 3.0, [250, 204, 21]);

        $this->drawText($this->margin + 12, $top - 17, $org, 9, true, [147, 197, 253]);
        $this->drawText($this->margin + 12, $top - 33, $this->fitText($title, $w - 190, 11), 11, true, [255, 255, 255]);
        $this->drawTextRight($this->margin + $w - 10, $top - 22, $rightMeta, 8, false, [226, 232, 240]);

        $this->y = $bottom - 12;
    }

    public function drawInfoBanner(string $title, string $subtitle, string $badgeText, array $badgeRgb): void
    {
        $w = $this->pageWidth - ($this->margin * 2);
        $h = 38.0;
        $this->ensureSpace($h + 8);
        $bottom = $this->y - $h;

        $this->fillRect($this->margin, $bottom, $w, $h, [241, 245, 249]);
        $this->strokeRect($this->margin, $bottom, $w, $h, [203, 213, 225]);
        $this->fillRect($this->margin, $bottom, 4.0, $h, $badgeRgb);

        $this->drawText($this->margin + 12, $this->y - 15, $this->fitText($title, $w - 220, 10.5), 10.5, true, [15, 23, 42]);
        $this->drawText($this->margin + 12, $this->y - 29, $this->fitText($subtitle, $w - 220, 8.2), 8.2, false, [71, 85, 105]);

        $bw = min(195.0, max(110.0, $this->estimateTextWidth($badgeText, 8) + 20));
        $bx = $this->margin + $w - $bw - 10;
        $this->fillRect($bx, $bottom + 9, $bw, 20, $badgeRgb);
        $this->drawText($bx + 10, $bottom + 16, $this->fitText($badgeText, $bw - 14, 8), 8, true, [255, 255, 255]);

        $this->y = $bottom - 10;
    }

    public function drawSectionTitle(string $title): void
    {
        $this->ensureSpace(26);
        $w = $this->pageWidth - ($this->margin * 2);
        $this->drawText($this->margin, $this->y - 11, $title, 9.5, true, [12, 45, 72]);
        $this->fillRect($this->margin, $this->y - 15, $w, 1.0, [203, 213, 225]);
        $this->y -= 21;
    }

    public function drawKeyValueGrid(array $rows): void
    {
        $w = $this->pageWidth - ($this->margin * 2);
        $colWidths = [$w * 0.20, $w * 0.30, $w * 0.20, $w * 0.30];
        $rowH = 18.0;

        foreach ($rows as $idx => $cols) {
            $this->ensureSpace($rowH + 2);
            $bottom = $this->y - $rowH;
            $x = $this->margin;

            foreach ($cols as $c => $val) {
                $cw = $colWidths[$c];
                $isLabel = ($c % 2 === 0);
                $fill = $isLabel ? [241, 245, 249] : ($idx % 2 === 0 ? [255, 255, 255] : [248, 250, 252]);
                $this->fillRect($x, $bottom, $cw, $rowH, $fill);
                $this->strokeRect($x, $bottom, $cw, $rowH, [226, 232, 240]);
                $this->drawText($x + 5, $bottom + 6, $this->fitText((string) $val, $cw - 10, 7.8), 7.8, $isLabel, $isLabel ? [51, 65, 85] : [15, 23, 42]);
                $x += $cw;
            }
            $this->y = $bottom;
        }
        $this->y -= 10;
    }

    public function drawNoteBox(string $text): void
    {
        $w = $this->pageWidth - ($this->margin * 2);
        $h = 20.0;
        $this->ensureSpace($h + 6);
        $bottom = $this->y - $h;
        $this->fillRect($this->margin, $bottom, $w, $h, [254, 249, 195]);
        $this->strokeRect($this->margin, $bottom, $w, $h, [250, 204, 21]);
        $this->drawText($this->margin + 8, $bottom + 6.5, $this->fitText($text, $w - 16, 7.8), 7.8, false, [113, 63, 18]);
        $this->y = $bottom - 10;
    }

    public function drawTable(array $headers, array $widths, array $rows, array $aligns = []): void
    {
        $headerH = 19.0;
        $rowH = 16.5;
        $totalW = array_sum($widths);
        $usableW = $this->pageWidth - ($this->margin * 2);
        if ($totalW > 0 && abs($totalW - $usableW) > 1.0) {
            $scale = $usableW / $totalW;
            $widths = array_map(fn ($v) => $v * $scale, $widths);
        }

        $drawHeaderFn = function () use ($headers, $widths, $headerH, $rowH): void {
            $this->ensureSpace($headerH + $rowH);
            $bottom = $this->y - $headerH;
            $x = $this->margin;
            foreach ($headers as $i => $hLabel) {
                $cw = $widths[$i];
                $this->fillRect($x, $bottom, $cw, $headerH, [30, 58, 95]);
                $this->strokeRect($x, $bottom, $cw, $headerH, [51, 85, 125]);
                $this->drawText($x + 4, $bottom + 6.5, $this->fitText($hLabel, $cw - 8, 7.4), 7.4, true, [255, 255, 255]);
                $x += $cw;
            }
            $this->y = $bottom;
        };

        $drawHeaderFn();

        foreach ($rows as $rIdx => $row) {
            if (($this->y - $rowH) < ($this->margin + 22)) {
                $this->addPage();
                $drawHeaderFn();
            }
            $bottom = $this->y - $rowH;
            $x = $this->margin;
            $bg = ($rIdx % 2 === 0) ? [255, 255, 255] : [248, 250, 252];

            foreach ($row as $i => $cellVal) {
                $cw = $widths[$i];
                $align = $aligns[$i] ?? 'L';
                $this->fillRect($x, $bottom, $cw, $rowH, $bg);
                $this->strokeRect($x, $bottom, $cw, $rowH, [226, 232, 240]);
                $fitted = $this->fitText((string) $cellVal, $cw - 8, 7.3);
                if ($align === 'R') {
                    $this->drawTextRight($x + $cw - 4, $bottom + 5.5, $fitted, 7.3, false, [15, 23, 42]);
                } elseif ($align === 'C') {
                    $tw = $this->estimateTextWidth($fitted, 7.3);
                    $tx = $x + max(3.0, ($cw - $tw) / 2);
                    $this->drawText($tx, $bottom + 5.5, $fitted, 7.3, false, [15, 23, 42]);
                } else {
                    $this->drawText($x + 4, $bottom + 5.5, $fitted, 7.3, false, [15, 23, 42]);
                }
                $x += $cw;
            }
            $this->y = $bottom;
        }
        $this->y -= 10;
    }

    public function output(): string
    {
        $totalPages = count($this->pages);
        for ($i = 0; $i < $totalPages; $i++) {
            $this->currentPage = $i;
            $footerY = 18.0;
            $this->fillRect($this->margin, $footerY + 10, $this->pageWidth - ($this->margin * 2), 0.6, [203, 213, 225]);
            $this->drawText($this->margin, $footerY, 'SIGINJAI PLN UP3 Jambi - Dokumen Kajian Kelayakan Proyek (KKO-KKF)', 7.2, false, [100, 116, 139]);
            $this->drawTextRight($this->pageWidth - $this->margin, $footerY, 'Halaman ' . ($i + 1) . ' / ' . $totalPages, 7.2, false, [100, 116, 139]);
        }

        $objects = [];
        $pageRefs = [];
        $nextObj = 5;

        for ($i = 0; $i < $totalPages; $i++) {
            $pageObjId = $nextObj++;
            $streamObjId = $nextObj++;
            $pageRefs[] = "{$pageObjId} 0 R";

            $streamData = $this->pages[$i];
            $len = strlen($streamData);
            $objects[$pageObjId] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 " .
                number_format($this->pageWidth, 2, '.', '') . ' ' .
                number_format($this->pageHeight, 2, '.', '') .
                "] /Resources << /Font << /F1 3 0 R /F2 4 0 R >> >> /Contents {$streamObjId} 0 R >>";
            $objects[$streamObjId] = "<< /Length {$len} >>\nstream\n{$streamData}endstream";
        }

        $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[2] = '<< /Type /Pages /Kids [' . implode(' ', $pageRefs) . "] /Count {$totalPages} >>";
        $objects[3] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objects[4] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= "{$id} 0 obj\n{$body}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $maxId = max(array_keys($objects));
        $pdf .= "xref\n0 " . ($maxId + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($id = 1; $id <= $maxId; $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
        }
        $pdf .= "trailer\n<< /Size " . ($maxId + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF\n";

        return $pdf;
    }

    private function fillRect(float $x, float $y, float $w, float $h, array $rgb): void
    {
        $r = number_format($rgb[0] / 255, 3, '.', '');
        $g = number_format($rgb[1] / 255, 3, '.', '');
        $b = number_format($rgb[2] / 255, 3, '.', '');
        $this->append(sprintf('%s %s %s rg %.2F %.2F %.2F %.2F re f', $r, $g, $b, $x, $y, $w, $h));
    }

    private function strokeRect(float $x, float $y, float $w, float $h, array $rgb): void
    {
        $r = number_format($rgb[0] / 255, 3, '.', '');
        $g = number_format($rgb[1] / 255, 3, '.', '');
        $b = number_format($rgb[2] / 255, 3, '.', '');
        $this->append(sprintf('%s %s %s RG 0.5 w %.2F %.2F %.2F %.2F re S', $r, $g, $b, $x, $y, $w, $h));
    }

    private function drawText(float $x, float $y, string $text, float $size, bool $bold, array $rgb): void
    {
        $font = $bold ? '/F2' : '/F1';
        $r = number_format($rgb[0] / 255, 3, '.', '');
        $g = number_format($rgb[1] / 255, 3, '.', '');
        $b = number_format($rgb[2] / 255, 3, '.', '');
        $esc = $this->escapePdfText($text);
        $this->append(sprintf('BT %s %.2F Tf %s %s %s rg 1 0 0 1 %.2F %.2F Tm (%s) Tj ET', $font, $size, $r, $g, $b, $x, $y, $esc));
    }

    private function drawTextRight(float $rightX, float $y, string $text, float $size, bool $bold, array $rgb): void
    {
        $w = $this->estimateTextWidth($text, $size);
        $this->drawText(max($this->margin, $rightX - $w), $y, $text, $size, $bold, $rgb);
    }

    private function escapePdfText(string $text): string
    {
        $clean = str_replace(["\r\n", "\r", "\n"], ' ', $text);
        $clean = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $clean) ?: preg_replace('/[^\x20-\x7E]/', '?', $clean);
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], (string) $clean);
    }

    private function estimateTextWidth(string $text, float $size): float
    {
        return strlen($text) * ($size * 0.50);
    }

    private function fitText(string $text, float $maxWidth, float $size): string
    {
        $clean = trim(str_replace(["\r\n", "\r", "\n"], ' ', $text));
        if ($clean === '') {
            return '';
        }
        $maxChars = max(3, (int) floor($maxWidth / ($size * 0.51)));
        if (mb_strlen($clean) <= $maxChars) {
            return $clean;
        }
        return mb_substr($clean, 0, max(1, $maxChars - 3)) . '...';
    }
}
