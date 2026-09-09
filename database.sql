-- =========================================================
-- SYSTEM KERJAYA SEKOLAH RENDAH - SCHEMA & INITIAL DATA
-- =========================================================

CREATE DATABASE IF NOT EXISTS `sistem_kerjaya` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sistem_kerjaya`;

-- 1. Jadual Users (Superadmin & Admin)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nama` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'superadmin') NOT NULL DEFAULT 'admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Jadual Submisi Pelajar (Responses)
CREATE TABLE IF NOT EXISTS `responses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL,
    `nama` VARCHAR(255) NOT NULL,
    `tahun` VARCHAR(20) NOT NULL,
    `kelas` VARCHAR(50) NOT NULL,
    `luahan_rasa` TEXT NULL,
    `riasec_pilihan` VARCHAR(255) NULL,
    `fail_kerjaya` VARCHAR(255) NULL,
    `fail_kerjaya_blob` LONGTEXT NULL,
    `komen_status` VARCHAR(255) NOT NULL,
    `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Jadual Log Keselamatan & Ancaman (Security Threat Audit Logs)
CREATE TABLE IF NOT EXISTS `security_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ip_address` VARCHAR(45) NOT NULL,
    `event_type` VARCHAR(100) NOT NULL,
    `description` TEXT NOT NULL,
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- DATA AWAL (INITIAL SEEDING)
-- =========================================================

-- Masukkan Akaun Superadmin & Admin Utama (jika belum wujud)
INSERT INTO `users` (`nama`, `email`, `password`, `role`) VALUES
('Guru Besar (Superadmin)', 'superadmin@kerjaya.edu.my', '$2y$10$D7TyuJsZuZw7KOe94NyL7uDAdW5vu9CxO2XnAYciL9Rnsj5FPCzJO', 'superadmin'),
('Cikgu Aishah (Kaunselor Utama)', 'admin@kerjaya.edu.my', '$2y$10$dwPAV/ZM5ZbYppc2SulV.erkQrgn1aDyvq616FvqXVJ1/0y3VczN6', 'admin'),
('Cikgu Amirul (Guru Bimbingan)', 'kaunseling@kerjaya.edu.my', '$2y$10$dwPAV/ZM5ZbYppc2SulV.erkQrgn1aDyvq616FvqXVJ1/0y3VczN6', 'admin')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Masukkan Log Keselamatan Awalan
INSERT INTO `security_logs` (`ip_address`, `event_type`, `description`, `user_agent`) VALUES
('127.0.0.1', 'SYSTEM_INIT', 'Sistem Penerokaan Kerjaya berjaya dilancarkan dan pangkalan data diinisialisasi.', 'System Auto Log');
