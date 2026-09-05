<?php
$page_title = "Soal Jawab Kerjaya Saya";
require_once 'config/db.php';
require_once 'includes/logger.php';

$success_msg = null;
$error_msg = null;

// PROSES BORANG PENYERAHAN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email'] ?? '');
    $nama = sanitize_input($_POST['nama'] ?? '');
    $tahun = sanitize_input($_POST['tahun'] ?? '');
    $kelas = sanitize_input($_POST['kelas'] ?? '');
    $luahan_rasa = sanitize_input($_POST['luahan_rasa'] ?? '');
    $gardner_array = $_POST['gardner_pilihan'] ?? [];
    $komen_status = sanitize_input($_POST['komen_status'] ?? '');
    $fail_kerjaya_path = null;

    // Sanitasi array Teori Howard Gardner
    if (is_array($gardner_array)) {
        $riasec_pilihan = implode(', ', array_map('sanitize_input', $gardner_array));
    } else {
        $riasec_pilihan = sanitize_input($gardner_array);
    }

    // PROSES MUAT NAIK FAIL KERJAYA (SECTION D)
    if (isset($_FILES['fail_kerjaya']) && $_FILES['fail_kerjaya']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['fail_kerjaya']['tmp_name'];
        $file_name = $_FILES['fail_kerjaya']['name'];
        $file_size = $_FILES['fail_kerjaya']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_exts = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg'];
        
        if (!in_array($file_ext, $allowed_exts)) {
            $error_msg = "Fail yang dimuat naik tidak dibenarkan. Sila guna format (PDF, DOC, DOCX, PNG, JPG, JPEG).";
        } elseif ($file_size > 10 * 1024 * 1024) { // Max 10MB
            $error_msg = "Saiz fail terlalu besar (Maksimum 10MB).";
        } else {
            // Cipta nama fail selamat
            $clean_email = preg_replace('/[^a-zA-Z0-9]/', '_', $email);
            $new_filename = "kerjaya_" . $clean_email . "_" . time() . "." . $file_ext;
            $target_dir = __DIR__ . "/uploads/";
            
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            
            $target_file = $target_dir . $new_filename;
            if (move_uploaded_file($file_tmp, $target_file)) {
                $fail_kerjaya_path = "uploads/" . $new_filename;
            } else {
                $error_msg = "Gagal memuat naik fail. Sila cuba lagi.";
            }
        }
    }

    // Semakan asas keselamatan (Pengesanan skrip berbahaya)
    if (preg_match('/<script>|javascript:|SELECT|UNION|DROP TABLE/i', json_encode($_POST))) {
        log_threat($pdo, 'SUSPICIOUS_INPUT', "Percubaan input berbahaya dikesan dari e-mel: $email");
    }

    // Validasi Medan Wajib
    if (empty($error_msg)) {
        if (empty($email) || empty($nama) || empty($tahun) || empty($kelas) || empty($komen_status)) {
            $error_msg = "Sila lengkapkan semua maklumat yang bertanda wajib (*).";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = "Sila masukkan format e-mel yang sah (contoh: murid@sekolah.edu.my).";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO responses (email, nama, tahun, kelas, luahan_rasa, riasec_pilihan, fail_kerjaya, komen_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$email, $nama, $tahun, $kelas, $luahan_rasa, $riasec_pilihan, $fail_kerjaya_path, $komen_status]);
                
                $success_msg = "Tahniah $nama! Soal jawab kerjaya anda telah berjaya dihantar kepada Guru Bimbingan & Kaunseling. 🎉";
            } catch (PDOException $e) {
                $error_msg = "Ralat semasa menyimpan jawapan. Sila cuba semula.";
                log_threat($pdo, 'DB_ERROR', "Ralat SQL penyerahan borang: " . $e->getMessage());
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container" style="padding-top: 40px; padding-bottom: 60px;">
    
    <!-- HEADER BORANG -->
    <div style="text-align: center; margin-bottom: 40px;">
        <span style="background:#e0e7ff; color:var(--primary); font-weight:800; padding:6px 18px; border-radius:50px; font-size:0.95rem;">
            📝 Sesi Soal Jawab Kerjaya
        </span>
        <h1 style="font-size:2.8rem; color:#1e1b4b; margin-top:10px; margin-bottom:10px;">
            Mari Isi <span class="highlight">Maklumat Kerjaya Anda</span>
        </h1>
        <p style="color:var(--text-muted); font-size:1.1rem; max-width:650px; margin:0 auto;">
            Jawab soalan di bawah dengan jujur untuk membantu kami mengenali minat dan cita-cita anda!
        </p>
    </div>

    <!-- NOTIFIKASI BOLEH DILIHAT -->
    <?php if ($success_msg): ?>
        <div style="background:#dcfce7; border:2px solid #86efac; color:#166534; border-radius:var(--radius-md); padding:20px; text-align:center; max-width:900px; margin:0 auto 30px; font-size:1.1rem; font-weight:700;">
            <?php echo $success_msg; ?>
            <div style="margin-top:15px;">
                <a href="index.php" class="btn-primary nav-btn" style="text-decoration:none;">🏠 Kembali ke Laman Utama</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
        <div style="background:#fee2e2; border:2px solid #fca5a5; color:#991b1b; border-radius:var(--radius-md); padding:20px; text-align:center; max-width:900px; margin:0 auto 30px; font-size:1.05rem; font-weight:700;">
            ⚠️ <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <!-- CARD BORANG UTAMA (MULTIPART FOR FILE UPLOAD) -->
    <div class="form-card">
        <form action="soal_jawab.php" method="POST" enctype="multipart/form-data">
            
            <!-- MASUKKAN EMAIL (PRIMARY KEY) -->
            <div class="form-group">
                <label class="form-label" for="email">
                    📧 Masukkan E-mel Anda <span style="color:#ef4444">*</span>
                    <br><small>(Digunakan sebagai Primary Key pengecam akaun pengguna)</small>
                </label>
                <input type="email" id="email" name="email" class="form-control" placeholder="contoh: adam.haris@student.edu.my" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <hr style="border:0; border-top:2px dashed #e2e8f0; margin:30px 0;">

            <!-- BAHAGIAN A: MAKLUMAT DIRI -->
            <h3 style="font-size:1.4rem; color:var(--primary); margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                👤 (a) Maklumat Diri
            </h3>

            <!-- Nama -->
            <div class="form-group">
                <label class="form-label" for="nama">Nama Penuh Murid <span style="color:#ef4444">*</span></label>
                <input type="text" id="nama" name="nama" class="form-control" placeholder="Masukkan nama penuh anda..." required value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>">
            </div>

            <!-- Tahun (MCA) -->
            <div class="form-group">
                <label class="form-label">Tahun / Peringkat persekolahan (Pilihan)*</label>
                <div class="option-grid">
                    <?php 
                    $tahun_options = ['1', '2', '3', '4', '5', '6', 'PPKI'];
                    $selected_tahun = $_POST['tahun'] ?? '';
                    foreach ($tahun_options as $t): 
                    ?>
                        <div class="option-box">
                            <input type="radio" id="tahun_<?php echo $t; ?>" name="tahun" value="<?php echo $t; ?>" required <?php echo ($selected_tahun === $t) ? 'checked' : ''; ?>>
                            <label class="option-label" for="tahun_<?php echo $t; ?>">
                                Tahun <?php echo $t; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Nama Kelas (MCA) -->
            <div class="form-group">
                <label class="form-label">Nama Kelas (Pilihan)*</label>
                <div class="option-grid" style="grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));">
                    <?php 
                    $kelas_options = ['Amanah', 'Bestari', 'Cemerlang', 'Dedikasi', 'Efektif', 'Fasih', 'Gigih', 'Hebat', 'Viva', 'Persona'];
                    $selected_kelas = $_POST['kelas'] ?? '';
                    foreach ($kelas_options as $k): 
                    ?>
                        <div class="option-box">
                            <input type="radio" id="kelas_<?php echo strtolower($k); ?>" name="kelas" value="<?php echo $k; ?>" required <?php echo ($selected_kelas === $k) ? 'checked' : ''; ?>>
                            <label class="option-label" for="kelas_<?php echo strtolower($k); ?>">
                                <?php echo $k; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <hr style="border:0; border-top:2px dashed #e2e8f0; margin:30px 0;">

            <!-- BAHAGIAN B: CERITALAH LUAHAN RASA -->
            <h3 style="font-size:1.4rem; color:var(--secondary); margin-bottom:10px; display:flex; align-items:center; gap:8px;">
                💬 (b) Ceritalah Luahan Rasa Anda
            </h3>
            <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:15px;">
                Tuliskan apa sahaja impian, hobi, masalah belajar, atau perasaan anda pada ruangan di bawah:
            </p>
            <div class="form-group">
                <textarea name="luahan_rasa" class="form-control" rows="4" placeholder="Ceritakan cita-cita anda, perkara yang anda suka buat waktu lapang, atau apa sahaja perasaan anda..."><?php echo htmlspecialchars($_POST['luahan_rasa'] ?? ''); ?></textarea>
            </div>

            <hr style="border:0; border-top:2px dashed #e2e8f0; margin:30px 0;">

            <!-- BAHAGIAN C: PETI EXPLORASI KECERDASAN PELBAGAI (TEORI HOWARD GARDNER) -->
            <h3 style="font-size:1.4rem; color:var(--accent-purple); margin-bottom:10px; display:flex; align-items:center; gap:8px;">
                🧠 (c) Peti Explorasi Kecerdasan Pelbagai (Teori Howard Gardner)
            </h3>
            <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:18px;">
                Tekan mana-mana butang di bawah untuk membaca penerangan maksud & 5 pekerjaan yang sesuai, kemudian tandakan kategori kecerdasan pilihan anda!
            </p>

            <div class="gardner-buttons-wrapper" style="margin-bottom:20px;">
                <button type="button" class="gardner-btn gardner-btn-verbal" onclick="showGardnerDetail('verbal')">📚 Verbal-Linguistik</button>
                <button type="button" class="gardner-btn gardner-btn-logik" onclick="showGardnerDetail('logik')">🔢 Logik-Matematik</button>
                <button type="button" class="gardner-btn gardner-btn-visual" onclick="showGardnerDetail('visual')">🎨 Visual-Ruang</button>
                <button type="button" class="gardner-btn gardner-btn-kinestetik" onclick="showGardnerDetail('kinestetik')">⚽ Kinestetik</button>
                <button type="button" class="gardner-btn gardner-btn-muzik" onclick="showGardnerDetail('muzik')">🎵 Muzik</button>
                <button type="button" class="gardner-btn gardner-btn-interpersonal" onclick="showGardnerDetail('interpersonal')">🤝 Interpersonal</button>
                <button type="button" class="gardner-btn gardner-btn-intrapersonal" onclick="showGardnerDetail('intrapersonal')">🧘 Intrapersonal</button>
                <button type="button" class="gardner-btn gardner-btn-naturalis" onclick="showGardnerDetail('naturalis')">🌿 Naturalis</button>
                <button type="button" class="gardner-btn gardner-btn-eksistensial" onclick="showGardnerDetail('eksistensial')">🌌 Eksistensial</button>
            </div>

            <!-- Bekas Paparan Detail Teori Howard Gardner -->
            <div id="gardnerDisplay" class="gardner-detail-display" style="margin-bottom:24px;"></div>

            <!-- Penandaan Pilihan Murid (Susunan Kemas 3x3 Grid) -->
            <div class="form-group">
                <label class="form-label" style="margin-bottom:14px;">Tandakan Teori Kecerdasan Yang Paling Sesuai Dengan Anda (Boleh pilih lebih dari satu):</label>
                <div class="gardner-select-grid">
                    <?php 
                    $gardner_list = [
                        'Verbal-Linguistik' => '📚 Verbal-Linguistik',
                        'Logik-Matematik' => '🔢 Logik-Matematik',
                        'Visual-Ruang' => '🎨 Visual-Ruang',
                        'Kinestetik' => '⚽ Kinestetik',
                        'Muzik' => '🎵 Muzik',
                        'Interpersonal' => '🤝 Interpersonal',
                        'Intrapersonal' => '🧘 Intrapersonal',
                        'Naturalis' => '🌿 Naturalis',
                        'Eksistensial' => '🌌 Eksistensial'
                    ];
                    foreach ($gardner_list as $code => $label): 
                    ?>
                        <div class="option-box">
                            <input type="checkbox" id="check_<?php echo md5($code); ?>" name="gardner_pilihan[]" value="<?php echo $code; ?>">
                            <label class="option-label gardner-option-card" for="check_<?php echo md5($code); ?>">
                                <span><?php echo $label; ?></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <hr style="border:0; border-top:2px dashed #e2e8f0; margin:30px 0;">

            <!-- BAHAGIAN D: SILA UPLOAD FILE KERJAYA ANDA -->
            <h3 style="font-size:1.4rem; color:#0284c7; margin-bottom:10px; display:flex; align-items:center; gap:8px;">
                📁 (d) Sila Upload File Kerjaya Anda
            </h3>
            <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:15px;">
                Muat naik lukisan kerjaya, sijil, resume ringkas, atau dokumen cita-cita anda (Format PDF, DOC, DOCX, PNG, JPG, JPEG - Maksimum 10MB):
            </p>
            <div class="form-group">
                <div class="file-upload-box" style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:30px 20px;">
                    <input type="file" id="fail_kerjaya" name="fail_kerjaya" accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" class="form-control" style="border:none; background:transparent; max-width:340px; margin:0 auto; text-align:center;">
                    <small style="color:var(--text-muted); display:block; margin-top:12px; text-align:center;">
                        📌 Pilih fail dari peranti anda untuk dihantar kepada Guru Kaunseling.
                    </small>
                </div>
            </div>

            <hr style="border:0; border-top:2px dashed #e2e8f0; margin:30px 0;">

            <!-- BAHAGIAN E: KOMEN SAYA (MCA) -->
            <h3 style="font-size:1.4rem; color:var(--accent-green); margin-bottom:10px; display:flex; align-items:center; gap:8px;">
                ⭐ (e) Komen & Tindakan Selanjutnya
            </h3>
            <div class="form-group">
                <label class="form-label">Sila pilih satu komen maklum balas anda: <span style="color:#ef4444">*</span></label>
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <?php 
                    $komen_list = [
                        'Berpuas hati' => '😊 Berpuas hati',
                        'Perlu bantuan PRS' => '🤝 Perlu bantuan Pembimbing Rakan Sebaya (PRS)',
                        'Ingin berjumpa guru bimbingan dan kaunseling' => '👩‍🏫 Ingin berjumpa Guru Bimbingan dan Kaunseling'
                    ];
                    $selected_komen = $_POST['komen_status'] ?? '';
                    foreach ($komen_list as $val => $txt): 
                    ?>
                        <div class="option-box">
                            <input type="radio" id="komen_<?php echo md5($val); ?>" name="komen_status" value="<?php echo $val; ?>" required <?php echo ($selected_komen === $val) ? 'checked' : ''; ?>>
                            <label class="option-label" for="komen_<?php echo md5($val); ?>" style="text-align:left; padding:16px;">
                                <?php echo $txt; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- BUTANG HANTAR ANIMATED -->
            <div style="text-align:center; margin-top:40px;">
                <button type="submit" class="btn-cta-big" style="width:100%; justify-content:center;">
                    🚀 Hantar Soal Jawab Kerjaya Saya!
                </button>
            </div>

        </form>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
