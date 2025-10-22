<?php
header('Content-Type: application/json');
include 'db.php';

// Ambil data JSON dari AJAX
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['cart'])) {
    echo json_encode(["success" => false, "message" => "Data tidak valid"]);
    exit;
}

$faktur  = $data['faktur'];
$total   = $data['total'];
$bayar   = $data['bayar'];
$kembali = $data['kembali'];
$cart    = $data['cart'];

// Simpan ke tabel penjualan
$stmt = $koneksi->prepare("INSERT INTO penjualan (no_faktur, total_belanja, total_bayar, kembalian) VALUES (?, ?, ?, ?)");
$stmt->bind_param("siii", $faktur, $total, $bayar, $kembali);

if ($stmt->execute()) {
    // Simpan detail transaksi
    $detailStmt = $koneksi->prepare("INSERT INTO penjualan_detail (no_faktur, id_produk, qty, harga_jual, total) VALUES (?, ?, ?, ?, ?)");

    foreach ($cart as $item) {
        $id_produk = $item['id'];
        $qty       = $item['qty'];
        $harga     = $item['harga'];
        $subtotal  = $qty * $harga;
        $detailStmt->bind_param("siiii", $faktur, $id_produk, $qty, $harga, $subtotal);
        $detailStmt->execute();

        // Kurangi stok produk
        $koneksi->query("UPDATE produk SET stok = stok - $qty WHERE id_produk = $id_produk");
    }

    echo json_encode(["success" => true, "message" => "Transaksi berhasil disimpan"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal menyimpan transaksi: " . $koneksi->error]);
}
?>
