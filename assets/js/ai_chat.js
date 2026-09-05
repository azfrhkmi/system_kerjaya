// =========================================================
// SISTEM PETI CHERITALAH - DYNAMIC AI CHAT ENGINE
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

    // 3. Proses Jawapan AI Kerjaya
    setTimeout(async () => {
        await processAiTextAnswer(userText, loadingId);
    }, 400);
}

// LOGIK JAWAPAN DINAMIK AI KERJAYA (TERUS KE BACKEND API_AI.PHP)
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

// ENJIN AI DINAMIK FALLBACK KHAS UNTUK PELBAGAI PEKERJAAN
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
            3. 🎨 <strong>Visual-Ruang</strong>: Bijak seni, binaan & lukisan (Arkitek, Pelukis).<br>
            4. ⚽ <strong>Kinestetik</strong>: Bijak pergerakan fizikal & sukan (Atlet, Bomba, Askar).<br>
            5. 🎵 <strong>Muzik</strong>: Pendengaran peka irama & nada (Penyanyi, Komposer).<br>
            6. 🤝 <strong>Interpersonal</strong>: Mesra & mudah berkawan (Guru, Kaunselor).<br>
            7. 🧘 <strong>Intrapersonal</strong>: Memahami perasaan diri sendiri (Pakar Psikologi).<br>
            8. 🌿 <strong>Naturalis</strong>: Suka alam sekitar & haiwan (Doktor Haiwan, Petani).<br>
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

    // 3. JAWAPAN DINAMIK MENGIKUT SUBJEK
    const cleanSubject = text.replace(/siapa|apa|kenapa|bagaimana|macam|mana|bila|adakah|kah|tu|ni|tugas|kerja|cita|nak|jadi/gi, '').trim();
    const displaySubject = cleanSubject ? cleanSubject : text;
    const capitalized = displaySubject.charAt(0).toUpperCase() + displaySubject.slice(1);

    return `
        🌟 <strong>Penerangan Kerjaya: ${escapeHtml(capitalized)}</strong><br><br>
        Pekerjaan <strong>${escapeHtml(capitalized)}</strong> ialah salah satu tugas yang sangat berharga dan menyumbang perkhidmatan penting kepada masyarakat.<br><br>
        🌟 <strong>Peranan Utama:</strong><br>
        • 🎯 <strong>Melaksanakan Tugas Profesional</strong>: Menggunakan kemahiran khas untuk menyelesaikan tugasan.<br>
        • 🛠️ <strong>Kemahiran & Perkhidmatan</strong>: Menyediakan perkhidmatan dan alatan khas mengikut bidang.<br>
        • 🤝 <strong>Membantu Masyarakat</strong>: Menjaga kebajikan dan membantu orang ramai.<br><br>
        📚 <strong>Subjek & Kemahiran Yang Perlu Dikuasai:</strong><br>
        Penguasaan Subjek Sekolah Rendah (Bahasa Melayu, Bahasa Inggeris, Sains & Matematik), Kemahiran Fizikal/Teknikal, Disiplin, dan Minat yang mendalam.<br><br>
        💡 <em>Nasihat AI Peti Cheritalah:</em> Semua pekerjaan di dunia—sama ada besar atau kecil—mempunyai nilai yang sangat mulia! Terus belajar dengan rajin untuk mencapai cita-cita anda!
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
            <span>.</span><span>.</span><span>.</span> AI sedang berfikir & merangka jawapan... 💡
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
