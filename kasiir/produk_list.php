<?php
// tampilkan daftar produk (HTML) untuk area #produk-area
session_start();
require_once 'db.php';

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$start = ($page - 1) * $limit;

// ambil total
$stmtTotal = $koneksi->prepare("SELECT COUNT(*) AS total FROM produk");
$stmtTotal->execute();
$resTotal = $stmtTotal->get_result()->fetch_assoc();
$total = (int)$resTotal['total'];
$pages = ($total > 0) ? ceil($total / $limit) : 1;

// ambil data
$stmt = $koneksi->prepare("SELECT * FROM produk ORDER BY id_produk DESC LIMIT ?, ?");
$stmt->bind_param("ii", $start, $limit);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="table-responsive">
  <table class="table table-striped table-bordered align-middle">
    <thead class="table-light">
      <tr class="text-center">
        <th>#</th>
        <th>Kode</th>
        <th>Nama Produk</th>
        <th>Kategori</th>
        <th>Harga Jual</th>
        <th>Stok</th>
        <th>Satuan</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): $no = $start + 1; ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['kode_produk']) ?></td>
            <td><?= htmlspecialchars($row['nama_produk']) ?></td>
            <td><?= htmlspecialchars($row['kategori']) ?></td>
            <td class="text-end"><?= number_format($row['harga_jual'],2,',','.') ?></td>
            <td class="text-center"><?= $row['stok'] ?></td>
            <td class="text-center"><?= $row['satuan'] ?></td>
            <td class="text-center">
              <button class="btn btn-sm btn-primary btn-add" onclick="addToCart(<?= $row['id_produk'] ?>)">
                <i class="fa fa-cart-plus"></i> Add to Cart
              </button>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="8" class="text-center">Belum ada produk.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Pagination -->
<nav>
  <ul class="pagination">
    <?php for($i=1;$i<=$pages;$i++): ?>
      <li class="page-item <?= ($i==$page)?'active':'' ?>">
        <a class="page-link" href="javascript:void(0)" onclick="loadProduk(<?= $i ?>)"><?= $i ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>
