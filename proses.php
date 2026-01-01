<?php
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta'); // Set timezone

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';
$karyawan_id = isset($_GET['id']) ? $_GET['id'] : '';
$tanggal = date('Y-m-d');
$jam_sekarang = date('H:i:s');

// Ambil setting jam masuk
$query_setting = mysqli_query($kon, "SELECT setting_value FROM settings WHERE setting_key='jam_masuk'");
$data_setting = mysqli_fetch_array($query_setting);
$jam_masuk_kantor = $data_setting['setting_value'];

if ($aksi == "checkin") {
    $status = $_GET['status'];
    $status_kehadiran = "Tepat Waktu";

    if ($status == 'Hadir') {
        if ($jam_sekarang > $jam_masuk_kantor) {
            $status_kehadiran = "Terlambat";
        }
    } else {
        $status_kehadiran = "-"; // Kalau izin tidak ada terlambat
    }

    $cek = mysqli_query($kon, "SELECT * FROM absensi WHERE karyawan_id='$karyawan_id' AND tanggal='$tanggal'");
    if (mysqli_num_rows($cek) == 0) {
        $simpan = mysqli_query($kon, "INSERT INTO absensi (karyawan_id, tanggal, jam_masuk, status, status_kehadiran) 
                                      VALUES ('$karyawan_id', '$tanggal', '$jam_sekarang', '$status', '$status_kehadiran')");
        if ($simpan) {
            header("Location: absensi.php?pesan=checkin_sukses");
        } else {
            echo "Error: " . mysqli_error($kon);
        }
    } else {
        header("Location: absensi.php?pesan=sudah_absen");
    }

} elseif ($aksi == "checkout") {
    $update = mysqli_query($kon, "UPDATE absensi SET jam_keluar='$jam_sekarang' WHERE karyawan_id='$karyawan_id' AND tanggal='$tanggal'");
    if ($update) {
        header("Location: absensi.php?pesan=checkout_sukses");
    } else {
        echo "Error: " . mysqli_error($kon);
    }
} else {
    header("Location: index.php");
}
?>