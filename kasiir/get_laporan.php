<?php
include "db.php";

$tgl_awal  = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';

if (!$tgl_awal || !$tgl_akhir) {
    echo json_encode([]);
    exit;
}

$sql = "
    SELECT 
        p.tanggal,
        pr.nama_produk,
        d.harga_jual,
        d.qty,
        d.total AS total_harga
    FROM penjualan p
    INNER JOIN penjualan_detail d ON p.no_faktur = d.no_faktur
    INNER JOIN produk pr ON d.id_produk = pr.id_produk
    WHERE DATE(p.tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
    ORDER BY p.tanggal ASC
";

$result = $koneksi->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
?>
