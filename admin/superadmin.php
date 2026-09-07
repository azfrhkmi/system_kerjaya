<?php
$page_title = "Pusat Kawalan Superadmin & Log Keselamatan";
require_once '../config/db.php';
require_once '../includes/logger.php';

// KAWALAN AKSES STRICT: HANYA SUPERADMIN
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'superadmin') {
    log_threat($pdo, 'UNAUTHORIZED_SUPERADMIN_ACCESS', "Percubaan pencerobohan ke panel Superadmin oleh pengguna ID: " . ($_SESSION['user_id'] ?? 'Guest'));
    header('Location: dashboard.php');
    exit;
}

$msg_success = null;
$msg_error = null;

// 1. PROSES TAMBAH ADMIN BARU
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_admin') {
    $nama = sanitize_input($_POST['nama'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = sanitize_input($_POST['role'] ?? 'admin');

    if (empty($nama) || empty($email) || empty($password)) {
        $msg_error = "Sila lengkapkan semua medan pengurusan admin.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg_error = "Format e-mel tidak sah.";
    } else {
        try {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama, $email, $hashed, $role]);

            $msg_success = "Admin baharu ($nama - $email) telah berjaya ditambah! 🎉";
            log_threat($pdo, 'ADMIN_ADDED', "Superadmin {$_SESSION['user_email']} telah menambah admin baharu: $email ($role)");
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                $msg_error = "E-mel ini sudah wujud dalam sistem.";
            } else {
                $msg_error = "Ralat pangkalan data semasa menambah admin.";
            }
        }
    }
}

// 2. PROSES SUNTING PROFIL ADMIN (OLEH SUPERADMIN)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_admin') {
    $target_id = (int)($_POST['target_id'] ?? 0);
    $nama = sanitize_input($_POST['nama'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $role = sanitize_input($_POST['role'] ?? 'admin');
    $new_password = $_POST['new_password'] ?? '';

    if ($target_id <= 0 || empty($nama) || empty($email)) {
        $msg_error = "Sila isi nama dan e-mel yang sah.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg_error = "Format e-mel tidak sah.";
    } else {
        try {
            // Semak e-mel bertindih
            $stmt_check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt_check->execute([$email, $target_id]);
            if ($stmt_check->fetch()) {
                $msg_error = "E-mel ini telah digunakan oleh pentadbir lain.";
            } else {
                if (!empty($new_password)) {
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                    $stmt_up = $pdo->prepare("UPDATE users SET nama = ?, email = ?, role = ?, password = ? WHERE id = ?");
                    $stmt_up->execute([$nama, $email, $role, $hashed_password, $target_id]);
                } else {
                    $stmt_up = $pdo->prepare("UPDATE users SET nama = ?, email = ?, role = ? WHERE id = ?");
                    $stmt_up->execute([$nama, $email, $role, $target_id]);
                }

                // Jika superadmin menyunting akaun kendiri, kemaskini sesi
                if ($target_id === (int)$_SESSION['user_id']) {
                    $_SESSION['user_name'] = $nama;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_role'] = $role;
                }

                $msg_success = "Profil akaun pentadbir ($email) telah berjaya dikemaskinikan!";
                log_threat($pdo, 'ADMIN_UPDATED', "Superadmin {$_SESSION['user_email']} telah mengemas kini profil admin ID #{$target_id} ($email).");
            }
        } catch (PDOException $e) {
            $msg_error = "Ralat pangkalan data semasa menyunting akaun admin.";
            log_threat($pdo, 'DB_ERROR', "Ralat sunting admin: " . $e->getMessage());
        }
    }
}

// 3. PROSES PADAM ADMIN
if (isset($_GET['delete_admin'])) {
    $admin_id_to_delete = (int)$_GET['delete_admin'];

    // Elakkan superadmin daripada memadam akaun sendiri menerusi link ini
    if ($admin_id_to_delete === (int)$_SESSION['user_id']) {
        $msg_error = "Untuk memadam akaun anda sendiri, sila gunakan halaman Profil Saya.";
    } else {
        try {
            // Ambil maklumat admin sebelum dipadam untuk pencatatan log
            $stmt_fetch = $pdo->prepare("SELECT email FROM users WHERE id = ?");
            $stmt_fetch->execute([$admin_id_to_delete]);
            $target_admin = $stmt_fetch->fetch();

            if ($target_admin) {
                $stmt_del = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt_del->execute([$admin_id_to_delete]);

                $msg_success = "Akaun Admin ({$target_admin['email']}) telah berjaya dipadam.";
                log_threat($pdo, 'ADMIN_REMOVED', "Superadmin {$_SESSION['user_email']} telah memadam akaun admin: {$target_admin['email']}");
            }
        } catch (PDOException $e) {
            $msg_error = "Gagal memadam akaun admin.";
        }
    }
}

// AMBIL SENARAI SEMUA ADMIN
$users_list = $pdo->query("SELECT * FROM users ORDER BY role ASC, created_at DESC")->fetchAll();

// AMBIL SENARAI LOG KESELAMATAN & ANCAMAN
$threat_logs = $pdo->query("SELECT * FROM security_logs ORDER BY created_at DESC LIMIT 50")->fetchAll();

require_once '../includes/header.php';
?>

<div class="container admin-container">

    <!-- HEADER SUPERADMIN -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:30px; gap:16px;">
        <div>
            <span style="background:#fce7f3; color:var(--secondary); font-weight:800; padding:6px 16px; border-radius:50px; font-size:0.9rem;">
                👑 Pusat Kawalan Utama Superadmin
            </span>
            <h1 style="font-size:2.4rem; color:#1e1b4b; margin-top:8px;">Pengurusan Admin & Audit Ancaman Keselamatan</h1>
            <p style="color:var(--text-muted);">Akses penuh menguruskan pengguna pentadbir dan memantau keselamatan sistem.</p>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="profile.php" class="nav-btn btn-outline" style="border-color:var(--primary); color:var(--primary);">
                👤 Profil Saya
            </a>
            <a href="dashboard.php" class="nav-btn btn-outline">
                📊 Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- MESEJ NOTIFIKASI -->
    <?php if ($msg_success): ?>
        <div style="background:#dcfce7; border:1px solid #86efac; color:#166534; padding:14px 20px; border-radius:var(--radius-md); font-weight:700; margin-bottom:24px;">
            ✅ <?php echo $msg_success; ?>
        </div>
    <?php endif; ?>

    <?php if ($msg_error): ?>
        <div style="background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; padding:14px 20px; border-radius:var(--radius-md); font-weight:700; margin-bottom:24px;">
            ⚠️ <?php echo $msg_error; ?>
        </div>
    <?php endif; ?>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(400px, 1fr)); gap:24px; margin-bottom:40px;">
        
        <!-- BORANG TAMBAH ADMIN BAHARU -->
        <div class="table-card" style="margin-bottom:0;">
            <h3 style="font-size:1.3rem; color:#1e1b4b; margin-bottom:15px; display:flex; align-items:center; gap:8px;">
                ➕ Tambah Admin / Pentadbir Baharu
            </h3>

            <form action="superadmin.php" method="POST">
                <input type="hidden" name="action" value="add_admin">

                <div class="form-group">
                    <label class="form-label" for="nama">Nama Admin</label>
                    <input type="text" id="nama" name="nama" class="form-control" placeholder="Contoh: Cikgu Hafizah" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">E-mel Rasmi Admin</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="hafizah@kerjaya.edu.my" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Kata Laluan</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Kata laluan selamat..." required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Peranan (Role)</label>
                    <select name="role" id="role" class="form-control">
                        <option value="admin">Admin (Guru Kaunseling)</option>
                        <option value="superadmin">Superadmin (Guru Besar / Pentadbir Utama)</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary nav-btn" style="width:100%; justify-content:center;">
                    ✨ Tambah Pengguna Admin
                </button>
            </form>
        </div>

        <!-- SENARAI PENGGUNA ADMIN WUJUD -->
        <div class="table-card" style="margin-bottom:0;">
            <h3 style="font-size:1.3rem; color:#1e1b4b; margin-bottom:15px; display:flex; align-items:center; gap:8px;">
                👥 Senarai Akaun Admin & Superadmin
            </h3>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Nama & E-mel</th>
                            <th>Peranan</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users_list as $u): ?>
                            <tr>
                                <td>
                                    <strong style="color:#1e1b4b;"><?php echo htmlspecialchars($u['nama']); ?></strong>
                                    <br><small style="color:var(--text-muted);"><?php echo htmlspecialchars($u['email']); ?></small>
                                </td>
                                <td>
                                    <?php if ($u['role'] === 'superadmin'): ?>
                                        <span class="badge badge-danger">👑 Superadmin</span>
                                    <?php else: ?>
                                        <span class="badge badge-info">👨‍🏫 Admin</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div style="display:flex; gap:6px; align-items:center;">
                                        <button type="button" class="btn-outline nav-btn" onclick="openModal('editAdminModal_<?php echo $u['id']; ?>')" style="padding:4px 10px; font-size:0.8rem; border-color:var(--primary); color:var(--primary);">
                                            ✏️ Sunting
                                        </button>

                                        <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
                                            <a href="superadmin.php?delete_admin=<?php echo $u['id']; ?>" class="btn-outline nav-btn" style="padding:4px 10px; font-size:0.8rem; border-color:#ef4444; color:#ef4444;" onclick="return confirm('Adakah anda pasti mahu memadam akaun admin ini?')">
                                                🗑️ Padam
                                            </a>
                                        <?php else: ?>
                                            <span style="font-size:0.75rem; color:#94a3b8;">(Anda)</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- MODAL SUNTING ADMIN (UNTUK SETIAP PENGGUNA) -->
    <?php foreach ($users_list as $u): ?>
        <div id="editAdminModal_<?php echo $u['id']; ?>" class="modal-backdrop">
            <div class="modal-box" style="max-width:500px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; border-bottom:2px solid #e2e8f0; padding-bottom:10px;">
                    <h3 style="font-size:1.3rem; color:#1e1b4b; margin:0;">✏️ Sunting Akaun Pentadbir</h3>
                    <button type="button" onclick="closeModal('editAdminModal_<?php echo $u['id']; ?>')" style="background:none; border:none; font-size:1.4rem; cursor:pointer;">❌</button>
                </div>

                <form action="superadmin.php" method="POST">
                    <input type="hidden" name="action" value="edit_admin">
                    <input type="hidden" name="target_id" value="<?php echo $u['id']; ?>">

                    <div class="form-group">
                        <label class="form-label" for="edit_nama_<?php echo $u['id']; ?>">Nama Admin</label>
                        <input type="text" id="edit_nama_<?php echo $u['id']; ?>" name="nama" class="form-control" value="<?php echo htmlspecialchars($u['nama']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_email_<?php echo $u['id']; ?>">E-mel Admin</label>
                        <input type="email" id="edit_email_<?php echo $u['id']; ?>" name="email" class="form-control" value="<?php echo htmlspecialchars($u['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_role_<?php echo $u['id']; ?>">Peranan (Role)</label>
                        <select name="role" id="edit_role_<?php echo $u['id']; ?>" class="form-control">
                            <option value="admin" <?php echo ($u['role'] === 'admin') ? 'selected' : ''; ?>>Admin (Guru Kaunseling)</option>
                            <option value="superadmin" <?php echo ($u['role'] === 'superadmin') ? 'selected' : ''; ?>>Superadmin (Guru Besar / Pentadbir Utama)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="edit_pass_<?php echo $u['id']; ?>">Kata Laluan Baharu <small style="color:var(--text-muted); font-weight:normal;">(Kosongkan jika kekalkan lama)</small></label>
                        <input type="password" id="edit_pass_<?php echo $u['id']; ?>" name="new_password" class="form-control" placeholder="Kata laluan baharu...">
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                        <button type="button" class="btn-outline nav-btn" onclick="closeModal('editAdminModal_<?php echo $u['id']; ?>')">Batal</button>
                        <button type="submit" class="btn-primary nav-btn">💾 Simpan Kemaskini</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- PETI ANCAMAN & AUDIT LOG KESELAMATAN (SECURITY THREAT LOGS) -->
    <div class="table-card">
        
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
            <div>
                <h3 style="font-size:1.4rem; color:#991b1b; display:flex; align-items:center; gap:8px;">
                    🛡️ Peti Ancaman & Log Audit Keselamatan Sistem
                </h3>
                <p style="color:var(--text-muted); font-size:0.9rem;">Memantau setiap pencerobohan, percubaan log masuk gagal, dan aktiviti ancaman keselamatan secara automatik.</p>
            </div>
            
            <span class="badge badge-danger" style="font-size:0.9rem; padding:8px 16px;">
                🚨 Pemantauan Aktif 24/7
            </span>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Masa & Tarikh</th>
                        <th>Jenis Acara (Event Type)</th>
                        <th>IP Address</th>
                        <th>Butiran Penerangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($threat_logs) > 0): ?>
                        <?php foreach ($threat_logs as $idx => $t): ?>
                            <tr>
                                <td><strong><?php echo $idx + 1; ?></strong></td>
                                <td style="font-size:0.85rem; color:var(--text-muted); white-space:nowrap;">
                                    <?php echo date('d/m/Y h:i:s A', strtotime($t['created_at'])); ?>
                                </td>
                                <td>
                                    <?php 
                                    $evt = $t['event_type'];
                                    if ($evt === 'FAILED_LOGIN') {
                                        echo '<span class="badge badge-danger">🔒 LOGIN GAGAL</span>';
                                    } elseif ($evt === 'SUSPICIOUS_INPUT') {
                                        echo '<span class="badge badge-warning">⚠️ INPUT MENCURIGAKAN</span>';
                                    } elseif ($evt === 'UNAUTHORIZED_ACCESS' || $evt === 'UNAUTHORIZED_SUPERADMIN_ACCESS') {
                                        echo '<span class="badge badge-danger">⛔ PENCEROBOHAN</span>';
                                    } elseif ($evt === 'ADMIN_ADDED' || $evt === 'ADMIN_REMOVED' || $evt === 'ADMIN_UPDATED') {
                                        echo '<span class="badge badge-info">👤 PERUBAHAN ADMIN</span>';
                                    } else {
                                        echo '<span class="badge badge-success">' . htmlspecialchars($evt) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td style="font-family:monospace; font-size:0.9rem; font-weight:700;">
                                    <?php echo htmlspecialchars($t['ip_address']); ?>
                                </td>
                                <td style="font-size:0.92rem; color:#334155;">
                                    <?php echo htmlspecialchars($t['description']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding:25px; color:var(--text-muted);">
                                🛡️ Tiada rekod amaran ancaman keselamatan dikesan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<?php require_once '../includes/footer.php'; ?>
