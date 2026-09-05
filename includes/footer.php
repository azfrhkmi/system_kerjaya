    <!-- FOOTER -->
    <footer class="site-footer">
        <div class="container">
            <p style="font-weight: 700; color: #475569; margin-bottom: 8px;">
                🎓 Sistem Penerokaan Kerjaya Cherita Sekolah Rendah
            </p>
            <p style="font-size: 0.88rem; color: #94a3b8;">
                Direka khas dengan penuh kasih sayang untuk membantu murid membina impian & masa depan yang cemerlang.
            </p>
            <p style="margin-top: 15px; font-size: 0.85rem;">
                © <?php echo date('Y'); ?> Hak Cipta Terpelihara • KPM Bimbingan & Kaunseling
            </p>
        </div>
    </footer>

    <!-- AI CHAT WIDGET & SCRIPT -->
    <?php require_once __DIR__ . '/ai_chat_widget.php'; ?>
    <script src="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../assets/js/ai_chat.js' : 'assets/js/ai_chat.js'; ?>"></script>

    <!-- JS Scripts -->
    <script src="<?php echo (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../assets/js/main.js' : 'assets/js/main.js'; ?>"></script>
</body>
</html>
