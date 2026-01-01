<?php
include 'koneksi.php';

$aksi = $_REQUEST['aksi'];

if($aksi == "tambah"){
    $nik = $_POST['nik'];
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $ruangan_id = $_POST['ruangan_id'];
    
    $query = mysqli_query($kon, "INSERT INTO karyawan (nik, nama, jabatan, ruangan_id) VALUES ('$nik', '$nama', '$jabatan', '$ruangan_id')");
    
    if($query){
        header("Location: karyawan.php?pesan=sukses");
    } else {
        echo "Error: " . mysqli_error($kon);
    }

} elseif($aksi == "edit"){
    $id = $_POST['id'];
    $nik = $_POST['nik'];
    $nama = $_POST['nama'];
    $jabatan = $_POST['jabatan'];
    $ruangan_id = $_POST['ruangan_id'];
    
    $query = mysqli_query($kon, "UPDATE karyawan SET nik='$nik', nama='$nama', jabatan='$jabatan', ruangan_id='$ruangan_id' WHERE id='$id'");
    
    if($query){
        header("Location: karyawan.php?pesan=update");
    } else {
        echo "Error: " . mysqli_error($kon);
    }

} elseif($aksi == "hapus"){
    $id = $_GET['id'];
    $query = mysqli_query($kon, "DELETE FROM karyawan WHERE id='$id'");
    
    if($query){
        header("Location: karyawan.php?pesan=hapus");
    } else {
        echo "Error: " . mysqli_error($kon);
    }

} elseif($aksi == "reset_password"){
    $id = $_GET['id'];
    $password = password_hash('123456', PASSWORD_DEFAULT);
    
    $query = mysqli_query($kon, "UPDATE karyawan SET password='$password' WHERE id='$id'");
    
    if($query){
        echo "<script>alert('Password berhasil direset menjadi 123456'); window.location='karyawan.php';</script>";
    } else {
        echo "Error: " . mysqli_error($kon);
    }
}
?>
