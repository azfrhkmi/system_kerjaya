<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . " - " : ""; ?>Sistem Penerokaan Kerjaya Impianku</title>
    
    <!-- Meta SEO -->
    <meta name="description" content="Sistem Penerokaan Kerjaya Interaktif Sekolah Rendah berdasarkan Teori Howard Gardner dan RIASEC.">
    <meta name="author" content="Sistem Kerjaya Sekolah Rendah">

    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../assets/css/style.css' : 'assets/css/style.css'; ?>">
    
    <!-- Chart.js CDN (Untuk Dashboard Admin) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../index.php' : 'index.php'; ?>" class="navbar-brand">
            <span class="logo-icon">🚀</span>
            <span>KERJAYA<span style="color:var(--secondary)">IMPIANKU</span></span>
        </a>

        <div class="nav-links">
            <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../index.php' : 'index.php'; ?>" class="nav-btn btn-outline">
                🏠 Utama
            </a>
            
            <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../soal_jawab.php' : 'soal_jawab.php'; ?>" class="nav-btn btn-primary">
                📝 Soal Jawab Kerjaya
            </a>

            <?php if (isset($_SESSION['user_role'])): ?>
                <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? 'dashboard.php' : 'admin/dashboard.php'; ?>" class="nav-btn btn-outline" style="border-color:var(--accent-purple); color:var(--accent-purple)">
                    📊 Dashboard Admin
                </a>
                
                <?php if ($_SESSION['user_role'] === 'superadmin'): ?>
                    <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? 'superadmin.php' : 'admin/superadmin.php'; ?>" class="nav-btn btn-outline" style="border-color:var(--secondary); color:var(--secondary)">
                        👑 Superadmin Panel
                    </a>
                <?php endif; ?>

                <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../logout.php' : 'logout.php'; ?>" class="nav-btn btn-outline" style="border-color:#ef4444; color:#ef4444">
                    🚪 Log Keluar
                </a>
            <?php else: ?>
                <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../login.php' : 'login.php'; ?>" class="nav-btn btn-outline">
                    🔐 Log Masuk Guru
                </a>
            <?php endif; ?>
        </div>
    </nav>
