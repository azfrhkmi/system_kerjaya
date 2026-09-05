<?php
// =========================================================
// DATABASE CONNECTION CONFIGURATION (PDO)
// Menyokong persekitaran tempatan (XAMPP) & Awam (Railway/Cloud)
// =========================================================

$db_host = getenv('MYSQLHOST') ?: (getenv('DB_HOST') ?: '127.0.0.1');
$db_name = getenv('MYSQLDATABASE') ?: (getenv('DB_NAME') ?: 'sistem_kerjaya');
$db_user = getenv('MYSQLUSER') ?: (getenv('DB_USER') ?: 'root');
$db_pass = getenv('MYSQLPASSWORD') ?: (getenv('DB_PASS') ?: '');
$db_port = getenv('MYSQLPORT') ?: (getenv('DB_PORT') ?: '3306');

try {
    // Sambungan PDO ke pangkalan data MySQL
    $pdo = new PDO("mysql:host=$db_host;port=$db_port;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Inisialisasi Pangkalan Data jika belum wujud
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db_name`");

    // Bina jadual automatik jika dalam persekitaran baharu (Railway)
    $table_check = $pdo->query("SHOW TABLES LIKE 'responses'")->rowCount();
    if ($table_check === 0 && file_exists(__DIR__ . '/../database.sql')) {
        $sql_schema = file_get_contents(__DIR__ . '/../database.sql');
        $pdo->exec($sql_schema);
    }

} catch (PDOException $e) {
    die("<div style='font-family:sans-serif; padding:20px; background:#fee2e2; color:#991b1b; border-radius:12px; margin:20px;'>
        <h2>⚠️ Ralat Sambungan Pangkalan Data</h2>
        <p>Sistem tidak dapat berhubung dengan pangkalan data MySQL.</p>
        <small>Butiran Ralat: " . htmlspecialchars($e->getMessage()) . "</small>
    </div>");
}

// Mulakan sesi jika belum dimulakan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
