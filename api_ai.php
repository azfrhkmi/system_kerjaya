<?php
header('Content-Type: application/json; charset=utf-8');

// Ambil input JSON atau POST
$raw_input = file_get_contents('php://input');
$json_data = json_decode($raw_input, true);
$prompt = trim($_POST['prompt'] ?? ($json_data['prompt'] ?? ($json_data['message'] ?? '')));

if (empty($prompt)) {
    echo json_encode(['success' => false, 'reply' => 'Sila taip soalan anda.']);
    exit;
}

// Semak jika ada API Key luaran (Groq, Gemini, OpenRouter)
$ai_reply = null;

$groq_key = getenv('GROQ_API_KEY');
$gemini_key = getenv('GEMINI_API_KEY');

if ($groq_key) {
    $ai_reply = callGroqApi($prompt, $groq_key);
} elseif ($gemini_key) {
    $ai_reply = callGeminiApi($prompt, $gemini_key);
}

// Jika tiada API key atau luaran offline, jalankan Enjin AI Kerjaya Pintar Bahasa Melayu
if (!$ai_reply) {
    $ai_reply = generateHumanLikeAiResponse($prompt);
}

echo json_encode([
    'success' => true,
    'reply' => $ai_reply
]);
exit;

// FUNGSI GROQ API (Llama-3.3-70b-versatile)
function callGroqApi($prompt, $apiKey) {
    $url = 'https://api.groq.com/openai/v1/chat/completions';
    $systemPrompt = "Anda ialah AI Peti Cheritalah, pembantu kerjaya pintar mesra murid sekolah rendah di Malaysia. Jawab soalan pengguna secara semula jadi, ramah, seperti manusia yang sangat bijak, terperinci, dan ada emoji dalam Bahasa Melayu.";

    $payload = [
        'model' => 'llama-3.3-70b-versatile',
        'messages' => [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $prompt]
        ],
        'temperature' => 0.7,
        'max_tokens' => 800
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $result) {
        $arr = json_decode($result, true);
        if (!empty($arr['choices'][0]['message']['content'])) {
            return $arr['choices'][0]['message']['content'];
        }
    }
    return null;
}

// FUNGSI GEMINI API
function callGeminiApi($prompt, $apiKey) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $apiKey;
    $systemPrompt = "Anda ialah AI Peti Cheritalah, pembantu kerjaya sekolah rendah di Malaysia. Jawab mesra, pintar, terperinci dalam Bahasa Melayu.";

    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $systemPrompt . "\n\nSoalan murid: " . $prompt]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $result) {
        $arr = json_decode($result, true);
        if (!empty($arr['candidates'][0]['content']['parts'][0]['text'])) {
            return $arr['candidates'][0]['content']['parts'][0]['text'];
        }
    }
    return null;
}

// ENJIN AI KERJAYA PINTAR & FLEKSIBEL (HUMAN-LIKE RESPONSE ENGINE)
function generateHumanLikeAiResponse($userQuery) {
    $raw = trim($userQuery);
    $lower = strtolower($raw);

    // KECERDASAN PELBAGAI HOWARD GARDNER
    if (strpos($lower, 'howard') !== false || strpos($lower, 'gardner') !== false || strpos($lower, 'kecerdasan') !== false) {
        return "🧠 <strong>Teori 9 Kecerdasan Pelbagai Howard Gardner:</strong><br><br>" .
               "Setiap murid dilahirkan dengan kelebihan dan potensi yang berbeza! Berikut ialah 9 jenis kecerdasan pelbagai:<br><br>" .
               "1. 📚 <strong>Verbal-Linguistik</strong>: Bijak kata-kata, penulisan & bahasa (Contoh: Penulis, Peguam, Wartawan).<br>" .
               "2. 🔢 <strong>Logik-Matematik</strong>: Bijak nombor, penyelesaian masalah & sains (Contoh: Jurutera, Akauntan, Saintis).<br>" .
               "3. 🎨 <strong>Visual-Ruang</strong>: Bijak seni, lukisan & binaan 3D (Contoh: Pelukis, Arkitek, Animator).<br>" .
               "4. ⚽ <strong>Kinestetik</strong>: Bijak pergerakan fizikal & sukan (Contoh: Pemain Sukan, Atlet, Bomba).<br>" .
               "5. 🎵 <strong>Muzik</strong>: Peka pada irama, melodi & nada (Contoh: Penyanyi, Komposer).<br>" .
               "6. 🤝 <strong>Interpersonal</strong>: Mesra, memahami orang lain & pandai berkawan (Contoh: Guru, Kaunselor).<br>" .
               "7. 🧘 <strong>Intrapersonal</strong>: Memahami emosi & matlamat diri (Contoh: Pakar Psikologi, Motivator).<br>" .
               "8. 🌿 <strong>Naturalis</strong>: Suka haiwan, tumbuhan & alam semula jadi (Contoh: Veterinar, Ahli Botani).<br>" .
               "9. 🌌 <strong>Eksistensial</strong>: Suka berfikir tentang alam semesta & hikmah kehidupan.<br><br>" .
               "💡 <em>Anda boleh memilih kecerdasan anda dalam borang soal jawab di atas!</em>";
    }

    // KERJAYA STEM
    if (strpos($lower, 'stem') !== false) {
        return "🚀 <strong>Dunia Kerjaya STEM (Sains, Teknologi, Kejuruteraan & Matematik):</strong><br><br>" .
               "STEM ialah bidang masa depan yang sangat menarik kerana ia membina peradaban moden!<br><br>" .
               "🌟 <strong>Pekerjaan Hebat Dalam Bidang STEM:</strong><br>" .
               "• 👨‍💻 <strong>Jurutera Perisian / AI</strong>: Mencipta sistem komputer & kecerdasan buatan.<br>" .
               "• 🤖 <strong>Jurutera Robotik</strong>: Membina robot automatik untuk membantu manusia.<br>" .
               "• 🔬 <strong>Ahli Sains & Bioteknologi</strong>: Menyelidik ubat-ubatan & tenaga hijau.<br>" .
               "• 🩺 <strong>Pakar Perubatan & Surihanketepatan</strong>: Menjaga kesihatan & keselamatan pesakit.<br><br>" .
               "💡 <em>Petua Sukses:</em> Rajin belajar subjek Sains dan Matematik di sekolah!";
    }

    // DATABASE KERJAYA LENGKAP & TERPERINCI
    $jobs = [
        'askar' => [
            'name' => 'Askar / Pegawai Tentera (Angkatan Tentera Malaysia)',
            'icon' => '🪖',
            'desc' => 'Askar ialah wira perwira tanah air yang bertindak mempertahankan kedaulatan, keamanan, dan keselamatan negara daripada sebarang ancaman musuh.',
            'duties' => [
                '🛡️ <strong>Mempertahankan Sempadan Negara</strong>: Mengawal sempadan darat, laut, dan udara 24 jam sehari.',
                '🚁 <strong>Misi Penyelamat & Bencana</strong>: Membantu memindahkan mangsa banjir, kemalangan, dan situasi kecemasan.',
                '🎯 <strong>Latihan Ketenteraan</strong>: Menjalankan latihan menembak, perancangan taktik, dan ketahanan fizikal.',
                '🌐 3 Cabang Utama: <strong>Tentera Darat (TDM)</strong>, <strong>Tentera Laut (TLDM)</strong>, dan <strong>Tentera Udara (TUDM)</strong>.'
            ],
            'skills' => 'Kecergasan Fizikal & Mental, Disiplin Tinggi, Keberanian, serta menguasai Sains, Matematik & Bahasa Inggeris.'
        ],
        'tentera' => [
            'name' => 'Pegawai Tentera / Askar',
            'icon' => '🪖',
            'desc' => 'Tentera ialah perwira negara yang menjaga ketenteraman dan kedaulatan tanah air.',
            'duties' => [
                '🛡️ Menjaga keselamatan sempadan tanah air.',
                '⚓ Mengawal perairan negara dengan kapal perang (TLDM).',
                '✈️ Mengawal ruang udara dengan jet pejuang (TUDM).'
            ],
            'skills' => 'Disiplin, Keberanian, Kecergasan Fizikal, Sains & Matematik.'
        ],
        'doktor' => [
            'name' => 'Doktor Perubatan / Pakar Kesihatan',
            'icon' => '🩺',
            'desc' => 'Doktor ialah wira kesihatan yang merawat orang sakit, mendiagnosis penyakit, dan menyelamatkan nyawa manusia.',
            'duties' => [
                '🩺 <strong>Memeriksa Pesakit</strong>: Mendengar aduan kesihatan & melakukan ujian kesihatan.',
                '💊 <strong>Preskripsi Ubat & Rawatan</strong>: Memberikan ubat yang betul dan prosedur rawatan.',
                '🏥 <strong>Pembedahan & Kecemasan</strong>: Membantu pesakit yang mengalami kesakitan serius di hospital.'
            ],
            'skills' => 'Menguasai Sains, Biologi, Kimia, Bahasa Inggeris & Mempunyai sifat penyayang.'
        ],
        'jurutera' => [
            'name' => 'Jurutera (Engineer)',
            'icon' => '⚙️',
            'desc' => 'Jurutera ialah pereka binaan dan teknologi yang mereka cipta mesin, perisian, jambatan, dan bangunan canggih.',
            'duties' => [
                '🏢 <strong>Jurutera Awam</strong>: Merancang binaan bangunan tinggi, jambatan & jalan raya.',
                '🤖 <strong>Jurutera Robotik & Perisian</strong>: Membina sistem mesin automatik & aplikasi.',
                '⚡ <strong>Jurutera Elektrik</strong>: Merancang bekalan tenaga & litar elektronik.'
            ],
            'skills' => 'Matematik, Fizik, Kemahiran Mengod (Coding) & Pemikiran Kritis.'
        ],
        'polis' => [
            'name' => 'Pegawai Polis (Polis Diraja Malaysia)',
            'icon' => '👮‍♂️',
            'desc' => 'Polis ialah penguat kuasa undang-undang yang menjaga keamanan awam dan mencegah jenayah dalam masyarakat.',
            'duties' => [
                '🚔 <strong>Rondaan Keselamatan</strong>: Memastikan kawasan perumahan & bandar selamat.',
                '🔍 <strong>Memburu Penjenayah</strong>: Menyelidik & menangkap pesalah undang-undang.',
                '🚦 <strong>Mengawal Lalu Lintas</strong>: Memastikan laluan jalan raya lancar & selamat.'
            ],
            'skills' => 'Disiplin, Ketegasan, Pendidikan Moral/Islam, Kesihatan & Sukan.'
        ],
        'bomba' => [
            'name' => 'Anggota Bomba & Penyelamat',
            'icon' => '👨‍🚒',
            'desc' => 'Bomba ialah wira penyelamat yang memadamkan kebakaran dan membantu orang awam semasa kecemasan.',
            'duties' => [
                '🔥 <strong>Memadamkan Kebakaran</strong>: Menyelamatkan bangunan & nyawa daripada api.',
                '🌊 <strong>Misi Penyelamat Air & Kemalangan</strong>: Membantu mangsa lemas & terperangkap.',
                '🐍 <strong>Khidmat Khas</strong>: Menangkap haiwan berbisa berbahaya di kediaman.'
            ],
            'skills' => 'Keberanian Tinggi, Pertolongan Cemas, Kecergasan Fizikal & Sains Dasar.'
        ],
        'pilot' => [
            'name' => 'Juruterbang (Pilot)',
            'icon' => '👨‍✈️',
            'desc' => 'Juruterbang ialah pengemudi pesawat udara yang membawa penumpang dan kargo merentasi ruang udara dunia.',
            'duties' => [
                '🛫 <strong>Mengawal Penerbangan</strong>: Menerbangkan pesawat dari berlepas hingga mendarat dengan selamat.',
                '🗺️ <strong>Semakan Navigasi & Cuaca</strong>: Merancang laluan udara yang selamat bersama menara kawalan.',
                '👨‍✈️ <strong>Keselamatan Penumpang</strong>: Memastikan semua sistem kapal terbang berfungsi sempurna.'
            ],
            'skills' => 'Matematik, Fizik, Bahasa Inggeris Komunikasi & Penglihatan Tajam.'
        ],
        'guru' => [
            'name' => 'Guru / Pendidik / Cikgu',
            'icon' => '👨‍🏫',
            'desc' => 'Guru ialah penyampai ilmu dan pembimbing insan yang mendidik generasi muda menjadi manusia berguna.',
            'duties' => [
                '📚 <strong>Mengajar Subjek Akademik</strong>: Menyampaikan pelajaran di dalam kelas dengan menarik.',
                '❤️ <strong>Membimbing Sahsiah</strong>: Membentuk akhlak, disiplin & emosi murid.',
                '🎨 <strong>Menganjurkan Aktiviti</strong>: Memimpin kelab, sukan & persatuan sekolah.'
            ],
            'skills' => 'Verbal-Linguistik, Interpersonal, Kesabaran & Penguasaan Ilmu Subjek.'
        ],
        'chef' => [
            'name' => 'Chef / Tukang Masak Profesion',
            'icon' => '👨‍🍳',
            'desc' => 'Chef ialah pakar kulinari yang merancang menu, mencipta resipi lazat, dan menyajikan hidangan menarik.',
            'duties' => [
                '🍳 <strong>Memasak Hidangan</strong>: Menyediakan makanan lazat dengan teknik memasak profesional.',
                '🥗 <strong>Rekabentuk Menu</strong>: Mencipta gubahan makanan sihat & menarik.',
                '🧹 <strong>Kebersihan Dapur</strong>: Memastikan piawaian kebersihan makanan berada di tahap tertinggi.'
            ],
            'skills' => 'Kreativiti Kulinari, Deria Rasa Peka, Sains Makanan & Kerja Berpasukan.'
        ],
        'pelukis' => [
            'name' => 'Pelukis / Pereka Seni / Animator',
            'icon' => '🎨',
            'desc' => 'Pelukis & animator ialah pengkarya visual yang menghasilkan lukisan, seni grafik 2D/3D, dan komik.',
            'duties' => [
                '🖌️ <strong>Menghasilkan Karya Seni</strong>: Melukis watak komik, pemandangan, dan ilustrasi digital.',
                '🎬 <strong>Animasi Kartun & Filem</strong>: Membina pergerakan watak animasi 3D.',
                '📱 <strong>Pereka Grafik</strong>: Merancang visual iklan & media sosial.'
            ],
            'skills' => 'Visual-Ruang, Daya Imaginasi Tinggi, Pendidikan Seni Visual & Alatan Digital.'
        ],
        'saintis' => [
            'name' => 'Ahli Sains (Scientist)',
            'icon' => '🔬',
            'desc' => 'Saintis ialah penyelidik alam semula jadi yang menjalankan ujian makmal untuk menerokai rahsia sains.',
            'duties' => [
                '🧪 <strong>Eksperimen Makmal</strong>: Menjalankan ujian bahan kimia, biologi, dan fizik.',
                '💊 <strong>Penemuan Ubat & Teknologi</strong>: Mencari ubat baharu untuk menyembuhkan penyakit.',
                '📊 <strong>Analisis Data Sains</strong>: Menulis penemuan baharu untuk dikongsi bersama dunia.'
            ],
            'skills' => 'Sains, Kimia, Biologi, Fizik & Sikap Ingin Tahu Yang Tinggi.'
        ]
    ];

    // SEMAK JIKA SOALAN MENGANDUNGI NAMA PEKERJAAN
    foreach ($jobs as $key => $data) {
        if (strpos($lower, $key) !== false) {
            $duties_html = implode("<br>", $data['duties']);
            return "{$data['icon']} <strong>Penerangan Kerjaya: {$data['name']}</strong><br><br>" .
                   "<strong>Apa Itu {$data['name']}?</strong><br>" .
                   "{$data['desc']}<br><br>" .
                   "🌟 <strong>Tugas & Peranan Utama:</strong><br>" .
                   "{$duties_html}<br><br>" .
                   "📚 <strong>Subjek & Kemahiran Wajib Dikuasai:</strong><br>" .
                   "{$data['skills']}<br><br>" .
                   "🎨 <em>Petua Khas:</em> Taip <strong>'Jana gambar {$key}'</strong> untuk melihat gambaran diri anda dalam cita-cita ini!";
        }
    }

    // JIKA TIADA PEKERJAAN SPECIFIC, BINA JAWAPAN DINAMIK MANUSIA PINTAR
    $cleanPrompt = preg_replace('/(siapa|apa|kenapa|bagaimana|macam|mana|bila|adakah|kah|tu|ni|gas)/i', '', $raw);
    $topic = trim($cleanPrompt) ?: $raw;

    return "🌟 <strong>Pembantu AI Kerjaya Cherita:</strong><br><br>" .
           "Mengenai soalan anda tentang <strong>\"" . htmlspecialchars($topic) . "\"</strong>:<br><br>" .
           "1. 🎯 <strong>Fahami Minat Diri</strong>: Setiap cita-cita dan perkara yang anda terokai memerlukan semangat belajar yang berterusan.<br>" .
           "2. 📚 <strong>Kuasai Ilmu Di Sekolah</strong>: Subjek Bahasa Melayu, Bahasa Inggeris, Matematik, dan Sains ialah tiang asas untuk mencapai kejayaan.<br>" .
           "3. 🤝 <strong>Dapatkan Bimbingan Guru</strong>: Sentiasa berbincang bersama Guru Bimbingan & Kaunseling sekolah anda.<br><br>" .
           "💡 <em>Taip soalan spesifik seperti:</em> <strong>'Tugas doktor'</strong>, <strong>'Tugas askar'</strong>, <strong>'Tugas jurutera'</strong>, atau <strong>'Jana gambar cita-cita saya'</strong> untuk jawapan terperinci! 🎨";
}
