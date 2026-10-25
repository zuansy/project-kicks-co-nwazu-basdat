<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nwazu Kicks & Co. - Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Mengatur Background Gambar Utama & Font */
        body {
            background-image: url('/sepatuz.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-color: #e0f7fa;
            font-family: 'Poppins', sans-serif;
        }

        /* Lapisan biru air transparan di seluruh layar */
        .overlay {
            background-color: rgba(0, 150, 255, 0.15);
            min-height: 100vh;
            padding-top: 40px;
            padding-bottom: 40px;
        }

        /* Kontainer tabel dengan efek Air/Kaca Transparan */
        .kontainer-lebar {
            max-width: 95%;
            margin: auto;
            background-color: rgba(224, 247, 255, 0.65);
            backdrop-filter: blur(8px);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 100, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        /* Styling Khusus Tabel Kaca Transparan (Glassmorphism) */
        .table-glass {
            background-color: rgba(255, 255, 255, 0.75) !important;
            backdrop-filter: blur(10px);
            border-radius: 12px;
            overflow: hidden;
        }
        .table-glass th {
            background-color: rgba(30, 41, 59, 0.85) !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.1);
        }
        .table-glass td {
            background-color: transparent !important;
            vertical-align: middle;
            border-color: rgba(0, 150, 255, 0.1);
        }
        /* Efek waktu kursor mouse nyentuh baris tabel */
        .table-glass tbody tr:hover {
            background-color: rgba(0, 150, 255, 0.1) !important;
            transition: 0.2s ease-in-out;
        }
    </style>
</head>
<body>
    <div class="overlay">

        <div class="kontainer-lebar">

            <!-- Header & Judul Toko -->
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h1 class="fw-bold text-dark"><i class="fa-solid fa-shoe-prints text-primary"></i> Nwazu Kicks & Co.</h1>
                <span class="text-muted">Manajemen Inventori & Pengadaan</span>
            </div>

            <!-- Tombol Fitur Tambah / Pengadaan -->
            <div class="d-flex justify-content-between mb-4">
                <h3 class="fw-semibold">Daftar Barang</h3>
                <div>
                    <a href="/barang/tambah" class="btn btn-primary shadow-sm"><i class="fa-solid fa-plus"></i> Tambah Barang Baru</a>
                    <a href="/pengadaan" class="btn btn-warning shadow-sm text-dark"><i class="fa-solid fa-truck-fast"></i> Catat Pengadaan</a>
                    <a href="/penerimaan" class="btn btn-success shadow-sm"><i class="fa-solid fa-box-open"></i> Penerimaan Stok</a>
                </div>
            </div>

            <!-- Tabel Data dengan Efek Glassmorphism -->
            <div class="table-responsive">
                <table class="table table-glass align-middle shadow-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Produk</th>
                            <th>Tipe</th>
                            <th>Harga Satuan</th>
                            <th>Stok</th>
                            <th>Status Ketersediaan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barang as $b)
                        <tr>
                            <td class="fw-bold text-secondary">#{{ $b->idbarang }}</td>
                            <!-- Kolom nama asli database -->
                            <td class="fw-bold">{{ $b->nama }}</td>
                            <td>
                                <!-- Menggunakan kolom 'jenis' dari database -->
                                @if($b->jenis == 'S')
                                    <span class="badge bg-secondary"><i class="fa-solid fa-shoe-prints"></i> Sepatu</span>
                                @else
                                    <span class="badge bg-info text-dark">Aksesoris</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($b->harga, 0, ',', '.') }}</td>
                            <!-- Kolom stok baru -->
                            <td class="fw-bold text-primary">{{ $b->stok ?? 0 }}</td>
                            <td>
                                <!-- Menggunakan kolom 'status' dari database -->
                                @if($b->status == 1)
                                    <span class="badge bg-success rounded-pill px-3">Tersedia</span>
                                @else
                                    <span class="badge bg-danger rounded-pill px-3">Habis</span>
                                @endif
                            </td>
                            <td>
                                <!-- Tombol Edit -->
                                <a href="/barang/edit/{{ $b->idbarang }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen-to-square"></i></a>

                                <!-- Tombol Hapus -->
                                <a href="/barang/hapus/{{ $b->idbarang }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin mau hapus sepatu ini?')"><i class="fa-solid fa-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</body>
</html>
