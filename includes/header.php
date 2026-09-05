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
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . " - " : ""; ?>Sistem Penerokaan Kerjaya Cherita</title>
    
    <!-- Meta SEO & Mobile Viewport -->
    <meta name="description" content="Sistem Penerokaan Kerjaya Interaktif Sekolah Rendah berdasarkan Teori Howard Gardner dan RIASEC.">
    <meta name="author" content="Sistem Kerjaya Sekolah Rendah">
    <meta name="theme-color" content="#6366f1">

    <!-- CSS & Fonts -->
    <link rel="stylesheet" href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../assets/css/style.css' : 'assets/css/style.css'; ?>">
    
    <!-- Chart.js CDN (Untuk Dashboard Admin) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <!-- NAVBAR RESPONSIF -->
    <nav class="navbar">
        <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../index.php' : 'index.php'; ?>" class="navbar-brand">
            <span class="logo-icon">🎁</span>
            <span>PETI<span style="color:var(--secondary)">CHERITALAH</span></span>
        </a>

        <div class="nav-links">
            <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../index.php' : 'index.php'; ?>" class="nav-btn btn-outline">
                🏠 Utama
            </a>
            
            <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../soal_jawab.php' : 'soal_jawab.php'; ?>" class="nav-btn btn-primary">
                📝 Soal Jawab
            </a>

            <button type="button" class="nav-btn btn-outline" style="border-color:#f59e0b; color:#b45309;" onclick="openModal('qrCodeModal')">
                📱 Kod QR
            </button>

            <?php if (isset($_SESSION['user_role'])): ?>
                <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? 'dashboard.php' : 'admin/dashboard.php'; ?>" class="nav-btn btn-outline" style="border-color:var(--accent-purple); color:var(--accent-purple)">
                    📊 Dashboard
                </a>
                
                <?php if ($_SESSION['user_role'] === 'superadmin'): ?>
                    <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? 'superadmin.php' : 'admin/superadmin.php'; ?>" class="nav-btn btn-outline" style="border-color:var(--secondary); color:var(--secondary)">
                        👑 Superadmin
                    </a>
                <?php endif; ?>

                <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../logout.php' : 'logout.php'; ?>" class="nav-btn btn-outline" style="border-color:#ef4444; color:#ef4444">
                    🚪 Keluar
                </a>
            <?php else: ?>
                <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../login.php' : 'login.php'; ?>" class="nav-btn btn-outline">
                    🔐 Log Masuk Guru
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- MODAL KOD QR PORTAL -->
    <div id="qrCodeModal" class="modal-backdrop">
        <div class="modal-box" style="text-align:center; max-width:440px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; border-bottom:2px solid #e2e8f0; padding-bottom:10px;">
                <h3 style="font-size:1.4rem; color:#1e1b4b; margin:0;">📱 Kod QR Imbasan Portal</h3>
                <button type="button" onclick="closeModal('qrCodeModal')" style="background:none; border:none; font-size:1.4rem; cursor:pointer;">❌</button>
            </div>
            
            <p style="font-size:0.9rem; color:var(--text-muted); margin-bottom:15px;">
                Imbas Kod QR ini menggunakan kamera telefon pintar anda untuk terus membuka portal <strong>Peti Cheritalah</strong>!
            </p>

            <div style="background:#f8fafc; padding:18px; border-radius:18px; border:2px dashed #cbd5e1; display:inline-block; margin-bottom:15px;">
                <img src="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../assets/images/qr_code.png' : 'assets/images/qr_code.png'; ?>" alt="QR Code Peti Cheritalah" style="width:220px; height:220px; border-radius:10px;">
            </div>

            <div style="font-size:0.82rem; color:#64748b; background:#f1f5f9; padding:8px 12px; border-radius:8px; word-break:break-all; margin-bottom:20px;">
                🔗 <strong>Link Portal:</strong><br>
                <a href="https://systemkerjaya-production.up.railway.app/index.php" target="_blank" style="color:var(--primary); font-weight:700;">https://systemkerjaya-production.up.railway.app/index.php</a>
            </div>

            <div style="display:flex; justify-content:center; gap:10px; flex-wrap:wrap;">
                <a href="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../assets/images/qr_code.png' : 'assets/images/qr_code.png'; ?>" download="Kod_QR_PetiCheritalah.png" class="btn-primary nav-btn" style="text-decoration:none; padding:10px 16px; font-size:0.85rem;">
                    📥 Muat Turun PNG
                </a>
                <button type="button" class="btn-outline nav-btn" onclick="closeModal('qrCodeModal')" style="padding:10px 16px; font-size:0.85rem;">Tutup</button>
            </div>
        </div>
    </div>
