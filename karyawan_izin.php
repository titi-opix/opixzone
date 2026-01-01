<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['karyawan_id'])) {
    header("Location: login.php");
    exit();
}

$karyawan_id = $_SESSION['karyawan_id'];

// Handle Submit
if (isset($_POST['submit_izin'])) {
    $mulai = $_POST['tanggal_mulai'];
    $selesai = $_POST['tanggal_selesai'];
    $jenis = $_POST['jenis_izin'];
    $keterangan = $_POST['keterangan'];

    // Upload Bukti (Optional)
    $bukti = "";
    if ($_FILES['bukti_foto']['name'] != "") {
        $target_dir = "uploads/izin/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES["bukti_foto"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["bukti_foto"]["tmp_name"], $target_file)) {
            $bukti = $target_file;
        }
    }

    $query = "INSERT INTO izin (karyawan_id, tanggal_mulai, tanggal_selesai, jenis_izin, keterangan, bukti_foto, status) 
              VALUES ('$karyawan_id', '$mulai', '$selesai', '$jenis', '$keterangan', '$bukti', 'Menunggu')";
    
    if (mysqli_query($kon, $query)) {
        echo "<script>alert('Pengajuan izin berhasil dikirim!'); window.location='karyawan_izin.php';</script>";
    } else {
        echo "<script>alert('Gagal mengirim pengajuan.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Izin - Absensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .mobile-card {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            max-width: 500px;
            margin: 0 auto 20px;
        }
        h2 { margin-top: 0; color: #2b2d42; font-size: 1.25rem; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px; margin-bottom: 20px; }
        
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; color: #6c757d; font-weight: 500; }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            box-sizing: border-box; /* Fix padding issue */
        }
        .form-control:focus { border-color: #4361ee; outline: none; }
        
        .btn-submit {
            background: #4361ee; color: white;
            border: none; padding: 12px;
            width: 100%; border-radius: 8px;
            font-weight: bold; font-size: 1rem;
            cursor: pointer;
        }
        .btn-back {
            background: #e0e0e0; color: #333;
            text-decoration: none; display: block;
            text-align: center; padding: 12px;
            border-radius: 8px; margin-top: 10px;
            font-weight: bold;
        }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 0.9rem; }
        th { text-align: left; background: #f8f9fa; padding: 10px; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; }
        .badge-success { background: #e8f5e9; color: #2e7d32; }
        .badge-danger { background: #ffebee; color: #c62828; }
        .badge-secondary { background: #f5f5f5; color: #666; }
    </style>
</head>
<body>

<div class="mobile-card">
    <h2><i class="fas fa-edit"></i> Form Pengajuan Izin</h2>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Jenis Izin</label>
            <select name="jenis_izin" class="form-control" required>
                <option value="Sakit">Sakit</option>
                <option value="Cuti">Cuti</option>
                <option value="Izin">Izin Keperluan Lain</option>
            </select>
        </div>
        <div class="form-group">
            <label>Tanggal Mulai</label>
            <input type="date" name="tanggal_mulai" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Tanggal Selesai</label>
            <input type="date" name="tanggal_selesai" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3" required placeholder="Jelaskan alasan izin/cuti..."></textarea>
        </div>
        <div class="form-group">
            <label>Bukti Foto (Opsional)</label>
            <input type="file" name="bukti_foto" class="form-control" style="padding: 10px;">
        </div>
        
        <button type="submit" name="submit_izin" class="btn-submit">Kirim Pengajuan</button>
        <a href="karyawan_dashboard.php" class="btn-back">Kembali ke Dashboard</a>
    </form>
</div>

<div class="mobile-card">
    <h2><i class="fas fa-history"></i> Riwayat Pengajuan</h2>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Tgl</th>
                    <th>Jenis</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $q_izin = mysqli_query($kon, "SELECT * FROM izin WHERE karyawan_id='$karyawan_id' ORDER BY id DESC LIMIT 10");
                if (mysqli_num_rows($q_izin) > 0) {
                    while ($d = mysqli_fetch_array($q_izin)) {
                        $status_badge = 'badge-secondary';
                        if($d['status'] == 'Disetujui') $status_badge = 'badge-success';
                        if($d['status'] == 'Ditolak') $status_badge = 'badge-danger';
                        
                        echo "<tr>
                            <td>".date('d/m', strtotime($d['tanggal_mulai']))."</td>
                            <td>{$d['jenis_izin']}</td>
                            <td><span class='badge $status_badge'>{$d['status']}</span></td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align:center; color:#999;'>Belum ada riwayat.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
