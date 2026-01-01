<?php
include 'koneksi.php';

// Create shifts table
$query1 = "CREATE TABLE IF NOT EXISTS shifts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_shift VARCHAR(50) NOT NULL,
    jam_masuk TIME NOT NULL,
    jam_pulang TIME NOT NULL
)";
if (mysqli_query($kon, $query1)) {
    echo "Table 'shifts' created successfully.\n";
} else {
    echo "Error creating 'shifts': " . mysqli_error($kon) . "\n";
}

// Create jadwal_kerja table
$query2 = "CREATE TABLE IF NOT EXISTS jadwal_kerja (
    id INT AUTO_INCREMENT PRIMARY KEY,
    karyawan_id INT NOT NULL,
    shift_id INT NOT NULL,
    tanggal DATE NOT NULL,
    FOREIGN KEY (karyawan_id) REFERENCES karyawan(id) ON DELETE CASCADE,
    FOREIGN KEY (shift_id) REFERENCES shifts(id) ON DELETE CASCADE
)";
if (mysqli_query($kon, $query2)) {
    echo "Table 'jadwal_kerja' created successfully.\n";
} else {
    echo "Error creating 'jadwal_kerja': " . mysqli_error($kon) . "\n";
}

// Insert dummy data for shifts (optional but helpful)
$check_shift = mysqli_query($kon, "SELECT * FROM shifts");
if (mysqli_num_rows($check_shift) == 0) {
    mysqli_query($kon, "INSERT INTO shifts (nama_shift, jam_masuk, jam_pulang) VALUES ('Pagi', '08:00:00', '16:00:00'), ('Siang', '13:00:00', '21:00:00')");
    echo "Inserted default shifts.\n";
}
?>
