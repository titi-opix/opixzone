<?php
include 'koneksi.php';
$result = mysqli_query($kon, "DESCRIBE karyawan");
while ($row = mysqli_fetch_assoc($result)) {
    print_r($row);
    echo "\n";
}
?>
