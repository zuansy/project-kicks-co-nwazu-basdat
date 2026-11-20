@php $b = $barang ?? null; @endphp

<div class="mb-3">
    <label class="form-label" for="nama">Nama Barang</label>
    <input type="text" id="nama" name="nama" maxlength="45" required
        class="form-control @error('nama') is-invalid @enderror"
        value="{{ old('nama', $b?->nama) }}" placeholder="Contoh: Nike Air Jordan 1">
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="jenis">Jenis</label>
        <select id="jenis" name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
            @foreach ($jenis as $kode => $label)
                <option value="{{ $kode }}" @selected(old('jenis', $b?->jenis ?? 'S') === $kode)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label" for="idsatuan">Satuan</label>
        <select id="idsatuan" name="idsatuan" class="form-select @error('idsatuan') is-invalid @enderror" required>
            <option value="">— pilih satuan —</option>
            @foreach ($satuan as $s)
                <option value="{{ $s->idsatuan }}" @selected((string) old('idsatuan', $b?->idsatuan) === (string) $s->idsatuan)>
                    {{ $s->nama_satuan }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="mb-3">
    <label class="form-label" for="harga">Harga Dasar (Rp)</label>
    <input type="number" id="harga" name="harga" min="0" step="1" required
        class="form-control @error('harga') is-invalid @enderror"
        value="{{ old('harga', $b?->harga) }}" placeholder="Contoh: 2500000">
    <div class="form-text">Harga acuan beli. Harga jual = harga dasar + margin penjualan yang aktif.</div>
</div>

@if ($b)
<div class="mb-4">
    <label class="form-label" for="status">Status</label>
    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
        <option value="1" @selected((string) old('status', $b?->status ?? 1) === '1')>Aktif</option>
        <option value="0" @selected((string) old('status', $b?->status ?? 1) === '0')>Nonaktif</option>
    </select>
    <div class="form-text">Stok tidak diisi di sini — stok bertambah otomatis lewat menu Penerimaan.</div>
</div>
@else
<div class="mb-4">
    <div class="form-text">Barang baru otomatis berstatus <strong>Aktif</strong> dengan stok 0. Stok bertambah lewat menu Penerimaan (setelah disetujui).</div>
</div>
@endif


<div class="d-flex justify-content-end gap-2">
    <a href="{{ route('barang.index') }}" class="btn btn-outline-secondary">Batal</a>
    <button type="submit" class="btn btn-primary">{{ $tombol }}</button>
</div>
