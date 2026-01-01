<?php
session_start();
include 'koneksi.php';
include 'ip_helper.php';

if (!isset($_SESSION['karyawan_id'])) {
    header("Location: login.php");
    exit();
}

$karyawan_id = $_SESSION['karyawan_id'];
$tanggal = date('Y-m-d');
$jam_sekarang = date('H:i:s');
$karyawan_id = $_SESSION['karyawan_id']; // Re-declare to be safe or ensure it's available
$current_ip = get_client_ip();

// Ambil data karyawan & ruangan for validation
$q_karyawan = mysqli_query($kon, "SELECT karyawan.*, ruangan.ip_address as room_ip, ruangan.nama_ruangan 
                                  FROM karyawan 
                                  LEFT JOIN ruangan ON karyawan.ruangan_id = ruangan.id 
                                  WHERE karyawan.id='$karyawan_id'");
$data_karyawan = mysqli_fetch_array($q_karyawan);
$room_ip = $data_karyawan['room_ip'];
$room_name = $data_karyawan['nama_ruangan'];

// Handle POST request (from form with photo)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi IP Berdasarkan Ruangan (Subnet Match) SEBELUM proses absen
    if (!is_same_subnet($current_ip, $room_ip)) {
        echo "<script>alert('Akses Ditolak! Anda harus berada di Jaringan WiFi: $room_name ($room_ip). IP Anda: $current_ip'); window.location='karyawan_dashboard.php';</script>";
        exit();
    }

    $aksi = $_POST['aksi'];
    $foto_base64 = $_POST['foto'];
    
    // Process Image
    $image_parts = explode(";base64,", $foto_base64);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    
    $folder = "uploads/absensi/" . $tanggal . "/";
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }
    
    $file_name = $karyawan_id . "_" . time() . ".png";
    $file_path = $folder . $file_name;
    
    file_put_contents($file_path, $image_base64);
    
    if ($aksi == 'checkin') {
         // Cek jadwal personal hari ini
        $cek_jadwal = mysqli_query($kon, "SELECT shifts.jam_masuk 
                                          FROM jadwal_kerja 
                                          JOIN shifts ON jadwal_kerja.shift_id = shifts.id 
                                          WHERE jadwal_kerja.karyawan_id='$karyawan_id' 
                                          AND jadwal_kerja.tanggal='$tanggal'");
        
        if(mysqli_num_rows($cek_jadwal) > 0){
            // Gunakan jam masuk dari shift
            $data_jadwal = mysqli_fetch_array($cek_jadwal);
            $jam_masuk_kantor = $data_jadwal['jam_masuk'];
        } else {
            // Fallback: Ambil setting jam masuk global
            $query_setting = mysqli_query($kon, "SELECT setting_value FROM settings WHERE setting_key='jam_masuk'");
            $data_setting = mysqli_fetch_array($query_setting);
            $jam_masuk_kantor = $data_setting['setting_value'];
        }
        
        $status_kehadiran = 'Tepat Waktu';
        
        // Grace period 15 menit
        $jam_masuk_toleransi = date('H:i:s', strtotime($jam_masuk_kantor) + (15 * 60));

        if ($jam_sekarang > $jam_masuk_toleransi) {
            $status_kehadiran = 'Terlambat';
        }
        
        $simpan = mysqli_query($kon, "INSERT INTO absensi (karyawan_id, tanggal, jam_masuk, status, status_kehadiran, foto_masuk) 
                                      VALUES ('$karyawan_id', '$tanggal', '$jam_sekarang', 'Hadir', '$status_kehadiran', '$file_path')");
        
        if ($simpan) {
            header("Location: karyawan_dashboard.php?pesan=sukses_masuk");
        } else {
            echo "Error: " . mysqli_error($kon);
        }

    } elseif ($aksi == 'checkout') {
        $update = mysqli_query($kon, "UPDATE absensi SET jam_keluar='$jam_sekarang', foto_keluar='$file_path' 
                              WHERE karyawan_id='$karyawan_id' AND tanggal='$tanggal'");
        
        if ($update) {
            header("Location: karyawan_dashboard.php?pesan=sukses_keluar");
        } else {
            echo "Error: " . mysqli_error($kon);
        }
    }

} else {
    // Fallback for GET requests (legacy or direct access without photo)
    // For now, redirect back to dashboard because photo is mandatory
     header("Location: karyawan_dashboard.php");
}
?>
