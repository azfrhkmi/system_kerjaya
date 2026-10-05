<?php
// =========================================================
// SIKRIP BACKUP & MIGRASI DATA (RAILWAY <-> LOCALHOST)
// =========================================================
ini_set('memory_limit', '512M');
set_time_limit(300);

require_once '../config/db.php';
require_once '../includes/logger.php';

// KAWALAN AKSES KESELAMATAN (PERLU LOG MASUK ADMIN / SUPERADMIN)
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'superadmin'])) {
    header('Location: ../login.php');
    exit;
}

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$message = '';
$message_type = '';

// =========================================================
// 1. EKSPOR BACKUP LENGKAP (.JSON)
// =========================================================
if ($action === 'export') {
    if (ob_get_level()) ob_end_clean();

    $sql = "SELECT * FROM responses ORDER BY id ASC";
    $stmt = $pdo->query($sql);
    $responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql_users = "SELECT id, nama, email, password, role, created_at FROM users";
    $users = $pdo->query($sql_users)->fetchAll(PDO::FETCH_ASSOC);

    $backup_payload = [
        'system' => 'sistem_kerjaya',
        'version' => '1.0',
        'exported_at' => date('Y-m-d H:i:s'),
        'total_responses' => count($responses),
        'users' => $users,
        'responses' => $responses
    ];

    $filename = "backup_sistem_kerjaya_" . date('Y-m-d_His') . ".json";

    header('Content-Type: application/json; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo json_encode($backup_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// =========================================================
// 2. IMPORT BACKUP KE LOCALHOST (.JSON)
// =========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'import') {
    if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['backup_file']['tmp_name'];
        $json_content = file_get_contents($file_tmp);
        $data = json_decode($json_content, true);

        if (!$data || !isset($data['system']) || $data['system'] !== 'sistem_kerjaya') {
            $message = "Fail backup tidak sah atau rosak. Sila pastikan fail .json yang diproses adalah dari sistem ini.";
            $message_type = "danger";
        } else {
            $imported_responses = 0;
            $imported_images = 0;

            // Pastikan folder uploads wujud
            $upload_dir = __DIR__ . '/../uploads/';
            if (!file_exists($upload_dir)) {
                @mkdir($upload_dir, 0777, true);
            }

            if (!empty($data['responses']) && is_array($data['responses'])) {
                foreach ($data['responses'] as $r) {
                    $email = $r['email'] ?? '';
                    $nama = $r['nama'] ?? '';
                    $tahun = $r['tahun'] ?? '';
                    $kelas = $r['kelas'] ?? '';
                    $luahan = $r['luahan_rasa'] ?? '';
                    $riasec = $r['riasec_pilihan'] ?? '';
                    $fail_path = $r['fail_kerjaya'] ?? null;
                    $fail_blob = $r['fail_kerjaya_blob'] ?? null;
                    $komen = $r['komen_status'] ?? 'Belum Dibaca';
                    $submitted_at = $r['submitted_at'] ?? date('Y-m-d H:i:s');

                    // Jika fail_blob wujud tetapi fail fizikal tiada di uploads/, bina semula fail gambar fizikal
                    if (!empty($fail_blob) && !empty($fail_path)) {
                        $phys_file = __DIR__ . '/../' . ltrim($fail_path, '/');
                        if (!file_exists($phys_file)) {
                            $decoded_raw = base64_decode($fail_blob);
                            if ($decoded_raw !== false) {
                                @file_put_contents($phys_file, $decoded_raw);
                                $imported_images++;
                            }
                        }
                    }

                    // Semak jika rekod sudah wujud berdasarkan email & submitted_at
                    $check_stmt = $pdo->prepare("SELECT id FROM responses WHERE email = ? AND (submitted_at = ? OR (nama = ? AND kelas = ?)) LIMIT 1");
                    $check_stmt->execute([$email, $submitted_at, $nama, $kelas]);
                    $existing_id = $check_stmt->fetchColumn();

                    if ($existing_id) {
                        // Kemaskini rekod sedia ada
                        $update_stmt = $pdo->prepare("UPDATE responses SET nama=?, tahun=?, kelas=?, luahan_rasa=?, riasec_pilihan=?, fail_kerjaya=?, fail_kerjaya_blob=?, komen_status=?, submitted_at=? WHERE id=?");
                        $update_stmt->execute([$nama, $tahun, $kelas, $luahan, $riasec, $fail_path, $fail_blob, $komen, $submitted_at, $existing_id]);
                    } else {
                        // Masukkan rekod baru
                        $insert_stmt = $pdo->prepare("INSERT INTO responses (email, nama, tahun, kelas, luahan_rasa, riasec_pilihan, fail_kerjaya, fail_kerjaya_blob, komen_status, submitted_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $insert_stmt->execute([$email, $nama, $tahun, $kelas, $luahan, $riasec, $fail_path, $fail_blob, $komen, $submitted_at]);
                        $imported_responses++;
                    }
                }
            }

            $message = "🎉 Berjaya mengimport {$imported_responses} rekod murid dan membina semula {$imported_images} fail gambar ke pangkalan data tempatan (localhost)!";
            $message_type = "success";
            
            log_security_event($pdo, 'DATA_IMPORT_SUCCESS', "Admin mengimport data backup ke pangkalan data.");
        }
    } else {
        $message = "Sila pilih fail .json backup yang sah untuk dimuat naik.";
        $message_type = "danger";
    }
}

$page_title = "Pindahan & Backup Data (Railway -> Localhost)";
require_once '../includes/header.php';
?>

<div class="container" style="margin-top:30px; margin-bottom:50px; max-width:900px;">
    <div style="background:#fff; border-radius:16px; padding:30px; box-shadow:0 10px 30px rgba(0,0,0,0.08); border:1px solid #e2e8f0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; border-bottom:2px solid #f1f5f9; padding-bottom:16px;">
            <div>
                <h1 style="font-size:1.6rem; color:#0f172a; margin:0 0 6px 0; font-weight:800;">
                    📦 Pindahan & Backup Data Murid
                </h1>
                <p style="color:#64748b; margin:0; font-size:0.92rem;">
                    Alat migrasi automatik untuk memindahkan rekod & gambar murid dari Railway ke Localhost.
                </p>
            </div>
            <a href="dashboard.php" class="btn-outline nav-btn" style="padding:8px 16px;">
                ⬅️ Kembali ke Dashboard
            </a>
        </div>

        <?php if (!empty($message)): ?>
            <div style="padding:14px 18px; border-radius:10px; margin-bottom:24px; font-weight:600; <?php echo ($message_type === 'success') ? 'background:#dcfce7; color:#166534; border:1px solid #bbf7d0;' : 'background:#fee2e2; color:#991b1b; border:1px solid #fecaca;'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            
            <!-- LANGKAH 1: EKSPOR BACKUP DARI RAILWAY -->
            <div style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:12px; padding:20px;">
                <h3 style="color:#1e3a8a; margin-top:0; display:flex; align-items:center; gap:8px;">
                    1️⃣ Eksport Data (Di Railway)
                </h3>
                <p style="color:#475569; font-size:0.88rem; line-height:1.5;">
                    Muat turun pakej backup penuh yang mengandungi semua pangkalan data murid, soalan, dan fail gambar Base64.
                </p>
                <a href="backup.php?action=export" class="btn-primary" style="display:inline-block; text-align:center; width:100%; padding:12px; background:linear-gradient(135deg, #2563eb, #1d4ed8); text-decoration:none; font-weight:700; margin-top:10px; border-radius:8px;">
                    ⬇️ Muat Turun Backup (.json)
                </a>
            </div>

            <!-- LANGKAH 2: IMPORT BACKUP KE LOCALHOST -->
            <div style="background:#f0fdf4; border:1px solid #a7f3d0; border-radius:12px; padding:20px;">
                <h3 style="color:#065f46; margin-top:0; display:flex; align-items:center; gap:8px;">
                    2️⃣ Import Data (Di Localhost)
                </h3>
                <p style="color:#334155; font-size:0.88rem; line-height:1.5;">
                    Pilih fail <code>.json</code> yang di-download tadi dan tekan Import untuk masukkan semua rekod & gambar ke Localhost.
                </p>
                
                <form method="POST" action="backup.php" enctype="multipart/form-data" style="margin-top:10px;">
                    <input type="hidden" name="action" value="import">
                    <input type="file" name="backup_file" accept=".json" required style="width:100%; margin-bottom:12px; font-size:0.85rem; padding:6px; background:#fff; border:1px solid #cbd5e1; border-radius:8px;">
                    <button type="submit" class="btn-primary" style="width:100%; padding:12px; background:linear-gradient(135deg, #10b981, #047857); border:none; font-weight:700; cursor:pointer; border-radius:8px;">
                        📤 Import Ke Localhost
                    </button>
                </form>
            </div>

        </div>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
