<?php 
include 'koneksi.php';
include 'layout_header.php'; 

// Handle Add Schedule
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_schedule'])){
    $karyawan_id = $_POST['karyawan_id'];
    $shift_id = $_POST['shift_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_akhir = $_POST['tanggal_akhir'];
    
    $start = new DateTime($tanggal_mulai);
    $end = new DateTime($tanggal_akhir);
    $end->modify('+1 day'); // Include end date
    
    $interval = DateInterval::createFromDateString('1 day');
    $period = new DatePeriod($start, $interval, $end);
    
    foreach ($period as $dt) {
        $tgl = $dt->format("Y-m-d");
        // Hapus jadwal lama jika ada
        mysqli_query($kon, "DELETE FROM jadwal_kerja WHERE karyawan_id='$karyawan_id' AND tanggal='$tgl'");
        // Insert jadwal baru
        mysqli_query($kon, "INSERT INTO jadwal_kerja (karyawan_id, shift_id, tanggal) VALUES ('$karyawan_id', '$shift_id', '$tgl')");
    }
    
    echo "<script>alert('Jadwal berhasil disimpan!'); window.location='jadwal.php';</script>";
}

// Handle Delete Schedule
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';
if($aksi == 'hapus' && isset($_GET['id'])){
    $id = $_GET['id'];
    mysqli_query($kon, "DELETE FROM jadwal_kerja WHERE id='$id'");
    echo "<script>window.location='jadwal.php';</script>";
}

// Filter View
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
?>

<h1 class="page-title">Jadwal Kerja Karyawan</h1>

<div class="row" style="display:flex; gap:20px;">
    <!-- Form Assign Schedule -->
    <div class="card" style="flex:1;">
        <h3>Atur Jadwal</h3>
        <form method="POST">
            <div class="form-group">
                <label>Karyawan</label>
                <select name="karyawan_id" class="form-control" required style="width:100%;">
                    <?php
                    $qk = mysqli_query($kon, "SELECT * FROM karyawan ORDER BY nama ASC");
                    while($k = mysqli_fetch_array($qk)){
                        echo "<option value='".$k['id']."'>".$k['nama']."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group" style="margin-top:10px;">
                <label>Shift</label>
                <select name="shift_id" class="form-control" required style="width:100%;">
                    <?php
                    $qs = mysqli_query($kon, "SELECT * FROM shifts ORDER BY nama_shift ASC");
                    while($s = mysqli_fetch_array($qs)){
                        echo "<option value='".$s['id']."'>".$s['nama_shift']." (".$s['jam_masuk']."-".$s['jam_pulang'].")</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group" style="margin-top:10px;">
                <label>Dari Tanggal</label>
                <input type="date" name="tanggal_mulai" class="form-control" required style="width:100%;">
            </div>
             <div class="form-group" style="margin-top:10px;">
                <label>Sampai Tanggal</label>
                <input type="date" name="tanggal_akhir" class="form-control" required style="width:100%;">
            </div>
            
            <button type="submit" name="assign_schedule" class="btn btn-primary" style="margin-top:20px; width:100%;">Simpan Jadwal</button>
        </form>
    </div>

    <!-- View Schedule List -->
    <div class="card" style="flex:2;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3>Daftar Jadwal</h3>
            <form method="GET" style="display:flex; gap:10px;">
                <select name="bulan" class="form-control">
                    <?php
                    for($m=1; $m<=12; $m++){
                        $selected = ($m == $filter_bulan) ? 'selected' : '';
                        echo "<option value='".sprintf("%02d", $m)."' $selected>".date("F", mktime(0, 0, 0, $m, 1))."</option>";
                    }
                    ?>
                </select>
                <select name="tahun" class="form-control">
                    <?php
                    $yr = date('Y');
                    for($y=$yr-1; $y<=$yr+1; $y++){
                        $selected = ($y == $filter_tahun) ? 'selected' : '';
                        echo "<option value='$y' $selected>$y</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="btn btn-primary">Lihat</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Karyawan</th>
                    <th>Shift</th>
                    <th>Jam Kerja</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $qj = mysqli_query($kon, "SELECT jadwal_kerja.*, karyawan.nama, shifts.nama_shift, shifts.jam_masuk, shifts.jam_pulang 
                                         FROM jadwal_kerja 
                                         JOIN karyawan ON jadwal_kerja.karyawan_id = karyawan.id 
                                         JOIN shifts ON jadwal_kerja.shift_id = shifts.id 
                                         WHERE MONTH(tanggal)='$filter_bulan' AND YEAR(tanggal)='$filter_tahun'
                                         ORDER BY tanggal DESC, karyawan.nama ASC");
                
                if(mysqli_num_rows($qj) > 0){
                    while($j = mysqli_fetch_array($qj)){
                ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($j['tanggal'])); ?></td>
                    <td><?php echo $j['nama']; ?></td>
                    <td><?php echo $j['nama_shift']; ?></td>
                    <td><?php echo $j['jam_masuk'] . " - " . $j['jam_pulang']; ?></td>
                    <td>
                        <a href="jadwal.php?aksi=hapus&id=<?php echo $j['id']; ?>" class="btn btn-danger" onclick="return confirm('Hapus jadwal ini?')" style="padding:5px 10px; font-size:0.8rem;">Hapus</a>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center;'>Belum ada jadwal bulan ini.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout_footer.php'; ?>
