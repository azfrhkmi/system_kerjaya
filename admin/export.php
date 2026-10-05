<?php
// =========================================================
// SIKRIP EKSPOR DATA REKOD MURID (CSV & STREAMING ZIP GAMBAR)
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

// AMBIL PARAMETER TAPISAN & JENIS EKSPOR
$search = sanitize_input($_GET['search'] ?? '');
$filter_kelas = sanitize_input($_GET['kelas'] ?? '');
$filter_tahun = sanitize_input($_GET['tahun'] ?? '');
$type = strtolower(trim($_GET['type'] ?? 'csv'));

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

// =========================================================
// 1. EKSPOR DATA JAWAPAN MURID SEBAGAI FAIL .CSV
// =========================================================
if ($type === 'csv') {
    if (ob_get_level()) ob_end_clean();

    $filename = "rekod_jawapan_murid_" . date('Y-m-d_His') . ".csv";

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // Hantar UTF-8 BOM untuk memastikan Microsoft Excel Windows membaca tulisan Bahasa Melayu/simbol dengan betul
    fwrite($output, "\xEF\xBB\xBF");

    // Tajuk Kolum CSV
    fputcsv($output, [
        'No',
        'Masa Penyerahan',
        'Nama Murid',
        'E-mel (Primary Key)',
        'Tahun',
        'Kelas',
        'Teori Kecerdasan Howard Gardner',
        'Luahan Rasa Murid',
        'Status Fail Kerjaya',
        'Status Maklum Balas Kaunseling'
    ]);

    $sql = "SELECT id, submitted_at, nama, email, tahun, kelas, riasec_pilihan, luahan_rasa, fail_kerjaya, 
            (fail_kerjaya_blob IS NOT NULL AND CHAR_LENGTH(fail_kerjaya_blob) > 0) AS has_blob, 
            komen_status FROM responses";
    if (count($where_clauses) > 0) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }
    $sql .= " ORDER BY id ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $idx = 1;
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $time_str = !empty($r['submitted_at']) ? date('d/m/Y h:i A', strtotime($r['submitted_at'])) : '-';
        $luahan = str_replace(["\r\n", "\r", "\n"], ' ', $r['luahan_rasa'] ?? '');
        
        $status_fail = 'Tiada Fail';
        if (!empty($r['has_blob']) || !empty($r['fail_kerjaya'])) {
            $status_fail = 'Ada Fail Lampiran';
        }

        fputcsv($output, [
            $idx++,
            $time_str,
            $r['nama'] ?? '',
            $r['email'] ?? '',
            ($r['tahun'] === 'PPKI') ? 'PPKI' : 'Tahun ' . ($r['tahun'] ?? ''),
            $r['kelas'] ?? '',
            $r['riasec_pilihan'] ?? '',
            $luahan,
            $status_fail,
            $r['komen_status'] ?? ''
        ]);
    }

    fclose($output);
    exit;
}

// =========================================================
// 2. EKSPOR SEMUA GAMBAR MURID SEBAGAI FAIL .ZIP (STREAMING LOW-MEMORY)
// =========================================================
if ($type === 'zip') {
    $sql = "SELECT id, nama, tahun, kelas, fail_kerjaya, fail_kerjaya_blob FROM responses";
    if (count($where_clauses) > 0) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }
    $sql .= " ORDER BY id ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $zip_filename = "gambar_murid_kerjaya_" . date('Y-m-d_His') . ".zip";
    $tmp_file = tempnam(sys_get_temp_dir(), 'zip_');
    $file_count = 0;

    if (class_exists('ZipArchive')) {
        $zip = new ZipArchive();
        if ($zip->open($tmp_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $student_name = $r['nama'] ?? 'Murid';
                $clean_nama = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $student_name);
                $clean_nama = preg_replace('/\s+/', '_', trim($clean_nama));
                if (empty($clean_nama)) $clean_nama = "Murid_" . $r['id'];

                $tahun_str = ($r['tahun'] === 'PPKI') ? 'PPKI' : 'Tahun' . preg_replace('/[^a-zA-Z0-9]/', '', $r['tahun'] ?? '');
                $kelas_str = preg_replace('/[^a-zA-Z0-9]/', '', $r['kelas'] ?? '');
                
                $orig_filename = basename($r['fail_kerjaya'] ?? 'gambar.png');
                $ext = strtolower(pathinfo($orig_filename, PATHINFO_EXTENSION));
                if (empty($ext)) $ext = 'png';

                $zip_entry_name = "{$r['id']}_{$clean_nama}_{$tahun_str}_{$kelas_str}.{$ext}";

                $binary_data = null;
                if (!empty($r['fail_kerjaya_blob'])) {
                    $decoded = base64_decode($r['fail_kerjaya_blob']);
                    if ($decoded !== false && strlen($decoded) > 0) {
                        $binary_data = $decoded;
                    }
                }

                if (!$binary_data && !empty($r['fail_kerjaya'])) {
                    $local_path = __DIR__ . '/../' . ltrim($r['fail_kerjaya'], '/');
                    if (file_exists($local_path) && is_file($local_path)) {
                        $binary_data = file_get_contents($local_path);
                    }
                }

                if ($binary_data !== null) {
                    $zip->addFromString($zip_entry_name, $binary_data);
                    $file_count++;
                }

                unset($binary_data, $decoded, $r);
            }
            $zip->close();
        }
    } else {
        // Fallback Penjana ZIP Pure-PHP Secara Streaming ke Fail Disk
        $fp = fopen($tmp_file, 'wb');
        $pure_zip = new PurePhpZipStream($fp);

        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $student_name = $r['nama'] ?? 'Murid';
            $clean_nama = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $student_name);
            $clean_nama = preg_replace('/\s+/', '_', trim($clean_nama));
            if (empty($clean_nama)) $clean_nama = "Murid_" . $r['id'];

            $tahun_str = ($r['tahun'] === 'PPKI') ? 'PPKI' : 'Tahun' . preg_replace('/[^a-zA-Z0-9]/', '', $r['tahun'] ?? '');
            $kelas_str = preg_replace('/[^a-zA-Z0-9]/', '', $r['kelas'] ?? '');
            
            $orig_filename = basename($r['fail_kerjaya'] ?? 'gambar.png');
            $ext = strtolower(pathinfo($orig_filename, PATHINFO_EXTENSION));
            if (empty($ext)) $ext = 'png';

            $zip_entry_name = "{$r['id']}_{$clean_nama}_{$tahun_str}_{$kelas_str}.{$ext}";

            $binary_data = null;
            if (!empty($r['fail_kerjaya_blob'])) {
                $decoded = base64_decode($r['fail_kerjaya_blob']);
                if ($decoded !== false && strlen($decoded) > 0) {
                    $binary_data = $decoded;
                }
            }

            if (!$binary_data && !empty($r['fail_kerjaya'])) {
                $local_path = __DIR__ . '/../' . ltrim($r['fail_kerjaya'], '/');
                if (file_exists($local_path) && is_file($local_path)) {
                    $binary_data = file_get_contents($local_path);
                }
            }

            if ($binary_data !== null) {
                $pure_zip->addFile($zip_entry_name, $binary_data);
                $file_count++;
            }

            unset($binary_data, $decoded, $r);
        }

        $pure_zip->finish();
        fclose($fp);
    }

    if ($file_count === 0) {
        if (file_exists($tmp_file)) @unlink($tmp_file);
        $_SESSION['flash_error'] = "Tiada fail gambar murid ditemui untuk dimuat turun dalam format .ZIP bagi rekod ini.";
        header('Location: dashboard.php');
        exit;
    }

    if (ob_get_level()) ob_end_clean();

    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
    header('Content-Length: ' . filesize($tmp_file));
    header('Pragma: no-cache');
    header('Expires: 0');
    readfile($tmp_file);
    @unlink($tmp_file);
    exit;
}

// =========================================================
// PENJANA ZIP PURE-PHP SECARA STREAMING (EPISEIEN MEMORI)
// =========================================================
class PurePhpZipStream {
    private $stream;
    private $files = [];
    private $offset = 0;

    public function __construct($stream) {
        $this->stream = $stream;
    }

    public function addFile($name, $data) {
        $size = strlen($data);
        $crc = crc32($data);

        $d = getdate();
        $dosTime = ($d['hours'] << 11) | ($d['minutes'] << 5) | ($d['seconds'] >> 1);
        $dosDate = (($d['year'] - 1980) << 9) | ($d['mon'] << 5) | $d['mday'];

        // Local File Header
        $lh = "\x50\x4b\x03\x04\x14\x00\x00\x00\x00\x00";
        $lh .= pack('v', $dosTime) . pack('v', $dosDate);
        $lh .= pack('V', $crc) . pack('V', $size) . pack('V', $size);
        $lh .= pack('v', strlen($name)) . pack('v', 0);
        $lh .= $name;

        fwrite($this->stream, $lh . $data);

        $this->files[] = [
            'name' => $name,
            'size' => $size,
            'crc' => $crc,
            'dosTime' => $dosTime,
            'dosDate' => $dosDate,
            'offset' => $this->offset
        ];

        $this->offset += strlen($lh) + $size;
    }

    public function finish() {
        $cdr = '';
        foreach ($this->files as $file) {
            $cd = "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x00\x00";
            $cd .= pack('v', $file['dosTime']) . pack('v', $file['dosDate']);
            $cd .= pack('V', $file['crc']) . pack('V', $file['size']) . pack('V', $file['size']);
            $cd .= pack('v', strlen($file['name'])) . pack('v', 0) . pack('v', 0) . pack('v', 0) . pack('v', 0);
            $cd .= pack('V', 0) . pack('V', $file['offset']);
            $cd .= $file['name'];
            $cdr .= $cd;
        }

        $cdrLen = strlen($cdr);
        $count = count($this->files);

        // End of Central Directory Record (EOCD)
        $eocd = "\x50\x4b\x05\x06\x00\x00\x00\x00";
        $eocd .= pack('v', $count) . pack('v', $count);
        $eocd .= pack('V', $cdrLen) . pack('V', $this->offset);
        $eocd .= "\x00\x00";

        fwrite($this->stream, $cdr . $eocd);
    }
}
