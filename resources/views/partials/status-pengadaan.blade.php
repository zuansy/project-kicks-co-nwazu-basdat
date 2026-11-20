@if ($status === config('inventori.status_pengadaan_selesai'))
    <span class="badge bg-success">Selesai</span>
@else
    <span class="badge bg-warning text-dark">Proses</span>
@endif
