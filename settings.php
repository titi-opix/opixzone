<?php 
include 'koneksi.php';
include 'layout_header.php'; 

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $jam_masuk = $_POST['jam_masuk'];
    $jam_pulang = $_POST['jam_pulang'];
    
    mysqli_query($kon, "UPDATE settings SET setting_value='$jam_masuk' WHERE setting_key='jam_masuk'");
    mysqli_query($kon, "UPDATE settings SET setting_value='$jam_pulang' WHERE setting_key='jam_pulang'");
    
    echo "<script>alert('Pengaturan disimpan!'); window.location='settings.php';</script>";
}

$q_masuk = mysqli_query($kon, "SELECT setting_value FROM settings WHERE setting_key='jam_masuk'");
$jam_masuk = mysqli_fetch_array($q_masuk)['setting_value'];

$q_pulang = mysqli_query($kon, "SELECT setting_value FROM settings WHERE setting_key='jam_pulang'");
$jam_pulang = mysqli_fetch_array($q_pulang)['setting_value'];
?>

<h1 class="page-title">Pengaturan Aplikasi</h1>

<div class="card" style="max-width: 500px;">
    <form method="POST">
        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 10px; font-weight: 500;">Jam Masuk (Batas Terlambat)</label>
            <input type="time" name="jam_masuk" value="<?php echo $jam_masuk; ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 10px; font-weight: 500;">Jam Pulang</label>
            <input type="time" name="jam_pulang" value="<?php echo $jam_pulang; ?>" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        
        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
    </form>
</div>

<?php include 'layout_footer.php'; ?>
