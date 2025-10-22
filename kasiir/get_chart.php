<?php
session_start();
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$cart = $_SESSION['cart'];
$total_all = 0;
?>
<div class="table-responsive">
  <table class="table table-bordered align-middle">
    <thead class="table-light text-center">
      <tr>
        <th>#</th>
        <th>Nama Produk</th>
        <th>Harga Jual</th>
        <th>Qty</th>
        <th>Total</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($cart) > 0): $no=1; foreach($cart as $c): 
        $total_all += $c['total'];
      ?>
        <tr class="text-center">
          <td><?= $no++ ?></td>
          <td class="text-start"><?= htmlspecialchars($c['nama_produk']) ?></td>
          <td class="text-end"><?= number_format($c['harga_jual'],2,',','.') ?></td>
          <td style="width:120px;">
            <input type="number" class="form-control cart-qty text-center" data-id="<?= $c['id_produk'] ?>" value="<?= $c['qty'] ?>" min="1">
          </td>
          <td class="text-end"><?= number_format($c['total'],2,',','.') ?></td>
          <td><button class="btn btn-sm btn-danger btn-remove-item" data-id="<?= $c['id_produk'] ?>">Hapus</button></td>
        </tr>
      <?php endforeach; else: ?>
        <tr><td colspan="6" class="text-center">Keranjang kosong</td></tr>
      <?php endif; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="4" class="text-end fw-bold">Total</td>
        <td class="text-end fw-bold"><?= number_format($total_all,2,',','.') ?></td>
        <td></td>
      </tr>
    </tfoot>
  </table>
</div>

<!-- hidden untuk JS membaca total -->
<input type="hidden" id="cart-total-hidden" value="<?= $total_all ?>">
