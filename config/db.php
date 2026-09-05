<?php
// =========================================================
// DATABASE CONNECTION CONFIGURATION (DUAL MODE: MYSQL + SQLITE FALLBACK)
// =========================================================

$db_host = getenv('MYSQLHOST') ?: (getenv('DB_HOST') ?: '127.0.0.1');
$db_name = getenv('MYSQLDATABASE') ?: (getenv('DB_NAME') ?: 'sistem_kerjaya');
$db_user = getenv('MYSQLUSER') ?: (getenv('DB_USER') ?: 'root');
$db_pass = getenv('MYSQLPASSWORD') ?: (getenv('DB_PASS') ?: '');
$db_port = getenv('MYSQLPORT') ?: (getenv('DB_PORT') ?: '3306');

$pdo = null;

// CUBA SAMBUNGAN MYSQL DAHULU
try {
    $pdo = new PDO("mysql:host=$db_host;port=$db_port;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$db_name`");

    // Inisialisasi skrip SQL jika jadual belum wujud
    $table_check = $pdo->query("SHOW TABLES LIKE 'responses'")->rowCount();
    if ($table_check === 0 && file_exists(__DIR__ . '/../database.sql')) {
        $sql_schema = file_get_contents(__DIR__ . '/../database.sql');
        $pdo->exec($sql_schema);
    }

} catch (Exception $e_mysql) {
    
    // JIKA MYSQL TIDAK BERHUBUNG (CONNECTION REFUSED), GUNAKAN SQLITE AUTOMATIK
    try {
        $sqlite_file = __DIR__ . '/../sistem_kerjaya.sqlite';
        $pdo = new PDO("sqlite:" . $sqlite_file, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        // Cipta Jadual SQLite jika baru
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nama TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                role TEXT NOT NULL DEFAULT 'admin',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS responses (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT NOT NULL,
                nama TEXT NOT NULL,
                tahun TEXT NOT NULL,
                kelas TEXT NOT NULL,
                luahan_rasa TEXT NULL,
                riasec_pilihan TEXT NULL,
                komen_status TEXT NOT NULL,
                submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS security_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ip_address TEXT NOT NULL,
                event_type TEXT NOT NULL,
                description TEXT NOT NULL,
                user_agent TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");

        // Seed data akaun asas jika SQLite masih kosong
        $count_user = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        if ($count_user == 0) {
            $pass_super = password_hash('super123', PASSWORD_BCRYPT);
            $pass_admin = password_hash('admin123', PASSWORD_BCRYPT);

            $stmt_u = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt_u->execute(['Guru Besar (Superadmin)', 'superadmin@kerjaya.edu.my', $pass_super, 'superadmin']);
            $stmt_u->execute(['Cikgu Aishah (Kaunselor)', 'admin@kerjaya.edu.my', $pass_admin, 'admin']);

            // Sample responses
            $stmt_r = $pdo->prepare("INSERT INTO responses (email, nama, tahun, kelas, luahan_rasa, riasec_pilihan, komen_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_r->execute(['adam.haris@student.edu.my', 'Adam Haris bin Azman', '6', 'Bestari', 'Saya suka membaiki mainan dan basikal. Cita-cita nak jadi Jurutera!', 'R (Realistik), I (Investigatif)', 'Berpuas hati']);
            $stmt_r->execute(['nur.sara@student.edu.my', 'Nur Sara Damia', '5', 'Amanah', 'Saya suka membantu kawan-kawan yang sedih. Ingin jadi Guru!', 'S (Sosial)', 'Berpuas hati']);
            $stmt_r->execute(['daniel.hakim@student.edu.my', 'Daniel Hakim', '3', 'Efektif', 'Saya rasa kurang berkeyakinan di kelas.', 'R (Realistik)', 'Ingin berjumpa guru bimbingan dan kaunseling']);
        }

    } catch (Exception $e_sqlite) {
        die("<div style='font-family:sans-serif; padding:20px; background:#fee2e2; color:#991b1b; border-radius:12px; margin:20px;'>
            <h2>⚠️ Ralat Sambungan Pangkalan Data</h2>
            <p>Sistem tidak dapat membuka pangkalan data.</p>
            <small>" . htmlspecialchars($e_sqlite->getMessage()) . "</small>
        </div>");
    }
}

// Mulakan sesi jika belum dimulakan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
