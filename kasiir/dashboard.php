<?php
session_start();
?>
<?php
session_start();

// // Cek apakah sudah login
// if (!isset($_SESSION['user_id'])) {
//     // header("Location: login.html");
//     // exit();
// }
// ?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard Kasir</title>
  
  <!-- Bootstrap CDN untuk styling dan grid -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- FontAwesome CDN untuk icon -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f8f9fa;
      text-align: center;
      padding: 30px;
      font-family: Arial, sans-serif;
    }
    .logo {
      margin-bottom: 40px;
    }
    .logo img {
      width: 150px;
      height: 150px;
      object-fit: contain;
    }
    .menu-button {
      background-color: #007bff;
      color: white;
      border-radius: 10px;
      padding: 40px 20px;
      margin-bottom: 20px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      user-select: none;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 180px;
    }
    .menu-button:hover {
      background-color: #0056b3;
      text-decoration: none;
      color: white;
    }
    .menu-icon {
      font-size: 70px;
      margin-bottom: 15px;
    }
    .menu-text {
      font-size: 22px;
      font-weight: 600;
    }
  </style>
</head>
<body>

  <div class="logo">
    <!-- Icon kasir besar di tengah -->
    <i class="fa-solid fa-cash-register" style="font-size:150px; color:#007bff;"></i>
    <span>Selamat datang <?= htmlspecialchars($_SESSION['username']) ?></span>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-md-4">
        <a href="produk.php" class="menu-button text-decoration-none">
          <i class="fa-solid fa-box-open menu-icon"></i>
          <div class="menu-text">Produk</div>
        </a>
      </div>
      <div class="col-md-4">
        <a href="kasir.php" class="menu-button text-decoration-none">
          <i class="fa-solid fa-cash-register menu-icon"></i>
          <div class="menu-text">Kasir</div>
        </a>
      </div>
      <div class="col-md-4">
        <a href="laporan.php" class="menu-button text-decoration-none">
          <i class="fa-solid fa-file-lines menu-icon"></i>
          <div class="menu-text">Laporan</div>
        </a>
      </div>
    </div>
  </div>
   <a href="logout.php" role="button"> log out</a>

</body>
</html>
