<?php
$page_title = "Muat Turun Fail Kerjaya";
require_once 'config/db.php';
require_once 'includes/logger.php';

// Ambil ID rekod atau laluan fail dari param URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$file_param = isset($_GET['file']) ? trim($_GET['file']) : '';

$file_relative_path = null;
$file_blob_data = null;
$student_nama = "Murid";

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM responses WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        if ($res) {
            $student_nama = $res['nama'] ?? 'Murid';
            $file_relative_path = $res['fail_kerjaya'] ?? null;
            $file_blob_data = $res['fail_kerjaya_blob'] ?? null;
        }
    } catch (Exception $e_q) {
        $stmt = $pdo->prepare("SELECT nama, fail_kerjaya FROM responses WHERE id = ?");
        $stmt->execute([$id]);
        $res = $stmt->fetch();
        if ($res) {
            $student_nama = $res['nama'] ?? 'Murid';
            $file_relative_path = $res['fail_kerjaya'] ?? null;
        }
    }
} elseif (!empty($file_param)) {
    // Sanitasi laluan fail daripada traversal serangan
    $file_param = str_replace(['..', '\\'], ['', '/'], $file_param);
    $file_relative_path = $file_param;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM responses WHERE fail_kerjaya LIKE ? LIMIT 1");
        $stmt->execute(['%' . basename($file_param)]);
        $res = $stmt->fetch();
        if ($res) {
            $student_nama = $res['nama'] ?? 'Murid';
            $file_blob_data = $res['fail_kerjaya_blob'] ?? null;
        }
    } catch (Exception $e_q2) {
        $stmt = $pdo->prepare("SELECT nama, fail_kerjaya FROM responses WHERE fail_kerjaya LIKE ? LIMIT 1");
        $stmt->execute(['%' . basename($file_param)]);
        $res = $stmt->fetch();
        if ($res) {
            $student_nama = $res['nama'] ?? 'Murid';
        }
    }
}

$full_file_path = $file_relative_path ? __DIR__ . '/' . ltrim($file_relative_path, '/') : null;

// 1. Semak jika fail wujud secara fizikal pada pelayan disk
if ($full_file_path && file_exists($full_file_path) && is_file($full_file_path)) {
    $mime_type = mime_content_type($full_file_path) ?: 'application/octet-stream';
    $file_name = basename($full_file_path);

    // Hantar fail secara terus ke pelayar web
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: inline; filename="' . $file_name . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($full_file_path));
    readfile($full_file_path);
    exit;
}

// 2. JIKA FAIL TIADA DI DISK (KONTENA RAILWAY REDEPLOY), PAPARKAN SANDARAN DARIPADA DATABASE MYSQL (BASE64 BLOB)
if (!empty($file_blob_data)) {
    $raw_bytes = base64_decode($file_blob_data);
    if ($raw_bytes !== false && strlen($raw_bytes) > 0) {
        $file_name = basename($file_relative_path ?: 'fail_kerjaya.png');
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $mime_map = [
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        $mime_type = $mime_map[$ext] ?? 'application/octet-stream';

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: inline; filename="' . $file_name . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($raw_bytes));
        echo $raw_bytes;
        exit;
    }
}

// JIKA FAIL TIDAK DITEMUI (Terpadam semasa Railway Container Redeploy)
require_once 'includes/header.php';
?>

<div class="container" style="padding-top: 60px; padding-bottom: 80px; max-width: 680px;">
    <div style="background:#fff; border-radius:16px; padding:35px; border:2px solid #fecdd3; text-align:center; box-shadow:0 10px 25px rgba(0,0,0,0.05);">
        <div style="font-size:3.5rem; margin-bottom:15px;">📁⚠️</div>
        <h2 style="color:#9f1239; font-size:1.8rem; margin-bottom:10px;">Fail Tidak Ditemui Pada Pelayan</h2>
        <p style="color:#475569; font-size:1.05rem; line-height:1.6; margin-bottom:20px;">
            Maaf, fail kerjaya bagi murid <strong><?php echo htmlspecialchars($student_nama); ?></strong> tidak lagi ditemui pada simpanan pelayan.
        </p>

        <div style="background:#fff1f2; border:1px solid #fda4af; border-radius:12px; padding:18px; text-align:left; font-size:0.92rem; color:#881337; margin-bottom:25px; line-height:1.5;">
            <strong>💡 Mengapa perkara ini berlaku?</strong>
            <ul style="margin-top:8px; margin-bottom:0; padding-left:20px;">
                <li>Pelayan awan (Railway container) telah melakukan <em>redeploy</em> (peluncuran semula) selepas fail ini dimuat naik.</li>
                <li>Penyimpanan fail pada kontena percuma adalah sementara apabila pelayan dibina semula dari GitHub.</li>
            </ul>
        </div>

        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap;">
            <a href="<?php echo (isset($_SESSION['user_role'])) ? 'admin/dashboard.php' : 'index.php'; ?>" class="btn-primary nav-btn" style="text-decoration:none;">
                🏠 Kembali ke Dashboard Admin
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
