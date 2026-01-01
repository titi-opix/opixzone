<?php 
include 'koneksi.php';
include 'layout_header.php'; 
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 class="page-title" style="margin: 0;">Data Karyawan</h1>
    <div>
        <a href="import_karyawan.php" class="btn btn-secondary" style="background: #10b981; color: white; margin-right: 10px;"><i class="fas fa-file-csv"></i> Import CSV</a>
        <a href="karyawan_form.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Karyawan</a>
    </div>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Ruangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $query = mysqli_query($kon, "SELECT karyawan.*, ruangan.nama_ruangan FROM karyawan LEFT JOIN ruangan ON karyawan.ruangan_id = ruangan.id ORDER BY karyawan.nama ASC");
            while($row = mysqli_fetch_array($query)){
            ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $row['nik']; ?></td>
                <td><?php echo $row['nama']; ?></td>
                <td><?php echo $row['jabatan']; ?></td>
                <td><?php echo $row['nama_ruangan'] ? $row['nama_ruangan'] : '-'; ?></td>
                <td>
                    <a href="karyawan_form.php?id=<?php echo $row['id']; ?>" class="btn btn-success" style="padding: 5px 10px; font-size: 0.8rem;">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="karyawan_proses.php?aksi=reset_password&id=<?php echo $row['id']; ?>" class="btn btn-warning" style="background: #f59e0b; color: white; padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Yakin ingin mereset password user ini menjadi 123456?')">
                        <i class="fas fa-key"></i> Reset
                    </a>
                    <a href="karyawan_proses.php?aksi=hapus&id=<?php echo $row['id']; ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8rem;" onclick="return confirm('Yakin ingin menghapus data karyawan ini?')">
                        <i class="fas fa-trash"></i> Hapus
                    </a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include 'layout_footer.php'; ?>
