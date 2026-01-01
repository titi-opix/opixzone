<?php
include 'layout_header.php';
include 'koneksi.php';

// Action: Approve/Reject
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id_izin = $_GET['id'];
    $status_baru = ($_GET['aksi'] == 'terima') ? 'Disetujui' : 'Ditolak';
    
    $update = mysqli_query($kon, "UPDATE izin SET status='$status_baru' WHERE id='$id_izin'");
    if ($update) {
        echo "<script>alert('Izin berhasil $status_baru'); window.location='admin_izin.php';</script>";
    }
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Kelola Izin / Cuti</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengajuan Masuk (Menunggu)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Karyawan</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Keterangan</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT izin.*, karyawan.nama 
                                  FROM izin 
                                  JOIN karyawan ON izin.karyawan_id = karyawan.id 
                                  WHERE izin.status = 'Menunggu' 
                                  ORDER BY tanggal_pengajuan ASC";
                        $result = mysqli_query($kon, $query);
                        
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_array($result)) {
                                $file_link = ($row['bukti_foto']) ? "<a href='{$row['bukti_foto']}' target='_blank'>Lihat</a>" : "-";
                                echo "<tr>
                                    <td>{$row['nama']}</td>
                                    <td>".date('d F Y', strtotime($row['tanggal_mulai']))." s/d ".date('d F Y', strtotime($row['tanggal_selesai']))."</td>
                                    <td>{$row['jenis_izin']}</td>
                                    <td>{$row['keterangan']}</td>
                                    <td>$file_link</td>
                                    <td>
                                        <a href='admin_izin.php?aksi=terima&id={$row['id']}' class='btn btn-success btn-sm' onclick='return confirm(\"Setujui izin ini?\")'>Setujui</a>
                                        <a href='admin_izin.php?aksi=tolak&id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Tolak izin ini?\")'>Tolak</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Tidak ada pengajuan baru.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-secondary">Riwayat Persetujuan (Terakhir 20)</h6>
        </div>
        <div class="card-body">
             <div class="table-responsive">
                <table class="table table-bordered">
                     <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php
                        $query_history = "SELECT izin.*, karyawan.nama 
                                          FROM izin 
                                          JOIN karyawan ON izin.karyawan_id = karyawan.id 
                                          WHERE izin.status != 'Menunggu' 
                                          ORDER BY id DESC LIMIT 20";
                        $res_hist = mysqli_query($kon, $query_history);
                        while ($h = mysqli_fetch_array($res_hist)) {
                             $badge = ($h['status'] == 'Disetujui') ? 'badge-success' : 'badge-danger';
                             echo "<tr>
                                <td>{$h['nama']}</td>
                                <td>".date('d/m/y', strtotime($h['tanggal_mulai']))." - ".date('d/m/y', strtotime($h['tanggal_selesai']))."</td>
                                <td>{$h['jenis_izin']}</td>
                                <td><span class='badge $badge'>{$h['status']}</span></td>
                             </tr>";
                        }
                        ?>
                    </tbody>
                </table>
             </div>
        </div>
    </div>
</div>

<?php include 'layout_footer.php'; ?>
