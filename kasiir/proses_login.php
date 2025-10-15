<?php
session_start();

// Konfigurasi koneksi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "kasir_app";

// Koneksi ke database
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari form
$username_email = $_POST['username_email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username_email) || empty($password)) {
    die("Username/Email dan password harus diisi.");
}

// Cari user berdasarkan username atau email
$sql = "SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username_email, $username_email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    // Verifikasi password
    if (password_verify($password, $user['password'])) {
        // Login sukses, simpan session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Redirect ke dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Password salah.";
    }
} else {
    echo "Username atau email tidak ditemukan.";
}

$stmt->close();
$conn->close();
?>
