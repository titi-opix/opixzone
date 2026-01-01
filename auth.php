<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($kon, $_POST['username']);
    $password = $_POST['password'];

    // 1. Cek Admin
    $query_admin = mysqli_query($kon, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($query_admin) > 0) {
        $user = mysqli_fetch_assoc($query_admin);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = 'admin';
            header("Location: dashboard.php");
            exit();
        }
    }

    // 2. Cek Karyawan (Login pakai NIK)
    $query_karyawan = mysqli_query($kon, "SELECT * FROM karyawan WHERE nik='$username'");
    if (mysqli_num_rows($query_karyawan) > 0) {
        $karyawan = mysqli_fetch_assoc($query_karyawan);
        // Password verify (default hash is for 'password')
        if (password_verify($password, $karyawan['password'])) {
            $_SESSION['karyawan_id'] = $karyawan['id'];
            $_SESSION['nama'] = $karyawan['nama'];
            $_SESSION['role'] = 'karyawan';
            header("Location: karyawan_dashboard.php");
            exit();
        }
    }
    
    header("Location: login.php?error=1");
    exit();
}
?>
