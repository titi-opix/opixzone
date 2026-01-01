<?php
include 'koneksi.php';

if (isset($_POST['import'])) {
    $file = $_FILES['file_csv']['tmp_name'];
    
    // Cek file
    if(empty($file)){
         echo "<script>alert('Mohon pilih file CSV!'); window.location='import_karyawan.php';</script>";
         exit;
    }

    $handle = fopen($file, "r");
    
    // Default Password Hash
    $default_password = password_hash('123456', PASSWORD_DEFAULT);
    $success = 0;
    $failed = 0;

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Support semicolon separator if comma fails to split (common in some regions)
        if(count($data) == 1 && strpos($data[0], ';') !== false) {
             $data = explode(';', $data[0]);
        }

        // Validasi jumlah kolom minimal
        if(count($data) < 2) continue; 

        $nik = mysqli_real_escape_string($kon, trim($data[0]));
        $nama = mysqli_real_escape_string($kon, trim($data[1]));
        $jabatan = isset($data[2]) ? mysqli_real_escape_string($kon, trim($data[2])) : 'Staff';
        
        // Skip header row if labeled "NIK"
        if(strtolower($nik) == 'nik') continue;

        // Skip empty code
        if(empty($nik)) continue;

        // Cek NIK Duplicate
        $check = mysqli_query($kon, "SELECT id FROM karyawan WHERE nik='$nik'");
        if(mysqli_num_rows($check) > 0){
            $failed++; // Skip/Fail duplicates
        } else {
            $insert = mysqli_query($kon, "INSERT INTO karyawan (nik, nama, jabatan, password) VALUES ('$nik', '$nama', '$jabatan', '$default_password')");
            if($insert) $success++;
        }
    }
    
    fclose($handle);
    echo "<script>alert('Import Selesai!\\nBerhasil: $success\\nGagal/Duplikat: $failed'); window.location='karyawan.php';</script>";
}
?>