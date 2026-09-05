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

$msg_success = null;
$msg_error = null;

// PROSES PADAM REKOD SOAL JAWAB MURID (OLEH ADMIN / SUPERADMIN)
if (isset($_GET['delete_response'])) {
    $response_id_to_delete = (int)$_GET['delete_response'];
    try {
        // Ambil maklumat murid untuk pemadaman fail & logging
        $stmt_fetch = $pdo->prepare("SELECT * FROM responses WHERE id = ?");
        $stmt_fetch->execute([$response_id_to_delete]);
        $target_response = $stmt_fetch->fetch();

        if ($target_response) {
            // Padam fail dimuat naik jika wujud
            if (!empty($target_response['fail_kerjaya'])) {
                $file_to_delete = __DIR__ . '/../' . $target_response['fail_kerjaya'];
                if (file_exists($file_to_delete)) {
                    @unlink($file_to_delete);
                }
            }

            // Padam rekod dari database
            $stmt_del = $pdo->prepare("DELETE FROM responses WHERE id = ?");
            $stmt_del->execute([$response_id_to_delete]);

            $msg_success = "Rekod jawapan murid (" . htmlspecialchars($target_response['nama']) . ") telah berjaya dipadam!";
            log_threat($pdo, 'RESPONSE_DELETED', "Pengguna {$_SESSION['user_email']} ({$_SESSION['user_role']}) telah memadam rekod murid ID #{$response_id_to_delete} ({$target_response['nama']} - {$target_response['email']})");
        } else {
            $msg_error = "Rekod murid tidak ditemui.";
        }
    } catch (PDOException $e) {
        $msg_error = "Ralat pangkalan data semasa memadam rekod murid.";
        log_threat($pdo, 'DB_ERROR', "Ralat SQL padam rekod: " . $e->getMessage());
    }
}

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
// 1. Mengikut Kelas
$kelas_stats_raw = $pdo->query("SELECT kelas, COUNT(*) as cnt FROM responses GROUP BY kelas")->fetchAll();
$kelas_labels = [];
$kelas_counts = [];
foreach ($kelas_stats_raw as $r) {
    $kelas_labels[] = $r['kelas'];
    $kelas_counts[] = (int)$r['cnt'];
}

// 2. Mengikut Status Komen
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
        
        <!-- Carta 1: Taburan Mengikut Kelas -->
        <div class="chart-card">
            <h3 style="font-size:1.25rem; color:#1e1b4b; margin-bottom:15px; display:flex; align-items:center; gap:8px;">
                📊 Statistik Penyertaan Mengikut Kelas
            </h3>
            <div style="position:relative; height:280px;">
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
                        <option value="<?php echo $t; ?>" <?php echo ($filter_tahun === $t) ? 'selected' : ''; ?>>Tahun <?php echo $t; ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="kelas" class="form-control" style="width:140px; padding:8px 14px;">
                    <option value="">-- Semua Kelas --</option>
                    <?php foreach (['Amanah', 'Bestari', 'Cemerlang', 'Dedikasi', 'Efektif', 'Fasih', 'Gigih', 'Hebat', 'Viva', 'Persona'] as $k): ?>
                        <option value="<?php echo $k; ?>" <?php echo ($filter_kelas === $k) ? 'selected' : ''; ?>><?php echo $k; ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn-primary nav-btn" style="padding:8px 16px;">🔍 Tapis</button>
                
                <?php if (!empty($search) || !empty($filter_kelas) || !empty($filter_tahun)): ?>
                    <a href="dashboard.php" class="btn-outline nav-btn" style="padding:8px 14px;">🔄 Set Semula</a>
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
                                <td><span class="badge badge-info">Tahun <?php echo htmlspecialchars($r['tahun']); ?></span></td>
                                <td><span class="badge badge-info"><?php echo htmlspecialchars($r['kelas']); ?></span></td>
                                <td>
                                    <small style="font-weight:700; color:#6366f1;">
                                        <?php echo htmlspecialchars($r['riasec_pilihan'] ?: 'Tiada'); ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if (!empty($r['fail_kerjaya'])): ?>
                                        <a href="../<?php echo htmlspecialchars($r['fail_kerjaya']); ?>" target="_blank" class="badge badge-success" style="text-decoration:none;">
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
                                        <a href="dashboard.php?delete_response=<?php echo $r['id']; ?>" class="btn-outline nav-btn" style="padding:5px 10px; font-size:0.8rem; border-color:#ef4444; color:#ef4444; text-decoration:none;" onclick="return confirm('Adakah anda pasti untuk memadam rekod jawapan murid <?php echo htmlspecialchars(addslashes($r['nama'])); ?>?')">
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
document.addEventListener('DOMContentLoaded', () => {
    // 1. CARTA MENGIKUT KELAS
    const ctxKelas = document.getElementById('chartKelas').getContext('2d');
    new Chart(ctxKelas, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($kelas_labels); ?>,
            datasets: [{
                label: 'Jumlah Murid',
                data: <?php echo json_encode($kelas_counts); ?>,
                backgroundColor: 'rgba(99, 102, 241, 0.75)',
                borderColor: '#4f46e5',
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
        failLink = `<a href="../${data.fail_kerjaya}" target="_blank" style="background:#10b981; color:white; padding:6px 14px; border-radius:8px; text-decoration:none; font-weight:700; display:inline-block; margin-top:4px;">📥 Buka / Muat Turun Fail Kerjaya</a>`;
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
    document.getElementById('modalDeleteBtn').href = "dashboard.php?delete_response=" + data.id;
    openModal('studentDetailModal');
}
</script>

<?php require_once '../includes/footer.php'; ?>
