<?php include 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi Modern</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5; /* Indigo-600 */
            --primary-light: #818cf8;
            --secondary: #64748b;
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #0f172a;
            --light: #f8fafc;
            --sidebar-bg: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-active: #3b82f6;
            --sidebar-active-bg: #334155;
            --sidebar-width: 260px;
            --header-height: 70px;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; display: flex; color: #334155; }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            color: var(--sidebar-text);
            transition: all 0.3s;
        }
        .brand {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: white;
            border-bottom: 1px solid #334155;
        }
        .brand i { margin-right: 12px; color: var(--success); }
        
        .menu { padding: 1.5rem 1rem; flex: 1; display: flex; flex-direction: column; gap: 5px; }
        .menu-title { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin: 10px 10px 5px; font-weight: 600; }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: 0.2s;
            font-weight: 500;
            border-radius: 8px;
            font-size: 0.95rem;
        }
        .menu-item:hover, .menu-item.active {
            background: var(--sidebar-active-bg);
            color: white;
        }
        .menu-item.active {
            background: #2563eb; 
        }
        .menu-item i { width: 24px; margin-right: 10px; text-align: center; }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Header */
        .topbar {
            height: var(--header-height);
            background: #f1f5f9; 
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
        }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-avatar {
            width: 40px; height: 40px;
            background: #e2e8f0;
            color: var(--secondary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        /* Page Content */
        .content { padding: 1rem 2rem 2rem; }
        .page-title { margin-bottom: 2rem; font-size: 1.8rem; font-weight: 700; color: var(--dark); }
        
        /* Cards */
        .card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: none;
            margin-bottom: 1.5rem;
        }
        
        /* Grid */
        .grid-4 { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; }
        
        /* Stats Card */
        .stat-card { display: flex; align-items: center; justify-content: space-between; }
        .stat-info p { color: #64748b; font-size: 0.9rem; font-weight: 500; margin-bottom: 0.5rem; }
        .stat-info h3 { font-size: 2rem; font-weight: 700; color: var(--dark); margin: 0; }
        
        /* Utility Colors for Icons/Badges */
        .bg-blue { background: #eff6ff; color: #3b82f6; }
        .bg-green { background: #f0fdf4; color: #22c55e; }
        .bg-orange { background: #fff7ed; color: #f97316; }
        .bg-red { background: #fef2f2; color: #ef4444; }
        
        .stat-icon { 
            width: 50px; height: 50px; 
            border-radius: 12px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 1.5rem;
        }
        
        /* Tables */
        table { width: 100%; border-collapse: separate; border-spacing: 0; }
        th { text-align: left; padding: 1rem; color: #64748b; font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; }
        td { padding: 1rem; border-bottom: 1px solid #f1f5f9; color: var(--dark); font-size: 0.95rem; }
        tr:last-child td { border-bottom: none; }
        
        /* Badges */
        .badge { padding: 0.35rem 0.75rem; border-radius: 6px; font-size: 0.8rem; font-weight: 600; display: inline-block; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        
        /* Buttons */
        .btn { padding: 0.6rem 1.2rem; border-radius: 8px; text-decoration: none; display: inline-block; font-size: 0.95rem; border: none; cursor: pointer; transition: 0.2s; font-weight: 500; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-success { background: var(--success); color: white; }
        .btn-sm { padding: 0.4rem 0.8rem; font-size: 0.85rem; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }

        /* Helpers */
        .text-right { text-align: right; }
        .d-flex { display: flex; }
        .align-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .mt-4 { margin-top: 1.5rem; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <i class="fas fa-shapes"></i> AbsensiApp
    </div>
    <div class="menu">
        <div class="menu-title">Main Menu</div>
        <a href="dashboard.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="karyawan.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'karyawan.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Data Karyawan
        </a>
        <a href="admin_izin.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_izin.php' ? 'active' : ''; ?>">
            <i class="fas fa-envelope-open-text"></i> Izin / Sakit
        </a>
        <a href="absensi.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'absensi.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-check"></i> Absensi Harian
        </a>
        
        <div class="menu-title">Manajemen</div>
         <a href="shifts.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'shifts.php' ? 'active' : ''; ?>">
            <i class="fas fa-clock"></i> Shift Kerja
        </a>
        <a href="jadwal_input.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'jadwal_input.php' ? 'active' : ''; ?>">
            <i class="fas fa-calendar-alt"></i> Jadwal Kerja
        </a>
        <a href="ruangan.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'ruangan.php' ? 'active' : ''; ?>">
            <i class="fas fa-door-open"></i> Ruangan
        </a>
        <a href="laporan.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : ''; ?>">
            <i class="fas fa-file-alt"></i> Laporan
        </a>
        
        <div style="margin-top: auto; border-top: 1px solid #334155; padding-top:10px;">
            <a href="settings.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i> Pengaturan
            </a>
            <a href="ip_settings.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'ip_settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-wifi"></i> Kelola WiFi
            </a>
            <a href="logout.php" class="menu-item" style="color: #ef4444;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="topbar">
        <div class="user-info">
             <span style="font-weight: 500; color: #64748b;">Hi, <?php echo $_SESSION['username']; ?></span>
        </div>
        
        <div class="d-flex align-center gap-4">
             <div class="date-display" style="color: #64748b; font-size: 0.9rem; margin-right: 20px;">
                <?php echo date('l, d F Y'); ?> <span id="clock" style="font-weight:600; color: var(--dark); margin-left: 5px;"></span>
            </div>
             <div class="user-avatar">
                <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
            </div>
        </div>
        
        <script>
            function updateClock() {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');
                const s = String(now.getSeconds()).padStart(2, '0');
                document.getElementById('clock').innerText = h + ":" + m + ":" + s + " WIT";
            }
            setInterval(updateClock, 1000);
            updateClock();
        </script>
    </div>
    <div class="content">
