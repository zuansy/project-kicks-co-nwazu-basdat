<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Barang - Nwazu Kicks & Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('/sepatuz.png');
            background-size: cover; background-position: center; background-attachment: fixed;
            background-color: #e0f7fa;
        }
        .overlay { background-color: rgba(0, 150, 255, 0.15); min-height: 100vh; padding-top: 40px; }
        .form-box {
            max-width: 600px; margin: auto;
            background-color: rgba(224, 247, 255, 0.85); backdrop-filter: blur(8px);
            padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0, 100, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
    </style>
</head>
<body>
    <div class="overlay">
        <div class="form-box">
            <h3 class="fw-bold mb-4 text-dark">Tambah Barang Baru</h3>

            <form action="/barang/simpan" method="POST">
    @csrf
               <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Produk</label>
                    <input type="text" class="form-control" name="nama_barang" placeholder="Contoh: Nike Air Jordan 1" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Harga Satuan (Rp)</label>
                    <input type="number" class="form-control" name="harga" placeholder="Contoh: 2500000" required>
                </div>

                <!-- INI KOTAK BARU YANG KITA TAMBAHKAN (STOK) -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Stok Awal</label>
                    <input type="number" class="form-control" name="stok" placeholder="Contoh: 15" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Status Ketersediaan</label>
                    <select class="form-select" name="status_barang">
                        <option value="Tersedia">Tersedia</option>
                        <option value="Habis">Habis</option>
                    </select>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/barang" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Barang</button>
                </div>
            </form>

        </div>
    </div>
</body>
</html>
