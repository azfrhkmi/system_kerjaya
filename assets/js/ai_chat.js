// =========================================================
// SISTEM KERJAYA CHERITA - AI CHAT & IMAGE GENERATOR LOGIC
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

    // 3. Semak Pengesanan Hajat Jana Gambar (Image Generation Intent)
    const isImageReq = pregMatchImage(userText);

    if (isImageReq) {
        await processAiImageGeneration(userText, loadingId);
    } else {
        await processAiTextAnswer(userText, loadingId);
    }
}

function pregMatchImage(text) {
    const lower = text.toLowerCase();
    return lower.includes('jana gambar') || 
           lower.includes('lukis gambar') || 
           lower.includes('generate image') || 
           lower.includes('buat gambar') || 
           lower.includes('gambar') || 
           lower.includes('lukisan');
}

// LOGIK JANA GAMBAR AI PERCUMA & UNLIMITED (POLLINATIONS AI)
async function processAiImageGeneration(userText, loadingId) {
    try {
        // Bina prompt 3D Pixar / Kartun Ceria untuk Sekolah Rendah
        let cleanPrompt = userText.replace(/jana gambar|lukis gambar|generate image|buat gambar|gambar|lukisan/gi, '').trim();
        if (!cleanPrompt) cleanPrompt = "Murid sekolah rendah menjadi jurutera robotik canggih";

        const enhancedPrompt = `High quality 3D Pixar style digital art of ${cleanPrompt}, vibrant colors, happy Malaysian primary school student, inspiring career background, detailed, 8k resolution, kid-friendly`;

        const seed = Math.floor(Math.random() * 1000000);
        const imageUrl = `https://image.pollinations.ai/prompt/${encodeURIComponent(enhancedPrompt)}?width=512&height=512&seed=${seed}&nologo=true`;

        // Pra-muat gambar
        const img = new Image();
        img.src = imageUrl;

        img.onload = () => {
            removeLoadingBubble(loadingId);

            const contentHtml = `
                <div>🎨 **Gambar Inspirasi Cita-Cita AI Anda:**</div>
                <div style="margin:10px 0;">
                    <img src="${imageUrl}" alt="AI Career Image" style="width:100%; max-height:280px; object-fit:cover; border-radius:14px; box-shadow:0 6px 16px rgba(0,0,0,0.15); border:2px solid #e0e7ff;">
                </div>
                <div style="font-size:0.88rem; color:#475569; margin-bottom:10px;">
                    ✨ *Prompt:* "${cleanPrompt}"
                </div>
                <a href="${imageUrl}" target="_blank" download="Cita_Cita_AI.jpg" class="btn-primary nav-btn" style="padding:6px 14px; font-size:0.85rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                    📥 Muat Turun Gambar AI (Untuk Upload Di Sec-D)
                </a>
            `;

            appendMessage('ai', contentHtml, true);
        };

        img.onerror = () => {
            removeLoadingBubble(loadingId);
            appendMessage('ai', "⚠️ Maaf, fail pangkalan pelayan gambar AI agak sibuk. Sila cuba tekan butang hantar sekali lagi!");
        };

    } catch (err) {
        removeLoadingBubble(loadingId);
        appendMessage('ai', "⚠️ Ralat semasa menjana gambar. Sila cuba lagi.");
    }
}

// LOGIK JAWAPAN TEKS AI KERJAYA (MALAY KIDS FRIENDLY)
async function processAiTextAnswer(userText, loadingId) {
    try {
        // Cuba panggil API Pollinations Text
        const systemContext = "Anda ialah Pembantu AI Kerjaya Cherita khusus untuk pelajar sekolah rendah di Malaysia. Jawab dalam Bahasa Melayu yang ceria, mesra, mudah fahami, dan penuh motivasi. Gunakan emoji yang comel dan susunan bullet point yang kemas.";
        
        const response = await fetch(`https://text.pollinations.ai/${encodeURIComponent(systemContext + " Soalan murid: " + userText)}`);

        if (response.ok) {
            let aiText = await response.text();
            removeLoadingBubble(loadingId);
            // Formatkan markdown asas
            aiText = aiText.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            aiText = aiText.replace(/\n/g, '<br>');
            appendMessage('ai', aiText, true);
        } else {
            throw new Error('API offline');
        }

    } catch (err) {
        removeLoadingBubble(loadingId);
        // Fallback Jawapan Pintar Terbina
        const fallbackText = getSmartFallbackResponse(userText);
        appendMessage('ai', fallbackText, true);
    }
}

// JAWAPAN PINTAR FALLBACK
function getSmartFallbackResponse(text) {
    const lower = text.toLowerCase();

    if (lower.includes('stem')) {
        return `🚀 **Apa Itu Kerjaya STEM?**<br><br>STEM bermaksud **Sains, Teknologi, Kejuruteraan (Engineering), dan Matematik**!<br><br>Contoh kerjaya STEM yang sangat hebat:<br>1. 👨‍💻 **Jurutera Perisian / AI**: Mencipta aplikasi & permainan video canggih.<br>2. 🤖 **Jurutera Robotik**: Membina robot pintar pembantu manusia.<br>3. 🔬 **Ahli Sains**: Menemui ubat-ubatan baharu.<br>4. 🚀 **Jurutera Angkasa**: Membina roket ke angkasa lepas!<br><br>💡 *Petua Murid:* Rajin belajar Matematik & Sains dari sekolah rendah!`;
    }

    if (lower.includes('doktor')) {
        return `🩺 **Cita-Cita Menjadi Doktor Perubatan!**<br><br>Doktor ialah wira yang merawat pesakit dan menjaga kesihatan masyarakat.<br><br>🎯 **Syarat & Cara Capai:**<br>• Menguasai subjek Sains, Biologi & Bahasa Inggeris.<br>• Mempunyai sifat penyayang dan suka membantu orang lain.<br><br>🎨 *Petua:* Taip **'Jana gambar doktor perubatan'** untuk melihat gambaran anda sebagai doktor!`;
    }

    if (lower.includes('howard') || lower.includes('gardner') || lower.includes('kecerdasan')) {
        return `🧠 **Teori 9 Kecerdasan Pelbagai Howard Gardner:**<br><br>1. 📚 **Verbal-Linguistik**: Bijak bahasa & menulis.<br>2. 🔢 **Logik-Matematik**: Bijak nombor & sains.<br>3. 🎨 **Visual-Ruang**: Bijak lukis & seni.<br>4. ⚽ **Kinestetik**: Bijak sukan & fizikal.<br>5. 🎵 **Muzik**: Bijak irama & lagu.<br>6. 🤝 **Interpersonal**: Pandai berkawan.<br>7. 🧘 **Intrapersonal**: Paham perasaan diri.<br>8. 🌿 **Naturalis**: Suka alam & haiwan.<br>9. 🌌 **Eksistensial**: Pemikir mendalam.`;
    }

    return `🌟 **Terima kasih atas soalan hebat anda!**<br><br>Untuk menjadi insan yang berjaya dalam cita-cita anda:<br>1. ✨ Kenali minat dan kecerdasan semula jadi anda.<br>2. 📚 Belajar dengan tekun di sekolah.<br>3. 🎨 Terokai teknologi AI di portal **DELIMa**.<br><br>💡 *Cuba taip:* **'Jana gambar [cita-cita anda]'** untuk saya lukiskan gambar kerjaya impian anda!`;
}

// HELPER APPEND MESSAGES
function appendMessage(sender, text, isHtml = false) {
    const body = document.getElementById('aiChatBody');
    const bubble = document.createElement('div');
    bubble.className = `chat-bubble ${sender === 'user' ? 'user-bubble' : 'ai-bubble'}`;

    const senderTitle = sender === 'user' ? '👤 Anda:' : '🤖 AI Kerjaya Cherita:';
    
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
        <div class="bubble-sender">🤖 AI Kerjaya Cherita:</div>
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
