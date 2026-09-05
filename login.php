<?php
$page_title = "Log Masuk Admin & Superadmin";
require_once 'config/db.php';
require_once 'includes/logger.php';

$login_error = null;

// Jika pengguna sudah log masuk, lencongkan mengikut peranan
if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'superadmin') {
        header('Location: admin/superadmin.php');
        exit;
    } else {
        header('Location: admin/dashboard.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $login_error = "Sila masukkan e-mel dan kata laluan.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Log masuk berjaya
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nama'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                log_threat($pdo, 'ADMIN_LOGIN_SUCCESS', "Pengguna {$user['email']} ({$user['role']}) berjaya log masuk.");

                if ($user['role'] === 'superadmin') {
                    header('Location: admin/superadmin.php');
                } else {
                    header('Location: admin/dashboard.php');
                }
                exit;
            } else {
                $login_error = "E-mel atau kata laluan tidak sah.";
                log_threat($pdo, 'FAILED_LOGIN', "Percubaan log masuk gagal untuk e-mel: $email");
            }
        } catch (PDOException $e) {
            $login_error = "Ralat sistem. Sila cuba sebentar lagi.";
            log_threat($pdo, 'DB_ERROR', "Ralat log masuk SQL: " . $e->getMessage());
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container" style="padding-top:60px; padding-bottom:80px;">
    <div style="max-width:480px; margin:0 auto;">
        
        <div style="background:white; border-radius:var(--radius-lg); padding:40px; box-shadow:var(--shadow-hover); border:2px solid #e0e7ff; text-align:center;">
            
            <div style="font-size:3.5rem; margin-bottom:10px;">🔐</div>
            <h2 style="font-size:2rem; color:#1e1b4b; margin-bottom:8px;">Portal Guru & Admin</h2>
            <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:28px;">
                Sila log masuk untuk mengakses papan pemuka statistik & pengurusan murid.
            </p>

            <?php if ($login_error): ?>
                <div style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; border-radius:var(--radius-md); padding:12px; font-size:0.9rem; font-weight:700; margin-bottom:20px;">
                    ⚠️ <?php echo htmlspecialchars($login_error); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" style="text-align:left;">
                
                <div class="form-group">
                    <label class="form-label" for="email">E-mel Pentadbir</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="admin@kerjaya.edu.my" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Laluan</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-primary nav-btn" style="width:100%; justify-content:center; padding:14px; font-size:1.1rem; margin-top:10px;">
                    🔑 Log Masuk Sekarang
                </button>

            </form>

            

        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
