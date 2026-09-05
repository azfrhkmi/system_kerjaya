<?php
// =========================================================
// SECURITY THREAT & AUDIT LOGGER
// =========================================================

if (!function_exists('log_threat')) {
    function log_threat($pdo, $event_type, $description) {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown User-Agent';

            $stmt = $pdo->prepare("INSERT INTO security_logs (ip_address, event_type, description, user_agent) VALUES (?, ?, ?, ?)");
            $stmt->execute([$ip, $event_type, $description, $user_agent]);
        } catch (Exception $e) {
            // Abaikan jika pencatatan log gagal supaya tidak memutuskan aliran utama
            error_log("Failed to log threat: " . $e->getMessage());
        }
    }
}

if (!function_exists('sanitize_input')) {
    function sanitize_input($data) {
        if (is_array($data)) {
            return array_map('sanitize_input', $data);
        }
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
}
