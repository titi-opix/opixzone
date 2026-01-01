<?php
include 'koneksi.php';
include 'layout_header.php';

// Filter Variables
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$ruangan_id = isset($_GET['ruangan_id']) ? $_GET['ruangan_id'] : '';

// Helper: Get Days in Month
$num_days = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// Helper: Get Shifts
$shifts = [];
$q_shifts = mysqli_query($kon, "SELECT * FROM shifts");
while($s = mysqli_fetch_assoc($q_shifts)){
    $shifts[$s['id']] = $s;
}

// Handle Save
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_schedule'])){
    // Loop through posted data
    // Format: schedule[karyawan_id][tanggal key (d)] = shift_id
    if(isset($_POST['schedule'])){
        foreach($_POST['schedule'] as $kid => $days){
            foreach($days as $day => $sid){
                // Construct Date
                $date_str = "$tahun-$bulan-" . sprintf("%02d", $day);
                
                // Check if different from DB or force update
                // Simple approach: Delete then Insert if value exists
                mysqli_query($kon, "DELETE FROM jadwal_kerja WHERE karyawan_id='$kid' AND tanggal='$date_str'");
                
                if(!empty($sid)){
                    mysqli_query($kon, "INSERT INTO jadwal_kerja (karyawan_id, shift_id, tanggal) VALUES ('$kid', '$sid', '$date_str')");
                }
            }
        }
        echo "<script>alert('Jadwal berhasil disimpan!'); window.location='jadwal_input.php?bulan=$bulan&tahun=$tahun&ruangan_id=$ruangan_id';</script>";
    }
}

// Get Data for Matrix
$where = "";
if($ruangan_id) {
    $where = "WHERE ruangan_id='$ruangan_id'";
}
$karyawan = [];
$qk = mysqli_query($kon, "SELECT * FROM karyawan $where ORDER BY nama ASC");
while($k = mysqli_fetch_assoc($qk)){
    $karyawan[] = $k;
}

// Get Existing Schedule Map
// Map[karyawan_id][day] = shift_id
$schedule_map = [];
$qj = mysqli_query($kon, "SELECT * FROM jadwal_kerja WHERE MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'");
while($j = mysqli_fetch_assoc($qj)){
    $d = intval(date('d', strtotime($j['tanggal'])));
    $schedule_map[$j['karyawan_id']][$d] = $j['shift_id'];
}
?>

<link rel="stylesheet" href="assets/css/schedule.css">

<div class="d-flex justify-between align-center" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="margin: 0;">Input Jadwal Kerja</h1>
    <div style="font-size: 1.1rem; font-weight: 600; color: var(--secondary);">
        Bulan : <?php echo date("F", mktime(0, 0, 0, $bulan, 1)); ?> <?php echo $tahun; ?> | Total : <?php echo count($karyawan); ?> Karyawan
    </div>
</div>

<div class="card">
    <form method="GET" class="d-flex gap-4 align-center" style="margin-bottom: 0;">
        <select name="bulan" class="form-control" onchange="this.form.submit()" style="padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1;">
            <?php
            for($m=1; $m<=12; $m++){
                $sel = ($m == $bulan) ? 'selected' : '';
                echo "<option value='".sprintf("%02d", $m)."' $sel>".date("F", mktime(0, 0, 0, $m, 1))."</option>";
            }
            ?>
        </select>
        <select name="tahun" class="form-control" onchange="this.form.submit()" style="padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1;">
            <?php
            for($y=date('Y')-1; $y<=date('Y')+1; $y++){
                $sel = ($y == $tahun) ? 'selected' : '';
                echo "<option value='$y' $sel>$y</option>";
            }
            ?>
        </select>
        <select name="ruangan_id" class="form-control" onchange="this.form.submit()" style="padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1;">
            <option value="">Semua Ruangan</option>
            <?php
            $qr = mysqli_query($kon, "SELECT * FROM ruangan ORDER BY nama_ruangan ASC");
            while($r = mysqli_fetch_array($qr)){
                $sel = ($r['id'] == $ruangan_id) ? 'selected' : '';
                echo "<option value='".$r['id']."' $sel>".$r['nama_ruangan']."</option>";
            }
            ?>
        </select>
    </form>
</div>

<form method="POST">
    <div class="schedule-matrix-container">
        <table class="schedule-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="min-width: 200px;">Nama</th>
                    <?php 
                    for($d=1; $d<=$num_days; $d++){
                        $date = "$tahun-$bulan-" . sprintf("%02d", $d);
                        $dayArg = date('D', strtotime($date)); // Mon, Tue...
                        $is_libur = ($dayArg == 'Sun'); // Simple Sunday check
                        $class = $is_libur ? 'day-header-libur' : '';
                        echo "<th class='$class' style='min-width: 40px;'>$d<br><small>$dayArg</small></th>";
                    } 
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach($karyawan as $k){ 
                ?>
                <tr>
                    <td style="text-align: center; background: white;"><?php echo $no++; ?></td>
                    <td style="background: white; font-weight: 500; text-align: left; padding-left: 10px;"><?php echo $k['nama']; ?></td>
                    <?php 
                    for($d=1; $d<=$num_days; $d++){
                        $current_sid = isset($schedule_map[$k['id']][$d]) ? $schedule_map[$k['id']][$d] : '';
                        $date = "$tahun-$bulan-" . sprintf("%02d", $d);
                        $dayArg = date('D', strtotime($date));
                        $is_libur = ($dayArg == 'Sun');
                        $bg_class = $is_libur ? 'day-libur' : '';
                    ?>
                    <td class="<?php echo $bg_class; ?>" style="padding: 0; min-width: 45px;">
                        <select name="schedule[<?php echo $k['id']; ?>][<?php echo $d; ?>]" class="shift-select" style="width: 100%; height: 100%; border: none; background: transparent; text-align-last: center;">
                            <option value=""></option>
                            <?php foreach($shifts as $s): ?>
                                <option value="<?php echo $s['id']; ?>" <?php echo ($current_sid == $s['id']) ? 'selected' : ''; ?> title="<?php echo $s['nama_shift']; ?>">
                                    <?php echo $s['kode']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <!-- Legend -->
    <div class="card" style="margin-top: 2rem; background: #fff;">
        <h4>Keterangan Kode Shift:</h4>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <?php foreach($shifts as $s): ?>
            <div style="display: flex; align-items: center; gap: 5px; border: 1px solid #eee; padding: 5px 10px; border-radius: 5px;">
                <span style="font-weight: bold; background: #eee; padding: 2px 6px; border-radius: 4px;"><?php echo $s['kode']; ?></span>
                <span>= <?php echo $s['nama_shift']; ?> (<?php echo $s['jam_masuk']; ?>-<?php echo $s['jam_pulang']; ?>)</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Floating Save Button -->
    <div style="position: fixed; bottom: 30px; right: 30px; z-index: 100;">
        <button type="submit" name="save_schedule" class="btn btn-primary" style="padding: 15px 30px; font-size: 1.1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
            <i class="fas fa-save"></i> SIMPAN PERUBAHAN
        </button>
    </div>
</form>

<?php include 'layout_footer.php'; ?>
