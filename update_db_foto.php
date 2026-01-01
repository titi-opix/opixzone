<?php
include 'koneksi.php';

// Add foto_masuk column
$query1 = "ALTER TABLE absensi ADD COLUMN foto_masuk TEXT NULL AFTER status_kehadiran";
if (mysqli_query($kon, $query1)) {
    echo "Column foto_masuk added successfully.\n";
} else {
    echo "Error adding foto_masuk: " . mysqli_error($kon) . "\n";
}

// Add foto_keluar column
$query2 = "ALTER TABLE absensi ADD COLUMN foto_keluar TEXT NULL AFTER foto_masuk";
if (mysqli_query($kon, $query2)) {
    echo "Column foto_keluar added successfully.\n";
} else {
    echo "Error adding foto_keluar: " . mysqli_error($kon) . "\n";
}
?>
