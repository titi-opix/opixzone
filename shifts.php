<?php 
include 'koneksi.php';
include 'layout_header.php'; 

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : 'list';
$id = isset($_GET['id']) ? $_GET['id'] : '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nama = $_POST['nama_shift'];
    $kode = $_POST['kode'];
    $masuk = $_POST['jam_masuk'];
    $pulang = $_POST['jam_pulang'];
    $id_edit = $_POST['id'];
    
    if($id_edit){
        mysqli_query($kon, "UPDATE shifts SET nama_shift='$nama', kode='$kode', jam_masuk='$masuk', jam_pulang='$pulang' WHERE id='$id_edit'");
        echo "<script>window.location='shifts.php';</script>";
    } else {
        mysqli_query($kon, "INSERT INTO shifts (nama_shift, kode, jam_masuk, jam_pulang) VALUES ('$nama', '$kode', '$masuk', '$pulang')");
        echo "<script>window.location='shifts.php';</script>";
    }
}

if($aksi == 'hapus'){
    mysqli_query($kon, "DELETE FROM shifts WHERE id='$id'");
    echo "<script>window.location='shifts.php';</script>";
}

$edit_nama = "";
$edit_masuk = "";
$edit_pulang = "";
$edit_id = "";
if($aksi == 'edit'){
    $q = mysqli_query($kon, "SELECT * FROM shifts WHERE id='$id'");
    $d = mysqli_fetch_array($q);
    $edit_nama = $d['nama_shift'];
    $edit_masuk = $d['jam_masuk'];
    $edit_pulang = $d['jam_pulang'];
    $edit_id = $d['id'];
}
?>

<h1 class="page-title">Manajemen Shift Kerja</h1>

<div class="card" style="margin-bottom: 20px;">
    <form method="POST" style="display: flex; gap: 10px; align-items: flex-end;">
        <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
        <div style="flex: 0 0 100px;">
            <label>Kode</label>
            <input type="text" name="kode" value="<?php echo isset($d['kode']) ? $d['kode'] : ''; ?>" required class="form-control" placeholder="A/P/L" style="width: 100%; text-align:center;">
        </div>
        <div style="flex: 2;">
            <label>Nama Shift</label>
            <input type="text" name="nama_shift" value="<?php echo $edit_nama; ?>" required class="form-control" placeholder="Contoh: Shift Pagi" style="width: 100%;">
        </div>
        <div style="flex: 1;">
            <label>Jam Masuk (Batas)</label>
            <input type="time" name="jam_masuk" value="<?php echo $edit_masuk; ?>" required class="form-control" style="width: 100%;">
        </div>
        <div style="flex: 1;">
            <label>Jam Pulang</label>
            <input type="time" name="jam_pulang" value="<?php echo $edit_pulang; ?>" required class="form-control" style="width: 100%;">
        </div>
        <button type="submit" class="btn btn-primary"><?php echo $edit_id ? 'Update' : 'Tambah'; ?></button>
         <?php if($edit_id): ?>
            <a href="shifts.php" class="btn" style="background: #ccc;">Batal</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Nama Shift</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = mysqli_query($kon, "SELECT * FROM shifts ORDER BY jam_masuk ASC");
            while($row = mysqli_fetch_array($query)){
            ?>
            <tr>
                <td><?php echo $row['nama_shift']; ?></td>
                <td><?php echo $row['jam_masuk']; ?></td>
                <td><?php echo $row['jam_pulang']; ?></td>
                <td>
                    <a href="shifts.php?aksi=edit&id=<?php echo $row['id']; ?>" class="btn btn-success">Edit</a>
                    <a href="shifts.php?aksi=hapus&id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Hapus shift ini? Jadwal terkait akan ikut terhapus.')">Hapus</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include 'layout_footer.php'; ?>
