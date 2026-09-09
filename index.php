<?php
$page_title = "Laman Utama Penerokaan Kerjaya";
require_once 'config/db.php';
require_once 'includes/header.php';
?>

<!-- HERO BANNER SECTION -->
<section class="hero-section">
    <h1 class="hero-title">
        Terokai Minat & <span class="highlight">Bina Kerjaya STEM!</span> 🎁
    </h1>
    
    <p class="hero-subtitle">
        Selamat datang ke Sistem Penerokaan Kerjaya Sekolah Rendah. Mari kenali potensi diri, kecerdasan pelbagai, dan kerjaya pilihan yang paling sesuai dengan impian anda!
    </p>
</section>

<div class="container">

    <!-- SEKSYEN 1: KERJAYA STEM -->
    <section class="section-block">
        <div class="section-header">
            <h2>🌈 Kerjaya STEM</h2>
            <p>Memahami dunia STEM (Sains, Teknologi, Kejuruteraan & Matematik) dan persediaan kerjaya masa depan murid</p>
        </div>

        <div class="gardner-interactive-container">
            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="font-size: 1.4rem; color: var(--primary); margin-bottom: 8px;">
                    🎯 Pilih Huruf STEM Di Bawah Untuk Menerokai Maksud & 10 Pekerjaan Berkaitan:
                </h3>
                <p style="color: var(--text-muted); font-size: 0.95rem;">
                    Tekan mana-mana butang S, T, E, atau M untuk melihat contoh bidang & cita-cita hebat!
                </p>
            </div>

            <!-- BUTANG MELINTANG S, T, E, M (BERSEJALAN DENGAN STYLES GARDNER) -->
            <div class="gardner-buttons-wrapper stem-buttons-big">
                <button type="button" class="gardner-btn gardner-btn-verbal stem-btn-s" onclick="showStemDetail('S')">🧬 S - Sains</button>
                <button type="button" class="gardner-btn gardner-btn-logik stem-btn-t" onclick="showStemDetail('T')">💻 T - Teknologi</button>
                <button type="button" class="gardner-btn gardner-btn-kinestetik stem-btn-e" onclick="showStemDetail('E')">⚙️ E - Kejuruteraan</button>
                <button type="button" class="gardner-btn gardner-btn-muzik stem-btn-m" onclick="showStemDetail('M')">🧮 M - Matematik</button>
            </div>

            <!-- BEKAS PAPARAN DETAIL STEM -->
            <div id="stemDisplay" class="gardner-detail-display" style="margin-top: 24px;"></div>
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

    <!-- SEKSYEN 3: SENARAI SEKOLAH MENENGAH DI MALAYSIA -->
    <section class="section-block">
        <div class="section-header">
            <h2>🏫 Senarai Sekolah Menengah di Malaysia</h2>
            <p>Pelbagai pilihan jenis sekolah menengah sebagai panduan hala tuju murid tahun 6 selepas tamat sekolah rendah!</p>
        </div>

        <div class="school-card-grid">
            
            <!-- 1. Sekolah Berasrama Penuh (SBP) - FLIP CARD -->
            <div class="school-flip-container" id="card-sbp">
                <div class="school-flip-inner">
                    <!-- Front -->
                    <div class="school-card school-flip-front" onclick="toggleSchoolFlip('card-sbp')" style="cursor:pointer;">
                        <div class="school-icon-box" style="border-color:#bfdbfe;">
                            <img src="logo-sekolah/sbp.png" alt="Logo Sekolah Berasrama Penuh (SBP)">
                        </div>
                        <h3 class="school-title">Sekolah Berasrama Penuh (SBP)</h3>
                        <div class="school-meta">
                            <span class="school-tag tag-state">📍 Seluruh Malaysia</span>
                            <span class="school-tag tag-category">SBP</span>
                        </div>
                        <div style="margin-top:auto; padding-top:10px;">
                            <span class="badge badge-info flip-badge-clickable" style="background:#e0e7ff; color:#3730a3; width:100%; display:block; text-align:center; padding:8px;">
                                🗺️ Tekan Untuk Lihat Peta SBP 🔄
                            </span>
                        </div>
                    </div>
                    <!-- Back -->
                    <div class="school-flip-back">
                        <div style="width:100%; display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                            <span style="font-size:0.82rem; font-weight:700; color:#1e1b4b;">🗺️ Peta Lokasi SBP</span>
                            <button type="button" onclick="toggleSchoolFlip('card-sbp')" style="background:#fee2e2; color:#ef4444; border:none; padding:3px 8px; border-radius:6px; font-size:0.75rem; font-weight:700; cursor:pointer;">❌ Tutup</button>
                        </div>
                        <div class="map-zoom-viewport" onwheel="handleMapZoom(event, this)">
                            <img src="logo-sekolah/peta-sbp.jpg" alt="Peta Lokasi SBP" class="map-zoom-img" data-scale="1.0">
                        </div>
                        <div style="display:flex; justify-content:space-between; width:100%; align-items:center; margin-top:6px; font-size:0.72rem; color:#64748b;">
                            <span>🔍 Skrol zoom & seret (drag) gambar</span>
                            <button type="button" onclick="resetMapZoom(this)" style="background:#f1f5f9; border:1px solid #cbd5e1; padding:2px 6px; border-radius:4px; font-size:0.7rem; cursor:pointer;">↺ Reset Zoom</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Maktab Rendah Sains Mara (MRSM) - FLIP CARD -->
            <div class="school-flip-container" id="card-mrsm">
                <div class="school-flip-inner">
                    <!-- Front -->
                    <div class="school-card school-flip-front" onclick="toggleSchoolFlip('card-mrsm')" style="cursor:pointer;">
                        <div class="school-icon-box" style="border-color:#fde68a;">
                            <img src="logo-sekolah/mrsm.png" alt="Logo Maktab Rendah Sains Mara (MRSM)">
                        </div>
                        <h3 class="school-title">Maktab Rendah Sains Mara (MRSM)</h3>
                        <div class="school-meta">
                            <span class="school-tag tag-state">📍 Seluruh Malaysia</span>
                            <span class="school-tag tag-category">MRSM</span>
                        </div>
                        <div style="margin-top:auto; padding-top:10px;">
                            <span class="badge badge-info flip-badge-clickable" style="background:#fef3c7; color:#92400e; width:100%; display:block; text-align:center; padding:8px;">
                                🗺️ Tekan Untuk Lihat Peta MRSM 🔄
                            </span>
                        </div>
                    </div>
                    <!-- Back -->
                    <div class="school-flip-back">
                        <div style="width:100%; display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                            <span style="font-size:0.82rem; font-weight:700; color:#1e1b4b;">🗺️ Peta Lokasi MRSM</span>
                            <button type="button" onclick="toggleSchoolFlip('card-mrsm')" style="background:#fee2e2; color:#ef4444; border:none; padding:3px 8px; border-radius:6px; font-size:0.75rem; font-weight:700; cursor:pointer;">❌ Tutup</button>
                        </div>
                        <div class="map-zoom-viewport" onwheel="handleMapZoom(event, this)">
                            <img src="logo-sekolah/peta-mrsm.jpg" alt="Peta Lokasi MRSM" class="map-zoom-img" data-scale="1.0">
                        </div>
                        <div style="display:flex; justify-content:space-between; width:100%; align-items:center; margin-top:6px; font-size:0.72rem; color:#64748b;">
                            <span>🔍 Skrol zoom & seret (drag) gambar</span>
                            <button type="button" onclick="resetMapZoom(this)" style="background:#f1f5f9; border:1px solid #cbd5e1; padding:2px 6px; border-radius:4px; font-size:0.7rem; cursor:pointer;">↺ Reset Zoom</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Sekolah Harian Biasa -->
            <div class="school-card">
                <div class="school-icon-box" style="border-color:#e2e8f0;">
                    <img src="logo-sekolah/jata-malaysia.png" alt="Logo Jata Malaysia - Sekolah Harian Biasa">
                </div>
                <h3 class="school-title">Sekolah Harian Biasa</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Setiap Daerah</span>
                    <span class="school-tag tag-category">KPM</span>
                </div>
            </div>

            <!-- 4. Sekolah Sukan Malaysia (SSM) -->
            <div class="school-card">
                <div class="school-icon-box" style="border-color:#fed7aa;">
                    <img src="logo-sekolah/jata-malaysia.png" alt="Logo Jata Malaysia - Sekolah Sukan Malaysia">
                </div>
                <h3 class="school-title">Sekolah Sukan Malaysia (SSM)</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Khas Atlet</span>
                    <span class="school-tag tag-category">SSM</span>
                </div>
            </div>

            <!-- 5. Sekolah Seni Malaysia (SSeM) -->
            <div class="school-card">
                <div class="school-icon-box" style="border-color:#fbcfe8;">
                    <img src="logo-sekolah/jata-malaysia.png" alt="Logo Jata Malaysia - Sekolah Seni Malaysia">
                </div>
                <h3 class="school-title">Sekolah Seni Malaysia (SSeM)</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Khas Bakat Seni</span>
                    <span class="school-tag tag-category">SSeM</span>
                </div>
            </div>

            <!-- 6. Kolej Permata Pintar (UKM) -->
            <div class="school-card">
                <div class="school-icon-box" style="border-color:#ddd6fe;">
                    <img src="logo-sekolah/permata-pintar-ukm.png" alt="Logo Kolej Permata Pintar (UKM)">
                </div>
                <h3 class="school-title">Kolej Permata Pintar (UKM)</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Bangi, Selangor</span>
                    <span class="school-tag tag-category">Pintar UKM</span>
                </div>
            </div>

            <!-- 7. Kolej Permata Insan (USiM) -->
            <div class="school-card">
                <div class="school-icon-box" style="border-color:#ccfbf1;">
                    <img src="logo-sekolah/permata-insan-usim.png" alt="Logo Kolej Permata Insan (USiM)">
                </div>
                <h3 class="school-title">Kolej Permata Insan (USiM)</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Nilai, N.Sembilan</span>
                    <span class="school-tag tag-category">Insan USiM</span>
                </div>
            </div>

            <!-- 8. Sekolah Pendidikan Khas -->
            <div class="school-card">
                <div class="school-icon-box" style="border-color:#a7f3d0;">
                    <img src="logo-sekolah/jata-malaysia.png" alt="Logo Jata Malaysia - Sekolah Pendidikan Khas">
                </div>
                <h3 class="school-title">Sekolah Pendidikan Khas</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Khas OKU / MBPK</span>
                    <span class="school-tag tag-category">KPM</span>
                </div>
            </div>

            <!-- 9. Akademi Sains Pendang (ASP) -->
            <div class="school-card">
                <div class="school-icon-box" style="border-color:#bae6fd;">
                    <img src="logo-sekolah/akademi-sains-pendang.png" alt="Logo Akademi Sains Pendang (ASP)">
                </div>
                <h3 class="school-title">Akademi Sains Pendang (ASP)</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Pendang, Kedah</span>
                    <span class="school-tag tag-category">KPM / Sains</span>
                </div>
            </div>

            <!-- 10. Sekolah Kawalan -->
            <div class="school-card">
                <div class="school-icon-box" style="border-color:#fecaca;">
                    <img src="logo-sekolah/jata-malaysia.png" alt="Logo Jata Malaysia - Sekolah Kawalan">
                </div>
                <h3 class="school-title">Sekolah Kawalan</h3>
                <div class="school-meta">
                    <span class="school-tag tag-state">📍 Setiap Negeri</span>
                    <span class="school-tag tag-category">JPN / KPM</span>
                </div>
            </div>

        </div>
    </section>

    <!-- SEKSYEN 4: SERTAI SOAL JAWAB (BOTTOM CTA) -->
    <section class="cta-banner">
        <h2>Adakah Anda Bersedia Terokai Kerjaya Anda? 🌟</h2>
        <a href="soal_jawab.php" class="btn-cta-big">
            🚀 Jom bina kerjaya STEM anda!
        </a>
    </section>

</div>

<script>
function toggleSchoolFlip(cardId) {
    const card = document.getElementById(cardId);
    if (card) {
        card.classList.toggle('flipped');
    }
}

// MANAGEMENT KEADAAN ZOOM & PAN (DRAG) GAMBAR PETA
const mapStates = new WeakMap();

function getMapState(viewport) {
    if (!mapStates.has(viewport)) {
        mapStates.set(viewport, { scale: 1.0, x: 0, y: 0, isDragging: false, startX: 0, startY: 0 });
    }
    return mapStates.get(viewport);
}

function updateMapTransform(viewport) {
    const state = getMapState(viewport);
    const img = viewport.querySelector('.map-zoom-img');
    if (img) {
        img.style.transform = `translate(${state.x}px, ${state.y}px) scale(${state.scale})`;
    }
}

function handleMapZoom(e, viewport) {
    e.preventDefault();
    e.stopPropagation();
    
    const state = getMapState(viewport);
    if (e.deltaY < 0) {
        state.scale = Math.min(4.5, state.scale + 0.3);
    } else {
        state.scale = Math.max(1.0, state.scale - 0.3);
        if (state.scale === 1.0) {
            state.x = 0;
            state.y = 0;
        }
    }
    updateMapTransform(viewport);
}

function resetMapZoom(btn) {
    const cardBack = btn.closest('.school-flip-back');
    if (cardBack) {
        const viewport = cardBack.querySelector('.map-zoom-viewport');
        if (viewport) {
            const state = getMapState(viewport);
            state.scale = 1.0;
            state.x = 0;
            state.y = 0;
            updateMapTransform(viewport);
        }
    }
}

// INIALISASI DRAG / PAN DENGAN MOUSE & TOUCH SCREEN
document.addEventListener('DOMContentLoaded', function() {
    const viewports = document.querySelectorAll('.map-zoom-viewport');
    
    viewports.forEach(viewport => {
        // MOUSE DOWN
        viewport.addEventListener('mousedown', function(e) {
            e.preventDefault();
            const state = getMapState(viewport);
            state.isDragging = true;
            state.startX = e.clientX - state.x;
            state.startY = e.clientY - state.y;
            viewport.style.cursor = 'grabbing';
        });

        // TOUCH START (MOBILE)
        viewport.addEventListener('touchstart', function(e) {
            if (e.touches.length === 1) {
                const state = getMapState(viewport);
                state.isDragging = true;
                state.startX = e.touches[0].clientX - state.x;
                state.startY = e.touches[0].clientY - state.y;
            }
        }, { passive: true });

        // TOUCH MOVE (MOBILE)
        viewport.addEventListener('touchmove', function(e) {
            if (e.touches.length === 1) {
                const state = getMapState(viewport);
                if (state.isDragging) {
                    state.x = e.touches[0].clientX - state.startX;
                    state.y = e.touches[0].clientY - state.startY;
                    updateMapTransform(viewport);
                }
            }
        }, { passive: true });

        // TOUCH END (MOBILE)
        viewport.addEventListener('touchend', function() {
            const state = getMapState(viewport);
            state.isDragging = false;
        });
    });

    // GLOBAL MOUSE MOVE & UP
    window.addEventListener('mousemove', function(e) {
        viewports.forEach(vp => {
            const state = getMapState(vp);
            if (state.isDragging) {
                state.x = e.clientX - state.startX;
                state.y = e.clientY - state.startY;
                updateMapTransform(vp);
            }
        });
    });

    window.addEventListener('mouseup', function() {
        viewports.forEach(vp => {
            const state = getMapState(vp);
            if (state.isDragging) {
                state.isDragging = false;
                vp.style.cursor = 'grab';
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
