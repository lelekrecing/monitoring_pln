<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Kajian Kelayakan Proyek (KKO-KKF)' }} — SIGINJAI PLN UP3 Jambi</title>
    <style>
        @page { size: {{ ($mode ?? 'single') === 'recap' ? 'A4 landscape' : 'A4 portrait' }}; margin: 14mm; }
        * { box-sizing: border-box; font-family: Arial, Helvetica, sans-serif; color: #0f172a; }
        body { margin: 0; padding: 18px; background: #f8fafc; font-size: 11px; }
        .sheet { max-width: 1080px; margin: 0 auto; background: #fff; padding: 20px; border: 1px solid #cbd5e1; border-radius: 8px; }
        .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; gap: 10px; }
        .btn { padding: 7px 12px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; font-weight: 700; cursor: pointer; text-decoration: none; font-size: 11px; color: #0f172a; }
        .btn-primary { background: #0c2d48; color: #fff; border-color: #0c2d48; }
        .header-banner { background: #0c2d48; color: #fff; padding: 14px 18px; border-radius: 8px; border-bottom: 4px solid #facc15; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center; }
        .header-banner h1 { margin: 2px 0 0; font-size: 15px; color: #fff; }
        .header-banner .org { font-size: 10px; color: #93c5fd; font-weight: 700; letter-spacing: .04em; }
        .sec-title { font-size: 11px; font-weight: 800; color: #0c2d48; border-bottom: 1.5px solid #cbd5e1; padding-bottom: 4px; margin: 14px 0 8px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 10px; }
        th { background: #1e3a5f; color: #fff; padding: 6px 7px; text-align: left; border: 1px solid #33557d; }
        td { padding: 5px 7px; border: 1px solid #cbd5e1; vertical-align: middle; }
        tr:nth-child(even) td { background: #f8fafc; }
        .num { text-align: right; font-variant-numeric: tabular-nums; }
        .badge { display: inline-block; padding: 2px 7px; border-radius: 999px; font-size: 9px; font-weight: 800; }
        .badge-layak { background: #dcfce7; color: #166534; }
        .badge-tidak { background: #ffe4e6; color: #9f1239; }
        @media print { .no-print { display: none !important; } body { background: #fff; padding: 0; } .sheet { border: none; padding: 0; max-width: none; } }
    </style>
</head>
<body>
<div class="sheet">
    <div class="topbar no-print">
        <div><strong>Preview Cetak A4 PDF — Kajian Kelayakan Proyek (KKO-KKF)</strong></div>
        <div style="display:flex; gap:8px;">
            <button class="btn btn-primary" onclick="window.print()">Cetak / Simpan PDF (Browser)</button>
            <a class="btn" href="{{ url()->current() }}">Download PDF Langsung</a>
        </div>
    </div>

    <div class="header-banner">
        <div>
            <div class="org">PT PLN (PERSERO) UP3 JAMBI • SIGINJAI</div>
            <h1>{{ $title ?? 'KAJIAN KELAYAKAN PROYEK (KKO - KKF)' }}</h1>
        </div>
        <div style="text-align:right; font-size:10px; color:#e2e8f0;">
            Dicetak: {{ now()->format('d/m/Y H:i') }} WIB
        </div>
    </div>

    @if(($mode ?? 'single') === 'single' && isset($proyek))
        <div class="sec-title">1. Identitas & Ringkasan Kelayakan</div>
        <table>
            <tr>
                <th style="width:20%;">Kode Kajian</th><td style="width:30%;">{{ $proyek->kode_kajian }}</td>
                <th style="width:20%;">Tahun Anggaran</th><td style="width:30%;">{{ $proyek->tahun }}</td>
            </tr>
            <tr>
                <th>Nama Pekerjaan</th><td>{{ $proyek->nama_pekerjaan }}</td>
                <th>ULP Pemohon</th><td>{{ $proyek->ulp }}</td>
            </tr>
            <tr>
                <th>Mode / Jarak Gudang</th><td>{{ strtoupper($proyek->mode) }} • {{ number_format((float)$proyek->jarak_gudang_km, 1, ',', '.') }} km</td>
                <th>Status / Kelayakan</th>
                <td>
                    {{ strtoupper(str_replace('_', ' ', $proyek->status)) }} —
                    <span class="badge {{ $proyek->layak_otomatis ? 'badge-layak' : 'badge-tidak' }}">
                        {{ $proyek->layak_otomatis ? 'LAYAK / DITERIMA' : 'BELUM LAYAK' }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Total Pelanggan</th><td class="num">{{ number_format((int)$proyek->total_pelanggan, 0, ',', '.') }} plg</td>
                <th>Total Tambah Daya</th><td class="num">{{ number_format((float)$proyek->total_daya_kva, 2, ',', '.') }} kVA</td>
            </tr>
            <tr>
                <th>Estimasi Nilai RAB</th><td class="num">Rp {{ number_format((float)$proyek->estimasi_nilai_rab, 0, ',', '.') }}</td>
                <th>Est. Pendapatan / Thn</th><td class="num">Rp {{ number_format((float)$proyek->estimasi_pendapatan_tahun, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Payback Period</th><td class="num">{{ $proyek->payback_tahun !== null ? number_format((float)$proyek->payback_tahun, 2, ',', '.') . ' tahun' : '-' }}</td>
                <th>Vendor Kontrak</th><td>{{ $proyek->vendor_kontrak ?: '-' }} {{ $proyek->no_kontrak ? '('.$proyek->no_kontrak.')' : '' }}</td>
            </tr>
        </table>

        <div class="sec-title">2. Data Pelanggan PB / PD</div>
        <table>
            <thead>
                <tr>
                    <th>Slot</th><th>Permohonan</th><th>Kategori</th><th>Tarif &amp; Daya Tujuan</th>
                    <th class="num">Sblm (kVA)</th><th class="num">Ssdh (kVA)</th><th class="num">Plg</th><th class="num">Tambah Daya (kVA)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proyek->pelanggans as $pel)
                    <tr>
                        <td>{{ (int)$pel->slot }}</td>
                        <td>{{ strtoupper($pel->jenis_permohonan) }}</td>
                        <td>{{ $pel->kategori_tarif ?: '-' }}</td>
                        <td>{{ $pel->kelompok_tarif }}</td>
                        <td class="num">{{ number_format((float)$pel->daya_sebelum_kva, 2, ',', '.') }}</td>
                        <td class="num">{{ number_format((float)$pel->daya_sesudah_kva, 2, ',', '.') }}</td>
                        <td class="num">{{ (int)$pel->jumlah_pelanggan }}</td>
                        <td class="num">{{ number_format((float)$pel->penambahan_daya_kva, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;">Belum ada data pelanggan.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="sec-title">3. Input URAI Konstruksi ({{ $proyek->uraiRows->count() }} Baris)</div>
        <table>
            <thead>
                <tr>
                    <th>Brs</th><th>AJ</th><th>AK</th><th>AL</th><th>AM</th><th class="num">VOL150</th><th class="num">BA</th><th class="num">BD</th><th>BG</th>
                    <th>CANTOL/PORTAL</th><th>DP</th><th class="num">VOLJTR</th><th class="num">DS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proyek->uraiRows as $row)
                    @php $d = $row->row_data ?? []; @endphp
                    <tr>
                        <td>{{ $row->row_number }}</td>
                        <td>{{ $d['AJ'] ?? '-' }}</td>
                        <td>{{ $d['AK'] ?? '-' }}</td>
                        <td>{{ $d['AL'] ?? '-' }}</td>
                        <td>{{ $d['AM'] ?? '-' }}</td>
                        <td class="num">{{ $d['VOL150'] ?? 0 }}</td>
                        <td class="num">{{ $d['BA'] ?? 0 }}</td>
                        <td class="num">{{ $d['BD'] ?? 0 }}</td>
                        <td>{{ $d['BG'] ?? '-' }}</td>
                        <td>{{ !empty($d['CANTOL']) ? 'C: '.$d['CANTOL'] : (!empty($d['PORTAL']) ? 'P: '.$d['PORTAL'] : '-') }}</td>
                        <td>{{ $d['DP'] ?? '-' }}</td>
                        <td class="num">{{ $d['VOLJTR'] ?? 0 }}</td>
                        <td class="num">{{ $d['DS'] ?? 0 }}</td>
                    </tr>
                @empty
                    <tr><td colspan="13" style="text-align:center;">Belum ada baris Input URAI terisi.</td></tr>
                @endforelse
            </tbody>
        </table>
    @else
        <div class="sec-title">1. Rekapitulasi Kajian Kelayakan Proyek ({{ count($projects ?? []) }} Data)</div>
        <table>
            <thead>
                <tr>
                    <th>No</th><th>Kode</th><th>Nama Pekerjaan</th><th>ULP</th><th>Thn</th><th>Status</th><th>Kelayakan</th>
                    <th class="num">Plg</th><th class="num">Daya (kVA)</th><th class="num">Est. RAB (Rp)</th><th>Vendor Kontrak</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects ?? [] as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p->kode_kajian }}</td>
                        <td>{{ $p->nama_pekerjaan }}</td>
                        <td>{{ $p->ulp ?: '-' }}</td>
                        <td>{{ $p->tahun }}</td>
                        <td>{{ strtoupper(str_replace('_', ' ', $p->status)) }}</td>
                        <td>{{ $p->layak_otomatis ? 'LAYAK' : 'BELUM LAYAK' }}</td>
                        <td class="num">{{ (int)$p->total_pelanggan }}</td>
                        <td class="num">{{ number_format((float)$p->total_daya_kva, 1, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format((float)$p->estimasi_nilai_rab, 0, ',', '.') }}</td>
                        <td>{{ $p->vendor_kontrak ?: '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="11" style="text-align:center;">Tidak ada data kajian.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="sec-title">2. Kuota Kontrak Vendor</div>
        <table>
            <thead>
                <tr>
                    <th>No</th><th>Nama Vendor</th><th>No. Kontrak</th><th>Thn</th>
                    <th class="num">Kuota Awal (Rp)</th><th class="num">Terpakai (Rp)</th><th class="num">Sisa Kuota (Rp)</th>
                    <th class="num">Pekerjaan</th><th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendorQuotas ?? [] as $i => $q)
                    @php $terpakai = (float)($q['realisasi_sheet_rp'] ?? 0) + (float)($q['terpakai_kajian_rp'] ?? 0); @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $q['vendor'] ?? '-' }}</td>
                        <td>{{ $q['no_kontrak'] ?: '-' }}</td>
                        <td>{{ $q['tahun'] ?? '-' }}</td>
                        <td class="num">Rp {{ number_format((float)($q['kuota_awal_rp'] ?? 0), 0, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format($terpakai, 0, ',', '.') }}</td>
                        <td class="num">Rp {{ number_format((float)($q['sisa_kuota_rp'] ?? 0), 0, ',', '.') }}</td>
                        <td class="num">{{ (int)($q['jumlah_terkontrak'] ?? 0) }} / {{ ((int)($q['kuota_pekerjaan_maks'] ?? 0)) > 0 ? $q['kuota_pekerjaan_maks'] : 'Unl' }}</td>
                        <td>{{ !empty($q['kuota_habis']) ? 'HABIS' : (empty($q['status_aktif']) ? 'NONAKTIF' : 'TERSEDIA') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="text-align:center;">Belum ada kuota vendor terdaftar.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif
</div>
</body>
</html>
