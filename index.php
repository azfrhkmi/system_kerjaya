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

    <!-- SEKSYEN 1: APA ITU KERJAYA? -->
    <section class="section-block">
        <div class="section-header">
            <h2>🌈 Apa Itu Kerjaya?</h2>
            <p>Memahami dunia pekerjaan dan persediaan masa depan sejak sekolah rendah</p>
        </div>

        <div style="background: white; border-radius: var(--radius-lg); padding: 36px; box-shadow: var(--shadow-soft); border: 2px solid #e0e7ff; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; align-items: center;">
            <div>
                <h3 style="font-size: 1.6rem; color: var(--primary); margin-bottom: 14px;">
                    🎯 Kenapa Kita Perlu Tahu Cita-Cita?
                </h3>
                <p style="font-size: 1.05rem; color: var(--text-muted); line-height: 1.7; margin-bottom: 16px;">
                    <strong>Kerjaya</strong> ialah pekerjaan atau lapangan aktiviti yang dipilih oleh seseorang untuk membina masa depan mereka. Setiap orang mempunyai bakat, minat, dan kecerdasan yang unik!
                </p>
                <p style="font-size: 1.05rem; color: var(--text-muted); line-height: 1.7;">
                    Dengan mengetahui minat seawal sekolah rendah, anda boleh belajar dengan lebih bersemangat, mengasah bakat semula jadi, dan mencapai kejayaan yang cemerlang di dunia & akhirat!
                </p>
            </div>
            
            <div style="background: linear-gradient(135deg, #e0e7ff, #fbcfe8); border-radius: 20px; padding: 24px; text-align: center;">
                <div style="font-size: 4rem; margin-bottom: 10px;">🏆</div>
                <h4 style="font-size: 1.3rem; color: #3730a3; margin-bottom: 8px;">Impian Anda, Masa Depan Anda!</h4>
                <p style="font-size: 0.95rem; color: #4338ca;">
                    "Setiap anak adalah bintang yang bersinar mengikut bakat tersendiri."
                </p>
            </div>
        </div>
    </section>

    <!-- SEKSYEN 2: TEORI HOWARD GARDNER (9 KECERDASAN PELBAGAI) -->
    <section class="section-block">
        <div class="section-header">
            <h2>🎁 Teori Kecerdasan Pelbagai Howard Gardner</h2>
            <p>Terdapat 9 jenis kecerdasan pelbagai yang diguna pakai dalam sistem pendidikan:</p>
        </div>

        <div class="gardner-grid">
            
            <!-- 1. Verbal-Linguistik -->
            <div class="gardner-card" style="--card-color-1:#3b82f6; --card-color-2:#60a5fa; --icon-bg:#dbeafe;">
                <div class="gardner-icon">📚</div>
                <h3>Verbal-Linguistik</h3>
                <p>Kebolehan menggunakan bahasa, kata-kata, dan perkataan secara berkesan sama ada secara lisan atau tulisan.</p>
            </div>

            <!-- 2. Logik-Matematik -->
            <div class="gardner-card" style="--card-color-1:#6366f1; --card-color-2:#818cf8; --icon-bg:#e0e7ff;">
                <div class="gardner-icon">🔢</div>
                <h3>Logik-Matematik</h3>
                <p>Kebolehan berfikir secara rasional, menganalisis masalah, dan menyelesaikan pengiraan nombor atau logik.</p>
            </div>

            <!-- 3. Visual-Ruang -->
            <div class="gardner-card" style="--card-color-1:#ec4899; --card-color-2:#f472b6; --icon-bg:#fce7f3;">
                <div class="gardner-icon">🎨</div>
                <h3>Visual-Ruang</h3>
                <p>Kebolehan mengesan, membayangkan, dan menterjemahkan dunia visual serta ruang secara grafik.</p>
            </div>

            <!-- 4. Kinestetik -->
            <div class="gardner-card" style="--card-color-1:#ef4444; --card-color-2:#f87171; --icon-bg:#fee2e2;">
                <div class="gardner-icon">⚽</div>
                <h3>Kinestetik</h3>
                <p>Kebolehan menggunakan seluruh anggota badan untuk melahirkan idea, perasaan, atau menyelesaikan masalah (pergerakan fizikal).</p>
            </div>

            <!-- 5. Muzik -->
            <div class="gardner-card" style="--card-color-1:#8b5cf6; --card-color-2:#a78bfa; --icon-bg:#ede9fe;">
                <div class="gardner-icon">🎵</div>
                <h3>Muzik</h3>
                <p>Kebolehan mengesan irama, melodi, nada, dan bunyi serta menghargai seni muzik.</p>
            </div>

            <!-- 6. Interpersonal -->
            <div class="gardner-card" style="--card-color-1:#f59e0b; --card-color-2:#fbbf24; --icon-bg:#fef3c7;">
                <div class="gardner-icon">🤝</div>
                <h3>Interpersonal</h3>
                <p>Kebolehan memahami, menyelami, dan berinteraksi secara berkesan dengan orang lain.</p>
            </div>

            <!-- 7. Intrapersonal -->
            <div class="gardner-card" style="--card-color-1:#14b8a6; --card-color-2:#2dd4bf; --icon-bg:#ccfbf1;">
                <div class="gardner-icon">🧘</div>
                <h3>Intrapersonal</h3>
                <p>Kebolehan memahami diri sendiri, emosi, kekuatan, kelemahan, serta matlamat peribadi.</p>
            </div>

            <!-- 8. Naturalis -->
            <div class="gardner-card" style="--card-color-1:#10b981; --card-color-2:#34d399; --icon-bg:#d1fae5;">
                <div class="gardner-icon">🌿</div>
                <h3>Naturalis</h3>
                <p>Kebolehan mengenali, menghargai, dan memahami alam semula jadi, flora, serta fauna.</p>
            </div>

            <!-- 9. Eksistensial -->
            <div class="gardner-card" style="--card-color-1:#64748b; --card-color-2:#94a3b8; --icon-bg:#f1f5f9;">
                <div class="gardner-icon">🌌</div>
                <h3>Eksistensial</h3>
                <p>Kebolehan merenung dan memikirkan persoalan mendalam mengenai kewujudan manusia, makna hidup, dan tujuan ciptaan.</p>
            </div>

        </div>
    </section>

    <!-- SEKSYEN 3: SERTAI SOAL JAWAB (BOTTOM CTA) -->
    <section class="cta-banner">
        <h2>Adakah Anda Bersedia Terokai Cita-Cita Anda? 🌟</h2>
        <p>Isi borang soal jawab kerjaya sekarang untuk berkongsi minat dan luahan rasa bersama Guru Bimbingan & Kaunseling sekolah anda!</p>
        <a href="soal_jawab.php" class="btn-cta-big">
            🎁 Tekan Untuk Sertai Soal Jawab Kerjaya
        </a>
    </section>

</div>

<?php require_once 'includes/footer.php'; ?>
