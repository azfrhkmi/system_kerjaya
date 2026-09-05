<!-- =========================================================
   AI CHAT ASSISTANT & IMAGE GENERATOR WIDGET (KERJAYA CHERITA)
   ========================================================= -->

<!-- BUTANG TERAPUNG AI (FLOATING FAB) -->
<button id="aiFabBtn" class="ai-fab-btn" onclick="toggleAiChat()" title="Tanya AI Kerjaya & Jana Gambar!">
    <span class="ai-fab-icon">🤖</span>
    <span class="ai-fab-text">Tanya AI Kerjaya</span>
    <span class="ai-fab-badge">PERCUMA</span>
</button>

<!-- TETINGKAP CHAT AI -->
<div id="aiChatWindow" class="ai-chat-window">
    
    <!-- HEADER TETINGKAP -->
    <div class="ai-chat-header">
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="font-size:2rem; background:rgba(255,255,255,0.2); padding:6px; border-radius:12px;">🤖</div>
            <div>
                <h4 style="font-family:var(--font-heading); font-size:1.2rem; margin:0;">Pembantu AI Kerjaya Cherita</h4>
                <small style="opacity:0.9; font-size:0.8rem;">✨ Bersedia Menjawab & Jana Gambar Cita-Cita!</small>
            </div>
        </div>
        <button type="button" class="ai-close-btn" onclick="toggleAiChat()">❌</button>
    </div>

    <!-- BEKAS MESEJ CHAT -->
    <div id="aiChatBody" class="ai-chat-body">
        
        <!-- Mesej Aluan AI -->
        <div class="chat-bubble ai-bubble">
            <div class="bubble-sender">🤖 AI Kerjaya Cherita:</div>
            Hai adik! 👋 Saya Pembantu AI Kerjaya anda.
            <br><br>
            Saya boleh **menerangkan pelbagai cita-cita**, subjek STEM, dan **menjana gambar cita-cita (AI Image)** untuk anda! 🎨
            <br><br>
            Sila taip soalan atau tekan cadangan di bawah:
        </div>

    </div>

    <!-- CADANGAN SOALAN PANTAS (QUICK PROMPT CHIPS) -->
    <div class="ai-quick-prompts">
        <button type="button" class="prompt-chip" onclick="sendQuickPrompt('Jana gambar kartun 3D Pixar seorang murid menjadi Jurutera Robotik canggih')">
            🎨 Jana Gambar Jurutera Robotik
        </button>
        <button type="button" class="prompt-chip" onclick="sendQuickPrompt('Jana gambar kartun 3D Pixar seorang murid menjadi Doktor Perubatan di hospital')">
            🩺 Jana Gambar Doktor
        </button>
        <button type="button" class="prompt-chip" onclick="sendQuickPrompt('Bolehkah anda terangkan cita-cita dalam bidang STEM untuk sekolah rendah?')">
            🚀 Apa itu Kerjaya STEM?
        </button>
        <button type="button" class="prompt-chip" onclick="sendQuickPrompt('Apakah 9 jenis kecerdasan pelbagai Teori Howard Gardner?')">
            🧠 Terangkan Teori Howard Gardner
        </button>
    </div>

    <!-- FOOER / INPUT CONTAINER -->
    <div class="ai-chat-input-area">
        <form id="aiChatForm" onsubmit="handleAiChatSubmit(event)">
            <input type="text" id="aiInputText" class="ai-input-field" placeholder="Tanya soalan kerjaya atau 'Jana gambar...'" autocomplete="off" required>
            <button type="submit" id="aiSendBtn" class="ai-send-btn">🚀 Hantar</button>
        </form>
        <div style="text-align:center; margin-top:6px; font-size:0.75rem; color:#94a3b8;">
            ⚡ Dikuasai oleh AI Kerjaya Cherita • Jana Gambar & Penerangan Percuma
        </div>
    </div>

</div>
