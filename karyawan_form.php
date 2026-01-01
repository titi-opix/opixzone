<?php 
include 'koneksi.php';
include 'layout_header.php'; 

$id = "";
$nik = "";
$nama = "";
$jabatan = "";
$action = "tambah";
$title = "Tambah Karyawan";

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $query = mysqli_query($kon, "SELECT * FROM karyawan WHERE id='$id'");
    $data = mysqli_fetch_array($query);
    $nik = $data['nik'];
    $nama = $data['nama'];
    $jabatan = $data['jabatan'];
    $action = "edit";
    $title = "Edit Karyawan";
}
?>

<h1 class="page-title"><?php echo $title; ?></h1>

<div class="card" style="max-width: 600px;">
    <form action="karyawan_proses.php" method="POST">
        <input type="hidden" name="aksi" value="<?php echo $action; ?>">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">NIK</label>
            <input type="text" name="nik" value="<?php echo $nik; ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Nama Lengkap</label>
            <input type="text" name="nama" value="<?php echo $nama; ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Jabatan</label>
            <input type="text" name="jabatan" value="<?php echo $jabatan; ?>" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Ruangan Kerja</label>
            <select name="ruangan_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                <option value="">-- Pilih Ruangan --</option>
                <?php
                $q_ruang = mysqli_query($kon, "SELECT * FROM ruangan ORDER BY nama_ruangan ASC");
                while($r = mysqli_fetch_array($q_ruang)){
                    $selected = "";
                    if(isset($data['ruangan_id']) && $data['ruangan_id'] == $r['id']) $selected = "selected";
                    echo "<option value='".$r['id']."' $selected>".$r['nama_ruangan']."</option>";
                }
                ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Simpan Data</button>
        <a href="karyawan.php" class="btn" style="background: #ccc; color: #333;">Batal</a>
    </form>
</div>

<?php include 'layout_footer.php'; ?>
