<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_absensi";

$kon = mysqli_connect($host, $user, $pass, $db);
if (!$kon) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set zona waktu ke Asia/Jayapura
date_default_timezone_set('Asia/Jayapura');
?>