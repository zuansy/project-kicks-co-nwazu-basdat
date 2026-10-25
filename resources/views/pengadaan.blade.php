<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Catat Pengadaan - Nwazu Kicks & Co.</title>
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
            <h3 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-truck-fast text-warning"></i> Form Catat Pengadaan Barang</h3>
            <form action="#" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Pilih Supplier / Vendor</label>
                    <select class="form-select" name="vendor">
                        <option>Nike Official Distributor</option>
                        <option>Adidas Supplier Indonesia</option>
                        <option>Vans Regional Partner</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tanggal Pengadaan</label>
                    <input type="date" class="form-control" name="tanggal" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Keterangan / Catatan</label>
                    <textarea class="form-control" name="keterangan" rows="3" placeholder="Contoh: Restock sepatu model baru gelombang pertama"></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="/barang" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-warning text-dark fw-semibold">Simpan Pengadaan</button>
                </div>
            </form>
        </div>
    </div>
</body>
