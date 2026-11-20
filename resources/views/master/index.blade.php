@extends('layouts.app')

@section('title', $k['judul'])

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                @include('layouts.title', ['judul' => $k['judul'], 'crumb' => $k['judul']])

                <div class="d-flex justify-content-between mb-3">
                    <a href="{{ route('master.hub') }}" class="btn btn-outline-secondary">&larr; Master Data</a>
                    <a href="{{ route($k['rute'] . '.create') }}" class="btn btn-primary">+ Tambah</a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        @foreach ($k['kolom'] as $kol)
                                            <th>{{ $kol['label'] }}</th>
                                        @endforeach
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $row)
                                        @php $id = $row->{$k['pk']}; @endphp
                                        <tr>
                                            @foreach ($k['kolom'] as $kol)
                                                <td>{{ $kol['isi']($row) }}</td>
                                            @endforeach
                                            <td class="text-end text-nowrap">
                                                @isset($k['aksi'])
                                                    {{ $k['aksi']($row) }}
                                                @endisset
                                                <a href="{{ route($k['rute'] . '.edit', $id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form action="{{ route($k['rute'] . '.destroy', $id) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Yakin hapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ count($k['kolom']) + 1 }}" class="text-center text-muted py-4">Belum ada data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">{{ $data->links() }}</div>
            </div>
        </div>
    </div>
@endsection
