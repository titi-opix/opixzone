<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
include __DIR__ . '/../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $karyawan_id = $_GET['karyawan_id'] ?? '';

    if (empty($karyawan_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Karyawan ID is required']);
        exit;
    }

    $karyawan_id = mysqli_real_escape_string($kon, $karyawan_id);

    // Get last 10 records
    $query = mysqli_query($kon, "SELECT * FROM absensi WHERE karyawan_id='$karyawan_id' ORDER BY tanggal DESC, id DESC LIMIT 10");
    
    $data = [];
    while ($row = mysqli_fetch_assoc($query)) {
        $data[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'data' => $data
    ]);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
