<?php include 'layout_header.php'; ?>

<div class="d-flex justify-between align-center" style="margin-bottom: 2rem;">
    <h1 class="page-title" style="margin: 0;">Import Data Karyawan</h1>
    <a href="karyawan.php" class="btn btn-secondary" style="background: #e2e8f0; color: #475569;"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="row" style="display: flex; gap: 2rem; flex-wrap: wrap;">
    <div class="card" style="flex: 1; min-width: 300px;">
        <h3><i class="fas fa-file-upload"></i> Upload File CSV</h3>
        <p style="color: #64748b; margin-bottom: 1.5rem;">Silakan upload file CSV berisi data karyawan baru. Password default untuk semua karyawan baru adalah <b>123456</b>.</p>
        
        <form action="proses_import.php" method="post" enctype="multipart/form-data">
            <div style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 2rem; text-align: center; margin-bottom: 1.5rem; background: #f8fafc;">
                <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #94a3b8; margin-bottom: 1rem;"></i>
                <br>
                <input type="file" name="file_csv" accept=".csv" required style="width: 100%; max-width: 300px;">
                <p style="margin-top: 10px; font-size: 0.9rem; color: #64748b;">Hanya file .csv yang didukung</p>
            </div>
            
            <button type="submit" name="import" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 1rem;">
                <i class="fas fa-check-circle"></i> Proses Import
            </button>
        </form>
    </div>
    
    <div class="card" style="flex: 1; min-width: 300px;">
        <h3><i class="fas fa-info-circle"></i> Panduan Format CSV</h3>
        <p style="color: #64748b;">Pastikan file CSV Anda memiliki urutan kolom sebagai berikut (tanpa header row):</p>
        
        <table class="table" style="margin-top: 1rem; font-size: 0.9rem;">
            <thead style="background: #f1f5f9;">
                <tr>
                    <th>Kolom 1</th>
                    <th>Kolom 2</th>
                    <th>Kolom 3</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>NIK</td>
                    <td>Nama Lengkap</td>
                    <td>Jabatan</td>
                </tr>
                <tr>
                    <td style="color: #64748b;">Contoh: 1001</td>
                    <td style="color: #64748b;">Budi Santoso</td>
                    <td style="color: #64748b;">Staff IT</td>
                </tr>
                <tr>
                    <td style="color: #64748b;">Contoh: 1002</td>
                    <td style="color: #64748b;">Siti Aminah</td>
                    <td style="color: #64748b;">Perawat</td>
                </tr>
            </tbody>
        </table>
        
        <div style="margin-top: 1.5rem; background: #fff7ed; padding: 1rem; border-radius: 6px; border-left: 4px solid #f97316;">
            <strong>Catatan:</strong>
            <ul style="margin-top: 5px; margin-left: 15px; color: #9a3412; font-size: 0.9rem;">
                <li>Gunakan pemisah koma (,) atau titik koma (;) tergantung setting Excel Anda.</li>
                <li>Pastikan NIK belum terdaftar sebelumnya.</li>
            </ul>
        </div>
    </div>
</div>

<?php include 'layout_footer.php'; ?>