@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-8 col-xl-6">
                @include('layouts.title', ['judul' => 'Edit Barang', 'crumb' => 'Edit'])
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('barang.update', $barang->idbarang) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @include('barang._form', ['tombol' => 'Simpan Perubahan'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
