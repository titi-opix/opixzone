<?php
session_start();
if (!isset($_SESSION['karyawan_id'])) {
    header("Location: login.php");
    exit();
}

$pesan = isset($_GET['pesan']) ? $_GET['pesan'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password Karyawan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .mobile-card {
            background: white;
            padding: 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            max-width: 400px;
            margin: 0 auto;
        }
        h2 { margin: 0 0 20px; color: #2b2d42; text-align: center; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #6c757d; font-weight: 500; }
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 1rem;
        }
        
        .btn-submit {
            display: block;
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 1rem;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            background: #4361ee;
            color: white;
            margin-top: 20px;
        }
        .btn-back {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            text-decoration: none;
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .alert-success { background: #e8f5e9; color: #2e7d32; }
        .alert-danger { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>

<div class="mobile-card">
    <h2>Ganti Password</h2>
    
    <?php if($pesan == 'sukses'): ?>
        <div class="alert alert-success">Password berhasil diubah!</div>
    <?php elseif($pesan == 'gagal'): ?>
        <div class="alert alert-danger">Password lama salah!</div>
    <?php elseif($pesan == 'tidakcocok'): ?>
        <div class="alert alert-danger">Konfirmasi password tidak cocok!</div>
    <?php elseif($pesan == 'pendek'): ?>
        <div class="alert alert-danger">Password minimal 6 karakter!</div>
    <?php endif; ?>

    <form action="karyawan_proses_password.php" method="POST">
        <div class="form-group">
            <label>Password Lama</label>
            <input type="password" name="password_lama" required>
        </div>
        
        <div class="form-group">
            <label>Password Baru</label>
            <input type="password" name="password_baru" required minlength="6">
        </div>
        
        <div class="form-group">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="konfirmasi_password" required minlength="6">
        </div>
        
        <button type="submit" class="btn-submit">Simpan Password</button>
    </form>
    
    <a href="karyawan_dashboard.php" class="btn-back">Kembali ke Dashboard</a>
</div>

</body>
</html>
