<?php 
include 'koneksi.php';
include 'layout_header.php'; 
include 'ip_helper.php';

$current_ip = get_client_ip();
$server_ip = gethostbyname(gethostname());

if(isset($_POST['add_ip'])){
    $ip = $_POST['ip_address'];
    $desc = $_POST['description'];
    mysqli_query($kon, "INSERT INTO allowed_ips (ip_address, description) VALUES ('$ip', '$desc')");
    echo "<script>window.location='ip_settings.php';</script>";
}

if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($kon, "DELETE FROM allowed_ips WHERE id='$id'");
    echo "<script>window.location='ip_settings.php';</script>";
}
?>

<h1 class="page-title">Pengaturan WiFi / IP</h1>

<div class="card" style="margin-bottom: 20px;">
    <?php if($current_ip == '::1' || $current_ip == '127.0.0.1'): ?>
        <div class="alert" style="background: #e0f2f1; color: #00695c; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <i class="fas fa-info-circle"></i> <b>Info detected:</b><br>
            IP Anda saat ini adalah <b><?php echo $current_ip; ?></b> (Localhost). <br>
            Jika Anda ingin mengizinkan akses dari HP/Laptop lain di WiFi yang sama, gunakan IP Server berikut:<br>
            <h2 style="margin: 10px 0;"><?php echo $server_ip; ?></h2>
            (Masukkan IP di atas ke dalam kolom Gateway)
        </div>
    <?php else: ?>
        <p>IP Anda saat ini: <b><?php echo $current_ip; ?></b></p>
    <?php endif; ?>
    <form method="POST" style="display: flex; gap: 10px; margin-top: 10px;">
        <input type="text" name="ip_address" placeholder="Gateway / Network IP (e.g., 192.168.1.1)" required style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; flex: 1;">
        <input type="text" name="description" placeholder="Keterangan (e.g., WiFi Lobby)" required style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; flex: 1;">
        <button type="submit" name="add_ip" class="btn btn-primary">Tambah IP</button>
    </form>
</div>

<div class="card">
    <h3>Daftar IP Diizinkan</h3>
    <table>
        <thead>
            <tr>
                <th>Gateway IP</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = mysqli_query($kon, "SELECT * FROM allowed_ips ORDER BY id DESC");
            while($row = mysqli_fetch_array($query)){
            ?>
            <tr>
                <td><?php echo $row['ip_address']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td>
                    <a href="ip_settings.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Hapus IP ini?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include 'layout_footer.php'; ?>
