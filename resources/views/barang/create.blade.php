@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-xl-6">
                @include('layouts.title', ['judul' => 'Tambah Barang', 'crumb' => 'Tambah'])
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('barang.store') }}" method="POST">
                            @csrf
                            @include('barang._form', ['tombol' => 'Simpan Barang'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
