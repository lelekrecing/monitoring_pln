<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Upload | SIGINJAI PLN</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        .filter-bar { display: flex; gap: 10px; align-items: center; margin-bottom: 18px; flex-wrap: wrap; }
        .filter-search-input {
            flex: 1; min-width: 220px; max-width: 340px; height: 40px; padding: 0 14px;
            border-radius: 10px; border: 1px solid #cbd5e1; background: #f8fafc; color: #0f172a; font-size: 13.5px; outline: none;
        }
        .filter-search-input:focus { background: #ffffff; border-color: #0d2137; box-shadow: 0 0 0 3px rgba(13,33,55,.08); }
        .filter-year-select {
            height: 40px; padding: 0 12px; border-radius: 10px; border: 1px solid #cbd5e1;
            background: #f8fafc; color: #0f172a; font-size: 13.5px; font-weight: 600; outline: none;
        }
        .chip {
            padding: 8px 14px; border-radius: 9px; font-size: 13px; font-weight: 600; text-decoration: none;
            color: #0d2137; background: #f1f5f9; border: 1px solid #cbd5e1;
        }
        .chip-info { font-weight: 500; color: #475569; }
        .chip-info a { color: #0d2137; font-weight: 700; text-decoration: underline; }
        .table-wrap { overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 12px; }
        table thead th:last-child { text-align: right; }
        .file-pill {
            display: inline-block; padding: 7px 12px; border-radius: 8px; background: #f8fafc;
            border: 1px solid #e2e8f0; font-size: 13px; color: #0f172a; font-weight: 600; word-break: break-all;
        }
        .badge { display: inline-block; padding: 5px 12px; border-radius: 999px; font-size: 11.5px; font-weight: 700; text-transform: uppercase; }
        .badge-aktif { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .badge-arsip { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }
        .badge-gagal { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .badge-proses { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .aksi { display: flex; justify-content: flex-end; gap: 10px; }
        .aksi form { display: inline; }
        .icon-btn { background: none; border: 0; cursor: pointer; padding: 5px; border-radius: 8px; line-height: 1; color: #64748b; transition: .18s; }
        .icon-btn .material-icons { font-size: 22px; }
        .icon-btn.activate:hover { color: #16a34a; background: #dcfce7; }
        .icon-btn.delete:hover { color: #dc2626; background: #fee2e2; }
        .icon-btn:disabled { cursor: default; color: #cbd5e1; opacity: .75; background: transparent !important; }
        .empty { padding: 30px; text-align: center; color: #64748b; }
    </style>
</head>
<body>

@includeIf('partials.simerah-admin-topbar', ['topSubTitle' => 'UP3 JAMBI • RIWAYAT UPLOAD'])

<div class="wrapper">
    @includeIf('partials.simerah-admin-sidebar', ['activeAdmin' => 'riwayat'])

    <main class="content">
        <div class="card">

            <div class="header-title">
                <h2>Riwayat Upload</h2>
                <small>Semua data yang pernah diupload tetap tersimpan di sini. Pilih salah satu untuk dijadikan tampilan aktif di dashboard, kapan saja.</small>
            </div>

            @if (session('success'))
                <div class="success-box">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="error-box">{{ session('error') }}</div>
            @endif

            <div class="filter-bar">
                <input type="text" id="riwayatSearchInput" class="filter-search-input" placeholder="Cari nama file, jenis monitoring, atau status..." autocomplete="off">
                <select id="riwayatYearSelect" class="filter-year-select" aria-label="Filter Tahun Upload">
                    <option value="">Semua Tahun</option>
                </select>
                <a href="{{ url('/riwayat-upload') }}" class="chip">Semua</a>
                @if (! $tampilkanSemua && $jumlahTersembunyi > 0)
                    <span class="chip chip-info">
                        &bull; Upload kosong/gagal (tanpa data) sedang disembunyikan.
                        <a href="{{ url('/riwayat-upload') }}?tampilkan_semua=1">Tampilkan semua</a>
                    </span>
                @endif
            </div>

            <div class="table-wrap">
                <table id="riwayatTable">
                    <thead>
                        <tr>
                            <th>Nama File</th>
                            <th>Jenis</th>
                            <th>Diupload</th>
                            <th>Progres</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($riwayat as $u)
                        @php
                            $namaFile = $u->nama_file ?? $u->file_name ?? $u->nama_asli
                                ?? (! empty($u->file_path) ? basename($u->file_path) : '-');
                            $aktif = $u->status === 'aktif';
                            $gagal = $u->status === 'gagal';
                            $kosong = (int) $u->jumlah_sheet === 0;
                        @endphp
                        <tr>
                            <td><span class="file-pill">{{ $namaFile }}</span></td>
                            <td>{{ strtoupper($u->jenis_monitoring ?? '-') }}</td>
                            <td>{{ $u->created_at ? \Illuminate\Support\Carbon::parse($u->created_at, 'UTC')->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('d M Y, H:i') : '-' }}</td>
                            <td>{{ (int) $u->sheet_selesai }}/{{ (int) $u->jumlah_sheet }} sheet</td>
                            <td>
                                @if ($aktif)
                                    <span class="badge badge-aktif">Sedang tampil</span>
                                @elseif ($gagal)
                                    <span class="badge badge-gagal" title="{{ $u->pesan_error }}">Gagal</span>
                                @elseif ($u->status === 'arsip')
                                    <span class="badge badge-arsip">Arsip</span>
                                @else
                                    <span class="badge badge-proses">{{ $u->status ?? 'Diproses' }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="aksi">
                                    <form method="POST" action="{{ route('admin.riwayat-upload.aktifkan', $u->id) }}"
                                          onsubmit="return confirm('Jadikan upload ini sebagai tampilan aktif di dashboard?')">
                                        @csrf
                                        <button type="submit" class="icon-btn activate"
                                                title="{{ $aktif ? 'Sedang tampil' : 'Jadikan tampilan aktif' }}"
                                                {{ ($aktif || $gagal || $kosong) ? 'disabled' : '' }}>
                                            <i class="material-icons">check_circle</i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.riwayat-upload.destroy', $u->id) }}"
                                          onsubmit="return confirm('Hapus riwayat upload ini beserta datanya? Tindakan ini tidak bisa dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="icon-btn delete" title="Hapus">
                                            <i class="material-icons">delete</i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">Belum ada riwayat upload.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</div>

@includeIf('partials.upload-progress-widget')

<script>
(function(){
    var tbl = document.getElementById('riwayatTable');
    var searchInput = document.getElementById('riwayatSearchInput');
    var yearSelect  = document.getElementById('riwayatYearSelect');
    if (!tbl || !searchInput || !yearSelect) return;

    var tbody = tbl.querySelector('tbody');
    if (!tbody) return;

    var rows = Array.prototype.slice.call(tbody.rows).filter(function(r){
        return !r.querySelector('td.empty');
    });

    var years = {};
    rows.forEach(function(r){
        var txt = (r.textContent || '').replace(/\s+/g, ' ').trim();
        r.dataset.search = txt.toLowerCase();
        var m = txt.match(/\b(20[1-3][0-9])\b/g) || [];
        r.dataset.years = m.join(',');
        m.forEach(function(y){ years[y] = true; });
    });

    Object.keys(years).sort(function(a,b){ return Number(b) - Number(a); }).forEach(function(y){
        var opt = document.createElement('option');
        opt.value = y;
        opt.textContent = 'Tahun ' + y;
        yearSelect.appendChild(opt);
    });

    function filterRiwayat(){
        var q = searchInput.value.trim().toLowerCase();
        var y = yearSelect.value;
        rows.forEach(function(r){
            var okQ = !q || (r.dataset.search || '').indexOf(q) !== -1;
            var okY = !y || (r.dataset.years || '').split(',').indexOf(y) !== -1;
            r.style.display = (okQ && okY) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterRiwayat);
    yearSelect.addEventListener('change', filterRiwayat);
})();
</script>

</body>
</html>