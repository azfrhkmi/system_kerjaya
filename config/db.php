<?php
// =========================================================
// DATABASE CONNECTION CONFIGURATION (PDO)
// =========================================================

$db_host = '127.0.0.1';
$db_name = 'sistem_kerjaya';
$db_user = 'root';
$db_pass = '';
$db_port = '3306';

try {
    // Sambungan pertama ke pelayan MySQL
    $pdo = new PDO("mysql:host=$db_host;port=$db_port;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Pastikan pangkalan data wujud
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db_name`");

} catch (PDOException $e) {
    die("<div style='font-family:sans-serif; padding:20px; background:#fee2e2; color:#991b1b; border-radius:12px; margin:20px;'>
        <h2>⚠️ Ralat Sambungan Pangkalan Data</h2>
        <p>Sistem tidak dapat berhubung dengan MySQL (XAMPP). Sila pastikan servis <strong>MySQL</strong> dalam XAMPP Control Panel telah dihidupkan (Started).</p>
        <small>Butiran Ralat: " . htmlspecialchars($e->getMessage()) . "</small>
    </div>");
}

// Mulakan sesi jika belum dimulakan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
