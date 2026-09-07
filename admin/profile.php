<?php
$page_title = "Profil Saya & Tetapan Akaun";
require_once '../config/db.php';
require_once '../includes/logger.php';

// KAWALAN AKSES STRICT: HANYA USER BERDAFTAR (ADMIN / SUPERADMIN)
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'superadmin'])) {
    log_threat($pdo, 'UNAUTHORIZED_ACCESS', "Percubaan akses tanpa kebenaran ke Halaman Profil dari IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown'));
    header('Location: ../login.php');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$msg_success = null;
$msg_error = null;

// PROSES 1: KEMASKINI PROFIL KENDIRI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $nama = sanitize_input($_POST['nama'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    if (empty($nama) || empty($email)) {
        $msg_error = "Sila isi semua medan nama dan e-mel.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg_error = "Format e-mel tidak sah.";
    } else {
        try {
            // Semak e-mel bertindih dengan pengguna lain
            $stmt_check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt_check->execute([$email, $user_id]);
            if ($stmt_check->fetch()) {
                $msg_error = "E-mel ini telah digunakan oleh pentadbir lain.";
            } else {
                if (!empty($new_password)) {
                    if (strlen($new_password) < 6) {
                        $msg_error = "Kata laluan baharu hendaklah sekurang-kurangnya 6 aksara.";
                    } else {
                        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                        $stmt_update = $pdo->prepare("UPDATE users SET nama = ?, email = ?, password = ? WHERE id = ?");
                        $stmt_update->execute([$nama, $email, $hashed_password, $user_id]);
                    }
                } else {
                    $stmt_update = $pdo->prepare("UPDATE users SET nama = ?, email = ? WHERE id = ?");
                    $stmt_update->execute([$nama, $email, $user_id]);
                }

                if (!$msg_error) {
                    // Kemaskini pembolehubah sesi
                    $_SESSION['user_name'] = $nama;
                    $_SESSION['user_email'] = $email;

                    $msg_success = "Maklumat profil anda telah berjaya dikemaskinikan! 🎉";
                    log_threat($pdo, 'PROFILE_UPDATED', "Pengguna ID #{$user_id} ({$email}) telah mengemas kini profil kendiri.");
                }
            }
        } catch (PDOException $e) {
            $msg_error = "Ralat pangkalan data semasa mengemas kini profil.";
            log_threat($pdo, 'DB_ERROR', "Ralat kemaskini profil: " . $e->getMessage());
        }
    }
}

// PROSES 2: PADAM AKAUN KENDIRI (SELF-DELETE)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_self') {
    // Jika pengguna ialah superadmin, pastikan wujud sekurang-kurangnya 1 lagi superadmin
    if ($_SESSION['user_role'] === 'superadmin') {
        $total_superadmins = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'superadmin'")->fetchColumn();
        if ($total_superadmins <= 1) {
            $msg_error = "Dilarang memadam akaun! Anda adalah satu-satunya Superadmin utama dalam sistem ini.";
        }
    }

    if (!$msg_error) {
        try {
            $deleted_email = $_SESSION['user_email'];
            
            $stmt_del = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt_del->execute([$user_id]);

            log_threat($pdo, 'ACCOUNT_SELF_DELETED', "Akaun pentadbir {$deleted_email} (ID #{$user_id}) telah dipadam oleh pengguna sendiri.");

            // Log keluar dan musnahkan sesi
            session_unset();
            session_destroy();

            header('Location: ../login.php?msg=account_deleted');
            exit;
        } catch (PDOException $e) {
            $msg_error = "Gagal memadam akaun anda.";
            log_threat($pdo, 'DB_ERROR', "Ralat padam akaun kendiri: " . $e->getMessage());
        }
    }
}

// AMBIL MAKLUMAT TERKINI DARI DATABASE
$stmt_user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt_user->execute([$user_id]);
$current_user = $stmt_user->fetch();

if (!$current_user) {
    header('Location: ../logout.php');
    exit;
}

require_once '../includes/header.php';
?>

<div class="container admin-container" style="max-width:850px; margin-top:40px; margin-bottom:60px;">

    <!-- HEADER HR PROFIL -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:30px; gap:16px;">
        <div>
            <span style="background:var(--primary-light); color:var(--primary); font-weight:800; padding:6px 16px; border-radius:50px; font-size:0.9rem;">
                👤 Profil & Tetapan Pentadbir
            </span>
            <h1 style="font-size:2.2rem; color:#1e1b4b; margin-top:8px;">Profil Saya</h1>
            <p style="color:var(--text-muted);">Kemaskini maklumat peribadi dan kata laluan akaun anda di sini.</p>
        </div>

        <div style="display:flex; gap:10px;">
            <a href="dashboard.php" class="nav-btn btn-outline">
                📊 Dashboard
            </a>
            <?php if ($_SESSION['user_role'] === 'superadmin'): ?>
                <a href="superadmin.php" class="nav-btn btn-outline" style="border-color:var(--secondary); color:var(--secondary);">
                    👑 Superadmin
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- NOTIFIKASI MESEJ -->
    <?php if ($msg_success): ?>
        <div style="background:#dcfce7; border:2px solid #86efac; color:#166534; border-radius:var(--radius-md); padding:16px 20px; margin-bottom:24px; font-weight:700; display:flex; align-items:center; gap:10px;">
            <span style="font-size:1.4rem;">✅</span>
            <div><?php echo $msg_success; ?></div>
        </div>
    <?php endif; ?>

    <?php if ($msg_error): ?>
        <div style="background:#fee2e2; border:2px solid #fca5a5; color:#991b1b; border-radius:var(--radius-md); padding:16px 20px; margin-bottom:24px; font-weight:700; display:flex; align-items:center; gap:10px;">
            <span style="font-size:1.4rem;">⚠️</span>
            <div><?php echo $msg_error; ?></div>
        </div>
    <?php endif; ?>

    <!-- KAD BORANG SUNGBATAN PROFIL -->
    <div class="table-card" style="margin-bottom:30px;">
        <h3 style="font-size:1.3rem; color:#1e1b4b; margin-bottom:20px; border-bottom:2px solid #f1f5f9; padding-bottom:12px; display:flex; align-items:center; gap:10px;">
            ✏️ Kemaskini Maklumat Profil
        </h3>

        <form action="profile.php" method="POST">
            <input type="hidden" name="action" value="update_profile">

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:20px;">
                <div class="form-group">
                    <label class="form-label" for="nama">Nama Penuh Pentadbir</label>
                    <input type="text" id="nama" name="nama" class="form-control" value="<?php echo htmlspecialchars($current_user['nama']); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">E-mel Pentadbir</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($current_user['email']); ?>" required>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:20px;">
                <div class="form-group">
                    <label class="form-label" for="role_display">Peranan (Role)</label>
                    <input type="text" id="role_display" class="form-control" value="<?php echo strtoupper($current_user['role']); ?>" disabled style="background:#f1f5f9; color:#64748b; font-weight:700;">
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password">Kata Laluan Baharu <small style="color:var(--text-muted); font-weight:normal;">(Biarkan kosong jika tidak mahu tukar)</small></label>
                    <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Kata laluan baharu...">
                </div>
            </div>

            <div style="margin-top:10px; display:flex; justify-content:flex-end;">
                <button type="submit" class="btn-primary nav-btn" style="padding:12px 24px;">
                    💾 Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>

    <!-- DANGER ZONE: PADAM AKAUN KENDIRI -->
    <div class="table-card" style="border:2px dashed #fca5a5; background:#fff5f5;">
        <h3 style="font-size:1.3rem; color:#991b1b; margin-bottom:12px; display:flex; align-items:center; gap:8px;">
            ⚠️ Ruangan Amaran: Padam Akaun Saya
        </h3>
        <p style="color:#7f1d1d; font-size:0.92rem; margin-bottom:18px; line-height:1.6;">
            Apabila anda memadam akaun anda, anda akan secara automatik dilog keluar dan akaun ini tidak lagi boleh digunakan untuk mengakses panel pentadbir.
        </p>

        <button type="button" class="btn-outline nav-btn" onclick="openModal('deleteAccountModal')" style="border-color:#ef4444; color:#ef4444; font-weight:700;">
            🗑️ Padam Akaun Saya Baharu Ini
        </button>
    </div>

</div>

<!-- MODAL PENGESAHAN PADAM AKAUN -->
<div id="deleteAccountModal" class="modal-backdrop">
    <div class="modal-box" style="max-width:460px; text-align:center;">
        <div style="margin-bottom:15px;">
            <span style="font-size:3rem;">🛑</span>
        </div>
        <h3 style="font-size:1.4rem; color:#991b1b; margin-bottom:10px;">Pengesahan Pemadaman Akaun</h3>
        <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:20px; line-height:1.5;">
            Adakah anda benar-benar pasti mahu memadam akaun (<strong><?php echo htmlspecialchars($current_user['email']); ?></strong>)? Tindakan ini adalah muktamad.
        </p>

        <form action="profile.php" method="POST" style="display:flex; justify-content:center; gap:12px;">
            <input type="hidden" name="action" value="delete_self">
            
            <button type="button" class="btn-outline nav-btn" onclick="closeModal('deleteAccountModal')">
                Batal
            </button>
            <button type="submit" class="btn-primary nav-btn" style="background:#ef4444; border-color:#ef4444;">
                Ya, Padam Akaun Saya
            </button>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
