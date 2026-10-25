<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penerimaan Stok - Nwazu Kicks & Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-image: url('/sepatuz.png'); background-size: cover; background-position: center; background-attachment: fixed; background-color: #e0f7fa; }
        .overlay { background-color: rgba(0, 150, 255, 0.15); min-height: 100vh; padding-top: 40px; }
        .form-box { max-width: 600px; margin: auto; background-color: rgba(224, 247, 255, 0.85); backdrop-filter: blur(8px); padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 100, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.6); }
    </style>
</head>
<body>
    <div class="overlay">
        <div class="form-box">
            <h3 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-box-open text-success"></i> Form Penerimaan Stok Barang</h3>
            <form action="#" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">ID / Nota Pengadaan</label>
                    <input type="text" class="form-control" name="id_pengadaan" placeholder="Contoh: PGD-2026-001" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jumlah Stok Diterima</label>
                    <input type="number" class="form-control" name="jumlah_terima" placeholder="Contoh: 25" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Kondisi Barang</label>
                    <select class="form-select" name="kondisi">
                        <option value="Baik">Baik & Sesuai (Lolos QC)</option>
                        <option value="Ada yang cacat">Ada yang Cacat / Rusak</option>
                    </select>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="/barang" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-success fw-semibold">Proses Penerimaan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
