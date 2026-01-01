<?php
include 'koneksi.php';

// Check if column 'kode' exists
$check = mysqli_query($kon, "SHOW COLUMNS FROM shifts LIKE 'kode'");
if(mysqli_num_rows($check) == 0) {
    // Add column
    mysqli_query($kon, "ALTER TABLE shifts ADD COLUMN kode VARCHAR(5) AFTER nama_shift");
    echo "Column 'kode' added.<br>";
    
    // Update existing records with defaults
    $shifts = mysqli_query($kon, "SELECT * FROM shifts");
    while($row = mysqli_fetch_array($shifts)){
        $id = $row['id'];
        // Generate code from first letter of name + first letter of second word if exists
        $words = explode(' ', $row['nama_shift']);
        $code = strtoupper(substr($words[0], 0, 1));
        if(isset($words[1])) {
            $code .= strtoupper(substr($words[1], 0, 1));
        }
        
        // Custom overrides if needed, or just let user edit later
        if(stripos($row['nama_shift'], 'Pagi') !== false) $code = 'P';
        if(stripos($row['nama_shift'], 'Siang') !== false) $code = 'S';
        if(stripos($row['nama_shift'], 'Malam') !== false) $code = 'M';
        if(stripos($row['nama_shift'], 'Libur') !== false) $code = 'L';
        
        mysqli_query($kon, "UPDATE shifts SET kode='$code' WHERE id='$id'");
        echo "Updated shift {$row['nama_shift']} to code $code.<br>";
    }
} else {
    echo "Column 'kode' already exists.<br>";
}
echo "Database update complete.";
?>
