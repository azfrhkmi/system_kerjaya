<?php
$page_title = "Soal Jawab Kerjaya Saya";
require_once 'config/db.php';
require_once 'includes/logger.php';

$success_msg = null;
$error_msg = null;

if (isset($_GET['submitted'])) {
    $submitted_nama = htmlspecialchars($_SESSION['last_submitted_nama'] ?? 'Murid');
    $success_msg = "Tahniah {$submitted_nama}! Soal jawab kerjaya anda telah berjaya dihantar kepada Guru Bimbingan & Kaunseling. 🎉";
    if (isset($_SESSION['upload_warning'])) {
        $success_msg .= "<br><small style='color:#b45309; font-weight:normal; display:block; margin-top:8px;'>⚠️ Nota: " . htmlspecialchars($_SESSION['upload_warning']) . "</small>";
        unset($_SESSION['upload_warning']);
    }
    unset($_SESSION['last_submitted_nama']);
}

// PROSES BORANG PENYERAHAN
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_email = trim($_POST['email'] ?? '');
    // Jika murid memasukkan username/nama tanpa '@', tambah domain lalai automatik
    if (!empty($raw_email) && strpos($raw_email, '@') === false) {
        $raw_email .= '@sekolah.edu.my';
    }
    $email = filter_var($raw_email, FILTER_SANITIZE_EMAIL);

    $nama = sanitize_input($_POST['nama'] ?? '');
    $tahun = sanitize_input($_POST['tahun'] ?? '');
    $kelas = sanitize_input($_POST['kelas'] ?? '');
    $luahan_rasa = sanitize_input($_POST['luahan_rasa'] ?? '');
    $gardner_array = $_POST['gardner_pilihan'] ?? [];
    $komen_status = sanitize_input($_POST['komen_status'] ?? '');
    $fail_kerjaya_path = null;
    $upload_warning = null;

    // Sanitasi array Teori Howard Gardner
    if (is_array($gardner_array)) {
        $riasec_pilihan = implode(', ', array_map('sanitize_input', $gardner_array));
    } else {
        $riasec_pilihan = sanitize_input($gardner_array);
    }

    // PROSES MUAT NAIK FAIL KERJAYA (SECTION D - TIDAK MEMBLOK PENYIMPANAN DATA REKOD)
    if (isset($_FILES['fail_kerjaya']) && $_FILES['fail_kerjaya']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['fail_kerjaya']['tmp_name'];
        $file_name = $_FILES['fail_kerjaya']['name'];
        $file_size = $_FILES['fail_kerjaya']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_exts = ['pdf', 'doc', 'docx', 'png', 'jpg', 'jpeg', 'heic', 'heif'];
        
        if (!in_array($file_ext, $allowed_exts)) {
            $upload_warning = "Fail tidak disimpan kerana format tidak disokong (PDF, DOC, DOCX, PNG, JPG, JPEG). Jawapan murid tetap berjaya direkodkan!";
        } elseif ($file_size > 15 * 1024 * 1024) { // Max 15MB
            $upload_warning = "Fail terlalu besar (>15MB). Jawapan murid tetap berjaya direkodkan!";
        } else {
            // Cipta nama fail selamat
            $clean_email = preg_replace('/[^a-zA-Z0-9]/', '_', $email);
            $new_filename = "kerjaya_" . $clean_email . "_" . time() . "." . $file_ext;
            $target_dir = __DIR__ . "/uploads/";
            
            if (!is_dir($target_dir)) {
                @mkdir($target_dir, 0755, true);
            }
            
            $target_file = $target_dir . $new_filename;
            if (@move_uploaded_file($file_tmp, $target_file)) {
                $fail_kerjaya_path = "uploads/" . $new_filename;
            } else {
                $upload_warning = "Fail tidak dapat dimuat naik ke pelayan. Jawapan murid tetap berjaya direkodkan!";
            }
        }
    }

    // Semakan asas keselamatan (Pengesanan skrip berbahaya)
    if (preg_match('/<script>|javascript:|SELECT|UNION|DROP TABLE/i', json_encode($_POST))) {
        log_threat($pdo, 'SUSPICIOUS_INPUT', "Percubaan input berbahaya dikesan dari e-mel: $email");
    }

    // Validasi Medan Wajib & Simpan Ke Pangkalan Data
    if (empty($email) || empty($nama) || empty($tahun) || empty($kelas) || empty($komen_status)) {
        $error_msg = "Sila lengkapkan semua maklumat yang bertanda wajib (*).";
    } else {
        try {
            $clean_email_input = strtolower(trim($email));
            // Sentiasa tambah rekod baru untuk setiap penyerahan borang murid
            $stmt_in = $pdo->prepare("INSERT INTO responses (email, nama, tahun, kelas, luahan_rasa, riasec_pilihan, fail_kerjaya, komen_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_in->execute([$clean_email_input, $nama, $tahun, $kelas, $luahan_rasa, $riasec_pilihan, $fail_kerjaya_path, $komen_status]);

            // Guna Post-Redirect-Get (PRG) untuk mengelakkan penyerahan semula borang pada Refresh
            $_SESSION['last_submitted_nama'] = $nama;
            if (!empty($upload_warning)) {
                $_SESSION['upload_warning'] = $upload_warning;
            }
            header('Location: soal_jawab.php?submitted=1');
            exit;
        } catch (PDOException $e) {
            $error_msg = "Ralat pangkalan data semasa menyimpan jawapan: " . htmlspecialchars($e->getMessage());
            log_threat($pdo, 'DB_ERROR', "Ralat SQL penyerahan borang: " . $e->getMessage());
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container" style="padding-top: 40px; padding-bottom: 60px;">
    
    <!-- HEADER BORANG -->
    <div style="text-align: center; margin-bottom: 40px;">
        <span style="background:#e0e7ff; color:var(--primary); font-weight:800; padding:6px 18px; border-radius:50px; font-size:0.95rem;">
            Lihat Potensi Impian Anda
        </span>
        <h1 style="font-size:2.8rem; color:#1e1b4b; margin-top:10px; margin-bottom:10px;">
            Mari Isi <span class="highlight">Maklumat Kerjaya Anda</span>
        </h1>
        <p style="color:var(--text-muted); font-size:1.1rem; max-width:650px; margin:0 auto;">
            Jawab soalan di bawah dengan jujur untuk membantu kami mengenali minat dan cita-cita anda!
        </p>
    </div>

    <!-- NOTIFIKASI BOLEH DILIHAT -->
    <?php if (!empty($success_msg)): ?>
        <div style="background:#dcfce7; border:2px solid #86efac; color:#166534; border-radius:var(--radius-md); padding:20px; text-align:center; max-width:900px; margin:0 auto 30px; font-size:1.1rem; font-weight:700;">
            <?php echo $success_msg; ?>
            <div style="margin-top:15px;">
                <a href="index.php" class="btn-primary nav-btn" style="text-decoration:none;">🏠 Kembali ke Laman Utama</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
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
                                <?php echo ($t === 'PPKI') ? 'PPKI' : 'Tahun ' . $t; ?>
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

            <!-- BAHAGIAN B: PENGGUNAAN APLIKASI AI KERJAYA -->
            <h3 style="font-size:1.4rem; color:var(--accent-purple); margin-bottom:10px; display:flex; align-items:center; gap:8px;">
                🤖 (b) Penggunaan Aplikasi AI Kerjaya
            </h3>
            <p style="color:var(--text-dark); font-size:1.05rem; font-weight:700; margin-bottom:4px;">
                Dengan menggunakan teknologi AI, anda boleh menghasilkan hasil kerjaya yang kreatif
            </p>
            <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:18px;">
                Berikut merupakan antara aplikasi AI yang boleh digunakan:
            </p>

            <!-- 10 FAMOUS AI APPS BUTTONS GRID -->
            <div class="ai-buttons-wrapper" style="margin-bottom:18px;">
                <button type="button" class="ai-btn" onclick="showAiDetail('chatgpt')">🤖 ChatGPT</button>
                <button type="button" class="ai-btn" onclick="showAiDetail('claude')">🧠 Claude AI</button>
                <button type="button" class="ai-btn" onclick="showAiDetail('gemini')">✨ Google Gemini</button>
                <button type="button" class="ai-btn" onclick="showAiDetail('canva')">🎨 Canva Magic Studio</button>
                <button type="button" class="ai-btn" onclick="showAiDetail('copilot')">💻 Microsoft Copilot</button>
                <button type="button" class="ai-btn" onclick="showAiDetail('midjourney')">🖼️ Midjourney</button>
                <button type="button" class="ai-btn" onclick="showAiDetail('perplexity')">🔍 Perplexity AI</button>
                <button type="button" class="ai-btn" onclick="showAiDetail('dalle')">🎭 DALL-E</button>
                <button type="button" class="ai-btn" onclick="showAiDetail('poe')">💡 Poe AI</button>
                <button type="button" class="ai-btn" onclick="showAiDetail('elevenlabs')">🎙️ ElevenLabs</button>
            </div>

            <!-- Bekas Paparan Detail Aplikasi AI -->
            <div id="aiDisplay" class="ai-detail-display" style="margin-bottom:24px;"></div>

            <hr style="border:0; border-top:2px dashed #e2e8f0; margin:30px 0;">

            <!-- BAHAGIAN C: TEORI KECERDASAN PELBAGAI HOWARD GARDNER -->
            <h3 style="font-size:1.4rem; color:#ec4899; margin-bottom:10px; display:flex; align-items:center; gap:8px;">
                🎁 (c) Explorasi Teori Kecerdasan Pelbagai
            </h3>
            <div class="form-group" style="margin-top:14px;">
                <label class="form-label" style="margin-bottom:14px; font-size:1.05rem; color:#1e1b4b;">
                    Tandakan Teori Kecerdasan Yang Paling Sesuai Dengan Anda (Boleh pilih lebih dari satu):
                </label>
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

            <!-- BAHAGIAN D: SILA UPLOAD BAHAN FILE INSPIRASI KERJAYA ANDA -->
            <h3 style="font-size:1.4rem; color:#0284c7; margin-bottom:10px; display:flex; align-items:center; gap:8px;">
                📁 (d) Sila Upload Bahan File Inspirasi Kerjaya Anda
            </h3>
            <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:16px; padding:18px; margin-bottom:18px;">
                <p style="color:#0369a1; font-size:0.98rem; margin-bottom:10px; line-height:1.6;">
                    Sila layari akaun <strong>DELIMa</strong> anda, dengan menggunakan aplikasi AI, hasilkan bahan berkaitan inspirasi kerjaya anda dan muat turun di ruangan yang disediakan.
                </p>
                <div style="background:white; border-radius:12px; padding:14px; border-left:5px solid #0284c7; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
                    <div style="font-weight:700; color:#0369a1; font-size:0.92rem; margin-bottom:4px;">💡 Contoh prompt untuk menghasilkan sebuah poster:</div>
                    <em style="color:#1e293b; font-size:0.95rem; line-height:1.5; display:block;">
                        "Saya (nama anda), saya ingin sambung belajar ke (nama pilihan sekolah anda). Inspirasi kerjaya saya ialah ( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ). Tuliskan tugas pilihan kerjaya saya."
                    </em>
                </div>
            </div>

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
                    🚀 Hantar!
                </button>
            </div>

        </form>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>
