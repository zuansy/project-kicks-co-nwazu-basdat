@extends('layouts.app')

@section('title', 'Detail Penjualan #' . $penjualan->idpenjualan)

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xl-9">
                @include('layouts.title', ['judul' => 'Penjualan #' . $penjualan->idpenjualan, 'crumb' => 'Detail'])

                <div class="card mb-4">
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Waktu</dt>
                            <dd class="col-sm-9">{{ \Illuminate\Support\Carbon::parse($penjualan->waktu)->format('d/m/Y H:i') }}</dd>
                            <dt class="col-sm-3">Kasir</dt>
                            <dd class="col-sm-9">{{ $penjualan->username }}</dd>
                            <dt class="col-sm-3">Margin dipakai</dt>
                            <dd class="col-sm-9">{{ rtrim(rtrim(number_format($penjualan->persen, 2, ',', '.'), '0'), ',') }}%</dd>
                        </dl>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr><th>Barang</th><th class="text-end">Jumlah</th><th class="text-end">Harga Satuan</th><th class="text-end">Subtotal</th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($detail as $d)
                                        <tr>
                                            <td>{{ $d->nama }}</td>
                                            <td class="text-end">{{ $d->jumlah }}</td>
                                            <td class="text-end">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                            <td class="text-end">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end">Rp {{ number_format($penjualan->subtotal_nilai, 0, ',', '.') }}</td></tr>
                                    <tr><td colspan="3" class="text-end">PPN</td><td class="text-end">Rp {{ number_format($penjualan->ppn, 0, ',', '.') }}</td></tr>
                                    <tr><th colspan="3" class="text-end">Total</th><th class="text-end">Rp {{ number_format($penjualan->total_nilai, 0, ',', '.') }}</th></tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <a href="{{ route('penjualan.index') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </div>
@endsection
