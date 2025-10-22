<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    echo json_encode(['success'=>false,'message'=>'Cart kosong']); exit;
}

// ambil data dari request
$total_belanja = isset($_POST['total_belanja']) ? (float)$_POST['total_belanja'] : 0;
$total_bayar   = isset($_POST['total_bayar']) ? (float)$_POST['total_bayar'] : 0;
$kembalian     = isset($_POST['kembalian']) ? (float)$_POST['kembalian'] : 0;

if ($total_belanja <= 0) {
    echo json_encode(['success'=>false,'message'=>'Total belanja tidak valid']); exit;
}
if ($total_bayar < $total_belanja) {
    echo json_encode(['success'=>false,'message'=>'Total bayar kurang']); exit;
}

// buat nomor faktur unik: INVyyyymmddHHMMSS + random 3 digit
$no_faktur = 'INV' . date('YmdHis') . rand(100,999);
$tanggal = date('Y-m-d H:i:s');

$koneksi->begin_transaction();
try {
    // insert header jual
    $stmt = $koneksi->prepare("INSERT INTO jual (no_faktur, tanggal, total_belanja, total_bayar, kembalian) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddd", $no_faktur, $tanggal, $total_belanja, $total_bayar, $kembalian);
    $stmt->execute();
    $id_jual = $koneksi->insert_id;

    // insert detail untuk setiap item cart
    $stmtDetail = $koneksi->prepare("INSERT INTO jual_detail (id_jual, id_produk, nama_produk, harga_jual, qty, total) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $item) {
        $idp = $item['id_produk'];
        $nama = $item['nama_produk'];
        $harga = $item['harga_jual'];
        $qty = $item['qty'];
        $total = $item['total'];
        $stmtDetail->bind_param("iisdid", $id_jual, $idp, $nama, $harga, $qty, $total);
        $stmtDetail->execute();

        // (opsional) kurangi stok pada tabel produk jika mau
        // $stmtUpd = $koneksi->prepare("UPDATE produk SET stok = stok - ? WHERE id_produk = ?");
        // $stmtUpd->bind_param("ii", $qty, $idp);
        // $stmtUpd->execute();
    }

    $koneksi->commit();

    // kosongkan cart
    unset($_SESSION['cart']);

    echo json_encode(['success'=>true, 'no_faktur' => $no_faktur]);
} catch (Exception $e) {
    $koneksi->rollback();
    echo json_encode(['success'=>false, 'message' => $e->getMessage()]);
}
