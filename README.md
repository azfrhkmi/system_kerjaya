# 🚀 Sistem Penerokaan Kerjaya Cherita (Sekolah Rendah)

Sistem Penerokaan Kerjaya Sekolah Rendah yang interaktif, menarik, dan mesra kanak-kanak dibangunkan dengan **PHP 8**, **MySQL / SQLite**, dan **Pembantu AI**. Sistem ini menyokong 3 peranan pengguna (**Pelajar**, **Admin Guru Kaunseling**, dan **Superadmin**).

---

## 🌟 Ciri-Ciri Utama

1. **Laman Utama & Hero Section**:
   - Reka bentuk berwarna-warni dengan animasi mikro CSS.
   - Penjelasan *"Apa itu Kerjaya?"* untuk sekolah rendah.
   - **Teori Howard Gardner (9 Kecerdasan Pelbagai)**.
   - **Pembantu AI Kerjaya Cherita** (Tanya AI & Jana Gambar Cita-cita 3D Pixar secara percuma & tanpa had).
   - Butang animasi CTA *"🚀 Tekan Untuk Sertai Soal Jawab Kerjaya"*.

2. **Soal Jawab Pelajar (Normal User)**:
   - Isian **E-mel** sebagai Primary Key pengecam akaun.
   - Maklumat Diri: Nama, Tahun (1-6, PPKI), Nama Kelas (Amanah, Bestari, Cemerlang, Dedikasi, Efektif, Fasih, Gigih, Hebat, Viva, Persona).
   - Luahan Rasa (Ruangan cerita/impian).
   - Minat RIASEC & Status Komen/Tindakan Kaunseling.

3. **Papan Pemuka Admin Guru Kaunseling**:
   - Carta statistik interaktif (Chart.js) mengikut **Kelas** dan **Status Komen**.
   - Senarai rekod jawapan murid dengan tapisan (Filter) & carian.
   - Modal paparan terperinci luahan rasa murid.

4. **Pusat Kawalan Superadmin**:
   - Tambah & Padam akaun Admin.
   - **Peti Ancaman Keselamatan (Security Threat Audit Log)** 24/7.

---

## 🛠️ Persediaan & Pemasangan (XAMPP)

1. Pastikan **Apache** & **MySQL** dihidupkan di XAMPP Control Panel.
2. Import fail `database.sql` ke MySQL (atau jalankan aplikasi secara terus, `config/db.php` akan menginisialisasi pangkalan data `sistem_kerjaya` secara automatik).
3. Salin projek ke `C:\xampp\htdocs\kerjaya_saya` atau jalankan pelayan PHP:
   ```bash
   php -S 127.0.0.1:8000
   ```
4. Buka pelayar web dan layari `http://127.0.0.1:8000/index.php`.

---

## 🔑 Maklumat Log Masuk Pentadbir

- **Superadmin**: `superadmin@kerjaya.edu.my` | Kata Laluan: `super123`
- **Admin**: `admin@kerjaya.edu.my` | Kata Laluan: `admin123`
