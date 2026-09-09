// =========================================================
// SISTEM PENEROKAAN KERJAYA - TEORI HOWARD GARDNER JS LOGIC
// =========================================================

document.addEventListener('DOMContentLoaded', () => {
    
    // DATA 9 TEORI KECERDASAN PELBAGAI HOWARD GARDNER (10 CONTOH PEKERJAAN SETIAP TEORI)
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
                '🗣️ Penterjemah Bahasa',
                '📻 Penyampai Radio (DJ)',
                '📜 Ahli Puisi / Penyajak',
                '🏫 Guru Bahasa & Sastera',
                '📢 Pegawai Perhubungan Awam',
                '📄 Penulis Skrip Drama / Filem'
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
                '⚙️ Jurutera Sistem / Robotik',
                '📈 Penganalisis Pasaran / Pelaburan',
                '🧑‍⚕️ Ahli Farmasi / Kimia',
                '💻 Pakar Keselamatan Siber',
                '🧮 Guru Matematik & Fizik',
                '🔎 Penyiasat Forensik Data'
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
                '🏡 Pereka Hiasan Dalaman',
                '✈️ Jurutera Rekabentuk Pesawat',
                '🎮 Pereka Permainan Video (Game Designer)',
                '🗺️ Ahli Kartografi / Pereka Peta',
                '👗 Pereka Fesyen & Pakaian',
                '🗿 Pengukir Seni & Pelukis Komik'
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
                '🥋 Jurulatih Fizikal & Pertahanan Diri',
                '👨‍⚕️ Pakar Bedah (Surgeon)',
                '👨‍🍳 Chef / Tukang Masak Profesional',
                '🧱 Tukang Bina / Pertukangan Kayu Seni',
                '🎪 Pelakon Teater & Stuntman',
                '💆 Terapi Fizikal / Fisioterapi'
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
                '👩‍🏫 Guru Muzik & Terapi Seni Bunyi',
                '🎻 Pengarah Orkestra / Konduktor',
                '🎹 Penala Piano & Pembuat Alat Muzik',
                '🎙️ Artis Alih Suara (Voice Actor)',
                '📻 Penerbit Audio & Podcast',
                '🎶 Jurutera Mastering Muzik'
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
                '👮 Pegawai Polis / Pekerja Sosial',
                '🧑‍💼 Pengurus Sumber Manusia (HR)',
                '🏛️ Ahli Politik / Pemimpin Masyarakat',
                '✈️ Pramugari / Pramugara',
                '👨‍⚕️ Doktor Perubatan / Jururawat',
                '🤝 Perunding Kerjaya / Organisasi'
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
                '🏛️ Ahli Falsafah / Penasihat Strategik',
                '📊 Perancang Strategik Perniagaan',
                '📈 Usahawan / Pengasas Syarikat',
                '📖 Penulis Diari & Bio-Pengarang',
                '🧘 Terapi Meditasi & Minda',
                '🔍 Penganalisis Etika & Risiko'
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
                '🏕️ Ranger Taman Negara',
                '👨‍🌾 Pengusaha Agronomi & Landskap',
                '🌋 Ahli Geologi / Kajian Bumi',
                '🐝 Ahli Entamologi (Serangga)',
                '🌤️ Ahli Meteorologi / Cuaca',
                '🐟 Pakar Akuakultur & Perikanan'
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
                '📜 Penasihat Etika & Kemanusiaan',
                '🌌 Ahli Astronomi / Penjelajah Angkasa',
                '⚖️ Penggubal Dasar Kemanusiaan',
                '🕊️ Aktivis Hak Asasi & Keamanan',
                '📚 Ahli Sejarah & Antropologi',
                '🔍 Penyelidik Kosmologi & Sains Fizik'
            ],
            color: '#64748b',
            bg: '#f1f5f9'
        }
    };

    // FUNGSI PAPARAN INTERAKTIF TEORI HOWARD GARDNER (WITH TOGGLE)
    window.showGardnerDetail = function(code) {
        const data = gardnerData[code];
        if (!data) return;

        const displayBox = document.getElementById('gardnerDisplay');
        const targetBtn = document.querySelector(`.gardner-btn-${code}`);

        // Jika butang yang sama ditekan lagi, tutup paparan (Toggle Hide)
        if (targetBtn && targetBtn.classList.contains('active') && displayBox && displayBox.classList.contains('active')) {
            targetBtn.classList.remove('active');
            displayBox.classList.remove('active');
            displayBox.style.display = 'none';
            return;
        }

        // Semak & buka paparan baharu (Toggle Show)
        document.querySelectorAll('.gardner-btn').forEach(btn => {
            if (!btn.classList.contains('stem-btn-s') && !btn.classList.contains('stem-btn-t') && !btn.classList.contains('stem-btn-e') && !btn.classList.contains('stem-btn-m')) {
                btn.classList.remove('active');
            }
        });
        if (targetBtn) targetBtn.classList.add('active');

        if (displayBox) {
            displayBox.style.display = 'block';
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
                    🎯 10 Pekerjaan Yang Sangat Sesuai Bagi Kecerdasan Ini:
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

    // DATA STEM (SAINS, TEKNOLOGI, KEJURUTERAAN, MATEMATIK)
    const stemData = {
        'S': {
            title: 'S - Sains (Science)',
            badge: '🧬 Meneroka Alam & Fenomena Alam',
            desc: 'Bidang Sains melibatkan kajian tentang alam fizikal dan semula jadi melalui pemerhatian, eksperimen, dan pembuktian fakta.',
            color: '#059669',
            bg: '#d1fae5',
            jobs: [
                '🧫 Ahli Biologi Makmal',
                '🔬 Saintis Kimia / Penyelidik',
                '🪐 Ahli Astronomi / Penjelajah Angkasa',
                '👨‍⚕️ Doktor Perubatan / Pakar Bedah',
                '🌱 Ahli Botani & Tumbuhan',
                '🌋 Ahli Geologi / Bumi',
                '🌿 Saintis Alam Sekitar',
                '💊 Ahli Farmasi',
                '🌤️ Ahli Meteorologi (Cuaca)',
                '🦁 Ahli Zoologi (Haiwan)'
            ]
        },
        'T': {
            title: 'T - Teknologi (Technology)',
            badge: '💻 Pembinaan Perisian & AI',
            desc: 'Bidang Teknologi berfokus kepada pembangunan perisian, kecerdasan buatan (AI), komputer, dan sistem digital masa kini.',
            color: '#2563eb',
            bg: '#dbeafe',
            jobs: [
                '💻 Jurutera Perisian / Programmer',
                '🌐 Pembangun Aplikasi Web & Mobile',
                '🛡️ Pakar Keselamatan Siber (Cybersecurity)',
                '📊 Saintis Data (Data Scientist)',
                '🎨 Pereka UX/UI Digital',
                '🤖 Jurutera AI & Robotik',
                '☁️ Pentadbir Sistem Awan (Cloud)',
                '🗄️ Pentadbir Pangkalan Data (DBA)',
                '🕹️ Pereka Permainan Video (Game Dev)',
                '📡 Jurutera Rangkaian & IT'
            ]
        },
        'E': {
            title: 'E - Kejuruteraan (Engineering)',
            badge: '⚙️ Pereka Bentuk & Mesin',
            desc: 'Bidang Kejuruteraan mengaplikasikan sains dan matematik untuk Mereka, Membina, dan Menyelesaikan masalah fizikal & infrastruktur.',
            color: '#d97706',
            bg: '#fef3c7',
            jobs: [
                '🏗️ Jurutera Awam / Bangunan',
                '⚡ Jurutera Elektrik & Elektronik',
                '⚙️ Jurutera Mekanikal',
                '✈️ Jurutera Aeronautikal (Pesawat)',
                '🤖 Jurutera Mekatronik & Robotik',
                '🦿 Jurutera Biomedikal',
                '🏭 Jurutera Kimia & Proses',
                '🚗 Jurutera Automotif',
                '🌊 Jurutera Kelautan / Marin',
                '🌱 Jurutera Alam Sekitar'
            ]
        },
        'M': {
            title: 'M - Matematik (Mathematics)',
            badge: '🧮 Nombor, Logik & Analisis Data',
            desc: 'Bidang Matematik memfokuskan kepada nombor, struktur, ruang, logik, dan analisis data kewangan & saintifik.',
            color: '#7c3aed',
            bg: '#ede9fe',
            jobs: [
                '🧮 Ahli Matematik Penyelidik',
                '📈 Aktuari (Pakar Risiko Insurans)',
                '📊 Penganalisis Statistik',
                '🔐 Pakar Kriptografi (Keselamatan Data)',
                '💼 Penganalisis Kewangan',
                '🏢 Penganalisis Risiko Perniagaan',
                '👩‍🏫 Guru / Pensyarah Matematik',
                '📉 Penganalisis Penyelidikan Operasi',
                '🏦 Pakar Ekonometrik Bank',
                '🔍 Penganalisis Kuantitatif (Quant)'
            ]
        }
    };

    // DATA 10 APLIKASI AI FAMOUS
    const aiData = {
        'chatgpt': {
            name: 'ChatGPT',
            company: 'OpenAI',
            icon: '🤖',
            color: '#10a37f',
            bg: '#d1fae5',
            desc: 'Aplikasi AI pembantu teks paling popular di dunia. Boleh membantu menjawab soalan, menulis karangan, menterjemah bahasa, dan menjana idea kerjaya kreatif.'
        },
        'claude': {
            name: 'Claude AI',
            company: 'Anthropic',
            icon: '🧠',
            color: '#d97706',
            bg: '#fef3c7',
            desc: 'AI pintar dengan kebolehan analisis teks mendalam, penulisan cerita yang sangat mesra dan semula jadi, serta pemahaman jawapan yang tepat.'
        },
        'gemini': {
            name: 'Google Gemini',
            company: 'Google',
            icon: '✨',
            color: '#2563eb',
            bg: '#dbeafe',
            desc: 'AI serba boleh daripada Google yang terhubung terus dengan maklumat carian internet terkini, gambar, dan pelbagai aplikasi Google.'
        },
        'canva': {
            name: 'Canva Magic Studio',
            company: 'Canva',
            icon: '🎨',
            color: '#7c3aed',
            bg: '#ede9fe',
            desc: 'Alatan grafik AI yang membolehkan murid menghasilkan poster inspirasi kerjaya, infografik, dan persembahan slaid yang sangat cantik secara automatik.'
        },
        'copilot': {
            name: 'Microsoft Copilot',
            company: 'Microsoft',
            icon: '💻',
            color: '#0284c7',
            bg: '#e0f2fe',
            desc: 'Pembantu AI yang diintegrasikan dalam Word, PowerPoint, dan Windows untuk membantu penulisan dokumen dan rekaan pembentangan.'
        },
        'midjourney': {
            name: 'Midjourney',
            company: 'Midjourney Inc',
            icon: '🖼️',
            color: '#be185d',
            bg: '#fce7f3',
            desc: 'Aplikasi AI penjana lukisan & gambaran seni grafik bertaraf profesional tinggi hanya daripada carian ayat prompt.'
        },
        'perplexity': {
            name: 'Perplexity AI',
            company: 'Perplexity',
            icon: '🔍',
            color: '#0d9488',
            bg: '#ccfbf1',
            desc: 'Enjin carian berasaskan AI yang memberikan jawapan tepat lengkap bersama rujukan sumber sahih di internet.'
        },
        'dalle': {
            name: 'DALL-E 3',
            company: 'OpenAI',
            icon: '🎭',
            color: '#ea580c',
            bg: '#ffedd5',
            desc: 'AI khas untuk menghasilkan poster, lukisan imajinasi, dan gambar inspirasi kerjaya daripada ayat promosi pengguna.'
        },
        'poe': {
            name: 'Poe AI',
            company: 'Quora',
            icon: '💡',
            color: '#4f46e5',
            bg: '#e0e7ff',
            desc: 'Platform perantara yang mengumpulkan pelbagai jenis bot AI dalam satu aplikasi mudah untuk dicuba oleh murid.'
        },
        'elevenlabs': {
            name: 'ElevenLabs',
            company: 'ElevenLabs',
            icon: '🎙️',
            color: '#475569',
            bg: '#f1f5f9',
            desc: 'Teknologi AI suara (voice AI) yang boleh menukar teks kepada alihan suara yang sangat realistik dalam pelbagai bahasa.'
        }
    };

    // FUNGSI PAPARAN INTERAKTIF STEM (S, T, E, M) - WITH TOGGLE
    window.showStemDetail = function(letter) {
        const data = stemData[letter];
        if (!data) return;

        const displayBox = document.getElementById('stemDisplay');
        const activeBtn = document.querySelector(`.stem-btn-${letter.toLowerCase()}`);

        // Jika butang yang sama ditekan lagi, tutup paparan (Toggle Hide)
        if (activeBtn && activeBtn.classList.contains('active') && displayBox && displayBox.classList.contains('active')) {
            activeBtn.classList.remove('active');
            displayBox.classList.remove('active');
            displayBox.style.display = 'none';
            return;
        }

        document.querySelectorAll('.stem-btn-s, .stem-btn-t, .stem-btn-e, .stem-btn-m').forEach(btn => btn.classList.remove('active'));
        if (activeBtn) activeBtn.classList.add('active');

        if (displayBox) {
            displayBox.style.display = 'block';
            displayBox.style.borderColor = data.color;
            displayBox.style.backgroundColor = 'white';
            displayBox.innerHTML = `
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:15px; border-bottom:2px solid ${data.bg}; padding-bottom:12px; flex-wrap:wrap; gap:10px;">
                    <div>
                        <span style="background:${data.bg}; color:${data.color}; font-weight:800; padding:6px 14px; border-radius:50px; font-size:0.9rem;">
                            ${data.badge}
                        </span>
                        <h3 style="font-size:1.6rem; color:${data.color}; margin-top:8px;">${data.title}</h3>
                    </div>
                    <div style="font-size:2.8rem;">🚀</div>
                </div>
                <p style="font-size:1.05rem; color:#475569; margin-bottom:20px; line-height:1.6;">${data.desc}</p>
                <h4 style="font-size:1.15rem; color:#1e1b4b; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                    🎯 10 Contoh Pekerjaan Berkaitan Bidang ${data.title.split('-')[1] || letter}:
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

    // FUNGSI PAPARAN INTERAKTIF APLIKASI AI (WITH TOGGLE)
    window.showAiDetail = function(code) {
        const data = aiData[code];
        if (!data) return;

        const displayBox = document.getElementById('aiDisplay');
        const eventBtn = event ? event.target.closest('.ai-btn') : null;

        // Jika butang yang sama ditekan lagi, tutup paparan (Toggle Hide)
        if (eventBtn && eventBtn.classList.contains('active') && displayBox && displayBox.classList.contains('active')) {
            eventBtn.classList.remove('active');
            displayBox.classList.remove('active');
            displayBox.style.display = 'none';
            return;
        }

        document.querySelectorAll('.ai-btn').forEach(btn => btn.classList.remove('active'));
        if (eventBtn) eventBtn.classList.add('active');

        if (displayBox) {
            displayBox.style.display = 'block';
            displayBox.style.borderColor = data.color;
            displayBox.style.backgroundColor = 'white';
            displayBox.innerHTML = `
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; border-bottom:2px solid ${data.bg}; padding-bottom:10px;">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <span style="font-size:2.2rem; background:${data.bg}; padding:8px; border-radius:14px;">${data.icon}</span>
                        <div>
                            <span style="background:${data.bg}; color:${data.color}; font-weight:800; padding:4px 10px; border-radius:50px; font-size:0.8rem;">
                                ${data.company}
                            </span>
                            <h3 style="font-size:1.4rem; color:${data.color}; margin-top:4px;">${data.name}</h3>
                        </div>
                    </div>
                </div>
                <p style="font-size:1rem; color:#475569; line-height:1.6;">${data.desc}</p>
            `;
            displayBox.classList.add('active');
            displayBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    };

    // Auto load STEM 'S' if container present
    if (document.getElementById('stemDisplay')) {
        showStemDetail('S');
    }

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
