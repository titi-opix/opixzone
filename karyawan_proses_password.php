<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['karyawan_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $karyawan_id = $_SESSION['karyawan_id'];
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi_password'];

    // Validasi input
    if (strlen($password_baru) < 6) {
        header("Location: karyawan_ganti_password.php?pesan=pendek");
        exit();
    }

    if ($password_baru !== $konfirmasi) {
        header("Location: karyawan_ganti_password.php?pesan=tidakcocok");
        exit();
    }

    // Ambil password lama dari database
    $query = mysqli_query($kon, "SELECT password FROM karyawan WHERE id='$karyawan_id'");
    $data = mysqli_fetch_array($query);
    $password_db = $data['password'];

    // Verifikasi password lama
    if (password_verify($password_lama, $password_db)) {
        // Hash password baru
        $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
        
        // Update di database
        $update = mysqli_query($kon, "UPDATE karyawan SET password='$password_hash' WHERE id='$karyawan_id'");
        
        if ($update) {
            header("Location: karyawan_ganti_password.php?pesan=sukses");
        } else {
            echo "Error: " . mysqli_error($kon);
        }
    } else {
        header("Location: karyawan_ganti_password.php?pesan=gagal");
    }
} else {
    header("Location: karyawan_ganti_password.php");
}
?>
