// =========================================================
// SISTEM PENEROKAAN KERJAYA - INTERACTIVE JS LOGIC
// =========================================================

document.addEventListener('DOMContentLoaded', () => {
    
    // DATA RIASEC UTAMA (MELAYU)
    const riasecData = {
        'R': {
            title: 'Realistik (R)',
            categoryName: 'Kemahiran Praktikal & Fizikal',
            desc: 'Individu Realistik gemar bekerja dengan objek, mesin, peralatan, tumbuhan, atau haiwan. Mereka lebih suka aktiviti di luar bangunan, membaiki barangan, dan menggunakan tangan.',
            jobs: [
                '👨‍🔧 Jurutera Mekanikal / Elektrikal',
                '👨‍✈️ Juruterbang / Kapten Kapal',
                '👨‍🌾 Ahli Botani / Penternak Moden',
                '👷 Arkitek Bina Bangunan',
                '👨‍🍳 Tukang Masak / Chef Profesional'
            ],
            color: '#dc2626',
            bg: '#fee2e2'
        },
        'I': {
            title: 'Investigatif (I)',
            categoryName: 'Penyelidikan & Analisis Sains',
            desc: 'Individu Investigatif gemar membuat pemerhatian, mempelajari hal baharu, menganalisis masalah, dan menyelesaikan teka-teki sains atau matematik.',
            jobs: [
                '👨‍🔬 Ahli Sains / Penyelidik',
                '👨‍⚕️ Doktor Perubatan / Pakar Bedah',
                '👨‍💻 Juruanalisis Data / Pengaturcara Software',
                '🕵️ Ahli Kriminologi / Penyiasat',
                '🧪 Ahli Farmasi / Ahli Kimia'
            ],
            color: '#0284c7',
            bg: '#e0f2fe'
        },
        'S': {
            title: 'Sosial (S)',
            categoryName: 'Membantu & Mendidik Masyarat',
            desc: 'Individu Sosial gemar berinteraksi, mengajar, membantu, merawat, serta berekspresi secara lisan dengan orang ramai.',
            jobs: [
                '👩‍🏫 Guru / Pendidik Sekolah',
                '👩‍⚕️ Jururawat / Pegawai Kesihatan',
                '🗣️ Kaunselor / Pakar Psikologi',
                '👮 Pegawai Polis / Bomba',
                '🤝 Pegawai Kerja Sosial'
            ],
            color: '#d97706',
            bg: '#fef3c7'
        },
        'E': {
            title: 'Enterprising (E) / Keusahawanan',
            categoryName: 'Kepimpinan & Perniagaan',
            desc: 'Individu Enterprising gemar memimpin, mempengaruhi orang lain, membuat keputusan, dan menguruskan perniagaan atau projek besar.',
            jobs: [
                '💼 Usahawan / Pemilik Perniagaan',
                '📈 Pengurus Pemasaran / Syarikat',
                '👨‍⚖️ Peguam / Hakim',
                '👨‍💼 Pegawai Pentadbiran / CEO',
                '🎙️ Pengacara TV / Wartawan Penyiaran'
            ],
            color: '#9333ea',
            bg: '#f3e8ff'
        },
        'K': {
            title: 'Konvensional (K)',
            categoryName: 'Pengurusan Data & Pentadbiran',
            desc: 'Individu Konvensional gemar bekerja dengan data, rekod, fail, peraturan tersusun, dan mengikut sistem pengurusan yang teratur.',
            jobs: [
                '📊 Akauntan / Juruaudit',
                '💻 Pentadbir Sistem Pangkalan Data',
                '🏛️ Pegawai Bank / Kewangan',
                '📚 Pustakawan / Pengurus Rekod',
                '📂 Setiausaha Pentadbiran'
            ],
            color: '#16a34a',
            bg: '#dcfce7'
        }
    };

    // FUNGSI UTAMA INTERAKSI BUTANG RIASEC
    window.showRiasecDetail = function(code) {
        const data = riasecData[code];
        if (!data) return;

        // Toggle active states pada butang
        document.querySelectorAll('.riasec-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        const targetBtn = document.querySelector(`.riasec-btn-${code}`);
        if (targetBtn) targetBtn.classList.add('active');

        // Kemaskini bekas paparan detail
        const displayBox = document.getElementById('riasecDisplay');
        if (displayBox) {
            displayBox.style.borderColor = data.color;
            displayBox.style.backgroundColor = 'white';
            displayBox.innerHTML = `
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:15px; border-bottom:2px solid ${data.bg}; padding-bottom:12px;">
                    <div>
                        <span style="background:${data.bg}; color:${data.color}; font-weight:800; padding:6px 14px; border-radius:50px; font-size:0.9rem;">
                            ${data.categoryName}
                        </span>
                        <h3 style="font-size:1.8rem; color:${data.color}; margin-top:8px;">${data.title}</h3>
                    </div>
                    <div style="font-size:3rem;">🌟</div>
                </div>
                <p style="font-size:1.1rem; color:#475569; margin-bottom:20px; line-height:1.6;">${data.desc}</p>
                <h4 style="font-size:1.2rem; color:#1e1b4b; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                    🎯 5 Pekerjaan Yang Sangat Sesuai Untuk Anda:
                </h4>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:12px;">
                    ${data.jobs.map(j => `
                        <div style="background:${data.bg}; color:${data.color}; padding:14px; border-radius:14px; font-weight:700; font-size:1rem; border:1px solid ${data.color}33;">
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
