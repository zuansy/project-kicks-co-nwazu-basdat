@extends('layouts.app')

@section('title', 'Catat Pengadaan')

@section('content')
    @php
        $baris = old('item', [['idbarang' => '', 'jumlah' => 1, 'harga' => ''], ['idbarang' => '', 'jumlah' => 1, 'harga' => '']]);
    @endphp
    <div class="container">
        <div class="row">
            <div class="col-xl-9">
                @include('layouts.title', ['judul' => 'Catat Pengadaan', 'crumb' => 'Catat'])

                <form action="{{ route('pengadaan.store') }}" method="POST">
                    @csrf
                    <div class="card mb-4">
                        <div class="card-body">
                            <label class="form-label" for="vendor_idvendor">Vendor / Supplier (pilih manual)</label>
                            <select id="vendor_idvendor" name="vendor_idvendor" class="form-select" required>
                                <option value="">— pilih vendor —</option>
                                @foreach ($vendor as $v)
                                    <option value="{{ $v->idvendor }}" @selected((string) old('vendor_idvendor') === (string) $v->idvendor)>{{ $v->nama_vendor }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Status pengadaan otomatis <strong>Proses</strong> dan berubah <strong>Selesai</strong> setelah semua barang diterima. PPN {{ $ppnPersen }}% dihitung otomatis.</div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-1">Barang yang dipesan</h5>
                            <p class="text-muted small">Minimal {{ $minBarang }} jenis barang. Harga satuan terisi dari harga dasar barang dan boleh diubah.</p>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 220px">Barang</th>
                                            <th style="width: 110px">Jumlah</th>
                                            <th style="width: 180px">Harga satuan (Rp)</th>
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
                                                            <option value="{{ $b->idbarang }}" data-harga="{{ $b->harga }}" @selected((string) ($it['idbarang'] ?? '') === (string) $b->idbarang)>{{ $b->nama }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" min="1" name="item[{{ $i }}][jumlah]" value="{{ $it['jumlah'] ?? 1 }}" class="form-control jumlah" required></td>
                                                <td><input type="number" min="0" name="item[{{ $i }}][harga]" value="{{ $it['harga'] ?? '' }}" class="form-control harga" required></td>
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
                        <a href="{{ route('pengadaan.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Pengadaan</button>
                    </div>
                </form>

                <template id="tpl-baris">
                    <tr class="baris">
                        <td>
                            <select name="item[__i__][idbarang]" class="form-select pilih-barang" required>
                                <option value="">— pilih barang —</option>
                                @foreach ($barang as $b)
                                    <option value="{{ $b->idbarang }}" data-harga="{{ $b->harga }}">{{ $b->nama }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" min="1" name="item[__i__][jumlah]" value="1" class="form-control jumlah" required></td>
                        <td><input type="number" min="0" name="item[__i__][harga]" class="form-control harga" required></td>
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
            var MIN = {{ (int) $minBarang }};
            var isi = document.getElementById('isi-item');
            var tpl = document.getElementById('tpl-baris');
            var indeks = isi.querySelectorAll('tr.baris').length;
            var rp = function (n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); };

            function hitung() {
                var sub = 0;
                isi.querySelectorAll('tr.baris').forEach(function (tr) {
                    var j = parseInt(tr.querySelector('.jumlah').value, 10) || 0;
                    var h = parseInt(tr.querySelector('.harga').value, 10) || 0;
                    tr.querySelector('.subtotal').textContent = rp(j * h);
                    sub += j * h;
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
            isi.addEventListener('change', function (e) {
                if (!e.target.classList.contains('pilih-barang')) return;
                var harga = e.target.selectedOptions[0] ? e.target.selectedOptions[0].dataset.harga : '';
                var kolom = e.target.closest('tr').querySelector('.harga');
                if (harga !== undefined && harga !== '') kolom.value = harga;
                hitung();
            });
            isi.addEventListener('click', function (e) {
                if (!e.target.classList.contains('hapus')) return;
                if (isi.querySelectorAll('tr.baris').length > MIN) {
                    e.target.closest('tr').remove();
                    hitung();
                }
            });

            hitung();
        })();
    </script>
@endpush
