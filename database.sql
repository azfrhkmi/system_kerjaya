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

-- Masukkan Sample Submisi Pelajar untuk Paparan Statistik
INSERT INTO `responses` (`email`, `nama`, `tahun`, `kelas`, `luahan_rasa`, `riasec_pilihan`, `komen_status`, `submitted_at`) VALUES
('adam.haris@student.edu.my', 'Adam Haris bin Azman', '6', 'Bestari', 'Saya suka membaiki mainan dan basikal di rumah. Cita-cita saya nak jadi Jurutera Mekanikal yang hebat!', 'Logik-Matematik, Kinestetik', 'Berpuas hati', NOW() - INTERVAL 5 DAY),
('nur.sara@student.edu.my', 'Nur Sara Damia', '5', 'Amanah', 'Saya suka bercakap dan bantu kawan-kawan yang sedih. Saya ingin menjadi Guru atau Kaunselor Sekolah.', 'Interpersonal, Verbal-Linguistik', 'Berpuas hati', NOW() - INTERVAL 4 DAY),
('muhd.iqbal@student.edu.my', 'Muhammad Iqbal', '4', 'Cemerlang', 'Kadang-kadang saya rasa agak sukar nak fokus belajar matematik. Saya perlukan bimbingan cara belajar yang betul.', 'Logik-Matematik, Visual-Ruang', 'Perlu bantuan PRS', NOW() - INTERVAL 3 DAY),
('siti.aishah@student.edu.my', 'Siti Aishah Binti Zulkifli', '6', 'Dedikasi', 'Saya sangat berminat berniaga kek dan biskut. Saya ingin jadi usahawan berjaya suatu hari nanti!', 'Interpersonal, Intrapersonal', 'Berpuas hati', NOW() - INTERVAL 2 DAY),
('daniel.hakim@student.edu.my', 'Daniel Hakim', '3', 'Efektif', 'Saya rasa takut bila fikir tentang masa depan dan rasa kurang berkeyakinan di kelas.', 'Kinestetik, Naturalis', 'Ingin berjumpa guru bimbingan dan kaunseling', NOW() - INTERVAL 1 DAY),
('amira.yasmin@student.edu.my', 'Amira Yasmin', 'PPKI', 'Viva', 'Saya suka lukis gambar alam semula jadi dan dengar muzik di kelas seni.', 'Visual-Ruang, Muzik', 'Berpuas hati', NOW());

-- Masukkan Log Keselamatan Awalan
INSERT INTO `security_logs` (`ip_address`, `event_type`, `description`, `user_agent`) VALUES
('127.0.0.1', 'SYSTEM_INIT', 'Sistem Penerokaan Kerjaya berjaya dilancarkan dan pangkalan data diinisialisasi.', 'System Auto Log');
