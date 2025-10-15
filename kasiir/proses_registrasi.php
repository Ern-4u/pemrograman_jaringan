<?php
// Konfigurasi koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "kasir_app";

// Koneksi ke MySQL
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari form
$username = $_POST['username'] ?? '';
$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Validasi sederhana (kosong atau tidak)
if (empty($username) || empty($email) || empty($password)) {
    die("Semua field harus diisi.");
}

// Enkripsi password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Query untuk insert ke database
$sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("sss", $username, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "Registrasi berhasil. <a href='login.html'>Login sekarang</a>";
    } else {
        echo "Registrasi gagal: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Kesalahan query: " . $conn->error;
}

$conn->close();
?>
