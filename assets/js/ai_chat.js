// =========================================================
// SISTEM PETI CHERITALAH - DYNAMIC AI CHAT & IMAGE GENERATOR LOGIC
// =========================================================

function toggleAiChat() {
    const chatWin = document.getElementById('aiChatWindow');
    if (chatWin) {
        chatWin.classList.toggle('show');
        if (chatWin.classList.contains('show')) {
            document.getElementById('aiInputText').focus();
        }
    }
}

function sendQuickPrompt(promptText) {
    const input = document.getElementById('aiInputText');
    if (input) {
        input.value = promptText;
        document.getElementById('aiChatForm').dispatchEvent(new Event('submit'));
    }
}

async function handleAiChatSubmit(e) {
    e.preventDefault();
    const input = document.getElementById('aiInputText');
    const userText = input.value.trim();
    if (!userText) return;

    // 1. Paparkan Mesej Pengguna
    appendMessage('user', userText);
    input.value = '';

    // 2. Paparkan Indicator Menunggu
    const loadingId = appendLoadingBubble();

    // Simulasikan masa pemprosesan AI yang realistik (800ms)
    setTimeout(async () => {
        // 3. Semak Pengesanan Hajat Jana Gambar (Image Generation Intent)
        const isImageReq = pregMatchImage(userText);

        if (isImageReq) {
            await processAiImageGeneration(userText, loadingId);
        } else {
            await processAiTextAnswer(userText, loadingId);
        }
    }, 600);
}

function pregMatchImage(text) {
    const lower = text.toLowerCase();
    return lower.includes('jana gambar') || 
           lower.includes('lukis gambar') || 
           lower.includes('generate image') || 
           lower.includes('buat gambar') || 
           lower.includes('tunjukkan gambar') || 
           lower.includes('gambar') || 
           lower.includes('lukisan');
}

// LOGIK JANA GAMBAR AI PERCUMA & UNLIMITED (POLLINATIONS AI)
async function processAiImageGeneration(userText, loadingId) {
    try {
        let cleanPrompt = userText.replace(/jana gambar|lukis gambar|generate image|buat gambar|tunjukkan gambar|gambar|lukisan/gi, '').trim();
        if (!cleanPrompt) cleanPrompt = "Murid sekolah rendah menjadi jurutera robotik canggih";

        const enhancedPrompt = `High quality 3D Pixar style digital art of ${cleanPrompt}, vibrant colors, happy Malaysian primary school student, inspiring career background, detailed, 8k resolution, kid-friendly`;

        const seed = Math.floor(Math.random() * 1000000);
        const imageUrl = `https://image.pollinations.ai/prompt/${encodeURIComponent(enhancedPrompt)}?width=512&height=512&seed=${seed}&nologo=true`;

        const img = new Image();
        img.src = imageUrl;

        img.onload = () => {
            removeLoadingBubble(loadingId);

            const contentHtml = `
                <div>🎨 <strong>Gambar Inspirasi Cita-Cita AI Anda:</strong></div>
                <div style="margin:10px 0;">
                    <img src="${imageUrl}" alt="AI Career Image" style="width:100%; max-height:280px; object-fit:cover; border-radius:14px; box-shadow:0 6px 16px rgba(0,0,0,0.15); border:2px solid #e0e7ff;">
                </div>
                <div style="font-size:0.88rem; color:#475569; margin-bottom:10px;">
                    ✨ <em>Subjek Gambar:</em> "${cleanPrompt}"
                </div>
                <a href="${imageUrl}" target="_blank" download="Cita_Cita_AI.jpg" class="btn-primary nav-btn" style="padding:6px 14px; font-size:0.85rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    📥 Muat Turun Gambar AI (Untuk Upload Di Sec-D)
                </a>
            `;

            appendMessage('ai', contentHtml, true);
        };

        img.onerror = () => {
            removeLoadingBubble(loadingId);
            appendMessage('ai', "⚠️ Maaf, pelayan gambar AI sedang sibuk. Sila tekan hantar sekali lagi!", false);
        };

    } catch (err) {
        removeLoadingBubble(loadingId);
        appendMessage('ai', "⚠️ Ralat semasa menjana gambar. Sila cuba lagi.", false);
    }
}

// LOGIK JAWAPAN DINAMIK AI KERJAYA (DINAMIK & BEBAS UNTUK APA SAHAJA SOALAN)
async function processAiTextAnswer(userText, loadingId) {
    try {
        const pathPrefix = (window.location.pathname.includes('/admin/')) ? '../' : '';
        const response = await fetch(pathPrefix + 'api_ai.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ prompt: userText })
        });
        
        if (response.ok) {
            const data = await response.json();
            removeLoadingBubble(loadingId);
            if (data && data.reply) {
                appendMessage('ai', data.reply, true);
                return;
            }
        }
        throw new Error('API Response Error');

    } catch (err) {
        removeLoadingBubble(loadingId);
        const aiAnswer = generateDynamicAiResponse(userText);
        appendMessage('ai', aiAnswer, true);
    }
}

// ENJIN AI DINAMIK (MENJANA JAWAPAN KHAS BERDASARKAN SOALAN MURID)
function generateDynamicAiResponse(userQuery) {
    const text = userQuery.trim();
    const lower = text.toLowerCase();

    // 1. KECERDASAN PELBAGAI HOWARD GARDNER
    if (lower.includes('howard') || lower.includes('gardner') || lower.includes('kecerdasan')) {
        return `
            🎁 <strong>Teori 9 Kecerdasan Pelbagai Howard Gardner:</strong><br><br>
            Setiap murid mempunyai kelebihan dan keunikan tersendiri! Berikut ialah 9 jenis kecerdasan:<br><br>
            1. 📚 <strong>Verbal-Linguistik</strong>: Bijak kata-kata & penulisan (Penulis, Wartawan).<br>
            2. 🔢 <strong>Logik-Matematik</strong>: Bijak nombor & penyelesaian masalah (Jurutera, Saintis).<br>
            3. 🎨 <strong>Visual-Ruang</strong>: Bijak seni, binaan & lukisan (Arkitek, Animator).<br>
            4. ⚽ <strong>Kinestetik</strong>: Bijak pergerakan fizikal & sukan (Pemain Sukan, Atlet).<br>
            5. 🎵 <strong>Muzik</strong>: Pendengaran peka irama & nada (Penyanyi, Komposer).<br>
            6. 🤝 <strong>Interpersonal</strong>: Mesra & mudah berkawan (Guru, Kaunselor).<br>
            7. 🧘 <strong>Intrapersonal</strong>: Memahami perasaan diri sendiri (Pakar Psikologi).<br>
            8. 🌿 <strong>Naturalis</strong>: Suka alam sekitar & haiwan (Doktor Haiwan, Botanis).<br>
            9. 🌌 <strong>Eksistensial</strong>: Suka berfikir tentang alam semula jadi & tujuan hidup.<br><br>
            💡 <em>Petua:</em> Anda boleh menanda kecerdasan pilihan anda pada borang di atas!
        `;
    }

    // 2. SOALAN BERKAITAN STEM
    if (lower.includes('stem') || lower.includes('sains') || lower.includes('teknologi') || lower.includes('matematik') || lower.includes('kejuruteraan')) {
        return `
            🚀 <strong>Dunia Kerjaya STEM (Sains, Teknologi, Kejuruteraan & Matematik):</strong><br><br>
            Bidang STEM sangat penting kerana ia membina teknologi masa depan!<br><br>
            🌟 <strong>Contoh Kerjaya STEM Yang Hebat:</strong><br>
            • 👨‍💻 <strong>Jurutera Perisian / AI</strong>: Mencipta aplikasi & robot pintar.<br>
            • 🔬 <strong>Ahli Sains / Penyelidik</strong>: Menemui ubat-ubatan & sains angkasa.<br>
            • 🤖 <strong>Jurutera Robotik</strong>: Merancang robot canggih untuk membantu manusia.<br>
            • 🩺 <strong>Pakar Perubatan & Bio-Teknologi</strong>: Menjaga kesihatan dan perubatan.<br><br>
            💡 <em>Langkah Permulaan:</em> Berikan perhatian penuh dalam subjek Sains dan Matematik di sekolah!
        `;
    }

    // 3. SOALAN BERKAITAN KERJAYA SPESIFIK (DOKTOR, POLIS, BOMBA, JURUTERA, PILOT, CHEF, GURU, DLL)
    const careerDatabase = {
        'doktor': { title: 'Doktor Perubatan', icon: '🩺', subject: 'Sains, Biologi & Bahasa Inggeris', duty: 'Merawat pesakit, mendiagnosis penyakit, dan menyelamatkan nyawa manusia.' },
        'jurutera': { title: 'Jurutera (Engineer)', icon: '⚙️', subject: 'Matematik, Fizik & Rekabentuk', duty: 'Membina bangunan, jambatan, perisian, dan mesin teknologi canggih.' },
        'polis': { title: 'Pegawai Polis', icon: '👮‍♂️', subject: 'Pendidikan Moral/Islam, Kesihatan & Sukan', duty: 'Menjaga keamanan negara, memburu penjenayah, dan melindungi masyarakat.' },
        'bomba': { title: 'Anggota Bomba & Penyelamat', icon: '👨‍🚒', subject: 'Sukan, Pendidikan Kesihatan & Sains Dasar', duty: 'Padamkan kebakaran, menyelamatkan mangsa kemalangan, dan situasi kecemasan.' },
        'pilot': { title: 'JuruTerbang (Pilot)', icon: '👨‍✈️', subject: 'Matematik, Bahasa Inggeris & Fizik', duty: 'Menerbangkan pesawat udara membawa penumpang merentasi dunia dengan selamat.' },
        'guru': { title: 'Guru / Pendidik', icon: '👨‍🏫', subject: 'Semua subjek teras akademik & Pedagogi', duty: 'Mendidik murid-murid, menyampaikan ilmu, dan membina sahsiah insani.' },
        'angkasawan': { title: 'Angkasawan (Astronaut)', icon: '👨‍🚀', subject: 'Sains Angkasa, Fizik & Matematik', duty: 'Menjalankan kajian di Stesen Angkasa Antarabangsa dan menerokai alam semesta.' },
        'chef': { title: 'Chef / Tukang Masak', icon: '👨‍🍳', subject: 'Sains Rumah Tangga & Kulinari', duty: 'Mencipta resipi makanan yang sedap, sihat, dan menyajikan hidangan menarik.' },
        'saintis': { title: 'Ahli Sains (Scientist)', icon: '🔬', subject: 'Sains, Kimia, Biologi & Fizik', duty: 'Membuat eksperimen di makmal untuk menemui inovasi baharu bagi dunia.' },
        'pelukis': { title: 'Pelukis / Pereka Seni', icon: '🎨', subject: 'Pendidikan Seni Visual & Reka Bentuk', duty: 'Menzahirkan seni visual, animasi 3D, ilustrasi komik, dan grafik kreatif.' },
        'program': { title: 'Pengaturcara Komputer (Programmer)', icon: '💻', subject: 'Matematik, Komputer & Sains Data', duty: 'Menulis kod komputer untuk membina perisian, laman web, dan permainan video.' },
        'tentera': { title: 'Pegawai Tentera Darat / Laut / Udara', icon: '🎖️', subject: 'Kecergasan Fizikal & Disiplin', duty: 'Pertahankan kedaulatan tanah air daripada sebarang ancaman luar.' },
        'veterinar': { title: 'Doktor Haiwan (Veterinar)', icon: '🐾', subject: 'Sains & Biologi Haiwan', duty: 'Merawat haiwan yang sakit dan menjaga kebajikan alam haiwan.' },
        'peguam': { title: 'Peguam / Pengamal Undang-Undang', icon: '⚖️', subject: 'Bahasa Melayu, Bahasa Inggeris & Sejarah', duty: 'Tegakkan keadilan di mahkamah dan membela hak kesaksamaan masyarakat.' }
    };

    for (let key in careerDatabase) {
        if (lower.includes(key)) {
            const item = careerDatabase[key];
            return `
                ${item.icon} <strong>Penerangan Kerjaya: ${item.title}</strong><br><br>
                ✨ <strong>Tugas Utama:</strong><br>
                ${item.duty}<br><br>
                📚 <strong>Subjek Utama Yang Perlu Dikuasai:</strong><br>
                ${item.subject}<br><br>
                🎯 <strong>Petua Untuk Sukses:</strong><br>
                1. Rajin membaca buku berkaitan bidang ini di perpustakaan.<br>
                2. Sertai kelab sekolah yang relevan.<br>
                3. Cuba taip <em>'Jana gambar ${item.title}'</em> untuk melihat inspirasi AI! 🎨
            `;
        }
    }

    // 4. SOALAN PETUA / NASIHAT / CARA BELAJAR / GAJI / HARAPAN
    if (lower.includes('cara') || lower.includes('bagaimana') || lower.includes('macam mana') || lower.includes('petua') || lower.includes('bantuan')) {
        return `
            💡 <strong>Panduan & Nasihat Pembantu AI Kerjaya:</strong><br><br>
            Untuk mencapai impian anda, berikut ialah 4 langkah terbaik yang boleh anda amalkan mulai hari ini:<br><br>
            1. 📖 <strong>Fokus Semasa P&P</strong>: Berikan tumpuan sepenuhnya kepada guru di dalam kelas.<br>
            2. 🤝 <strong>Dapatkan Bimbingan Guru Kaunseling</strong>: Tandakan borang di atas untuk sesi bersama cikgu kaunselor.<br>
            3. 🎨 <strong>Cipta Bahan Di DELIMa</strong>: Gunakan alatan AI di DELIMa dan muat naik dokumen anda di Bahagian (d).<br>
            4. 🚀 <strong>Jangan Takut Mencuba</strong>: Terus berusaha dan pupuk kecerdasan unik anda setiap hari!<br><br>
            🌟 <em>Ada soalan spesifik mengenai kerjaya lain? Sila tanya saya!</em>
        `;
    }

    // 5. JAWAPAN UMUM DINAMIK BERDASARKAN TAIPAN MURID (DINAMIK 100%)
    const cleanSubject = text.replace(/siapa|apa|kenapa|bagaimana|mana|bila|adakah|kah|tu|ni/gi, '').trim();
    const displaySubject = cleanSubject ? cleanSubject : text;

    return `
        🌟 <strong>Maklum Balas AI Kerjaya Mengenai: "${escapeHtml(displaySubject)}"</strong><br><br>
        Soalan yang sangat menarik daripada anda! Kunci utama kejayaan dalam bidang ini ialah:<br><br>
        • 🎯 <strong>Mengenal Pasti Minat Diri</strong>: Terus terokai perkara yang anda gemari.<br>
        • 📚 <strong>Kuasai Ilmu Di Sekolah</strong>: Ilmu Bahasa, Sains & Matematik ialah asas utama.<br>
        • 🤝 <strong>Bincang Bersama Guru Bimbingan</strong>: Guru kaunseling sentiasa bersedia membimbing anda.<br><br>
        💡 <em>Petua Khas:</em> Anda boleh menaip <strong>'Jana gambar ${escapeHtml(displaySubject)}'</strong> untuk saya lukiskan gambar inspirasi kartun 3D Pixar cita-cita anda! 🎨
    `;
}

// HELPER APPEND MESSAGES
function appendMessage(sender, text, isHtml = false) {
    const body = document.getElementById('aiChatBody');
    const bubble = document.createElement('div');
    bubble.className = `chat-bubble ${sender === 'user' ? 'user-bubble' : 'ai-bubble'}`;

    const senderTitle = sender === 'user' ? '👤 Anda:' : '🤖 AI Peti Cheritalah:';
    
    if (isHtml) {
        bubble.innerHTML = `<div class="bubble-sender">${senderTitle}</div>${text}`;
    } else {
        bubble.innerHTML = `<div class="bubble-sender">${senderTitle}</div>${escapeHtml(text)}`;
    }

    body.appendChild(bubble);
    body.scrollTop = body.scrollHeight;
}

function appendLoadingBubble() {
    const body = document.getElementById('aiChatBody');
    const id = 'loading_' + Date.now();
    const bubble = document.createElement('div');
    bubble.id = id;
    bubble.className = 'chat-bubble ai-bubble';
    bubble.innerHTML = `
        <div class="bubble-sender">🤖 AI Peti Cheritalah:</div>
        <div class="ai-typing-dots">
            <span>.</span><span>.</span><span>.</span> AI sedang berfikir & merangka jawapan... 🎨
        </div>
    `;
    body.appendChild(bubble);
    body.scrollTop = body.scrollHeight;
    return id;
}

function removeLoadingBubble(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
}

function escapeHtml(str) {
    return str.replace(/[&<>"']/g, function(m) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[m];
    });
}
