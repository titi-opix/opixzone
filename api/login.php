<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
include __DIR__ . '/../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Username and password are required']);
        exit;
    }

    $username = mysqli_real_escape_string($kon, $username);

    // Query Karyawan
    $query = mysqli_query($kon, "SELECT * FROM karyawan WHERE nik='$username'");
    
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        if (password_verify($password, $user['password'])) {
            // Success
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'id' => $user['id'],
                    'nama' => $user['nama'],
                    'email' => $user['email'],
                    'jabatan' => $user['jabatan'],
                    'ruangan_id' => $user['ruangan_id']
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
