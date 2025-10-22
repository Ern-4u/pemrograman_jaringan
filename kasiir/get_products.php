<?php
header('Content-Type: application/json');
include 'db.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$sql = "SELECT id_produk, nama_produk, harga_jual, stok FROM produk LIMIT $limit OFFSET $offset";
$result = $koneksi->query($sql);

if (!$result) {
    echo json_encode(["error" => "Query gagal: " . $koneksi->error]);
    exit;
}

$totalQuery = $koneksi->query("SELECT COUNT(*) AS total FROM produk");
$total = $totalQuery ? $totalQuery->fetch_assoc()['total'] : 0;

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

echo json_encode([
    "products" => $products,
    "total" => $total
]);
?>
