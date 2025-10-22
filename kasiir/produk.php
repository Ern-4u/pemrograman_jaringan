<?php
//Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "kasir_app");

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Proses simpan data produk
if (isset($_POST['simpan'])) {
    $kode_produk = $_POST['kode_produk'];
    $nama_produk = $_POST['nama_produk'];
    $kategori = $_POST['kategori'];
    $harga_beli = $_POST['harga_beli'];
    $harga_jual = $_POST['harga_jual'];
    $stok = $_POST['stok'];
    $satuan = $_POST['satuan'];

    $sql = "INSERT INTO produk (kode_produk, nama_produk, kategori, harga_beli, harga_jual, stok, satuan)
            VALUES ('$kode_produk', '$nama_produk', '$kategori', '$harga_beli', '$harga_jual', '$stok', '$satuan')";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>alert('Produk berhasil disimpan!');</script>";
    } else {
        echo "<script>alert('Gagal menyimpan produk: " . $koneksi->error . "');</script>";
    }
}

// Pagination setup
$limit = 10; // jumlah data per halaman
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Ambil data produk
$result = $koneksi->query("SELECT * FROM produk ORDER BY id_produk DESC LIMIT $start, $limit");
$produk = $result->fetch_all(MYSQLI_ASSOC);

// Hitung total data untuk pagination
$result_total = $koneksi->query("SELECT COUNT(*) AS total FROM produk");
$total_data = $result_total->fetch_assoc()['total'];
$total_page = ceil($total_data / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Input Produk - Aplikasi Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 30px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .btn-toggle {
            margin-bottom: 15px;
        }
        .table th {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Aplikasi Kasir - Input & Daftar Produk</h2>

    <!-- Tombol Show/Hide -->
    <button id="toggleFormBtn" class="btn btn-primary btn-toggle">Input Produk</button>

    <!-- Form Input Produk -->
    <div id="formProduk" style="display: none;">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Form Input Produk</h5>
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Kode Produk</label>
                            <input type="text" name="kode_produk" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Nama Produk</label>
                            <input type="text" name="nama_produk" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="makanan">Makanan</option>
                                <option value="minuman">Minuman</option>
                                <option value="cemilan">Cemilan</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Harga Beli</label>
                            <input type="number" name="harga_beli" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Harga Jual</label>
                            <input type="number" name="harga_jual" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Stok</label>
                            <input type="number" name="stok" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Satuan</label>
                            <select name="satuan" class="form-select" required>
                                <option value="">-- Pilih Satuan --</option>
                                <option value="pcs">pcs</option>
                                <option value="paket">paket</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-success">Simpan Produk</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Produk -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Daftar Produk</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kode</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($produk) > 0): ?>
                            <?php foreach ($produk as $p): ?>
                                <tr>
                                    <td><?= $p['id_produk'] ?></td>
                                    <td><?= htmlspecialchars($p['kode_produk']) ?></td>
                                    <td><?= htmlspecialchars($p['nama_produk']) ?></td>
                                    <td><?= ucfirst($p['kategori']) ?></td>
                                    <td>Rp <?= number_format($p['harga_beli'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($p['harga_jual'], 0, ',', '.') ?></td>
                                    <td><?= $p['stok'] ?></td>
                                    <td><?= $p['satuan'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8">Belum ada data produk</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_page; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Script Show/Hide Form -->
<script>
const toggleBtn = document.getElementById('toggleFormBtn');
const formProduk = document.getElementById('formProduk');

toggleBtn.addEventListener('click', () => {
    if (formProduk.style.display === 'none') {
        formProduk.style.display = 'block';
        toggleBtn.textContent = 'Sembunyikan Form';
        toggleBtn.classList.replace('btn-primary', 'btn-secondary');
    } else {
        formProduk.style.display = 'none';
        toggleBtn.textContent = 'Input Produk';
        toggleBtn.classList.replace('btn-secondary', 'btn-primary');
    }
});
</script>

</body>
</html>
