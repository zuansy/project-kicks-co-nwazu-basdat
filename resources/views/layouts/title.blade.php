<section class="scroll-section" id="title">
    <div class="page-title-container">
        <h1 class="mb-0 pb-0 display-4">{{ $judul }}</h1>
        <nav class="breadcrumb-container d-inline-block" aria-label="breadcrumb">
            <ul class="breadcrumb pt-0">
                <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Inventori</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $crumb ?? $judul }}</li>
            </ul>
        </nav>
    </div>
</section>
@include('layouts.alerts')
