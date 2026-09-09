<?php
$page_title = "Papan Pemuka Admin - Sistem Kerjaya";
require_once '../config/db.php';
require_once '../includes/logger.php';

// KAWALAN AKSES KESELAMATAN (PERLU LOG MASUK ADMIN / SUPERADMIN)
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'superadmin'])) {
    log_threat($pdo, 'UNAUTHORIZED_ACCESS', "Percubaan akses tanpa kebenaran ke Papan Pemuka Admin dari IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown'));
    header('Location: ../login.php');
    exit;
}

// TAPISAN CARIAN
$search = sanitize_input($_GET['search'] ?? '');
$filter_kelas = sanitize_input($_GET['kelas'] ?? '');
$filter_tahun = sanitize_input($_GET['tahun'] ?? '');

// PROSES PADAM SEMUA REKOD (BULK CLEAR OLEH ADMIN / SUPERADMIN)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_all_responses') {
    try {
        // Padam fail dimuat naik jika wujud
        $files = glob(__DIR__ . '/../uploads/*');
        if ($files) {
            foreach ($files as $file) {
                if (is_file($file)) @unlink($file);
            }
        }

        $pdo->exec("DELETE FROM responses");
        $_SESSION['flash_success'] = "Kesemua rekod jawapan murid telah berjaya dipadamkan dari sistem! 🗑️";
        log_threat($pdo, 'ALL_RESPONSES_DELETED', "Pengguna {$_SESSION['user_email']} ({$_SESSION['user_role']}) telah memadam KESEMUA rekod murid.");
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = "Ralat pangkalan data semasa memadam kesemua rekod.";
        log_threat($pdo, 'DB_ERROR', "Ralat SQL padam semua: " . $e->getMessage());
    }

    header('Location: dashboard.php');
    exit;
}

// PROSES PADAM SATU REKOD SOAL JAWAB MURID
if (isset($_GET['delete_response'])) {
    $response_id_to_delete = (int)$_GET['delete_response'];
    try {
        $stmt_fetch = $pdo->prepare("SELECT * FROM responses WHERE id = ?");
        $stmt_fetch->execute([$response_id_to_delete]);
        $target_response = $stmt_fetch->fetch();

        if ($target_response) {
            if (!empty($target_response['fail_kerjaya'])) {
                $file_to_delete = __DIR__ . '/../' . $target_response['fail_kerjaya'];
                if (file_exists($file_to_delete)) {
                    @unlink($file_to_delete);
                }
            }

            // Padam rekod secara pasti mengikut ID sahaja
            $stmt_del = $pdo->prepare("DELETE FROM responses WHERE id = ?");
            $stmt_del->execute([$response_id_to_delete]);

            $_SESSION['flash_success'] = "Rekod jawapan murid (" . htmlspecialchars($target_response['nama']) . ") telah berjaya dipadam!";
            log_threat($pdo, 'RESPONSE_DELETED', "Pengguna {$_SESSION['user_email']} ({$_SESSION['user_role']}) telah memadam rekod murid ID #{$response_id_to_delete} ({$target_response['nama']} - {$target_response['email']})");
        } else {
            $_SESSION['flash_error'] = "Rekod murid tidak ditemui.";
        }
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = "Ralat pangkalan data semasa memadam rekod murid.";
        log_threat($pdo, 'DB_ERROR', "Ralat SQL padam rekod: " . $e->getMessage());
    }

    $redirect_params = [];
    if (!empty($search)) $redirect_params['search'] = $search;
    if (!empty($filter_kelas)) $redirect_params['kelas'] = $filter_kelas;
    if (!empty($filter_tahun)) $redirect_params['tahun'] = $filter_tahun;

    $redirect_url = 'dashboard.php';
    if (!empty($redirect_params)) {
        $redirect_url .= '?' . http_build_query($redirect_params);
    }

    header('Location: ' . $redirect_url);
    exit;
}

// AMBIL MESEJ FLASH DARIPADA SESI JIKA ADA
$msg_success = $_SESSION['flash_success'] ?? null;
$msg_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// KEUPIAN QUERY DENGAN TAPISAN
$where_clauses = [];
$params = [];

if (!empty($search)) {
    $where_clauses[] = "(nama LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($filter_kelas)) {
    $where_clauses[] = "kelas = ?";
    $params[] = $filter_kelas;
}

if (!empty($filter_tahun)) {
    $where_clauses[] = "tahun = ?";
    $params[] = $filter_tahun;
}

$sql = "SELECT * FROM responses";
if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY submitted_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$responses = $stmt->fetchAll();

// METRIK STATISTIK PANTAS
$total_responses = $pdo->query("SELECT COUNT(*) FROM responses")->fetchColumn();
$total_kaunseling = $pdo->query("SELECT COUNT(*) FROM responses WHERE komen_status = 'Ingin berjumpa guru bimbingan dan kaunseling'")->fetchColumn();
$total_prs = $pdo->query("SELECT COUNT(*) FROM responses WHERE komen_status = 'Perlu bantuan PRS'")->fetchColumn();
$total_puas = $pdo->query("SELECT COUNT(*) FROM responses WHERE komen_status = 'Berpuas hati'")->fetchColumn();

// DATA STATISTIK UNTUK CARTA CHART.JS
// 1. Mengikut Tahun (Main Overview)
$tahun_order = ['1', '2', '3', '4', '5', '6', 'PPKI'];
$tahun_counts_map = array_fill_keys($tahun_order, 0);

$tahun_stats_raw = $pdo->query("SELECT tahun, COUNT(*) as cnt FROM responses GROUP BY tahun")->fetchAll();
foreach ($tahun_stats_raw as $r) {
    $t_val = (string)$r['tahun'];
    $tahun_counts_map[$t_val] = (int)$r['cnt'];
}

$tahun_labels = [];
$tahun_counts = [];
foreach ($tahun_counts_map as $t => $cnt) {
    $tahun_labels[] = ($t === 'PPKI') ? 'PPKI' : 'Tahun ' . $t;
    $tahun_counts[] = $cnt;
}

// 2. Breakdown Mengikut Kelas bagi Setiap Tahun (Drilldown Data)
$tahun_kelas_raw = $pdo->query("SELECT tahun, kelas, COUNT(*) as cnt FROM responses GROUP BY tahun, kelas")->fetchAll();
$tahun_kelas_data = [];
foreach ($tahun_order as $t) {
    $tahun_kelas_data[$t] = [];
}
foreach ($tahun_kelas_raw as $r) {
    $t_val = (string)$r['tahun'];
    $k_val = $r['kelas'];
    $tahun_kelas_data[$t_val][$k_val] = (int)$r['cnt'];
}

// 3. Mengikut Status Komen
$komen_stats_raw = $pdo->query("SELECT komen_status, COUNT(*) as cnt FROM responses GROUP BY komen_status")->fetchAll();
$komen_labels = [];
$komen_counts = [];
foreach ($komen_stats_raw as $r) {
    $komen_labels[] = $r['komen_status'];
    $komen_counts[] = (int)$r['cnt'];
}

require_once '../includes/header.php';
?>

<div class="container admin-container">
    
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

    <!-- HEADER PAPAN PEMUKA -->
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:30px; gap:16px;">
        <div>
            <span style="background:var(--primary-light); color:var(--primary); font-weight:800; padding:6px 16px; border-radius:50px; font-size:0.9rem;">
                👨‍🏫 Panel Pengurusan Guru Kaunseling
            </span>
            <h1 style="font-size:2.4rem; color:#1e1b4b; margin-top:8px;">Papan Pemuka Statistik & Soal Jawab</h1>
            <p style="color:var(--text-muted);">Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong> (<?php echo strtoupper($_SESSION['user_role']); ?>)</p>
        </div>

        <div>
            <?php if ($_SESSION['user_role'] === 'superadmin'): ?>
                <a href="superadmin.php" class="nav-btn btn-primary">
                    👑 Kawalan Superadmin & Log Ancaman
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- KAD RINGKASAN METRIK -->
    <div class="stat-grid">
        
        <div class="stat-card" style="--stat-color:var(--primary)">
            <div>
                <div class="stat-val"><?php echo $total_responses; ?></div>
                <div class="stat-title">Jumlah Jawapan Murid</div>
            </div>
            <div class="stat-icon">📋</div>
        </div>

        <div class="stat-card" style="--stat-color:#ef4444">
            <div>
                <div class="stat-val"><?php echo $total_kaunseling; ?></div>
                <div class="stat-title">Ingin Sesi Kaunseling</div>
            </div>
            <div class="stat-icon">👩‍🏫</div>
        </div>

        <div class="stat-card" style="--stat-color:#f59e0b">
            <div>
                <div class="stat-val"><?php echo $total_prs; ?></div>
                <div class="stat-title">Perlu Bantuan PRS</div>
            </div>
            <div class="stat-icon">🤝</div>
        </div>

        <div class="stat-card" style="--stat-color:#10b981">
            <div>
                <div class="stat-val"><?php echo $total_puas; ?></div>
                <div class="stat-title">Berpuas Hati</div>
            </div>
            <div class="stat-icon">😊</div>
        </div>

    </div>

    <!-- CARTA STATISTIK (CHART.JS) -->
    <div class="chart-grid">
        
        <!-- Carta 1: Taburan Mengikut Tahun & Pecahan Kelas -->
        <div class="chart-card">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
                <div>
                    <h3 id="chartTitleText" style="font-size:1.2rem; color:#1e1b4b; margin:0; display:flex; align-items:center; gap:6px;">
                        📊 Statistik Penyertaan Mengikut Tahun
                    </h3>
                    <p style="color:var(--text-muted); font-size:0.8rem; margin-top:2px; margin-bottom:0;">
                        Klik mana-mana tahun / palang di bawah untuk melihat pecahan statistik kelas.
                    </p>
                </div>
                <button id="resetTahunBtn" type="button" class="btn-outline nav-btn" style="display:none; padding:4px 10px; font-size:0.8rem; border-color:var(--primary); color:var(--primary);" onclick="renderTahunChart()">
                    ◀ Semua Tahun
                </button>
            </div>

            <!-- Butang Tapis Tahun Pantas -->
            <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:15px;">
                <button type="button" class="tahun-filter-btn nav-btn" data-tahun="all" style="padding:4px 10px; font-size:0.78rem; border-radius:20px; font-weight:700; background:var(--primary); color:#fff;" onclick="renderTahunChart()">
                    🌐 Semua Tahun
                </button>
                <?php foreach (['1','2','3','4','5','6','PPKI'] as $t_btn): ?>
                    <button type="button" class="tahun-filter-btn nav-btn" data-tahun="<?php echo $t_btn; ?>" style="padding:4px 10px; font-size:0.78rem; border-radius:20px; font-weight:700; background:#f1f5f9; color:#475569;" onclick="showClassBreakdownForTahun('<?php echo $t_btn; ?>')">
                        <?php echo ($t_btn === 'PPKI') ? 'PPKI' : 'Tahun ' . $t_btn; ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div style="position:relative; height:260px;">
                <canvas id="chartKelas"></canvas>
            </div>
        </div>

        <!-- Carta 2: Status Maklum Balas / Kaunseling -->
        <div class="chart-card">
            <h3 style="font-size:1.25rem; color:#1e1b4b; margin-bottom:15px; display:flex; align-items:center; gap:8px;">
                🎯 Statistik Keperluan Bimbingan & Kaunseling
            </h3>
            <div style="position:relative; height:280px;">
                <canvas id="chartKomen"></canvas>
            </div>
        </div>

    </div>

    <!-- JADUAL REKOD SOAL JAWAB MURID -->
    <div class="table-card">
        
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px; margin-bottom:20px;">
            <div>
                <h3 style="font-size:1.4rem; color:#1e1b4b;">📑 Senarai Rekod Jawapan Murid</h3>
                <p style="color:var(--text-muted); font-size:0.9rem;">Klik butang "Lihat Jawapan" untuk membaca luahan rasa, Teori Howard Gardner & fail muat naik.</p>
            </div>
            
            <!-- BORANG TAPISAN & CARIAN -->
            <form action="dashboard.php" method="GET" style="display:flex; gap:10px; flex-wrap:wrap;">
                
                <input type="text" name="search" class="form-control" style="width:200px; padding:8px 14px;" placeholder="Cari Nama / E-mel..." value="<?php echo htmlspecialchars($search); ?>">

                <select name="tahun" class="form-control" style="width:130px; padding:8px 14px;">
                    <option value="">-- Semua Tahun --</option>
                    <?php foreach (['1','2','3','4','5','6','PPKI'] as $t): ?>
                        <option value="<?php echo $t; ?>" <?php echo ($filter_tahun === $t) ? 'selected' : ''; ?>><?php echo ($t === 'PPKI') ? 'PPKI' : 'Tahun ' . $t; ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="kelas" class="form-control" style="width:140px; padding:8px 14px;">
                    <option value="">-- Semua Kelas --</option>
                    <?php foreach (['Amanah', 'Bestari', 'Cemerlang', 'Dedikasi', 'Efektif', 'Fasih', 'Gigih', 'Hebat', 'Viva', 'Persona'] as $k): ?>
                        <option value="<?php echo $k; ?>" <?php echo ($filter_kelas === $k) ? 'selected' : ''; ?>><?php echo $k; ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn-primary nav-btn" style="padding:8px 16px;">🔍 Tapis</button>
                
                <?php
                $current_filter_params = [];
                if (!empty($search)) $current_filter_params['search'] = $search;
                if (!empty($filter_kelas)) $current_filter_params['kelas'] = $filter_kelas;
                if (!empty($filter_tahun)) $current_filter_params['tahun'] = $filter_tahun;
                $filter_qs = !empty($current_filter_params) ? '&' . http_build_query($current_filter_params) : '';
                ?>

                <?php if ($total_responses > 0): ?>
                    <div style="display:flex; gap:10px; align-items:center; margin-left:auto; flex-wrap:wrap;">
                        <!-- BUTANG SIMPAN SEMUA REKOD (DROPDOWN CSV & ZIP) -->
                        <div class="dropdown-export-container" style="position:relative; display:inline-block;">
                            <button type="button" class="btn-primary nav-btn" style="background:linear-gradient(135deg, #10b981, #059669); color:#fff; padding:8px 16px; font-weight:700; border:none; display:flex; align-items:center; gap:6px; cursor:pointer;" onclick="toggleExportMenu(event)">
                                💾 Simpan Semua Rekod <span style="font-size:0.75rem;">▼</span>
                            </button>
                            <div id="exportDropdownMenu" style="display:none; position:absolute; right:0; top:115%; background:#ffffff; border:1px solid #cbd5e1; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.15); z-index:100; min-width:240px; overflow:hidden;">
                                <a href="export.php?type=csv<?php echo $filter_qs; ?>" style="display:flex; align-items:center; gap:12px; padding:12px 16px; color:#0f172a; text-decoration:none; font-weight:600; font-size:0.9rem; transition:background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                    <span style="font-size:1.3rem;">📄</span>
                                    <div>
                                        <div style="color:#065f46; font-weight:700;">Simpan Format .CSV</div>
                                        <small style="color:#64748b; font-weight:normal; font-size:0.78rem;">Data Rekod & Luahan Rasa</small>
                                    </div>
                                </a>
                                <div style="border-top:1px solid #e2e8f0;"></div>
                                <a href="export.php?type=zip<?php echo $filter_qs; ?>" style="display:flex; align-items:center; gap:12px; padding:12px 16px; color:#0f172a; text-decoration:none; font-weight:600; font-size:0.9rem; transition:background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='transparent'">
                                    <span style="font-size:1.3rem;">📦</span>
                                    <div>
                                        <div style="color:#1e40af; font-weight:700;">Simpan Fail .ZIP</div>
                                        <small style="color:#64748b; font-weight:normal; font-size:0.78rem;">Gambar Murid mengikut Nama</small>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- BUTANG PADAM SEMUA REKOD -->
                        <form method="POST" action="dashboard.php" style="display:inline; margin:0;" onsubmit="return confirm('⚠️ Adakah anda PASTI mahu memadam KESEMUA rekod jawapan murid dalam sistem? Tindakan ini kekal dan tidak boleh diundurkan!');">
                            <input type="hidden" name="action" value="delete_all_responses">
                            <button type="submit" class="btn-outline nav-btn" style="border-color:#ef4444; color:#ef4444; padding:8px 14px; font-weight:700;">
                                🗑️ Padam Semua Rekod
                            </button>
                        </form>
                    </div>
                <?php endif; ?>

            </form>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Masa Penyerahan</th>
                        <th>Nama Murid</th>
                        <th>E-mel (Primary Key)</th>
                        <th>Tahun</th>
                        <th>Kelas</th>
                        <th>Kecerdasan Howard</th>
                        <th>Fail Kerjaya</th>
                        <th>Status Maklum Balas</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($responses) > 0): ?>
                        <?php foreach ($responses as $idx => $r): ?>
                            <tr>
                                <td><strong><?php echo $idx + 1; ?></strong></td>
                                <td style="font-size:0.85rem; color:var(--text-muted);">
                                    <?php echo date('d/m/Y h:i A', strtotime($r['submitted_at'])); ?>
                                </td>
                                <td>
                                    <strong style="color:#1e1b4b;"><?php echo htmlspecialchars($r['nama']); ?></strong>
                                </td>
                                <td style="font-family:monospace; color:#475569;">
                                    <?php echo htmlspecialchars($r['email']); ?>
                                </td>
                                <td><span class="badge badge-info"><?php echo ($r['tahun'] === 'PPKI') ? 'PPKI' : 'Tahun ' . htmlspecialchars($r['tahun']); ?></span></td>
                                <td><span class="badge badge-info"><?php echo htmlspecialchars($r['kelas']); ?></span></td>
                                <td>
                                    <small style="font-weight:700; color:#6366f1;">
                                        <?php echo htmlspecialchars($r['riasec_pilihan'] ?: 'Tiada'); ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if (!empty($r['fail_kerjaya'])): ?>
                                        <a href="../download.php?id=<?php echo $r['id']; ?>" target="_blank" class="badge badge-success" style="text-decoration:none;">
                                            📁 Muat Turun
                                        </a>
                                    <?php else: ?>
                                        <span class="badge badge-info" style="background:#f1f5f9; color:#94a3b8;">Tiada Fail</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    if ($r['komen_status'] === 'Ingin berjumpa guru bimbingan dan kaunseling') {
                                        echo '<span class="badge badge-danger">⚠️ Sesi Kaunseling</span>';
                                    } elseif ($r['komen_status'] === 'Perlu bantuan PRS') {
                                        echo '<span class="badge badge-warning">🤝 Bantuan PRS</span>';
                                    } else {
                                        echo '<span class="badge badge-success">😊 Berpuas Hati</span>';
                                    }
                                    ?>
                                </td>
                                <td style="white-space:nowrap;">
                                    <div style="display:flex; gap:6px; flex-wrap:nowrap;">
                                        <button type="button" class="btn-outline nav-btn" style="padding:5px 10px; font-size:0.8rem;" onclick="viewStudentDetail(<?php echo htmlspecialchars(json_encode($r)); ?>)">
                                            👁️ Lihat
                                        </button>
                                        <a href="dashboard.php?delete_response=<?php echo $r['id'] . $filter_qs; ?>" class="btn-outline nav-btn" style="padding:5px 10px; font-size:0.8rem; border-color:#ef4444; color:#ef4444; text-decoration:none;" onclick="return confirm('Adakah anda pasti untuk memadam rekod jawapan murid <?php echo htmlspecialchars(addslashes($r['nama'])); ?>?')">
                                            🗑️ Padam
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" style="text-align:center; padding:30px; color:var(--text-muted);">
                                📭 Tiada rekod jawapan murid ditemui bagi tapisan ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

<!-- MODAL BUTIRAN JAWAPAN MURID -->
<div id="studentDetailModal" class="modal-backdrop">
    <div class="modal-box">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom:2px solid #e2e8f0; padding-bottom:12px;">
            <h3 style="font-size:1.5rem; color:#1e1b4b;" id="modalStudentTitle">Maklumat Terperinci Murid</h3>
            <button type="button" onclick="closeModal('studentDetailModal')" style="background:none; border:none; font-size:1.5rem; cursor:pointer;">❌</button>
        </div>

        <div id="modalStudentContent" style="display:flex; flex-direction:column; gap:16px;">
            <!-- Kandungan modal dijana secara dinamik oleh JS -->
        </div>

        <div style="margin-top:25px; display:flex; justify-content:space-between; align-items:center;">
            <a id="modalDeleteBtn" href="#" class="btn-outline nav-btn" style="border-color:#ef4444; color:#ef4444; text-decoration:none;" onclick="return confirm('Adakah anda pasti untuk memadam rekod murid ini?')">
                🗑️ Padam Rekod Ini
            </a>
            <button type="button" class="btn-primary nav-btn" onclick="closeModal('studentDetailModal')">Tutup</button>
        </div>
    </div>
</div>

<script>
let chartInstanceTahun = null;
const tahunLabels = <?php echo json_encode($tahun_labels); ?>;
const tahunKeys = <?php echo json_encode($tahun_order); ?>;
const tahunCounts = <?php echo json_encode($tahun_counts); ?>;
const breakdownByTahun = <?php echo json_encode($tahun_kelas_data); ?>;

function renderTahunChart() {
    const ctx = document.getElementById('chartKelas').getContext('2d');
    document.getElementById('chartTitleText').innerHTML = "📊 Statistik Penyertaan Mengikut Tahun";
    document.getElementById('resetTahunBtn').style.display = 'none';
    setActiveTahunBtn('all');

    if (chartInstanceTahun) chartInstanceTahun.destroy();

    chartInstanceTahun = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: tahunLabels,
            datasets: [{
                label: 'Jumlah Murid',
                data: tahunCounts,
                backgroundColor: [
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(14, 165, 233, 0.8)'
                ],
                borderColor: '#4f46e5',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        afterBody: function() {
                            return "👉 Klik palang ini untuk lihat pecahan kelas!";
                        }
                    }
                }
            },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
            onClick: (e, activeElements) => {
                if (activeElements && activeElements.length > 0) {
                    const clickedIndex = activeElements[0].index;
                    const clickedTahunKey = tahunKeys[clickedIndex];
                    showClassBreakdownForTahun(clickedTahunKey);
                }
            }
        }
    });
}

function showClassBreakdownForTahun(tahunKey) {
    const ctx = document.getElementById('chartKelas').getContext('2d');
    const tahunName = (tahunKey === 'PPKI') ? 'PPKI' : 'Tahun ' + tahunKey;
    document.getElementById('chartTitleText').innerHTML = `📊 Statistik Pecahan Kelas (${tahunName})`;
    document.getElementById('resetTahunBtn').style.display = 'inline-flex';
    setActiveTahunBtn(tahunKey);

    const classDataForTahun = breakdownByTahun[tahunKey] || {};
    const classLabels = Object.keys(classDataForTahun);
    const classCounts = Object.values(classDataForTahun);

    const finalLabels = classLabels.length > 0 ? classLabels : ['Tiada Data'];
    const finalCounts = classCounts.length > 0 ? classCounts : [0];

    if (chartInstanceTahun) chartInstanceTahun.destroy();

    chartInstanceTahun = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: finalLabels,
            datasets: [{
                label: `Jumlah Murid (${tahunName})`,
                data: finalCounts,
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: '#059669',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
}

function setActiveTahunBtn(key) {
    document.querySelectorAll('.tahun-filter-btn').forEach(btn => {
        if (btn.getAttribute('data-tahun') === key) {
            btn.style.background = 'var(--primary)';
            btn.style.color = '#fff';
        } else {
            btn.style.background = '#f1f5f9';
            btn.style.color = '#475569';
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    renderTahunChart();

    // 2. CARTA MENGIKUT STATUS KOMEN
    const ctxKomen = document.getElementById('chartKomen').getContext('2d');
    new Chart(ctxKomen, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($komen_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($komen_counts); ?>,
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});

// FUNGSI PAPARAN DETAIL MURID IN MODAL
function viewStudentDetail(data) {
    document.getElementById('modalStudentTitle').innerText = "📋 Jawapan: " + data.nama;
    
    let failLink = '<span style="color:#94a3b8;">Tiada fail dimuat naik.</span>';
    if (data.fail_kerjaya) {
        failLink = `<a href="../download.php?id=${data.id}" target="_blank" style="background:#10b981; color:white; padding:6px 14px; border-radius:8px; text-decoration:none; font-weight:700; display:inline-block; margin-top:4px;">📥 Buka / Muat Turun Fail Kerjaya</a>`;
    }

    const content = `
        <div style="background:#f8fafc; padding:16px; border-radius:12px; border:1px solid #e2e8f0;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:0.95rem;">
                <div><strong>📧 E-mel (Primary Key):</strong><br><span style="color:#6366f1;">${data.email}</span></div>
                <div><strong>🏫 Peringkat & Kelas:</strong><br>Tahun ${data.tahun} - ${data.kelas}</div>
                <div><strong>📅 Tarikh Penyerahan:</strong><br>${data.submitted_at}</div>
                <div><strong>⭐ Status Komen:</strong><br><span style="color:#d97706; font-weight:700;">${data.komen_status}</span></div>
            </div>
        </div>

        <div>
            <h4 style="color:#1e1b4b; font-size:1.1rem; margin-bottom:6px;">🎁 Kecerdasan Pelbagai Howard Gardner:</h4>
            <div style="background:#e0e7ff; color:#3730a3; padding:12px; border-radius:10px; font-weight:700;">
                ${data.riasec_pilihan ? data.riasec_pilihan : 'Tiada pilihan dibuat.'}
            </div>
        </div>

        <div>
            <h4 style="color:#1e1b4b; font-size:1.1rem; margin-bottom:6px;">📁 Fail Kerjaya Murid (Upload):</h4>
            <div>${failLink}</div>
        </div>

        <div>
            <h4 style="color:#1e1b4b; font-size:1.1rem; margin-bottom:6px;">💬 Cerita & Luahan Rasa Murid:</h4>
            <div style="background:#fff1f2; color:#9f1239; padding:16px; border-radius:12px; border:1px solid #fecdd3; font-style:italic; line-height:1.6;">
                "${data.luahan_rasa ? data.luahan_rasa : 'Murid tidak meninggalkan sebarang luahan rasa.'}"
            </div>
        </div>
    `;

    document.getElementById('modalStudentContent').innerHTML = content;
    document.getElementById('modalDeleteBtn').href = "dashboard.php?delete_response=" + data.id + "<?php echo addslashes($filter_qs); ?>";
    openModal('studentDetailModal');
}

// FUNGSI DROPDOWN MENU EKSPOR SIMPAN REKOD
function toggleExportMenu(e) {
    e.stopPropagation();
    const menu = document.getElementById('exportDropdownMenu');
    if (menu) {
        menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
    }
}

document.addEventListener('click', function(e) {
    const menu = document.getElementById('exportDropdownMenu');
    if (menu && !e.target.closest('.dropdown-export-container')) {
        menu.style.display = 'none';
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>
