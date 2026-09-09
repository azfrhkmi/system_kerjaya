<?php
$page_title = "Laman Utama Penerokaan Kerjaya";
require_once 'config/db.php';
require_once 'includes/header.php';
?>

<!-- HERO BANNER SECTION -->
<section class="hero-section">
    <div class="hero-badge">
        <span>🌟 Ujian Minat & Kecerdasan Pelbagai Murid</span>
    </div>
    
    <h1 class="hero-title">
        Terokai Minat & <span class="highlight">Bina Kerjaya STEM!</span> 🎁
    </h1>
    
    <p class="hero-subtitle">
        Selamat datang ke Sistem Penerokaan Kerjaya Sekolah Rendah. Mari kenali potensi diri, kecerdasan pelbagai, dan kerjaya pilihan yang paling sesuai dengan impian anda!
    </p>

    <div style="display:flex; justify-content:center; gap:16px; margin-top:25px; flex-wrap:wrap;">
        <a href="soal_jawab.php" class="btn-cta-big">
            ✨ Tekan Untuk Sertai Soal Jawab Kerjaya!
        </a>
    </div>
</section>

<div class="container">

    <!-- SEKSYEN 1: KERJAYA STEM -->
    <section class="section-block">
        <div class="section-header">
            <h2>🌈 Kerjaya STEM</h2>
            <p>Memahami dunia STEM (Sains, Teknologi, Kejuruteraan & Matematik) dan persediaan kerjaya masa depan murid</p>
        </div>

        <div style="background: white; border-radius: var(--radius-lg); padding: 30px 24px; box-shadow: var(--shadow-soft); border: 2px solid #e0e7ff; margin-bottom: 24px;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="font-size: 1.4rem; color: var(--primary); margin-bottom: 8px;">
                    🎯 Pilih Huruf STEM Di Bawah Untuk Menerokai Maksud & 10 Pekerjaan Berkaitan:
                </h3>
                <p style="color: var(--text-muted); font-size: 0.95rem;">
                    Tekan mana-mana butang S, T, E, atau M untuk melihat contoh bidang & cita-cita hebat!
                </p>
            </div>

            <!-- BUTANG MELINTANG S, T, E, M -->
            <div class="stem-buttons-wrapper">
                <button type="button" class="stem-btn stem-btn-s active" onclick="showStemDetail('S')">🧬 S - Sains</button>
                <button type="button" class="stem-btn stem-btn-t" onclick="showStemDetail('T')">💻 T - Teknologi</button>
                <button type="button" class="stem-btn stem-btn-e" onclick="showStemDetail('E')">⚙️ E - Kejuruteraan</button>
                <button type="button" class="stem-btn stem-btn-m" onclick="showStemDetail('M')">🧮 M - Matematik</button>
            </div>

            <!-- BEKAS PAPARAN DETAIL STEM -->
            <div id="stemDisplay" class="stem-detail-display active" style="margin-top: 24px;"></div>
        </div>
    </section>

    <!-- SEKSYEN 2: TEORI HOWARD GARDNER (9 KECERDASAN PELBAGAI INTERAKTIF) -->
    <section class="section-block">
        <div class="section-header">
            <h2>🎁 Teori Kecerdasan Pelbagai Howard Gardner</h2>
            <p>Tekan mana-mana teori di bawah untuk membaca penerangan terperinci & 10 pekerjaan yang sesuai:</p>
        </div>

        <div class="gardner-interactive-container">
            <div class="gardner-buttons-wrapper">
                <button type="button" class="gardner-btn gardner-btn-verbal" onclick="showGardnerDetail('verbal')">📚 Verbal-Linguistik</button>
                <button type="button" class="gardner-btn gardner-btn-logik" onclick="showGardnerDetail('logik')">🔢 Logik-Matematik</button>
                <button type="button" class="gardner-btn gardner-btn-visual" onclick="showGardnerDetail('visual')">🎨 Visual-Ruang</button>
                <button type="button" class="gardner-btn gardner-btn-kinestetik" onclick="showGardnerDetail('kinestetik')">⚽ Kinestetik</button>
                <button type="button" class="gardner-btn gardner-btn-muzik" onclick="showGardnerDetail('muzik')">🎵 Muzik</button>
                <button type="button" class="gardner-btn gardner-btn-interpersonal" onclick="showGardnerDetail('interpersonal')">🤝 Interpersonal</button>
                <button type="button" class="gardner-btn gardner-btn-intrapersonal" onclick="showGardnerDetail('intrapersonal')">🧘 Intrapersonal</button>
                <button type="button" class="gardner-btn gardner-btn-naturalis" onclick="showGardnerDetail('naturalis')">🌿 Naturalis</button>
                <button type="button" class="gardner-btn gardner-btn-eksistensial" onclick="showGardnerDetail('eksistensial')">🌌 Eksistensial</button>
            </div>

            <!-- Bekas Paparan Detail Teori Howard Gardner -->
            <div id="gardnerDisplay" class="gardner-detail-display" style="margin-top:24px;"></div>
        </div>
    </section>

    <!-- SEKSYEN 3: SENARAI PILIHAN SEKOLAH MENENGAH TERUNGGUL (TOP 10 MALAYSIA) -->
    <section class="section-block">
        <div class="section-header">
            <h2>🏫 Senarai Pilihan Sekolah Menengah Terunggul di Malaysia</h2>
            <p>Antara 10 Sekolah Berasrama Penuh (SBP) & MRSM Terbaik (Keputusan SPM / GPS 2025) yang menjadi inspirasi murid!</p>
        </div>

        <div class="school-card-grid">
            
            <!-- 1. MRSM Tun Ghafar Baba -->
            <div class="school-card school-card-gold">
                <div class="school-rank-badge">🥇 Kedudukan #1</div>
                <div class="school-icon-box" style="background:#fef3c7; color:#b45309;">🏛️</div>
                <h3 class="school-title">MRSM Tun Ghafar Baba</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Melaka</span>
                    <span class="school-tag tag-category">MRSM</span>
                </div>
                <div class="school-gps">📊 GPS 2025: <strong>1.139</strong></div>
            </div>

            <!-- 2. MRSM Gemencheh -->
            <div class="school-card school-card-silver">
                <div class="school-rank-badge">🥈 Kedudukan #2</div>
                <div class="school-icon-box" style="background:#e0e7ff; color:#3730a3;">🏫</div>
                <h3 class="school-title">MRSM Gemencheh</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Negeri Sembilan</span>
                    <span class="school-tag tag-category">MRSM</span>
                </div>
                <div class="school-gps">📊 GPS 2025: <strong>1.195</strong></div>
            </div>

            <!-- 3. MRSM Taiping -->
            <div class="school-card school-card-bronze">
                <div class="school-rank-badge">🥉 Kedudukan #3</div>
                <div class="school-icon-box" style="background:#ffedd5; color:#c2410c;">🏫</div>
                <h3 class="school-title">MRSM Taiping</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Perak</span>
                    <span class="school-tag tag-category">MRSM</span>
                </div>
                <div class="school-gps">📊 GPS 2025: <strong>1.224</strong></div>
            </div>

            <!-- 4. MRSM Pengkalan Chepa -->
            <div class="school-card">
                <div class="school-rank-badge rank-normal">⭐ Kedudukan #4</div>
                <div class="school-icon-box" style="background:#dbeafe; color:#1d4ed8;">🎓</div>
                <h3 class="school-title">MRSM Pengkalan Chepa</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Kelantan</span>
                    <span class="school-tag tag-category">MRSM</span>
                </div>
                <div class="school-gps">📊 GPS 2025: <strong>1.254</strong></div>
            </div>

            <!-- 5. MRSM Kuala Kubu Bharu -->
            <div class="school-card">
                <div class="school-rank-badge rank-normal">⭐ Kedudukan #5</div>
                <div class="school-icon-box" style="background:#ede9fe; color:#6d28d9;">📚</div>
                <h3 class="school-title">MRSM Kuala Kubu Bharu</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Selangor</span>
                    <span class="school-tag tag-category">MRSM</span>
                </div>
                <div class="school-gps">📊 GPS 2025: <strong>1.284</strong></div>
            </div>

            <!-- 6. Sekolah Tun Fatimah (STF) -->
            <div class="school-card">
                <div class="school-rank-badge rank-normal">⭐ Kedudukan #6</div>
                <div class="school-icon-box" style="background:#fce7f3; color:#be185d;">👑</div>
                <h3 class="school-title">Sekolah Tun Fatimah (STF)</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Johor</span>
                    <span class="school-tag tag-category">SBP Elite</span>
                </div>
                <div class="school-gps">📊 GPS 2025: <strong>1.380</strong></div>
            </div>

            <!-- 7=. SMS Sultan Mahmud (SESMA) -->
            <div class="school-card">
                <div class="school-rank-badge rank-normal">⭐ Kedudukan #7=</div>
                <div class="school-icon-box" style="background:#ccfbf1; color:#0f766e;">🏛️</div>
                <h3 class="school-title">SMS Sultan Mahmud (SESMA)</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Terengganu</span>
                    <span class="school-tag tag-category">SMS / SBP</span>
                </div>
                <div class="school-gps">📊 GPS 2025: <strong>1.470</strong></div>
            </div>

            <!-- 7=. Sekolah Sultan Alam Shah (SAS) -->
            <div class="school-card">
                <div class="school-rank-badge rank-normal">⭐ Kedudukan #7=</div>
                <div class="school-icon-box" style="background:#fef3c7; color:#b45309;">🌟</div>
                <h3 class="school-title">Sekolah Sultan Alam Shah (SAS)</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Putrajaya</span>
                    <span class="school-tag tag-category">SBP Elite</span>
                </div>
                <div class="school-gps">📊 GPS 2025: <strong>1.470</strong></div>
            </div>

            <!-- 9=. SMS Tuanku Munawir (SASER) -->
            <div class="school-card">
                <div class="school-rank-badge rank-normal">⭐ Kedudukan #9=</div>
                <div class="school-icon-box" style="background:#d1fae5; color:#047857;">🎓</div>
                <h3 class="school-title">SMS Tuanku Munawir (SASER)</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Negeri Sembilan</span>
                    <span class="school-tag tag-category">SMS / SBP</span>
                </div>
                <div class="school-gps">📊 GPS 2025: <strong>1.530</strong></div>
            </div>

            <!-- 9=. Kolej Islam Sultan Alam Shah (KISAS) -->
            <div class="school-card">
                <div class="school-rank-badge rank-normal">⭐ Kedudukan #9=</div>
                <div class="school-icon-box" style="background:#fee2e2; color:#b91c1c;">🌙</div>
                <h3 class="school-title">Kolej Islam Sultan Alam Shah (KISAS)</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Selangor</span>
                    <span class="school-tag tag-category">KISAS / SMKA</span>
                </div>
                <div class="school-gps">📊 GPS 2025: <strong>1.530</strong></div>
            </div>

        </div>
    </section>

    <!-- SEKSYEN 4: SERTAI SOAL JAWAB (BOTTOM CTA) -->
    <section class="cta-banner">
        <h2>Adakah Anda Bersedia Terokai Cita-Cita Anda? 🌟</h2>
        <p>Isi borang soal jawab kerjaya sekarang untuk berkongsi minat bersama Guru Bimbingan & Kaunseling sekolah anda!</p>
        <a href="soal_jawab.php" class="btn-cta-big">
            🎁 Tekan Untuk Sertai Soal Jawab Kerjaya
        </a>
    </section>

</div>

<?php require_once 'includes/footer.php'; ?>
