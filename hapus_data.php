<?php
include 'koneksi.php';

if (isset($_GET['konfirmasi']) && $_GET['konfirmasi'] == 'y') {
    // Hapus data absensi dulu karena ada foreign key
    mysqli_query($kon, "DELETE FROM absensi");
    // Kemudian hapus data karyawan
    mysqli_query($kon, "DELETE FROM karyawan");
    // Reset auto increment agar ID mulai dari 1 lagi
    mysqli_query($kon, "ALTER TABLE karyawan AUTO_INCREMENT = 1");
    
    echo "<script>alert('Semua data telah dibersihkan!'); window.location='index.php';</script>";
}
?>