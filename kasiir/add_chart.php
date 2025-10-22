<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// fungsi bantu cari index item berdasarkan id_produk
function find_index($cart, $id) {
    foreach ($cart as $k => $v) {
        if ($v['id_produk'] == $id) return $k;
    }
    return false;
}

// jika remove_id dikirim → hapus item
if (isset($_POST['remove_id'])) {
    $rid = (int)$_POST['remove_id'];
    $idx = find_index($_SESSION['cart'], $rid);
    if ($idx !== false) {
        array_splice($_SESSION['cart'], $idx, 1);
    }
    echo json_encode(['success'=>true]); exit;
}

// jika update qty
if (isset($_POST['update_id']) && isset($_POST['qty'])) {
    $uid = (int)$_POST['update_id'];
    $qty = max(1, (int)$_POST['qty']);
    $idx = find_index($_SESSION['cart'], $uid);
    if ($idx !== false) {
        $_SESSION['cart'][$idx]['qty'] = $qty;
        $_SESSION['cart'][$idx]['total'] = $_SESSION['cart'][$idx]['harga_jual'] * $qty;
        echo json_encode(['success'=>true]); exit;
    } else {
        echo json_encode(['success'=>false, 'message'=>'Item tidak ditemukan']); exit;
    }
}

// jika menambahkan id_produk
if (!isset($_POST['id_produk'])) {
    echo json_encode(['success'=>false,'message'=>'id_produk tidak ditemukan']); exit;
}

$id = (int)$_POST['id_produk'];

// ambil data produk dari DB
$stmt = $koneksi->prepare("SELECT id_produk, nama_produk, harga_jual FROM produk WHERE id_produk = ?");
$stmt->bind_param("i",$id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['success'=>false,'message'=>'Produk tidak ditemukan']); exit;
}
$row = $res->fetch_assoc();

// jika item sudah ada di cart → tambah qty
$idx = find_index($_SESSION['cart'], $id);
if ($idx !== false) {
    $_SESSION['cart'][$idx]['qty'] += 1;
    $_SESSION['cart'][$idx]['total'] = $_SESSION['cart'][$idx]['qty'] * $_SESSION['cart'][$idx]['harga_jual'];
} else {
    $_SESSION['cart'][] = [
        'id_produk' => (int)$row['id_produk'],
        'nama_produk' => $row['nama_produk'],
        'harga_jual' => (float)$row['harga_jual'],
        'qty' => 1,
        'total' => (float)$row['harga_jual']
    ];
}

echo json_encode(['success'=>true]);
