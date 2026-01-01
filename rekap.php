<?php include 'koneksi.php'; ?>
<h2>Laporan Absensi Harian</h2>
<a href="index.php">Kembali ke Input</a>
<table border="1" cellpadding="10" style="margin-top: 20px;">
    <tr>
        <th>Tanggal</th>
        <th>Nama</th>
        <th>Jam Masuk</th>
        <th>Status</th>
    </tr>
    <?php
    $sql = "SELECT absensi.*, karyawan.nama FROM absensi 
            JOIN karyawan ON absensi.karyawan_id = karyawan.id 
            ORDER BY absensi.tanggal DESC";
    $query = mysqli_query($kon, $sql);
    while($r = mysqli_fetch_array($query)){
    ?>
    <tr>
        <td><?php echo $r['tanggal']; ?></td>
        <td><?php echo $r['nama']; ?></td>
        <td><?php echo $r['jam_masuk']; ?></td>
        <td><?php echo $r['status']; ?></td>
    </tr>
    <?php } ?>
</table>