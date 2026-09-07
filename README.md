# 🎁 Sistem Penerokaan Peti Cheritalah (Sekolah Rendah)

**Sistem Penerokaan Peti Cheritalah** ialah sebuah aplikasi web penerokaan kerjaya yang interaktif, moden, berwarna-warni, dan mesra kanak-kanak yang dibangunkan khas untuk murid-murid sekolah rendah di Malaysia (Tahun 1 hingga 6 serta Program Pendidikan Khas Integrasi - PPKI).

Sistem ini membantu Guru Bimbingan & Kaunseling serta pihak pengurusan sekolah mengenal pasti potensi diri, minat, dan kecerdasan pelbagai murid mengikut **Teori 9 Kecerdasan Pelbagai Howard Gardner** serta integrasi alatan kecerdasan buatan (**Pembantu AI Kerjaya**).

---

## 👥 Peranan Pengguna (3-Tier User Role Architecture)

Sistem ini mengandungi 3 tahap peranan pengguna dengan hak akses yang terperinci:

1. 👦 **Pelajar / Murid (Normal User)**:
   - Meneliti penerangan *"Apa Itu Kerjaya?"* dan 9 jenis kecerdasan pelbagai Teori Howard Gardner.
   - Menyertai sesi Soal Jawab Kerjaya Interaktif di `soal_jawab.php`.
   - Berinteraksi dengan **Pembantu AI Peti Cheritalah** untuk soalan kerjaya dan menjana gambar cita-cita 3D Pixar secara percuma & tanpa had.
   - Muat naik fail hasil tugasan kerjaya daripada portal **DELIMa**.

2. 👨‍🏫 **Admin (Guru Bimbingan & Kaunseling)**:
   - Log masuk melalui Portal Pentadbir (`login.php`).
   - Mengakses Papan Pemuka Statistik (`admin/dashboard.php`).
   - Memantau carta statistik murid mengikut **Kelas** dan **Status Komen / Tindakan Kaunseling** (Chart.js).
   - Membuat carian dan tapisan (filter) mengikut Tahun/PPKI, Kelas, atau Nama/E-mel.
   - Membuka modal maklumat terperinci luahan rasa murid dan memuat turun fail kerjaya murid.
   - **Memadam Rekod Murid**: Memadam rekod murid berserta fail fizikal di server dengan catat log pengesahan.

3. 👑 **Superadmin (Guru Besar / Pentadbir Utama)**:
   - Mengakses Pusat Kawalan Superadmin (`admin/superadmin.php`).
   - **Pengurusan Pentadbir**: Menambah akaun Admin/Kaunselor baharu atau memadam akaun admin sedia ada (dilengkapi perlindungan menghalang pemadaman akaun sendiri).
   - **Peti Ancaman Keselamatan (24/7 Audit Log)**: Memantau log percubaan pencerobohan IP, pencerobohan URL tanpa kebenaran, ralat SQL, dan log keluar masuk sistem.

---

## 🌟 Modul & Ciri-Ciri Utama Sistem

### 1. 🏠 Laman Utama (`index.php`)
- **Hero Banner STEM**: Tajuk *"Terokai Minat & Bina Kerjaya STEM! 🎁"* dengan animasi mikro CSS dan butang CTA pantas.
- **Modul Penerokaan Kerjaya**: Memahami kepentingan cita-cita dan persediaan masa depan sejak sekolah rendah.
- **Peti Explorasi Teori Howard Gardner**: Kad penerangan visual bagi 9 jenis kecerdasan (Verbal-Linguistik, Logik-Matematik, Visual-Ruang, Kinestetik, Muzik, Interpersonal, Intrapersonal, Naturalis, Eksistensial).
- **📱 Kod QR Imbasan Website**: Modal imbasan Kod QR rasmi untuk pembukaan portal menerusi telefon pintar berserta butang muat turun PNG beresolusi tinggi.

### 2. 📝 Borang Soal Jawab Interaktif (`soal_jawab.php`)
- **Seksyen (a) Maklumat Diri**:
  - E-mel Murid *(Primary Key pengecam akaun)*.
  - Nama Penuh Murid.
  - Tahun / Peringkat Persekolahan (Pilihan Kad Radio: `Tahun 1` hingga `Tahun 6` dan `PPKI`).
  - Nama Kelas (Pilihan Kad Radio: `Amanah`, `Bestari`, `Cemerlang`, `Dedikasi`, `Efektif`, `Fasih`, `Gigih`, `Hebat`, `Viva`, `Persona`).
- **Seksyen (b) Ceritalah Luahan Rasa Anda**: Ruangan luahan cerita impian, hobi, atau masalah pembelajaran murid.
- **Seksyen (c) 🎁 Peti Explorasi Kecerdasan Pelbagai**:
  - 9 Butang Interaktif: Tekan mana-mana butang untuk membaca maksud kecerdasan & 5 contoh pekerjaan yang sesuai.
  - Grid Susunan Kemas 3x3 untuk murid menanda kecerdasan pilihan mereka.
- **Seksyen (d) 📁 Muat Naik Fail Kerjaya DELIMa**:
  - Arahan khas penerokaan AI di portal **DELIMa**.
  - Kotak muat naik kemas di tengah (*Centered Upload Box*) menyokong format `PDF`, `DOC`, `DOCX`, `PNG`, `JPG`, `JPEG` (Maksimum 10MB).
- **Seksyen (e) Status Tindakan Kaunseling**:
  - Pilihan maklum balas: *Berpuas hati*, *Perlu bantuan PRS*, atau *Ingin berjumpa guru bimbingan dan kaunseling*.

### 3. 🤖 Pembantu AI Peti Cheritalah (`includes/ai_chat_widget.php` & `assets/js/ai_chat.js` & `api_ai.php`)
- **Widget Terapung (Floating FAB)**: Boleh dibuka pada bila-bila masa di penjuru bawah skrin.
- **Enjin AI Dinamik & Fleksibel (`api_ai.php`)**:
  - Memproses soalan bebas murid mengenai pelbagai kerjaya (*Askar, Doktor, Jurutera, Polis, Bomba, Pilot, Chef, Guru, Pelukis, Saintis, dll.*).
  - Memberikan jawapan terperinci dalam Bahasa Melayu mesra kanak-kanak: Tugas utama, subjek wajib dikuasai di sekolah, kemahiran, dan petua motivasi.
  - Menyokong integrasi kunci API luaran (**Groq Llama-3.3-70B** & **Google Gemini API**).
- **🎨 Penjana Gambar AI 3D Pixar (Pollinations AI)**:
  - Apabila murid menaip *"Jana gambar..."*, AI menjana lukisan inspirasi 3D Pixar berkualiti tinggi berserta butang muat turun gambar PNG.

### 4. 📊 Papan Pemuka Admin (`admin/dashboard.php`)
- **Kad Metrik Ringkasan**: Jumlah penyerahan murid, jumlah yang memerlukan sesi kaunseling, bantuan PRS, dan berpuas hati.
- **Carta Statistik Interaktif (Chart.js)**: Graf bar taburan murid mengikut kelas & graf pai status kaunseling.
- **Borang Carian & Tapisan**: Cari rekod mengikut nama/emel, tapis mengikut Tahun/PPKI dan Kelas.
- **Jadual Rekod & Modal Jawapan**: Paparan lengkap beserta butang muat turun fail murid.
- **Tindakan Pemadaman (🗑️ Padam)**: Memadam rekod murid dari pangkalan data & memadam fail fizikal dari server berserta pengesahan dialog.

### 5. 👑 Pusat Kawalan Superadmin & Audit Log (`admin/superadmin.php`)
- **Pengurusan Pentadbir**: Tambah akaun admin baharu dan padam akaun admin sedia ada.
- **Peti Ancaman Keselamatan 24/7**: Memantau audit log aktiviti keselamatan seperti `ADMIN_LOGIN_SUCCESS`, `UNAUTHORIZED_ACCESS`, `RESPONSE_DELETED`, `FAILED_LOGIN`, dan `SYSTEM_INIT`.

---

## 🛠️ Senibina Pangkalan Data (Dual-Engine Automatic Fallback)

Sistem ini dibina dengan **Dual-Engine Database Configuration** (`config/db.php`) untuk menjamin 100% ketersediaan tanpa ralat (*Zero Downtime Deployment*):

1. **Mod Utama (MySQL)**:
   - Menyambung ke pelayan MySQL (XAMPP / Railway / DBeaver).
   - Menginisialisasi skrip `database.sql` secara automatik jika jadual belum wujud.
2. **Mod Sandaran Automatik (SQLite Fallback)**:
   - Jika pelayan MySQL tidak disambungkan (*connection refused*), sistem secara automatik bertukar ke pangkalan data terbenam SQLite (`sistem_kerjaya.sqlite`).
   - Mencipta jadual dan mengisikan data ujian (*initial seed data*) secara automatik tanpa memerlukan persediaan manual!

### Struktur Jadual Utama:
- **`users`**: `id`, `nama`, `email` (Unique), `password` (BCRYPT Hashed), `role` (`admin`/`superadmin`), `created_at`.
- **`responses`**: `id`, `email`, `nama`, `tahun`, `kelas`, `luahan_rasa`, `riasec_pilihan`, `fail_kerjaya`, `komen_status`, `submitted_at`.
- **`security_logs`**: `id`, `ip_address`, `event_type`, `description`, `user_agent`, `created_at`.

---

## 🔒 Keselamatan & Perlindungan Data

- **Penyulitan Kata Laluan**: Menggunakan fungsi `password_hash()` berasaskan algoritma `BCRYPT`.
- **Perlindungan SQL Injection**: Semua pertanyaan pangkalan data menggunakan `PDO Prepared Statements`.
- **Sanitasi XSS**: Input diproses menggunakan `htmlspecialchars()` dan pembersihan corak skrip berbahaya.
- **Kawalan Sesi (Session Control)**: Menyemak status `user_role` pada setiap halaman pentadbir.

---

## 🛠️ Pemasangan & Pelancaran Lokal (XAMPP)

1. Pastikan **Apache** dan **MySQL** dihidupkan di XAMPP Control Panel.
2. Salin folder projek ke lokasi htdocs XAMPP:
   ```bash
   C:\xampp\htdocs\kerjaya_saya
   ```
3. Import fail `database.sql` ke MySQL (Opsional, `config/db.php` akan menginisialisasi pangkalan data secara automatik).
4. Atau jalankan pelayan PHP CLI terus dari terminal:
   ```bash
   php -S 127.0.0.1:8000
   ```
5. Buka pelayar web dan layari:
   `http://127.0.0.1:8000/index.php`

---

## 🚀 Pelancaran Awam & Cloud (Railway / Docker)

- Laman Web Rasmi Deployment: `https://systemkerjaya-production.up.railway.app/index.php`
- Projek dilengkapi dengan `Dockerfile` berasaskan `php:8.2-cli` yang serasi untuk pelancaran kontena Docker & Railway.

---

## 🔑 Maklumat Log Masuk Pentadbir Awalan

| Peranan (Role) | E-mel Pentadbir | Kata Laluan | Halaman Akses |
| :--- | :--- | :--- | :--- |
| 👑 **Superadmin** | `superadmin@kerjaya.edu.my` | `super123` | `admin/superadmin.php` & `admin/dashboard.php` |
| 🔐 **Admin Kaunselor 1** | `admin@kerjaya.edu.my` | `admin123` | `admin/dashboard.php` |
| 🔐 **Admin Kaunselor 2** | `kaunseling@kerjaya.edu.my` | `admin123` | `admin/dashboard.php` |

---

© 2026 **Sistem Penerokaan Peti Cheritalah Sekolah Rendah** • Unit Bimbingan dan Kaunseling SKPP 8 (2).
