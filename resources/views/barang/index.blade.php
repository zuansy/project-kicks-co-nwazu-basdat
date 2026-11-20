@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                @include('layouts.title', ['judul' => 'Data Barang', 'crumb' => 'Barang'])

                <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
                    <form method="GET" action="{{ route('barang.index') }}" class="d-flex gap-2">
                        <input type="search" name="q" value="{{ $q }}" class="form-control"
                            placeholder="Cari nama barang..." style="min-width: 240px">
                        <button type="submit" class="btn btn-outline-primary">Cari</button>
                        @if ($q !== '')
                            <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary">Reset</a>
                        @endif
                    </form>
                    <a href="{{ route('barang.create') }}" class="btn btn-primary">+ Tambah Barang</a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Nama Barang</th>
                                        <th scope="col">Jenis</th>
                                        <th scope="col">Satuan</th>
                                        <th scope="col" class="text-end">Harga Dasar</th>
                                        <th scope="col" class="text-end">Stok</th>
                                        <th scope="col">Status</th>
                                        <th scope="col" class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($barang as $item)
                                        <tr>
                                            <th scope="row">{{ $item->idbarang }}</th>
                                            <td>{{ $item->nama_barang }}</td>
                                            <td>{{ \App\Models\Barang::JENIS[$item->jenis] ?? $item->jenis }}</td>
                                            <td>{{ $item->nama_satuan ?? '—' }}</td>
                                            <td class="text-end">
                                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                                            </td>
                                            <td class="text-end">{{ $item->stok }}</td>
                                            <td>
                                                @if ((int) $item->status === 1)
                                                    <span class="badge bg-success">Tersedia</span>
                                                @else
                                                    <span class="badge bg-secondary">Tidak Tersedia</span>
                                                @endif
                                            </td>
                                            <td class="text-end text-nowrap">
                                                <a href="{{ route('barang.edit', $item->idbarang) }}"
                                                    class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form action="{{ route('barang.destroy', $item->idbarang) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Yakin hapus barang ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                {{ $q !== '' ? 'Tidak ada barang yang cocok dengan "' . $q . '".' : 'Belum ada data barang.' }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">{{ $barang->links() }}</div>
            </div>
        </div>
    </div>
@endsection
