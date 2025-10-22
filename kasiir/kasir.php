<?php
session_start();
if(!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Kasir</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        table {
            width: 100%;
        }
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        .total-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <h3 class="text-center mb-4">🧾 Transaksi Kasir</h3>

    <!-- ======================== -->
    <!-- Daftar Produk -->
    <!-- ======================== -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <strong>Daftar Produk</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="productTable">
                    <thead>
                        <tr class="table-primary text-center">
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="productData"></tbody>
                </table>
            </div>
            <div id="pagination" class="text-center mt-3"></div>
        </div>
    </div>

    <!-- ======================== -->
    <!-- Cart -->
    <!-- ======================== -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <strong>Keranjang Belanja</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="cartTable">
                    <thead>
                        <tr class="table-success text-center">
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="cartData"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ======================== -->
    <!-- Form Pembayaran -->
    <!-- ======================== -->
    <div class="total-box">
        <div class="row mb-2">
            <div class="col-md-6">
                <label>Total Belanja (Rp)</label>
                <input type="text" id="totalBelanja" class="form-control" readonly>
            </div>
            <div class="col-md-6">
                <label>Total Bayar (Rp)</label>
                <input type="number" id="totalBayar" class="form-control">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Kembalian (Rp)</label>
                <input type="text" id="kembalian" class="form-control" readonly>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button class="btn btn-primary w-100" id="btnBayar">💰 Bayar</button>
            </div>
        </div>
    </div>
</div>

<!-- ======================== -->
<!-- JS Script -->
<!-- ======================== -->
<script>
let cart = [];
let currentPage = 1;
const itemsPerPage = 10;

// ===== Load Produk via AJAX =====
function loadProducts(page = 1) {
    fetch(`get_products.php?page=${page}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('productData');
            tbody.innerHTML = "";
            let start = (page - 1) * itemsPerPage + 1;

            data.products.forEach((p, i) => {
                tbody.innerHTML += `
                    <tr class="text-center">
                        <td>${start + i}</td>
                        <td>${p.nama_produk}</td>
                        <td>Rp ${parseInt(p.harga_jual).toLocaleString()}</td>
                        <td>${p.stok}</td>
                        <td><button class="btn btn-sm btn-success" onclick="addToCart('${p.id_produk}','${p.nama_produk}',${p.harga_jual})">Tambah</button></td>
                    </tr>
                `;
            });

            // Pagination
            const totalPages = Math.ceil(data.total / itemsPerPage);
            const pagination = document.getElementById('pagination');
            pagination.innerHTML = "";
            for (let i = 1; i <= totalPages; i++) {
                pagination.innerHTML += `<button class="btn btn-outline-primary btn-sm m-1 ${i === page ? 'active' : ''}" onclick="loadProducts(${i})">${i}</button>`;
            }
        })
        .catch(error => console.error("Gagal memuat produk:", error));
}

// ===== Tambah ke Cart =====
function addToCart(id, nama, harga) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({ id, nama, harga, qty: 1 });
    }
    renderCart();
}

// ===== Render Cart =====
function renderCart() {
    const tbody = document.getElementById('cartData');
    tbody.innerHTML = "";
    let total = 0;

    cart.forEach(item => {
        const totalHarga = item.harga * item.qty;
        total += totalHarga;
        tbody.innerHTML += `
            <tr>
                <td>${item.nama}</td>
                <td>Rp ${item.harga.toLocaleString()}</td>
                <td>${item.qty}</td>
                <td>Rp ${totalHarga.toLocaleString()}</td>
            </tr>
        `;
    });

    document.getElementById('totalBelanja').value = total.toLocaleString();
}

// ===== Hitung Kembalian =====
document.getElementById('totalBayar').addEventListener('input', function() {
    const total = parseInt(document.getElementById('totalBelanja').value.replace(/,/g, "")) || 0;
    const bayar = parseInt(this.value) || 0;
    const kembali = bayar - total;
    document.getElementById('kembalian').value = kembali >= 0 ? kembali.toLocaleString() : "0";
});

// ===== Proses Bayar via AJAX =====
document.getElementById('btnBayar').addEventListener('click', function() {
    if (cart.length === 0) {
        alert("Keranjang masih kosong!");
        return;
    }

    const total = parseInt(document.getElementById('totalBelanja').value.replace(/,/g, "")) || 0;
    const bayar = parseInt(document.getElementById('totalBayar').value) || 0;
    const kembali = bayar - total;

    if (bayar < total) {
        alert("Uang pembayaran kurang!");
        return;
    }

    const faktur = "TRX" + Date.now(); // Nomor faktur unik berbasis waktu
    const data = { faktur, total, bayar, kembali, cart };

    fetch("process_payment.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            alert("✅ Transaksi berhasil disimpan!");
            cart = [];
            renderCart();
            document.getElementById('totalBelanja').value = "";
            document.getElementById('totalBayar').value = "";
            document.getElementById('kembalian').value = "";
            loadProducts(); // refresh stok produk
        } else {
            alert("❌ Gagal menyimpan transaksi: " + result.message);
        }
    })
    .catch(error => {
        console.error("Error saat menyimpan transaksi:", error);
        alert("Terjadi kesalahan koneksi ke server.");
    });
});

// ===== Load Produk saat halaman dibuka =====
loadProducts();
</script>


</script>

</body>
</html>
