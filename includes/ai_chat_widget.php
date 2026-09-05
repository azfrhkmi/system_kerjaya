<!-- =========================================================
   AI CHAT ASSISTANT & IMAGE GENERATOR WIDGET (KERJAYA CHERITA)
   ========================================================= -->

<!-- BUTANG TERAPUNG AI (FLOATING FAB) -->
<button id="aiFabBtn" class="ai-fab-btn" onclick="toggleAiChat()" title="Tanya AI Kerjaya & Penerangan Cita-Cita!">
    <span class="ai-fab-icon">🤖</span>
    <span class="ai-fab-text">Tanya AI Kerjaya</span>
</button>

<!-- TETINGKAP CHAT AI -->
<div id="aiChatWindow" class="ai-chat-window">
    
    <!-- HEADER TETINGKAP -->
    <div class="ai-chat-header">
        <div style="display:flex; align-items:center; gap:10px;">
            <div style="font-size:2rem; background:rgba(255,255,255,0.2); padding:6px; border-radius:12px;">🤖</div>
            <div>
                <h4 style="font-family:var(--font-heading); font-size:1.2rem; margin:0;">Pembantu AI Peti Cheritalah</h4>
                <small style="opacity:0.9; font-size:0.8rem;">✨ Bersedia Menjawab Sebarang Soalan Cita-Cita Anda!</small>
            </div>
        </div>
        <button type="button" class="ai-close-btn" onclick="toggleAiChat()">❌</button>
    </div>

    <!-- BEKAS MESEJ CHAT -->
    <div id="aiChatBody" class="ai-chat-body">
        
        <!-- Mesej Aluan AI -->
        <div class="chat-bubble ai-bubble">
            <div class="bubble-sender">🤖 AI Peti Cheritalah:</div>
            Hai adik! 👋 Saya Pembantu AI Kerjaya anda.
            <br><br>
            Saya boleh **menerangkan pelbagai cita-cita di dunia**, subjek STEM, petua belajar, dan **penjelasan sebarang jenis pekerjaan** untuk anda! 💡
            <br><br>
            Sila taip soalan atau tekan cadangan di bawah:
        </div>

    </div>

    <!-- CADANGAN SOALAN PANTAS (QUICK PROMPT CHIPS) -->
    <div class="ai-quick-prompts">
        <button type="button" class="prompt-chip" onclick="sendQuickPrompt('Apakah tugas dan peranan seorang askar atau tentera?')">
            🪖 Apa Tugas Askar?
        </button>
        <button type="button" class="prompt-chip" onclick="sendQuickPrompt('Apakah tugas seorang doktor perubatan?')">
            🩺 Apa Tugas Doktor?
        </button>
        <button type="button" class="prompt-chip" onclick="sendQuickPrompt('Bolehkah anda terangkan cita-cita dalam bidang STEM untuk sekolah rendah?')">
            🚀 Apa itu Kerjaya STEM?
        </button>
        <button type="button" class="prompt-chip" onclick="sendQuickPrompt('Apakah 9 jenis kecerdasan pelbagai Teori Howard Gardner?')">
            🎁 Terangkan Teori Howard Gardner
        </button>
    </div>

    <!-- FOOER / INPUT CONTAINER -->
    <div class="ai-chat-input-area">
        <form id="aiChatForm" onsubmit="handleAiChatSubmit(event)">
            <input type="text" id="aiInputText" class="ai-input-field" placeholder="Tanya apa sahaja soalan kerjaya..." autocomplete="off" required>
            <button type="submit" id="aiSendBtn" class="ai-send-btn">🚀 Hantar</button>
        </form>
        <div style="text-align:center; margin-top:6px; font-size:0.75rem; color:#94a3b8;">
            ⚡ Dikuasai oleh AI Peti Cheritalah • Penerangan Sebarang Cita-Cita Di Dunia
        </div>
    </div>

</div>
