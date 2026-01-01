<?php
include 'koneksi.php';
include 'layout_header.php';

// Handle Bulk Insert
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bulk_assign'])) {
    $shift_id = $_POST['shift_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_akhir = $_POST['tanggal_akhir'];
    $karyawan_ids = isset($_POST['karyawan_ids']) ? $_POST['karyawan_ids'] : [];
    
    // Validasi basic
    if (empty($karyawan_ids)) {
        echo "<script>alert('Pilih minimal satu karyawan!');</script>";
    } else {
        $start = new DateTime($tanggal_mulai);
        $end = new DateTime($tanggal_akhir);
        $end->modify('+1 day'); // Include end date
        
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($start, $interval, $end);
        
        $count_success = 0;
        
        foreach ($karyawan_ids as $kid) {
            foreach ($period as $dt) {
                $tgl = $dt->format("Y-m-d");
                // Hapus jadwal lama di tanggal tsb utk user tsb
                mysqli_query($kon, "DELETE FROM jadwal_kerja WHERE karyawan_id='$kid' AND tanggal='$tgl'");
                // Insert jadwal baru
                $insert = mysqli_query($kon, "INSERT INTO jadwal_kerja (karyawan_id, shift_id, tanggal) VALUES ('$kid', '$shift_id', '$tgl')");
                if ($insert) $count_success++;
            }
        }
        
        echo "<script>alert('Berhasil membuat $count_success entri jadwal!'); window.location='jadwal_bulk.php';</script>";
    }
}
?>

<h1 class="page-title">Input Jadwal Massal</h1>

<div class="card">
    <form method="POST">
        <div class="row" style="display:flex; gap:20px; flex-wrap:wrap;">
            <!-- Kolom Kiri: Opsi Jadwal -->
            <div style="flex:1; min-width: 300px;">
                <h3 style="margin-top:0;">1. Atur Periode & Shift</h3>
                <div class="form-group" style="margin-bottom:15px;">
                    <label>Pilih Shift</label>
                    <select name="shift_id" class="form-control" required style="width:100%; padding:10px; border-radius:5px; border:1px solid #ddd;">
                        <?php
                        $qs = mysqli_query($kon, "SELECT * FROM shifts ORDER BY nama_shift ASC");
                        while ($s = mysqli_fetch_array($qs)) {
                            echo "<option value='" . $s['id'] . "'>" . $s['nama_shift'] . " (" . $s['jam_masuk'] . "-" . $s['jam_pulang'] . ")</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom:15px;">
                    <label>Dari Tanggal</label>
                    <input type="date" name="tanggal_mulai" class="form-control" required value="<?php echo date('Y-m-01'); ?>" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ddd;">
                </div>
                
                <div class="form-group" style="margin-bottom:15px;">
                    <label>Sampai Tanggal</label>
                    <input type="date" name="tanggal_akhir" class="form-control" required value="<?php echo date('Y-m-t'); ?>" style="width:100%; padding:10px; border-radius:5px; border:1px solid #ddd;">
                </div>
                
                <div class="alert" style="background:#e3f2fd; color:#0d47a1; padding:15px; border-radius:5px;">
                    <i class="fas fa-info-circle"></i> Info: <br>
                    Jadwal yang dipilih akan <b>menimpa (overwrite)</b> jadwal lama karyawan yang dipilih pada rentang tanggal tersebut.
                </div>
            </div>

            <!-- Kolom Kanan: Pilih Karyawan -->
            <div style="flex:2; min-width: 300px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h3 style="margin-top:0;">2. Pilih Karyawan</h3>
                    <div>
                        <button type="button" class="btn btn-primary" onclick="toggleSelect(true)" style="padding:5px 10px; font-size:0.8rem;">Pilih Semua</button>
                        <button type="button" class="btn btn-danger" onclick="toggleSelect(false)" style="padding:5px 10px; font-size:0.8rem;">Reset</button>
                    </div>
                </div>
                
                <div style="max-height: 400px; overflow-y: auto; border:1px solid #eee; padding:10px; border-radius:5px;">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="30"><input type="checkbox" id="masterCheck" onchange="toggleMaster(this)"></th>
                                <th>Nama Karyawan</th>
                                <th>Jabatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $qk = mysqli_query($kon, "SELECT * FROM karyawan ORDER BY nama ASC");
                            while ($k = mysqli_fetch_array($qk)) {
                            ?>
                            <tr>
                                <td style="text-align:center;">
                                    <input type="checkbox" name="karyawan_ids[]" value="<?php echo $k['id']; ?>" class="karyawan-check">
                                </td>
                                <td><?php echo $k['nama']; ?></td>
                                <td><?php echo $k['jabatan']; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                
                <button type="submit" name="bulk_assign" class="btn btn-success" style="width:100%; margin-top:20px; padding:15px; font-weight:bold; font-size:1.1rem;">
                    <i class="fas fa-save"></i> SIMPAN JADWAL MASSAL
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function toggleSelect(status) {
    const checks = document.querySelectorAll('.karyawan-check');
    checks.forEach(c => c.checked = status);
    document.getElementById('masterCheck').checked = status;
}
function toggleMaster(master) {
    const checks = document.querySelectorAll('.karyawan-check');
    checks.forEach(c => c.checked = master.checked);
}
</script>

<?php include 'layout_footer.php'; ?>
