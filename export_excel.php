<?php
include 'koneksi.php';

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Absensi_$bulan-$tahun.xls");
?>

<h3>Laporan Absensi Bulan <?php echo $bulan; ?> Tahun <?php echo $tahun; ?></h3>

<table border="1">
    <tr>
        <th>No</th>
        <th>NIK</th>
        <th>Nama Karyawan</th>
        <th>Jabatan</th>
        <th>Hadir</th>
        <th>Izin</th>
        <th>Terlambat</th>
        <th>Total Kehadiran</th>
    </tr>
    <?php
    $no = 1;
    $query = mysqli_query($kon, "SELECT * FROM karyawan ORDER BY nama ASC");
    while($d = mysqli_fetch_array($query)){
        $id_k = $d['id'];
        
        $q_hadir = mysqli_query($kon, "SELECT COUNT(*) as tot FROM absensi WHERE karyawan_id='$id_k' AND status='Hadir' AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'");
        $hadir = mysqli_fetch_assoc($q_hadir)['tot'];
        
        $q_izin = mysqli_query($kon, "SELECT COUNT(*) as tot FROM absensi WHERE karyawan_id='$id_k' AND status='Izin' AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'");
        $izin = mysqli_fetch_assoc($q_izin)['tot'];
        
        $q_telat = mysqli_query($kon, "SELECT COUNT(*) as tot FROM absensi WHERE karyawan_id='$id_k' AND status_kehadiran='Terlambat' AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'");
        $telat = mysqli_fetch_assoc($q_telat)['tot'];
        
        $total = $hadir + $izin;
    ?>
    <tr>
        <td><?php echo $no++; ?></td>
        <td><?php echo $d['nik']; ?></td>
        <td><?php echo $d['nama']; ?></td>
        <td><?php echo $d['jabatan']; ?></td>
        <td><?php echo $hadir; ?></td>
        <td><?php echo $izin; ?></td>
        <td><?php echo $telat; ?></td>
        <td><?php echo $total; ?></td>
    </tr>
    <?php } ?>
</table>