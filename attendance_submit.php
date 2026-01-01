<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
include __DIR__ . '/../koneksi.php';
include __DIR__ . '/../ip_helper.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $karyawan_id = $_POST['karyawan_id'] ?? '';
    $aksi = $_POST['aksi'] ?? ''; // checkin or checkout
    $foto_base64 = $_POST['foto'] ?? '';
    
    if (empty($karyawan_id) || empty($aksi) || empty($foto_base64)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    $tanggal = date('Y-m-d');
    $jam_sekarang = date('H:i:s');
    $current_ip = get_client_ip();

    // Validate IP / Network
    $q_karyawan = mysqli_query($kon, "SELECT karyawan.*, ruangan.ip_address as room_ip, ruangan.nama_ruangan 
                                      FROM karyawan 
                                      LEFT JOIN ruangan ON karyawan.ruangan_id = ruangan.id 
                                      WHERE karyawan.id='$karyawan_id'");
                                      
    if (mysqli_num_rows($q_karyawan) == 0) {
        echo json_encode(['status' => 'error', 'message' => 'Karyawan not found']);
        exit;
    }

    $data_karyawan = mysqli_fetch_array($q_karyawan);
    $room_ip = $data_karyawan['room_ip'];
    $room_name = $data_karyawan['nama_ruangan'];

    // Skip IP check if for some reason room_ip is not set, or implement strict policy. 
    // Here we implement strict policy consistent with web
    if (!empty($room_ip) && !is_same_subnet($current_ip, $room_ip)) {
        echo json_encode([
            'status' => 'error', 
            'message' => "Invalid IP. You are at $current_ip, but must be at $room_name ($room_ip)"
        ]);
        exit;
    }

    // Process Image
    // Expecting base64 string, might have header "data:image/png;base64," or just raw base64
    if (strpos($foto_base64, 'base64,') !== false) {
        $image_parts = explode(";base64,", $foto_base64);
        $image_base64 = base64_decode($image_parts[1]);
    } else {
        $image_base64 = base64_decode($foto_base64);
    }

    $folder = "../uploads/absensi/" . $tanggal . "/";
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }
    
    $file_name = $karyawan_id . "_" . time() . ".png";
    $file_path_db = "uploads/absensi/" . $tanggal . "/" . $file_name; // Path stored in DB (relative to root)
    $file_path_disk = $folder . $file_name; // Physical path
    
    file_put_contents($file_path_disk, $image_base64);

    if ($aksi == 'checkin') {
        // Check if already checked in
        $check = mysqli_query($kon, "SELECT id FROM absensi WHERE karyawan_id='$karyawan_id' AND tanggal='$tanggal'");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Already checked in today']);
            exit;
        }

        // Get Shift Info (Logic copied from karyawan_proses_absen.php)
        $cek_jadwal = mysqli_query($kon, "SELECT shifts.jam_masuk 
                                          FROM jadwal_kerja 
                                          JOIN shifts ON jadwal_kerja.shift_id = shifts.id 
                                          WHERE jadwal_kerja.karyawan_id='$karyawan_id' 
                                          AND jadwal_kerja.tanggal='$tanggal'");
        
        if(mysqli_num_rows($cek_jadwal) > 0){
            $data_jadwal = mysqli_fetch_array($cek_jadwal);
            $jam_masuk_kantor = $data_jadwal['jam_masuk'];
        } else {
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
                                      VALUES ('$karyawan_id', '$tanggal', '$jam_sekarang', 'Hadir', '$status_kehadiran', '$file_path_db')");
        
        if ($simpan) {
            echo json_encode(['status' => 'success', 'message' => 'Check-in successful']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($kon)]);
        }

    } elseif ($aksi == 'checkout') {
         // Check if checked in
         $check = mysqli_query($kon, "SELECT id, jam_keluar FROM absensi WHERE karyawan_id='$karyawan_id' AND tanggal='$tanggal'");
         if (mysqli_num_rows($check) == 0) {
             echo json_encode(['status' => 'error', 'message' => 'Have not checked in yet']);
             exit;
         }
         $data_absen = mysqli_fetch_assoc($check);
         if (!empty($data_absen['jam_keluar']) && $data_absen['jam_keluar'] != '00:00:00') {
             echo json_encode(['status' => 'error', 'message' => 'Already checked out']);
             exit;
         }

        $update = mysqli_query($kon, "UPDATE absensi SET jam_keluar='$jam_sekarang', foto_keluar='$file_path_db' 
                              WHERE karyawan_id='$karyawan_id' AND tanggal='$tanggal'");
        
        if ($update) {
            echo json_encode(['status' => 'success', 'message' => 'Check-out successful']);
        } else {
             echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($kon)]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
