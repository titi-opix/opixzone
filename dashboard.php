<?php 
include 'koneksi.php';
include 'layout_header.php'; 

// Get Stats
$tgl = date('Y-m-d');
$total_karyawan = mysqli_num_rows(mysqli_query($kon, "SELECT * FROM karyawan"));
$hadir_hari_ini = mysqli_num_rows(mysqli_query($kon, "SELECT * FROM absensi WHERE tanggal='$tgl' AND status='Hadir'"));
$izin_hari_ini = mysqli_num_rows(mysqli_query($kon, "SELECT * FROM absensi WHERE tanggal='$tgl' AND status='Izin'"));
$terlambat_hari_ini = mysqli_num_rows(mysqli_query($kon, "SELECT * FROM absensi WHERE tanggal='$tgl' AND status_kehadiran='Terlambat'"));
?>

<div class="page-header d-flex justify-between align-center" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="margin-bottom: 0;">Dashboard Overview</h1>
    <!-- Toggle placeholder if needed, for now just empty or verify responsiveness -->
</div>

<div class="grid-4">
    <div class="card stat-card">
        <div class="stat-info">
            <p>Total Karyawan</p>
            <h3><?php echo $total_karyawan; ?></h3>
        </div>
        <div class="stat-icon bg-blue">
            <i class="fas fa-users"></i>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-info">
            <p>Hadir Hari Ini</p>
            <h3><?php echo $hadir_hari_ini; ?></h3>
        </div>
        <div class="stat-icon bg-green">
            <i class="fas fa-check-circle"></i>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-info">
            <p>Terlambat</p>
            <h3><?php echo $terlambat_hari_ini; ?></h3>
        </div>
        <div class="stat-icon bg-orange">
            <i class="fas fa-clock"></i>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-info">
            <p>Izin / Sakit</p>
            <h3><?php echo $izin_hari_ini; ?></h3>
        </div>
        <div class="stat-icon bg-red">
            <i class="fas fa-envelope-open-text"></i>
        </div>
    </div>
</div>

<div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
    <!-- Chart Section -->
    <div class="card">
        <h3 style="font-size: 1.1rem; margin-bottom: 1.5rem; color: var(--dark);">Statistik Kehadiran Hari Ini</h3>
        <div style="position: relative; height: 250px; display: flex; justify-content: center;">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="card">
        <div class="d-flex justify-between align-center" style="margin-bottom: 1rem;">
            <h3 style="font-size: 1.1rem; color: var(--dark);">Aktivitas Terkini</h3>
            <a href="absensi.php" style="font-size: 0.85rem; text-decoration: none; color: var(--primary);">Lihat Semua</a>
        </div>
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Nama</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = mysqli_query($kon, "SELECT absensi.*, karyawan.nama FROM absensi 
                                             JOIN karyawan ON absensi.karyawan_id = karyawan.id 
                                             ORDER BY absensi.id DESC LIMIT 5");
                if(mysqli_num_rows($query) > 0){
                    while($row = mysqli_fetch_array($query)){
                        $badge_class = '';
                        $status_text = $row['status'];
                        
                        if($row['status'] == 'Hadir') {
                            $badge_class = 'badge-success';
                            if($row['status_kehadiran'] == 'Terlambat') {
                                $status_text .= ' (Telat)';
                                $badge_class = 'badge-warning';
                            }
                        } else {
                            $badge_class = 'badge-danger'; // Izin/Sakit
                        }
                        ?>
                        <tr>
                            <td style="font-weight: 500;"><?php echo date('H:i', strtotime($row['jam_masuk'])); ?></td>
                            <td>
                                <div style="font-weight: 500;"><?php echo $row['nama']; ?></div>
                                <div style="font-size: 0.8rem; color: #94a3b8;"><?php echo $row['status_kehadiran']; ?></div>
                            </td>
                            <td>
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align:center; padding: 2rem; color: #94a3b8;'>Belum ada data absensi hari ini</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Izin', 'Terlambat', 'Belum Absen'],
            datasets: [{
                data: [
                    <?php echo $hadir_hari_ini; ?>, 
                    <?php echo $izin_hari_ini; ?>, 
                    <?php echo $terlambat_hari_ini; ?>, 
                    <?php echo max(0, $total_karyawan - ($hadir_hari_ini + $izin_hari_ini)); ?>
                ],
                backgroundColor: [
                    '#22c55e', // Green
                    '#3b82f6', // Blue
                    '#f59e0b', // Orange (Terlambat)
                    '#e2e8f0'  // Grey
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            family: "'Inter', sans-serif",
                            size: 11
                        }
                    }
                }
            }
        }
    });
</script>

<?php include 'layout_footer.php'; ?>
