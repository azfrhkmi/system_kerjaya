# 📘 PANDUAN PELAN NAIK TARAF: SISTEM KERJAYA MULTI-SEKOLAH (STANDALONE LOCALHOST PACKAGE)

Dokumen ini merupakan **cetak biru (blueprint) dan panduan terperinci** untuk menukarkan **Sistem Penerokaan e-petiK** daripada sistem khusus satu sekolah kepada **Pakej Perisian Komersial Multi-Sekolah** yang boleh dipasang dan disesuaikan di pelbagai sekolah secara *Standalone Localhost* (komputer bilik UBK).

---

## 🎯 OBJEKTIF NAIK TARAF

1. **Pengubahsuaian Jenama Sekolah (White-Label Branding)**:
   Mana-mana sekolah yang membeli perisian boleh menukar **Nama Sekolah**, **Nama Unit**, **Logo Sekolah**, dan **Senarai Nama Kelas** tanpa menyentuh sebarang kod programming.
2. **Pengurusan Tetapan Terpusat (`admin/settings.php`)**:
   Menyediakan halaman tetapan di panel Superadmin/Admin untuk mengemaskini maklumat profil sekolah secara visual.
3. **Senibina Pangkalan Data Dinamik (`system_settings`)**:
   Menyimpan konfigurasi sekolah dalam pangkalan data MySQL / SQLite supaya header, footer, borang soal jawab, dan sijil/laporan membaca maklumat sekolah secara automatik.

---

## 🏗️ PELAN REKA BENTUK TEKNIKAL

### 1. Struktur Jadual Pangkalan Data Baharu (`system_settings`)
```sql
CREATE TABLE IF NOT EXISTS `system_settings` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `nama_sekolah` VARCHAR(255) NOT NULL DEFAULT 'SK Putrajaya Presint 8 (2)',
    `kod_sekolah` VARCHAR(50) NOT NULL DEFAULT 'SKPP8(2)',
    `nama_unit` VARCHAR(255) NOT NULL DEFAULT 'Unit Bimbingan dan Kaunseling',
    `logo_sekolah` VARCHAR(255) NULL,
    `senarai_kelas` TEXT NOT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 2. Modul Halaman Tetapan Baharu (`admin/settings.php`)
* **Medan Borang**:
  - Nama Penuh Sekolah (Contoh: `SK Seri Kembangan`)
  - Kod Sekolah / Singkatan (Contoh: `SKSK`)
  - Nama Unit / Kelab (Contoh: `Unit Bimbingan dan Kaunseling`)
  - Muat Naik Logo Sekolah (`.png`, `.jpg`)
  - Senarai Nama Kelas (Dipisahkan dengan koma, contoh: `Amanah, Bestari, Cemerlang, Dedikasi`)
* **Kawalan Akses**: Hanya pengguna berperanan `superadmin` / `admin` sahaja.

### 3. Dinamikkan Elemen Antaramuka (UI Dynamic Integration)
* **[includes/footer.php](file:///c:/Users/azfrh/OneDrive/Desktop/kerjaya_saya/includes/footer.php)**:
  Teks hak cipta footer digantikan daripada *hardcoded* kepada:
  ```php
  © <?php echo date('Y'); ?> Hak Cipta Terpelihara • <?php echo htmlspecialchars($setting['nama_unit']); ?> <?php echo htmlspecialchars($setting['nama_sekolah']); ?>
  ```
* **[soal_jawab.php](file:///c:/Users/azfrh/OneDrive/Desktop/kerjaya_saya/soal_jawab.php)**:
  Pilihan nama kelas pada Seksyen (a) dimuatkan secara dinamik daripada pembolehubah `senarai_kelas` dalam pangkalan data.

---

## 🤖 COMMAND PROMPT UNTUK DIHANTAR KEPADA AI PADA MASA HADAPAN

Apabila anda bersedia untuk membina ciri ini pada masa akan datang, anda hanya perlu **salin dan tampal (copy-paste)** prompt di bawah terus kepada saya:

```text
Tolong laksanakan pelan naik taraf Multi-Sekolah berasaskan panduan PANDUAN_NAIK_TARAF_MULTI_SEKOLAH.md:

1. Cipta jadual `system_settings` dalam config/db.php dan database.sql (dengan nilai awalan default).
2. Bina halaman `admin/settings.php` untuk Superadmin/Admin menyunting:
   - Nama Sekolah
   - Nama Unit Bimbingan & Kaunseling
   - Logo Sekolah (Muat naik fail)
   - Senarai Nama Kelas (Dipisahkan dengan koma)
3. Kemaskini `includes/footer.php` dan `includes/header.php` supaya membaca Nama Sekolah, Nama Unit, dan Logo secara dinamik dari database `system_settings`.
4. Kemaskini `soal_jawab.php` supaya pilihan Pilihan Kad Radio Nama Kelas dijana secara dinamik mengikut senarai kelas dari `system_settings`.
5. Uji dan pastikan tiada syntax error serta push ke GitHub.
```

---

## 📦 PANDUAN BUNGKUSAN JUALAN (COMMERCIAL DISTRIBUTION GUIDE)

Apabila anda menjual sistem ini kepada sekolah lain:

1. **Format Agihan**:
   - Salin keseluruhan folder projek `kerjaya_saya` dan zipkan sebagai `e-petiK_Pakej_UBK_v1.0.zip`.
2. **Pemasangan di Sekolah**:
   - Pihak sekolah hanya perlu memuat turun dan mengesrak zip ke folder `C:\xampp\htdocs\kerjaya_saya`.
   - Buka pelayar web di Komputer Bilik UBK: `http://localhost/kerjaya_saya`.
3. **Pengubahsuaian Pertama**:
   - Guru Kaunseling sekolah tersebut log masuk ➔ Buka **Profil & Tetapan Sekolah** (`admin/settings.php`) ➔ Tukar Nama Sekolah & Logo ➔ Klik **Simpan**.
   - Seluruh sistem akan bertukar imej serta jenama mengikut sekolah mereka secara automatik!
