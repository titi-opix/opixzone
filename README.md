# Sistem Absensi Mandiri

Aplikasi sederhana untuk pencatatan absensi karyawan berbasis web menggunakan PHP Native dan MySQL.

## Struktur Project

- **index.php**: Halaman utama untuk input absensi (Hadir/Izin).
- **koneksi.php**: Konfigurasi koneksi database.
- **proses.php**: Logic penyimpanan data absensi.
- **rekap.php**: Laporan absensi harian.
- **rekap_bulanan.php**: Laporan rekapitulasi absensi bulanan.
- **import_karyawan.php**: Fitur import data karyawan dari file CSV.
- **export_excel.php**: Fitur export laporan ke Excel.
- **hapus_data.php**: Reset database.

## Database

Aplikasi menggunakan database `db_absensi` dengan tabel:
1. **karyawan**: `id`, `nik`, `nama`, `jabatan`
2. **absensi**: `id`, `karyawan_id`, `tanggal`, `jam_masuk`, `status`

## Cara Penggunaan

1. **Import Data Karyawan**:
   - Siapkan file CSV dengan format: `NIK, Nama, Jabatan`.
   - Buka menu "Import Karyawan" dan upload file.
2. **Input Absensi**:
   - Di halaman utama, klik tombol "Hadir" atau "Izin" pada nama karyawan yang sesuai.
3. **Lihat Laporan**:
   - Gunakan menu "Laporan Harian" atau "Rekap Bulanan" untuk melihat data.

## Teknologi

- PHP (Native)
- MySQL (Database)
- HTML/CSS (Frontend sederhana)
