<?php 
include 'koneksi.php';
include 'layout_header.php'; 

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : 'list';
$id = isset($_GET['id']) ? $_GET['id'] : '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nama = $_POST['nama_ruangan'];
    $ip = $_POST['ip_address'];
    $id_edit = $_POST['id'];
    
    if($id_edit){
        mysqli_query($kon, "UPDATE ruangan SET nama_ruangan='$nama', ip_address='$ip' WHERE id='$id_edit'");
        echo "<script>window.location='ruangan.php';</script>";
    } else {
        mysqli_query($kon, "INSERT INTO ruangan (nama_ruangan, ip_address) VALUES ('$nama', '$ip')");
        echo "<script>window.location='ruangan.php';</script>";
    }
}

if($aksi == 'hapus'){
    mysqli_query($kon, "DELETE FROM ruangan WHERE id='$id'");
    echo "<script>window.location='ruangan.php';</script>";
}

$edit_nama = "";
$edit_ip = "";
$edit_id = "";
if($aksi == 'edit'){
    $q = mysqli_query($kon, "SELECT * FROM ruangan WHERE id='$id'");
    $d = mysqli_fetch_array($q);
    $edit_nama = $d['nama_ruangan'];
    $edit_ip = $d['ip_address'];
    $edit_id = $d['id'];
}
?>

<h1 class="page-title">Manajemen Ruangan & IP</h1>

<div class="card" style="margin-bottom: 20px;">
    <form method="POST" style="display: flex; gap: 10px; align-items: flex-end;">
        <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
        <div style="flex: 1;">
            <label>Nama Ruangan</label>
            <input type="text" name="nama_ruangan" value="<?php echo $edit_nama; ?>" required class="form-control" style="width: 100%;">
        </div>
        <div style="flex: 1;">
            <label>Gateway / Network IP (e.g. 192.168.1.1)</label>
            <input type="text" name="ip_address" value="<?php echo $edit_ip; ?>" required class="form-control" style="width: 100%;">
        </div>
        <button type="submit" class="btn btn-primary"><?php echo $edit_id ? 'Update' : 'Tambah'; ?></button>
        <?php if($edit_id): ?>
            <a href="ruangan.php" class="btn" style="background: #ccc;">Batal</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Nama Ruangan</th>
                <th>Gateway IP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = mysqli_query($kon, "SELECT * FROM ruangan ORDER BY nama_ruangan ASC");
            while($row = mysqli_fetch_array($query)){
            ?>
            <tr>
                <td><?php echo $row['nama_ruangan']; ?></td>
                <td><?php echo $row['ip_address']; ?></td>
                <td>
                    <a href="ruangan.php?aksi=edit&id=<?php echo $row['id']; ?>" class="btn btn-success">Edit</a>
                    <a href="ruangan.php?aksi=hapus&id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Hapus ruangan ini?')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include 'layout_footer.php'; ?>
