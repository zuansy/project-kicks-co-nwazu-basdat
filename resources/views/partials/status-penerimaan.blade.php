@if ($status === config('inventori.status_penerimaan_sebagian'))
    <span class="badge bg-warning text-dark">Diterima sebagian</span>
@else
    <span class="badge bg-success">Diterima tuntas</span>
@endif
