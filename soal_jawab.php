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
    $riasec_array = $_POST['riasec_pilihan'] ?? [];
    $komen_status = sanitize_input($_POST['komen_status'] ?? '');

    // Sanitasi array RIASEC
    if (is_array($riasec_array)) {
        $riasec_pilihan = implode(', ', array_map('sanitize_input', $riasec_array));
    } else {
        $riasec_pilihan = sanitize_input($riasec_array);
    }

    // Semakan asas keselamatan (Pengesanan skrip berbahaya)
    if (preg_match('/<script>|javascript:|SELECT|UNION|DROP TABLE/i', json_encode($_POST))) {
        log_threat($pdo, 'SUSPICIOUS_INPUT', "Percubaan input berbahaya dikesan dari e-mel: $email");
    }

    // Validasi Medan Wajib
    if (empty($email) || empty($nama) || empty($tahun) || empty($kelas) || empty($komen_status)) {
        $error_msg = "Sila lengkapkan semua maklumat yang bertanda wajib (*).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "Sila masukkan format e-mel yang sah (contoh: murid@sekolah.edu.my).";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO responses (email, nama, tahun, kelas, luahan_rasa, riasec_pilihan, komen_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$email, $nama, $tahun, $kelas, $luahan_rasa, $riasec_pilihan, $komen_status]);
            
            $success_msg = "Tahniah $nama! Soal jawab kerjaya anda telah berjaya dihantar kepada Guru Bimbingan & Kaunseling. 🎉";
        } catch (PDOException $e) {
            $error_msg = "Ralat semasa menyimpan jawapan. Sila cuba semula.";
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

    <!-- CARD BORANG UTAMA -->
    <div class="form-card">
        <form action="soal_jawab.php" method="POST">
            
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
                <div class="option-grid" style="grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));">
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

            <!-- BAHAGIAN C: PETI EXPLORASI KERJAYA (RIASEC) -->
            <h3 style="font-size:1.4rem; color:var(--accent-purple); margin-bottom:10px; display:flex; align-items:center; gap:8px;">
                🎁 (c) Peti Explorasi Kerjaya (RIASEC)
            </h3>
            <p style="color:var(--text-muted); font-size:0.95rem; margin-bottom:18px;">
                Tekan butang di bawah untuk melihat penerangan maksud & 5 pekerjaan yang sesuai, kemudian tandakan kategori minat pilihan anda!
            </p>

            <div class="riasec-buttons-wrapper" style="margin-bottom:20px;">
                <button type="button" class="riasec-btn riasec-btn-R" onclick="showRiasecDetail('R')">R (Realistik)</button>
                <button type="button" class="riasec-btn riasec-btn-I" onclick="showRiasecDetail('I')">I (Investigatif)</button>
                <button type="button" class="riasec-btn riasec-btn-S" onclick="showRiasecDetail('S')">S (Sosial)</button>
                <button type="button" class="riasec-btn riasec-btn-E" onclick="showRiasecDetail('E')">E (Enterprising)</button>
                <button type="button" class="riasec-btn riasec-btn-K" onclick="showRiasecDetail('K')">K (Konvensional)</button>
            </div>

            <!-- Display box apabila butang ditekan -->
            <div id="riasecDisplay" class="riasec-detail-display" style="margin-bottom:24px;"></div>

            <!-- Penandaan Pilihan Murid -->
            <div class="form-group">
                <label class="form-label">Tandakan RIASEC Yang Paling Sesuai Dengan Anda (Boleh pilih lebih dari satu):</label>
                <div class="option-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
                    <?php 
                    $riasec_list = [
                        'R (Realistik)' => 'R - Realistik',
                        'I (Investigatif)' => 'I - Investigatif',
                        'S (Sosial)' => 'S - Sosial',
                        'E (Enterprising)' => 'E - Enterprising',
                        'K (Konvensional)' => 'K - Konvensional'
                    ];
                    foreach ($riasec_list as $code => $label): 
                    ?>
                        <div class="option-box">
                            <input type="checkbox" id="check_<?php echo substr($code, 0, 1); ?>" name="riasec_pilihan[]" value="<?php echo $code; ?>">
                            <label class="option-label" for="check_<?php echo substr($code, 0, 1); ?>">
                                ✅ <?php echo $label; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <hr style="border:0; border-top:2px dashed #e2e8f0; margin:30px 0;">

            <!-- BAHAGIAN D: KOMEN SAYA (MCA) -->
            <h3 style="font-size:1.4rem; color:var(--accent-green); margin-bottom:10px; display:flex; align-items:center; gap:8px;">
                ⭐ (d) Komen & Tindakan Selanjutnya
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
