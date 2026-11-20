@extends('layouts.app')

@section('title', ($baris ? 'Edit ' : 'Tambah ') . $k['judul'])

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-xl-6">
                @include('layouts.title', ['judul' => ($baris ? 'Edit ' : 'Tambah ') . $k['judul'], 'crumb' => $baris ? 'Edit' : 'Tambah'])

                <div class="card">
                    <div class="card-body">
                        <form action="{{ $baris ? route($k['rute'] . '.update', $baris->{$k['pk']}) : route($k['rute'] . '.store') }}" method="POST">
                            @csrf
                            @if ($baris)
                                @method('PUT')
                            @endif

                            @foreach ($k['field'] as $f)
                                <div class="mb-3">
                                    <label class="form-label" for="{{ $f['nama'] }}">{{ $f['label'] }}</label>

                                    @if ($f['tipe'] === 'select')
                                        <select id="{{ $f['nama'] }}" name="{{ $f['nama'] }}"
                                            class="form-select @error($f['nama']) is-invalid @enderror" required>
                                            @foreach ($f['opsi']() as $nilai => $label)
                                                <option value="{{ $nilai }}" @selected((string) old($f['nama'], $baris?->{$f['nama']}) === (string) $nilai)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    @elseif ($f['tipe'] === 'password')
                                        <input type="password" id="{{ $f['nama'] }}" name="{{ $f['nama'] }}"
                                            class="form-control @error($f['nama']) is-invalid @enderror"
                                            autocomplete="new-password" @required(! $baris)>
                                    @else
                                        <input type="{{ $f['tipe'] }}" id="{{ $f['nama'] }}" name="{{ $f['nama'] }}"
                                            @if ($f['tipe'] === 'number') step="any" @endif
                                            class="form-control @error($f['nama']) is-invalid @enderror"
                                            value="{{ old($f['nama'], $baris?->{$f['nama']}) }}" required>
                                    @endif

                                    @isset($f['bantuan'])
                                        <div class="form-text">{{ $f['bantuan'] }}</div>
                                    @endisset
                                </div>
                            @endforeach

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route($k['rute'] . '.index') }}" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
