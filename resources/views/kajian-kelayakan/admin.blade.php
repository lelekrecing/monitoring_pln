@includeIf('partials.simerah-admin-topbar', ['pageTitle' => 'Kajian Kelayakan Proyek | SIGINJAI PLN', 'subTitle' => 'KKO / KKF, Simulasi & Kuota Kontrak'])

<div class="wrapper">

    @includeIf('partials.simerah-admin-sidebar', ['activeAdmin' => 'kajian'])

    <main class="content" style="padding:10px 0 40px;">
        @include('kajian-kelayakan.partials.workspace', ['isAdminPortal' => true])

        @if(isset($masterKuotas) && $masterKuotas->isNotEmpty())
            <div style="width:min(1680px, 96%);margin:0 auto 24px;">
                <div class="kk-card">
                    <div class="kk-card-head">
                        <div>
                            <div class="kk-card-title"><i class="fa fa-database"></i> Kelola Master Kuota Kontrak (Database)</div>
                            <div class="kk-card-sub">Daftar kuota kontrak yang diinput manual oleh Perencanaan / Admin.</div>
                        </div>
                    </div>
                    <div class="kk-table-wrap">
                        <table class="kk-table">
                            <thead>
                                <tr>
                                    <th>Vendor</th>
                                    <th>No. Kontrak</th>
                                    <th>Tahun</th>
                                    <th>Kuota Awal</th>
                                    <th>Realisasi Manual</th>
                                    <th>Maks. Pekerjaan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($masterKuotas as $mk)
                                    <tr>
                                        <td><strong>{{ $mk->vendor }}</strong></td>
                                        <td>{{ $mk->no_kontrak ?: '-' }}</td>
                                        <td>{{ $mk->tahun }}</td>
                                        <td>Rp {{ number_format($mk->kuota_awal_rp, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($mk->terpakai_manual_rp, 0, ',', '.') }}</td>
                                        <td>{{ $mk->kuota_pekerjaan_maks > 0 ? $mk->kuota_pekerjaan_maks . ' pekerjaan' : 'Tanpa batas slot' }}</td>
                                        <td>
                                            <span class="kk-badge {{ $mk->status_aktif ? 'kk-badge-green' : 'kk-badge-red' }}">
                                                {{ $mk->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <form method="POST" action="{{ route('admin.kajian-kelayakan.kuota.destroy', $mk->id) }}" onsubmit="return confirm('Hapus master kuota vendor ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="kk-btn kk-btn-danger kk-btn-sm">
                                                    <i class="fa fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </main>

</div>
</body>
</html>
