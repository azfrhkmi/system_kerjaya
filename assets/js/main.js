// =========================================================
// SISTEM PENEROKAAN KERJAYA - TEORI HOWARD GARDNER JS LOGIC
// =========================================================

document.addEventListener('DOMContentLoaded', () => {
    
    // DATA 9 TEORI KECERDASAN PELBAGAI HOWARD GARDNER
    const gardnerData = {
        'verbal': {
            title: 'Verbal-Linguistik (Bahasa & Penulisan)',
            categoryName: 'Penguasaan Bahasa & Komunikasi',
            desc: 'Individu Verbal-Linguistik mempunyai kebolehan tinggi menggunakan bahasa, kata-kata, dan perkataan secara berkesan sama ada secara lisan atau tulisan. Mereka suka membaca, menulis, berhujah, dan bercerita.',
            jobs: [
                '📚 Wartawan / Editor Buku',
                '✍️ Penulis Buku / Novelis',
                '👩‍⚖️ Peguam / Pendakwa Raya',
                '🎙️ Pengacara TV / Penyampai Berita',
                '🗣️ Penterjemah Bahasa'
            ],
            color: '#3b82f6',
            bg: '#dbeafe'
        },
        'logik': {
            title: 'Logik-Matematik (Pengiraan & Analisis)',
            categoryName: 'Analisis Logik & Sains',
            desc: 'Individu Logik-Matematik mempunyai kebolehan berfikir secara rasional, menganalisis masalah, dan menyelesaikan pengiraan nombor atau logik sains secara berstruktur.',
            jobs: [
                '👨‍💻 Jurutera Perisian / Software Developer',
                '📊 Akauntan / Juruaudit Kewangan',
                '🔬 Ahli Sains / Penyelidik Akademik',
                '🧮 Ahli Statistik / Data Scientist',
                '⚙️ Jurutera Sistem / Robotik'
            ],
            color: '#6366f1',
            bg: '#e0e7ff'
        },
        'visual': {
            title: 'Visual-Ruang (Lukisan & Grafik)',
            categoryName: 'Kreativiti Visual & Grafik',
            desc: 'Individu Visual-Ruang mempunyai kebolehan mengesan, membayangkan, dan menterjemahkan dunia visual serta ruang secara grafik, corak, dan warna.',
            jobs: [
                '🏗️ Arkitek / Pereka Pelan Bangunan',
                '🎨 Pereka Grafik / Animator 3D',
                '📸 Jurugambar / Videografi Profesional',
                '🎬 Pengarah Filem / Sinematografi',
                '🏡 Pereka Hiasan Dalaman (Interior Designer)'
            ],
            color: '#ec4899',
            bg: '#fce7f3'
        },
        'kinestetik': {
            title: 'Kinestetik (Pergerakan Fizikal)',
            categoryName: 'Kawalan Anggota Badan & Sukan',
            desc: 'Individu Kinestetik mempunyai kebolehan menggunakan seluruh anggota badan untuk melahirkan idea, perasaan, atau menyelesaikan masalah menerusi pergerakan fizikal.',
            jobs: [
                '🏃 Atlet Sukan / Pakar Kecergasan',
                '🩰 Penari Profesional / Koreografer',
                '🚑 Pegawai Paramedik / Anggota Bomba',
                '🛠️ Mekanikal & Jurutera Teknikal',
                '🥋 Jurulatih Fizikal & Pertahanan Diri'
            ],
            color: '#ef4444',
            bg: '#fee2e2'
        },
        'muzik': {
            title: 'Muzik (Irama & Bunyi)',
            categoryName: 'Apresiasi Seni Irama & Melodi',
            desc: 'Individu Muzik mempunyai kebolehan mengesan irama, melodi, nada, dan bunyi serta menghargai seni muzik secara mendalam.',
            jobs: [
                '🎼 Komposer Muzik / Pengubah Lagu',
                '🎤 Penyanyi / Vokalis Profesional',
                '🎷 Pemuzik / Jurutera Bunyi Audio',
                '🎧 DJ / Penerbit Muzik Digital',
                '👩‍🏫 Guru Muzik & Terapi Seni Bunyi'
            ],
            color: '#8b5cf6',
            bg: '#ede9fe'
        },
        'interpersonal': {
            title: 'Interpersonal (Hubungan Manusia)',
            categoryName: 'Komunikasi & Interaksi Sosial',
            desc: 'Individu Interpersonal mempunyai kebolehan memahami, menyelami, dan berinteraksi secara berkesan dengan orang lain serta memimpin masyarakat.',
            jobs: [
                '👩‍🏫 Guru / Pendidik Sekolah',
                '🗣️ Kaunselor / Pakar Psikologi',
                '🤝 Pegawai Hubungan Awam (PR)',
                '💼 Pengurus Pemasaran & Jualan',
                '👮 Pegawai Polis / Pekerja Sosial'
            ],
            color: '#f59e0b',
            bg: '#fef3c7'
        },
        'intrapersonal': {
            title: 'Intrapersonal (Refleksi Diri)',
            categoryName: 'Kesedaran Diri & Emosi',
            desc: 'Individu Intrapersonal mempunyai kebolehan memahami diri sendiri, emosi, kekuatan, kelemahan, serta matlamat peribadi secara mendalam.',
            jobs: [
                '🧠 Pakar Psikologi Klinikal / Terapi',
                '✍️ Penulis Motivasi / Pengarang Buku Diri',
                '🔬 Penyelidik Sains Sosial',
                '🧘 Jurulatih Pembangunan Diri (Life Coach)',
                '🏛️ Ahli Falsafah / Penasihat Strategik'
            ],
            color: '#14b8a6',
            bg: '#ccfbf1'
        },
        'naturalis': {
            title: 'Naturalis (Alam Semula Jadi)',
            categoryName: 'Penjagaan Flora & Fauna',
            desc: 'Individu Naturalis mempunyai kebolehan mengenali, menghargai, dan memahami alam semula jadi, tumbuhan (flora), serta haiwan (fauna).',
            jobs: [
                '🦁 Ahli Zoologi / Doktor Haiwan (Veterinar)',
                '🌿 Ahli Botani / Pertanian Moden',
                '🌊 Ahli Biologi Marin / Ekologi',
                '🌳 Pegawai Pemeliharaan Alam Sekitar',
                '🏕️ Ranger Taman Negara'
            ],
            color: '#10b981',
            bg: '#d1fae5'
        },
        'eksistensial': {
            title: 'Eksistensial (Makna Kewujudan)',
            categoryName: 'Pemikiran Mendalam & Etika',
            desc: 'Individu Eksistensial mempunyai kebolehan merenung dan memikirkan persoalan mendalam mengenai kewujudan manusia, makna hidup, dan tujuan ciptaan.',
            jobs: [
                '📖 Ahli Teologi / Agama',
                '🏛️ Ahli Falsafah & Penyelidik Sejarah',
                '✍️ Penulis Eseos / Pemikir Sosial',
                '🎓 Profesor & Penyelidik Akademik',
                '📜 Penasihat Etika & Kemanusiaan'
            ],
            color: '#64748b',
            bg: '#f1f5f9'
        }
    };

    // FUNGSI PAPARAN INTERAKTIF TEORI HOWARD GARDNER
    window.showGardnerDetail = function(code) {
        const data = gardnerData[code];
        if (!data) return;

        // Toggle active states pada butang
        document.querySelectorAll('.gardner-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        const targetBtn = document.querySelector(`.gardner-btn-${code}`);
        if (targetBtn) targetBtn.classList.add('active');

        // Kemaskini bekas paparan detail
        const displayBox = document.getElementById('gardnerDisplay');
        if (displayBox) {
            displayBox.style.borderColor = data.color;
            displayBox.style.backgroundColor = 'white';
            displayBox.innerHTML = `
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:15px; border-bottom:2px solid ${data.bg}; padding-bottom:12px; flex-wrap:wrap; gap:10px;">
                    <div>
                        <span style="background:${data.bg}; color:${data.color}; font-weight:800; padding:6px 14px; border-radius:50px; font-size:0.9rem;">
                            ${data.categoryName}
                        </span>
                        <h3 style="font-size:1.6rem; color:${data.color}; margin-top:8px;">${data.title}</h3>
                    </div>
                    <div style="font-size:2.8rem;">🌟</div>
                </div>
                <p style="font-size:1.05rem; color:#475569; margin-bottom:20px; line-height:1.6;">${data.desc}</p>
                <h4 style="font-size:1.15rem; color:#1e1b4b; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                    🎯 5 Pekerjaan Yang Sangat Sesuai Bagi Kecerdasan Ini:
                </h4>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:12px;">
                    ${data.jobs.map(j => `
                        <div style="background:${data.bg}; color:${data.color}; padding:14px; border-radius:14px; font-weight:700; font-size:0.95rem; border:1px solid ${data.color}44;">
                            ${j}
                        </div>
                    `).join('')}
                </div>
            `;
            displayBox.classList.add('active');
            displayBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    };

    // MODAL CONTROL FUNCTIONS
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('show');
    };

    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove('show');
    };

    // Close modal when clicking outside box
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                backdrop.classList.remove('show');
            }
        });
    });

});
