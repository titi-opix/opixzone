<?php 
include 'koneksi.php';
include 'layout_header.php'; 

$tanggal_hari_ini = date('Y-m-d');
?>

<h1 class="page-title">Absensi Harian (<?php echo date('d-m-Y'); ?>)</h1>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Nama Karyawan</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th>Foto</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = mysqli_query($kon, "SELECT * FROM karyawan ORDER BY nama ASC");
            while($k = mysqli_fetch_array($query)){
                $id_k = $k['id'];
                $cek_absen = mysqli_query($kon, "SELECT * FROM absensi WHERE karyawan_id='$id_k' AND tanggal='$tanggal_hari_ini'");
                $data_absen = mysqli_fetch_array($cek_absen);
                
                $jam_masuk = isset($data_absen['jam_masuk']) ? $data_absen['jam_masuk'] : '-';
                $jam_keluar = isset($data_absen['jam_keluar']) ? $data_absen['jam_keluar'] : '-';
                $status = isset($data_absen['status']) ? $data_absen['status'] : 'Belum Absen';
                $status_kehadiran = isset($data_absen['status_kehadiran']) ? $data_absen['status_kehadiran'] : '';
                
                // Foto paths
                $foto_masuk = isset($data_absen['foto_masuk']) ? $data_absen['foto_masuk'] : '';
                $foto_keluar = isset($data_absen['foto_keluar']) ? $data_absen['foto_keluar'] : '';
            ?>
            <tr>
                <td>
                    <b><?php echo $k['nama']; ?></b><br>
                    <small style="color: #888;"><?php echo $k['jabatan']; ?></small>
                </td>
                <td><?php echo $jam_masuk; ?></td>
                <td><?php echo $jam_keluar; ?></td>
                <td>
                    <?php if($foto_masuk): ?>
                        <a href="<?php echo $foto_masuk; ?>" target="_blank" title="Foto Masuk">
                            <img src="<?php echo $foto_masuk; ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;">
                        </a>
                    <?php endif; ?>
                    
                    <?php if($foto_keluar): ?>
                        <a href="<?php echo $foto_keluar; ?>" target="_blank" title="Foto Keluar">
                            <img src="<?php echo $foto_keluar; ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd; margin-left: 5px;">
                        </a>
                    <?php endif; ?>
                </td>
                </td>
                <td>
                    <?php 
                    if($status == 'Hadir') {
                        echo "<span style='color:green; font-weight:bold;'>Hadir</span>";
                        if($status_kehadiran == 'Terlambat') echo " <span style='color:red; font-size:0.8em;'>(Terlambat)</span>";
                    } elseif($status == 'Izin') {
                        echo "<span style='color:orange; font-weight:bold;'>Izin</span>";
                    } else {
                        echo "<span style='color:grey;'>Belum Absen</span>";
                    }
                    ?>
                </td>
                </td>
                <td>
                    <?php if(mysqli_num_rows($cek_absen) == 0): ?>
                        <a href="proses.php?aksi=checkin&id=<?php echo $k['id']; ?>&status=Hadir" class="btn btn-primary">Check In</a>
                        <a href="proses.php?aksi=checkin&id=<?php echo $k['id']; ?>&status=Izin" class="btn btn-success" style="background:orange;">Izin</a>
                    <?php elseif($data_absen['status'] == 'Hadir' && $data_absen['jam_keluar'] == NULL): ?>
                        <a href="proses.php?aksi=checkout&id=<?php echo $k['id']; ?>" class="btn btn-danger">Check Out</a>
                    <?php else: ?>
                        <span style="color: #aaa;"><i class="fas fa-check"></i> Selesai</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include 'layout_footer.php'; ?>
