<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2 class="mb-3 text-center">Laporan Penjualan</h2>

    <!-- Filter Rentang Tanggal -->
    <form id="filterForm" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="tgl_awal" class="form-label">Tanggal Awal</label>
            <input type="date" name="tgl_awal" id="tgl_awal" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label for="tgl_akhir" class="form-label">Tanggal Akhir</label>
            <input type="date" name="tgl_akhir" id="tgl_akhir" class="form-control" required>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
        </div>
    </form>

    <!-- Tabel Laporan -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped w-100 text-center align-middle" id="tabelLaporan">
            <thead class="table-dark">
                <tr>
                    <th>Tanggal Beli</th>
                    <th>Nama Produk</th>
                    <th>Harga Jual</th>
                    <th>Qty</th>
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody id="dataLaporan">
                <tr><td colspan="5">Silakan pilih rentang tanggal dan klik "Tampilkan"</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#filterForm').on('submit', function(e){
        e.preventDefault();
        let tgl_awal = $('#tgl_awal').val();
        let tgl_akhir = $('#tgl_akhir').val();

        $.ajax({
            url: 'get_laporan.php',
            type: 'POST',
            data: {tgl_awal: tgl_awal, tgl_akhir: tgl_akhir},
            success: function(response){
                $('#dataLaporan').html(response);
            },
            error: function(){
                alert("Gagal mengambil data.");
            }
        });
    });
});
</script>

</body>
</html>
