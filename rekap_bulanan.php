<?php 
include 'koneksi.php'; 
include 'header.php'; 

$bulan_pilihan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun_pilihan = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
?>

<h2>Laporan Bulanan: <?php echo $bulan_pilihan . "/" . $tahun_pilihan; ?></h2>

<form method="GET" style="margin-bottom: 20px; background: #eee; padding: 10px;">
    Bulan (1-12): <input type="number" name="bulan" value="<?php echo $bulan_pilihan; ?>" min="1" max="12">
    Tahun: <input type="number" name="tahun" value="<?php echo $tahun_pilihan; ?>">
    <button type="submit">Lihat Laporan</button>
    <a href="export_excel.php?bulan=<?php echo $bulan_pilihan; ?>&tahun=<?php echo $tahun_pilihan; ?>" 
       style="background: green; color: white; padding: 5px; text-decoration: none; border-radius: 3px; float: right;">
       Unduh Excel
    </a>
</form>

<table>
    <tr>
        <th>NIK</th>
        <th>Nama Karyawan</th>
        <th>Total Hadir</th>
        <th>Total Izin</th>
    </tr>
    <?php
    $query = mysqli_query($kon, "SELECT * FROM karyawan ORDER BY nama ASC");
    while($d = mysqli_fetch_array($query)){
        $id_k = $d['id'];
        $h = mysqli_fetch_assoc(mysqli_query($kon, "SELECT COUNT(*) as tot FROM absensi WHERE karyawan_id='$id_k' AND status='Hadir' AND MONTH(tanggal)='$bulan_pilihan' AND YEAR(tanggal)='$tahun_pilihan'"));
        $i = mysqli_fetch_assoc(mysqli_query($kon, "SELECT COUNT(*) as tot FROM absensi WHERE karyawan_id='$id_k' AND status='Izin' AND MONTH(tanggal)='$bulan_pilihan' AND YEAR(tanggal)='$tahun_pilihan'"));
    ?>
    <tr>
        <td><?php echo $d['nik']; ?></td>
        <td><?php echo $d['nama']; ?></td>
        <td><?php echo $h['tot']; ?></td>
        <td><?php echo $i['tot']; ?></td>
    </tr>
    <?php } ?>
</table>