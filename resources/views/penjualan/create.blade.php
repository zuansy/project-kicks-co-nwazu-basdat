@extends('layouts.app')

@section('title', 'Catat Penjualan')

@section('content')
    @php $baris = old('item', [['idbarang' => '', 'jumlah' => 1]]); @endphp
    <div class="container">
        <div class="row">
            <div class="col-xl-9">
                @include('layouts.title', ['judul' => 'Catat Penjualan', 'crumb' => 'Catat'])

                @if ($marginAktif === null)
                    <div class="alert alert-warning">
                        Belum ada <strong>margin penjualan yang aktif</strong>, jadi penjualan belum bisa disimpan.
                        @if (in_array(session('nama_role'), config('inventori.role_margin'), true))
                            <a href="{{ route('margin.index') }}">Atur margin</a>.
                        @else
                            Minta Administrator, Manajer Penjualan, atau Pemilik untuk mengaktifkannya.
                        @endif
                    </div>
                @endif

                <form action="{{ route('penjualan.store') }}" method="POST">
                    @csrf
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-1">Barang yang dijual</h5>
                            <p class="text-muted small">
                                Harga satuan otomatis (harga dasar + margin aktif
                                @if ($marginAktif !== null) {{ rtrim(rtrim(number_format($marginAktif, 2, ',', '.'), '0'), ',') }}% @endif
                                ). Hanya barang yang stoknya ada yang tampil.
                            </p>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 240px">Barang</th>
                                            <th style="width: 110px">Jumlah</th>
                                            <th class="text-end" style="width: 160px">Harga satuan</th>
                                            <th class="text-end" style="width: 160px">Subtotal</th>
                                            <th style="width: 50px"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="isi-item">
                                        @foreach ($baris as $i => $it)
                                            <tr class="baris">
                                                <td>
                                                    <select name="item[{{ $i }}][idbarang]" class="form-select pilih-barang" required>
                                                        <option value="">— pilih barang —</option>
                                                        @foreach ($barang as $b)
                                                            <option value="{{ $b->idbarang }}" data-harga="{{ $b->harga }}" data-stok="{{ $b->stok }}" @selected((string) ($it['idbarang'] ?? '') === (string) $b->idbarang)>{{ $b->nama }} (stok {{ $b->stok }})</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" min="1" name="item[{{ $i }}][jumlah]" value="{{ $it['jumlah'] ?? 1 }}" class="form-control jumlah" required></td>
                                                <td class="text-end harga-satuan">—</td>
                                                <td class="text-end subtotal">0</td>
                                                <td><button type="button" class="btn btn-sm btn-outline-danger hapus" title="Hapus baris">&times;</button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end" id="total-sub">0</td><td></td></tr>
                                        <tr><td colspan="3" class="text-end">PPN {{ $ppnPersen }}%</td><td class="text-end" id="total-ppn">0</td><td></td></tr>
                                        <tr><th colspan="3" class="text-end">Total</th><th class="text-end" id="total-all">0</th><td></td></tr>
                                    </tfoot>
                                </table>
                            </div>
                            <button type="button" class="btn btn-outline-primary" id="tambah-baris">+ Tambah barang</button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('penjualan.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary" @disabled($marginAktif === null)>Simpan Penjualan</button>
                    </div>
                </form>

                <template id="tpl-baris">
                    <tr class="baris">
                        <td>
                            <select name="item[__i__][idbarang]" class="form-select pilih-barang" required>
                                <option value="">— pilih barang —</option>
                                @foreach ($barang as $b)
                                    <option value="{{ $b->idbarang }}" data-harga="{{ $b->harga }}" data-stok="{{ $b->stok }}">{{ $b->nama }} (stok {{ $b->stok }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" min="1" name="item[__i__][jumlah]" value="1" class="form-control jumlah" required></td>
                        <td class="text-end harga-satuan">—</td>
                        <td class="text-end subtotal">0</td>
                        <td><button type="button" class="btn btn-sm btn-outline-danger hapus" title="Hapus baris">&times;</button></td>
                    </tr>
                </template>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var PPN = {{ (int) $ppnPersen }};
            var isi = document.getElementById('isi-item');
            var tpl = document.getElementById('tpl-baris');
            var indeks = isi.querySelectorAll('tr.baris').length;
            var rp = function (n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); };

            function hitung() {
                var sub = 0;
                isi.querySelectorAll('tr.baris').forEach(function (tr) {
                    var opt = tr.querySelector('.pilih-barang').selectedOptions[0];
                    var harga = opt && opt.dataset.harga ? parseInt(opt.dataset.harga, 10) : 0;
                    var stok = opt && opt.dataset.stok ? parseInt(opt.dataset.stok, 10) : null;
                    var kolomJumlah = tr.querySelector('.jumlah');
                    if (stok !== null) kolomJumlah.max = stok;
                    var j = parseInt(kolomJumlah.value, 10) || 0;
                    tr.querySelector('.harga-satuan').textContent = harga ? rp(harga) : '—';
                    tr.querySelector('.subtotal').textContent = rp(j * harga);
                    sub += j * harga;
                });
                var ppn = Math.round(sub * PPN / 100);
                document.getElementById('total-sub').textContent = rp(sub);
                document.getElementById('total-ppn').textContent = rp(ppn);
                document.getElementById('total-all').textContent = rp(sub + ppn);
            }

            document.getElementById('tambah-baris').addEventListener('click', function () {
                isi.insertAdjacentHTML('beforeend', tpl.innerHTML.replace(/__i__/g, indeks++));
                hitung();
            });
            isi.addEventListener('input', hitung);
            isi.addEventListener('change', hitung);
            isi.addEventListener('click', function (e) {
                if (!e.target.classList.contains('hapus')) return;
                if (isi.querySelectorAll('tr.baris').length > 1) {
                    e.target.closest('tr').remove();
                    hitung();
                }
            });
            hitung();
        })();
    </script>
@endpush
