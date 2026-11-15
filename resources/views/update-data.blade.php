@includeIf('partials.simerah-admin-topbar', ['pageTitle' => 'Update Data | SIGINJAI PLN', 'subTitle' => 'Pembaruan Data Monitoring'])

<div class="wrapper">

    @includeIf('partials.simerah-admin-sidebar', ['activeAdmin' => 'upload'])

    <main class="content">

        <div class="header-title">
            <div>
                <h2>Update Data Monitoring</h2>
                <p>Daftar dataset spreadsheet monitoring yang siap diperbarui.</p>
            </div>
            <div class="header-actions">
                <a href="{{ url('/input-data') }}" class="btn-primary">
                    <i class="fa fa-cloud-arrow-up"></i> Upload Excel Baru
                </a>
                <a href="{{ url('/riwayat-upload') }}" class="btn-secondary">
                    <i class="fa fa-clock-rotate-left"></i> Riwayat Upload
                </a>
            </div>
        </div>

        <div class="card">
            <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div>
                    <h3 style="margin-bottom:2px;">Daftar Sheet Monitoring</h3>
                    <p style="font-size:13px;color:var(--slate-500);margin:0;">Pilih sheet untuk diperbarui melalui modul upload Excel.</p>
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                    <input type="text" id="searchUpdateTable" class="form-control" style="width:260px;padding:8px 12px;" placeholder="Cari nama sheet atau kategori...">
                    <select id="filterUpdateYear" class="form-control" style="width:140px;padding:8px 12px;">
                        <option value="">Semua Tahun</option>
                        <option value="2026">Tahun 2026</option>
                        <option value="2025">Tahun 2025</option>
                    </select>
                </div>
            </div>

            <div style="overflow-x:auto;">
                <table id="updateDataTable">
                    <thead>
                        <tr>
                            <th>Nama Sheet</th>
                            <th>Kategori</th>
                            <th>Tahun</th>
                            <th>Jumlah Data</th>
                            <th>Terakhir Input</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-year="2026">
                            <td><strong>Detail KR</strong></td>
                            <td>KHS Jasa</td>
                            <td>2026</td>
                            <td>10</td>
                            <td>30 Mei 2026 <br><small style="color:var(--slate-500);">08.00 WIB</small></td>
                            <td>
                                <a href="{{ url('/input-data') }}" class="btn-save" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;padding:7px 14px;font-size:12.5px;">
                                    <i class="fa fa-rotate"></i> Update Data
                                </a>
                            </td>
                        </tr>
                        <tr data-year="2026">
                            <td><strong>Detail KR</strong></td>
                            <td>KHS Pembesian</td>
                            <td>2026</td>
                            <td>20</td>
                            <td>30 Mei 2026 <br><small style="color:var(--slate-500);">08.00 WIB</small></td>
                            <td>
                                <a href="{{ url('/input-data') }}" class="btn-save" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;padding:7px 14px;font-size:12.5px;">
                                    <i class="fa fa-rotate"></i> Update Data
                                </a>
                            </td>
                        </tr>
                        <tr data-year="2026">
                            <td><strong>Detail KR</strong></td>
                            <td>KHS Jasa</td>
                            <td>2026</td>
                            <td>40</td>
                            <td>30 Mei 2026 <br><small style="color:var(--slate-500);">08.00 WIB</small></td>
                            <td>
                                <a href="{{ url('/input-data') }}" class="btn-save" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;padding:7px 14px;font-size:12.5px;">
                                    <i class="fa fa-rotate"></i> Update Data
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchUpdateTable');
    const yearSelect = document.getElementById('filterUpdateYear');
    const rows = document.querySelectorAll('#updateDataTable tbody tr');

    function applyFilter() {
        const q = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const yr = yearSelect ? yearSelect.value : '';
        rows.forEach(function (tr) {
            const text = (tr.textContent || '').toLowerCase();
            const rowYear = tr.getAttribute('data-year') || '';
            const matchQ = !q || text.includes(q);
            const matchY = !yr || rowYear === yr;
            tr.style.display = (matchQ && matchY) ? '' : 'none';
        });
    }

    if (searchInput) searchInput.addEventListener('input', applyFilter);
    if (yearSelect) yearSelect.addEventListener('change', applyFilter);
});
</script>
</body>
</html>
