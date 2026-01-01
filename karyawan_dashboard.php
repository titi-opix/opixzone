<?php
session_start();
include 'koneksi.php';
include 'ip_helper.php';

if (!isset($_SESSION['karyawan_id'])) {
    header("Location: login.php");
    exit();
}

$karyawan_id = $_SESSION['karyawan_id'];
$nama = $_SESSION['nama'];
$tanggal = date('Y-m-d');
$current_ip = get_client_ip();

// Ambil info ruangan
$q_karyawan = mysqli_query($kon, "SELECT karyawan.*, ruangan.ip_address as room_ip, ruangan.nama_ruangan 
                                  FROM karyawan 
                                  LEFT JOIN ruangan ON karyawan.ruangan_id = ruangan.id 
                                  WHERE karyawan.id='$karyawan_id'");
$data_karyawan = mysqli_fetch_array($q_karyawan);
$room_ip = $data_karyawan['room_ip'];
$room_name = $data_karyawan['nama_ruangan'];

// Cek IP Match (Subnet)
$is_allowed = is_same_subnet($current_ip, $room_ip);

// Cek status absen hari ini
$query_absen = mysqli_query($kon, "SELECT * FROM absensi WHERE karyawan_id='$karyawan_id' AND tanggal='$tanggal'");
$data_absen = mysqli_fetch_array($query_absen);
$status_absen = 'Belum Absen';

if ($data_absen) {
    if ($data_absen['jam_keluar']) {
        $status_absen = 'Selesai';
    } else {
        $status_absen = 'Sudah Masuk';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Mobile</title>
    
    <!-- PWA Settings -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#4361ee">
    <link rel="apple-touch-icon" href="icon-512.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
          navigator.serviceWorker.register('sw.js').then(function(registration) {
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
          }, function(err) {
            console.log('ServiceWorker registration failed: ', err);
          });
        });
      }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .mobile-card {
            background: white;
            padding: 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            text-align: center;
            max-width: 400px;
            margin: 0 auto;
        }
        .avatar {
            width: 80px; height: 80px;
            background: #4361ee;
            color: white;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1rem;
        }
        h2 { margin: 0; color: #2b2d42; }
        p { color: #6c757d; margin: 5px 0 20px; }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            margin-bottom: 2rem;
        }
        .status-belum { background: #ffebee; color: #c62828; }
        .status-masuk { background: #e3f2fd; color: #1565c0; }
        .status-selesai { background: #e8f5e9; color: #2e7d32; }
        
        .btn-action {
            display: block;
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 1rem;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            margin-bottom: 1rem;
            transition: transform 0.2s;
        }
        .btn-action:active { transform: scale(0.98); }
        .btn-checkin { background: #4361ee; color: white; box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3); }
        .btn-checkout { background: #f72585; color: white; box-shadow: 0 4px 15px rgba(247, 37, 133, 0.3); }
        .btn-disabled { background: #e0e0e0; color: #9e9e9e; cursor: not-allowed; }
        
        .ip-alert {
            background: #fff3e0;
            color: #ef6c00;
            padding: 10px;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="mobile-card">
    <div class="avatar">
        <i class="fas fa-user"></i>
    </div>
    <h2><?php echo $nama; ?></h2>
    <p style="margin-bottom: 5px;">
        <?php echo date('l, d F Y'); ?> <br>
        <span id="clock" style="font-size: 1.5rem; font-weight: bold; color: #4361ee; display:block; margin-top:5px;"></span>
    </p>

    <script>
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').innerText = h + ":" + m + ":" + s + " WIT";
        }
        setInterval(updateClock, 1000);
        
        window.onload = function() {
            updateClock();
        };
    </script>
    <small style="color: #4361ee; display: block; margin-bottom: 20px;">
        <i class="fas fa-map-marker-alt"></i> <?php echo $room_name ? $room_name : 'Belum ada ruangan'; ?>
    </small>
    
    <div class="status-badge <?php echo ($status_absen == 'Belum Absen') ? 'status-belum' : (($status_absen == 'Sudah Masuk') ? 'status-masuk' : 'status-selesai'); ?>">
        <?php echo $status_absen; ?>
    </div>

    <?php if (!$is_allowed): ?>
        <div class="ip-alert">
            <i class="fas fa-wifi"></i> Anda harus terhubung ke WiFi: <b><?php echo $room_name; ?></b><br>
            <small>(IP WiFi: <?php echo $room_ip; ?> | IP Anda: <?php echo $current_ip; ?>)</small>
        </div>
    <?php endif; ?>

    <!-- Form Absen Hidden -->
    <form id="formAbsen" action="karyawan_proses_absen.php" method="POST">
        <input type="hidden" name="aksi" id="aksiInput">
        <input type="hidden" name="foto" id="fotoInput">
    </form>

    <?php if ($status_absen == 'Belum Absen'): ?>
        <?php if ($is_allowed): ?>
            <button onclick="startCamera('checkin')" class="btn-action btn-checkin">
                <i class="fas fa-sign-in-alt"></i> AMBIL FOTO & CHECK IN
            </button>
        <?php else: ?>
            <button class="btn-action btn-disabled" disabled>CHECK IN (WiFi Only)</button>
        <?php endif; ?>
    <?php elseif ($status_absen == 'Sudah Masuk'): ?>
        <?php if ($is_allowed): ?>
            <button onclick="startCamera('checkout')" class="btn-action btn-checkout">
                <i class="fas fa-sign-out-alt"></i> AMBIL FOTO & CHECK OUT
            </button>
        <?php else: ?>
            <button class="btn-action btn-disabled" disabled>CHECK OUT (WiFi Only)</button>
        <?php endif; ?>
    <?php else: ?>
        <div style="color: #2e7d32; font-weight: bold;">
            <i class="fas fa-check-circle"></i> Absensi Hari Ini Selesai
        </div>
    <?php endif; ?>
    
    <a href="karyawan_izin.php" class="btn-action" style="background: #ff9800; color: white; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);">
        <i class="fas fa-envelope"></i> AJUKAN IZIN / CUTI
    </a>

    <a href="karyawan_ganti_password.php" style="display: block; margin-top: 20px; color: #4361ee; text-decoration: none; font-weight: 600;">
        <i class="fas fa-key"></i> Ganti Password
    </a>
    
    <a href="logout.php" style="display: block; margin-top: 15px; color: #999; text-decoration: none;">Logout</a>
</div>

<!-- Modal Camera -->
<div id="cameraModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.9); z-index:9999; flex-direction:column; align-items:center; justify-content:center;">
    <h3 style="color:white; margin-bottom:10px;">Ambil Foto Selfie</h3>
    
    <div id="cameraPreview" style="display:none;">
        <video id="video" width="320" height="240" autoplay style="border-radius:10px; border:2px solid white; transform: scaleX(-1);"></video>
        <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
    </div>

    <!-- Pesan Error / Panduan -->
    <div id="cameraErrorMsg" style="display:none; flex-direction:column; align-items:center; color:white; text-align:center; max-width:90%; padding:20px;">
        <i class="fas fa-exclamation-triangle" style="font-size:3rem; color:#f72585; margin-bottom:10px;"></i>
        <p style="font-size:1.1rem; margin-bottom:5px;"><b>Kamera Tidak Dapat Diakses</b></p>
        <p style="font-size:0.9rem; color:#ccc; margin-bottom:20px;">
            Browser memblokir akses kamera karena koneksi tidak aman (HTTP).<br>
            Untuk menggunakan kamera real-time, gunakan HTTPS atau 'Localhost'.
        </p>
        <button onclick="useFallback()" style="background:#4361ee; color:white; border:none; padding:10px 20px; border-radius:30px; font-size:16px; font-weight:bold; cursor:pointer; margin-bottom:10px;">
            <i class="fas fa-upload"></i> Upload Manual / Kamera HP
        </button>
    </div>
    
    <div style="margin-top:20px;">
        <button onclick="capturePhoto()" id="btnJepret" style="background:#4361ee; color:white; border:none; padding:10px 20px; border-radius:30px; font-size:16px; font-weight:bold; cursor:pointer; margin-right:10px;">
            <i class="fas fa-camera"></i> JEPRET
        </button>
        <button onclick="closeCamera()" style="background:#f72585; color:white; border:none; padding:10px 20px; border-radius:30px; font-size:16px; font-weight:bold; cursor:pointer;">
            TUTUP
        </button>
    </div>
</div>

<script>
    // Update display toggle di script
    // Tambahkan logic untuk hide/show tombol Jepret saat error
</script>

<!-- Hidden File Input for Fallback -->
<input type="file" id="fallbackInput" accept="image/*" capture="user" style="display:none" onchange="processFallbackFile(this)">

<script>
    let currentAksi = '';
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const modal = document.getElementById('cameraModal');
    let stream = null;

    function startCamera(aksi) {
        currentAksi = aksi;
        modal.style.display = 'flex'; // Tampilkan modal dulu
        
        // Reset state
        document.getElementById('cameraErrorMsg').style.display = 'none';
        document.getElementById('cameraPreview').style.display = 'none';
        document.getElementById('btnJepret').style.display = 'none'; // Sembunyikan default
        
        // Cek support browser & HTTPS
        // Browser memblokir getUserMedia di HTTP (kecuali localhost)
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
            .then(function(s) {
                stream = s;
                video.srcObject = s;
                video.play();
                // Show Preview & Button
                document.getElementById('cameraPreview').style.display = 'block';
                document.getElementById('btnJepret').style.display = 'inline-block';
            })
            .catch(function(err) {
                console.warn("Camera access denied or failed: " + err);
                showCameraError();
            });
        } else {
            console.warn("Browser API not supported.");
            showCameraError();
        }
    }

    function showCameraError() {
        // Tampilkan pesan error di dalam modal
        document.getElementById('cameraPreview').style.display = 'none';
        document.getElementById('btnJepret').style.display = 'none';
        document.getElementById('cameraErrorMsg').style.display = 'flex';
    }

    function useFallback() {
        // Trigger file input click (Manual Upload)
        document.getElementById('fallbackInput').click();
    }

    function processFallbackFile(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const dataURL = e.target.result;
                // Set nilai ke form
                document.getElementById('aksiInput').value = currentAksi;
                document.getElementById('fotoInput').value = dataURL;
                // Submit form
                document.getElementById('formAbsen').submit();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function closeCamera() {
        modal.style.display = 'none';
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    }

    function capturePhoto() {
        const context = canvas.getContext('2d');
        // Gambar video ke canvas (flip horizontal agar seperti cermin)
        context.save();
        context.scale(-1, 1);
        context.drawImage(video, -320, 0, 320, 240);
        context.restore();

        const dataURL = canvas.toDataURL('image/png');
        
        // Set nilai ke form
        document.getElementById('aksiInput').value = currentAksi;
        document.getElementById('fotoInput').value = dataURL;
        
        // Submit form
        document.getElementById('formAbsen').submit();
        
        // Stop camera
        closeCamera();
    }
</script>

</body>
</html>
