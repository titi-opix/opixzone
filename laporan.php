<?php 
include 'koneksi.php';
include 'layout_header.php'; 

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$ruangan_id = isset($_GET['ruangan_id']) ? $_GET['ruangan_id'] : '';

// Helper: Get Days in Month
$num_days = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// 1. Fetch Employees (Filter by Room)
$query_karyawan = "SELECT * FROM karyawan";
if(!empty($ruangan_id)){
    $query_karyawan .= " WHERE ruangan_id='$ruangan_id'";
}
$query_karyawan .= " ORDER BY nama ASC";
$res_karyawan = mysqli_query($kon, $query_karyawan);
$karyawan = [];
while($row = mysqli_fetch_assoc($res_karyawan)){
    $karyawan[] = $row;
}

// 2. Fetch Attendance Data (Optimized)
// Map[karyawan_id][day] = Status Data
$attendance_map = [];

// Filter query for relevant records only
$query_absensi = "SELECT * FROM absensi 
                  WHERE MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'";

// Optimization: If room is selected, we could join with karyawan, but fetching all for the month is usually fine unless massive DB.
// Let's stick to simple fetch and map.
$res_absensi = mysqli_query($kon, $query_absensi);
while($row = mysqli_fetch_assoc($res_absensi)){
    $d = intval(date('d', strtotime($row['tanggal'])));
    $attendance_map[$row['karyawan_id']][$d] = [
        'status' => $row['status'],
        'status_kehadiran' => $row['status_kehadiran']
    ];
}
?>

<style>
/* Simplified Matrix Styles (Similar to schedule.css) */
.report-container {
    overflow-x: auto;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.report-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 0.85rem;
}
.report-table th, .report-table td {
    border: 1px solid #e2e8f0;
    padding: 8px;
    text-align: center;
    vertical-align: middle;
}
.report-table th {
    background: #1e293b;
    color: white;
    font-weight: 600;
}
/* Sticky Names */
.report-table th:nth-child(1),
.report-table td:nth-child(1) {
    position: sticky;
    left: 0;
    z-index: 10;
    background: inherit;
    width: 25px;
}
.report-table th:nth-child(2),
.report-table td:nth-child(2) {
    position: sticky;
    left: 25px; /* Adjust based on col 1 width */
    z-index: 10;
    background: inherit;
    text-align: left;
    min-width: 150px;
}
.report-table th:nth-child(2){ background: #1e293b; }

.report-table tbody tr:nth-child(odd) td:nth-child(1),
.report-table tbody tr:nth-child(odd) td:nth-child(2) { background: #f8fafc; }
.report-table tbody tr:nth-child(even) td:nth-child(2),
.report-table tbody tr:nth-child(even) td:nth-child(1) { background: white; }

/* Status Colors */
.status-v { color: #16a34a; font-weight: bold; background: #dcfce7 !important; -webkit-print-color-adjust: exact; } /* Hadir */
.status-t { color: #ea580c; font-weight: bold; background: #ffedd5 !important; -webkit-print-color-adjust: exact; } /* Terlambat */
.status-i { color: #2563eb; font-weight: bold; background: #dbeafe !important; -webkit-print-color-adjust: exact; } /* Izin */
.status-s { color: #db2777; font-weight: bold; background: #fce7f3 !important; -webkit-print-color-adjust: exact; } /* Sakit */
.status-a { color: #dc2626; font-weight: bold; background: #fee2e2 !important; -webkit-print-color-adjust: exact; } /* Alpha */
.status-l { color: #475569; font-weight: bold; background: #e2e8f0 !important; -webkit-print-color-adjust: exact; } /* Libur */

@media print {
    @page { size: landscape; margin: 5mm; }
    body * { visibility: hidden; }
    .report-container, .report-container * { visibility: visible; }
    
    .report-container { 
        position: absolute; 
        left: 0; 
        top: 30px; 
        width: 100% !important;
        max-width: 100% !important;
        box-shadow: none; 
        border: none; 
        overflow: visible !important; 
    }
    
    .page-title.print-only { 
        visibility: visible; 
        position: absolute; 
        top: 0; 
        left: 0; 
        font-size: 14pt;
    }
    
    .sidebar, .topbar, form, .btn, .card > form { display: none !important; }
    .main-content { margin-left: 0 !important; padding: 0 !important; }
    
    /* Table Optimization for Print */
    .report-table {
        width: 100% !important;
        border-collapse: collapse;
        font-size: 9pt; /* Smaller font */
    }
    
    .report-table th, .report-table td {
        border: 1px solid #000 !important;
        padding: 2px 1px !important; /* Minimal padding */
        height: auto !important;
    }

    /* Disable Sticky for Print (Causes clipping/overflow issues) */
    .report-table th, .report-table td,
    .report-table th:nth-child(1), .report-table td:nth-child(1),
    .report-table th:nth-child(2), .report-table td:nth-child(2) {
        position: static !important;
        background: white !important;
        color: black !important;
    }
    
    /* Column Widths */
    .report-table th:nth-child(1), .report-table td:nth-child(1) { width: 20px; } /* No */
    .report-table th:nth-child(2), .report-table td:nth-child(2) { width: 120px; } /* Nama */
    /* Day cols auto */
}
</style>

<h1 class="page-title print-only">Laporan Absensi: <?php echo date("F", mktime(0, 0, 0, $bulan, 1)) . " " . $tahun; ?></h1>

<div class="card">
    <form method="GET" style="margin-bottom: 20px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <select name="ruangan_id" class="form-control" style="width: auto;">
            <option value="">-- Semua Ruangan --</option>
            <?php
            $qr = mysqli_query($kon, "SELECT * FROM ruangan ORDER BY nama_ruangan ASC");
            while($r = mysqli_fetch_array($qr)){
                $sel = ($r['id'] == $ruangan_id) ? 'selected' : '';
                echo "<option value='".$r['id']."' $sel>".$r['nama_ruangan']."</option>";
            }
            ?>
        </select>

        <select name="bulan" class="form-control" style="width: auto;">
            <?php
            $months = [1=>'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            foreach($months as $k => $v){
                $selected = ($k == $bulan) ? 'selected' : '';
                echo "<option value='$k' $selected>$v</option>";
            }
            ?>
        </select>
        <select name="tahun" class="form-control" style="width: auto;">
            <?php
            for($y=date('Y'); $y>=2020; $y--){
                $selected = ($y == $tahun) ? 'selected' : '';
                echo "<option value='$y' $selected>$y</option>";
            }
            ?>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Lihat</button>
        
        <div style="margin-left: auto; display: flex; gap: 10px;">
            <button type="button" onclick="window.print()" class="btn btn-secondary" style="background: #64748b; color: white;">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="export_excel.php?bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>&ruangan_id=<?php echo $ruangan_id; ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </form>

    <div class="report-container">
        <table class="report-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <?php 
                    for($d=1; $d<=$num_days; $d++){
                        $date = "$tahun-$bulan-" . sprintf("%02d", $d);
                        $dayArg = date('D', strtotime($date));
                        $color = ($dayArg == 'Sun') ? '#ef4444' : 'inherit';
                        echo "<th style='background-color: ".($dayArg=='Sun'?'#b91c1c':'#1e293b')."; min-width: 35px;'>$d<br><small style='font-weight:400'>$dayArg</small></th>";
                    } 
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach($karyawan as $k): 
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $k['nama']; ?></td>
                    <?php 
                    // Optimization: Fetch Schedule Map for the Month
                    $schedule_map_report = [];
                    $q_sched = mysqli_query($kon, "SELECT jadwal_kerja.*, shifts.kode 
                                                   FROM jadwal_kerja 
                                                   LEFT JOIN shifts ON jadwal_kerja.shift_id = shifts.id 
                                                   WHERE MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'");
                    while($sch = mysqli_fetch_assoc($q_sched)){
                         $d_sch = intval(date('d', strtotime($sch['tanggal'])));
                         $schedule_map_report[$sch['karyawan_id']][$d_sch] = $sch['kode'];
                    }

                    for($d=1; $d<=$num_days; $d++){
                        $status_char = '-'; // Default
                        $class = '';
                        
                        // Check if attendance exists
                        if(isset($attendance_map[$k['id']][$d])){
                            $rec = $attendance_map[$k['id']][$d];
                            if($rec['status'] == 'Hadir'){
                                if($rec['status_kehadiran'] == 'Terlambat'){
                                    $status_char = 'T';
                                    $class = 'status-t';
                                } else {
                                    $status_char = 'v';
                                    $class = 'status-v';
                                }
                            } elseif($rec['status'] == 'Izin'){
                                $status_char = 'I';
                                $class = 'status-i';
                            } elseif($rec['status'] == 'Sakit'){
                                $status_char = 'S';
                                $class = 'status-s';
                            }
                        } else {
                            // Check Schedule First!
                            if(isset($schedule_map_report[$k['id']][$d])){
                                $kode_shift = $schedule_map_report[$k['id']][$d];
                                if($kode_shift == 'L'){
                                    $status_char = 'L';
                                    $class = 'status-l';
                                }
                            }

                            // Only check Sunday if NOT Libur (or maybe override? Usually Libur overrides)
                            if($status_char == '-'){
                                $date = "$tahun-$bulan-" . sprintf("%02d", $d);
                                $dayArg = date('D', strtotime($date));
                                if($dayArg == 'Sun'){
                                    $class = 'status-a'; // Just red bg
                                }
                            }
                        }
                        
                        echo "<td class='$class'>$status_char</td>";
                    } 
                    ?>
                </tr>
                <?php endforeach; ?>
                <?php if(count($karyawan) == 0): ?>
                <tr>
                    <td colspan="<?php echo $num_days + 2; ?>" style="padding: 2rem;">Tidak ada data karyawan.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Legend -->
    <div style="margin-top: 20px; display: flex; gap: 15px; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 5px;">
            <span style="width: 20px; height: 20px; display: inline-block; background: #dcfce7; border: 1px solid #16a34a; text-align: center; color: #16a34a; font-weight: bold; line-height: 18px; border-radius: 4px;">v</span>
            <span>Hadir</span>
        </div>
        <div style="display: flex; align-items: center; gap: 5px;">
            <span style="width: 20px; height: 20px; display: inline-block; background: #ffedd5; border: 1px solid #ea580c; text-align: center; color: #ea580c; font-weight: bold; line-height: 18px; border-radius: 4px;">T</span>
            <span>Terlambat</span>
        </div>
        <div style="display: flex; align-items: center; gap: 5px;">
            <span style="width: 20px; height: 20px; display: inline-block; background: #dbeafe; border: 1px solid #2563eb; text-align: center; color: #2563eb; font-weight: bold; line-height: 18px; border-radius: 4px;">I</span>
            <span>Izin</span>
        </div>
         <div style="display: flex; align-items: center; gap: 5px;">
            <span style="width: 20px; height: 20px; display: inline-block; background: #fce7f3; border: 1px solid #db2777; text-align: center; color: #db2777; font-weight: bold; line-height: 18px; border-radius: 4px;">S</span>
            <span>Sakit</span>
        </div>
        <div style="display: flex; align-items: center; gap: 5px;">
            <span style="width: 20px; height: 20px; display: inline-block; background: #e2e8f0; border: 1px solid #475569; text-align: center; color: #475569; font-weight: bold; line-height: 18px; border-radius: 4px;">L</span>
            <span>Libur</span>
        </div>
    </div>
</div>

<?php include 'layout_footer.php'; ?>
