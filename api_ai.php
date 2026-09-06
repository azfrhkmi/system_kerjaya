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

// KUNCI API GOOGLE GEMINI RASMI PENGGUNA
$default_gemini = base64_decode('QVEuQWI4Uk42STZXMUJrYzN0a1UzNXNENHRFaklGOWM3WFFlRFp3cWpMMEhYd1U5U2tKT0E=');
$gemini_key = getenv('GEMINI_API_KEY') ?: $default_gemini;
$groq_key = getenv('GROQ_API_KEY');

if ($gemini_key) {
    $ai_reply = callGeminiApi($prompt, $gemini_key);
} elseif ($groq_key) {
    $ai_reply = callGroqApi($prompt, $groq_key);
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

// FUNGSI GEMINI API (GOOGLE GEMINI 3.6 FLASH)
function callGeminiApi($prompt, $apiKey) {
    $models = ['gemini-3.6-flash', 'gemini-2.5-flash', 'gemini-2.0-flash'];
    $systemPrompt = "Anda ialah Pembantu AI Peti Cheritalah khusus untuk murid sekolah rendah di Malaysia. Jawab soalan pengguna secara semula jadi, mesra, pintar, seperti manusia yang sangat bijak, terperinci, dan ada emoji dalam Bahasa Melayu.";

    foreach ($models as $m) {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$m}:generateContent?key=" . $apiKey;

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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $result) {
            $arr = json_decode($result, true);
            if (!empty($arr['candidates'][0]['content']['parts'][0]['text'])) {
                $text = $arr['candidates'][0]['content']['parts'][0]['text'];
                // Formatkan Markdown ke HTML
                $text = preg_replace('/### (.*?)\n/m', '<h4 style="color:#1e1b4b; margin:12px 0 6px;">$1</h4>', $text);
                $text = preg_replace('/## (.*?)\n/m', '<h3 style="color:#1e1b4b; margin:14px 0 6px;">$1</h3>', $text);
                $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
                $text = preg_replace('/\* (.*?)\n/m', '• $1<br>', $text);
                $text = nl2br($text);
                return $text;
            }
        }
    }
    return null;
}

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
            $text = $arr['choices'][0]['message']['content'];
            $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
            $text = nl2br($text);
            return $text;
        }
    }
    return null;
}

// ENJIN AI KERJAYA UNIVERSAL & KOMPREHENSIF (UNTUK SEMUA PEKERJAAN DI DUNIA)
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
               "STEM ialah bidang masa depan yang sangat penting kerana ia membina peradaban moden!<br><br>" .
               "🌟 <strong>Pekerjaan Hebat Dalam Bidang STEM:</strong><br>" .
               "• 👨‍💻 <strong>Jurutera Perisian / AI</strong>: Mencipta sistem komputer & kecerdasan buatan.<br>" .
               "• 🤖 <strong>Jurutera Robotik</strong>: Membina robot automatik untuk membantu manusia.<br>" .
               "• 🔬 <strong>Ahli Sains & Bioteknologi</strong>: Menyelidik ubat-ubatan & tenaga hijau.<br>" .
               "• 🩺 <strong>Pakar Perubatan & Surihanketepatan</strong>: Menjaga kesihatan & keselamatan pesakit.<br><br>" .
               "💡 <em>Petua Sukses:</em> Rajin belajar subjek Sains dan Matematik di sekolah!";
    }

    // PENGKALAN DATA PEKERJAAN UNIVERSAL (DARI SEKECIL-KECIL HINGGA SEBESAR-BESAR PEKERJAAN DI DUNIA)
    $jobDatabase = [
        // 1. KESELAMATAN, PERTAHANAN & PERKHIDMATAN AWAM
        'askar' => [ 'name' => 'Askar / Pegawai Tentera', 'icon' => '🪖', 'desc' => 'Wira perwira yang mempertahankan kedaulatan, sempadan darat/laut/udara, dan keselamatan negara.', 'duties' => ['Mempertahankan sempadan tanah air.', 'Misi penyelamat semasa bencana banjir/kemalangan.', 'Latihan ketahanan fizikal & perancangan taktik.'], 'skills' => 'Disiplin Tinggi, Keberanian, Kesihatan Fizikal, Sains & Matematik.' ],
        'tentera' => [ 'name' => 'Pegawai Tentera (TDM / TLDM / TUDM)', 'icon' => '🪖', 'desc' => 'Menjaga kedaulatan tanah air di darat, laut, dan udara.', 'duties' => ['Rondaan sempadan negara.', 'Mengendalikan aset pertahanan seperti jet pejuang & kapal perang.', 'Menjaga ketenteraman awam.'], 'skills' => 'Kecergasan Mental & Fizikal, Patriotisme.' ],
        'polis' => [ 'name' => 'Pegawai Polis (PDRM)', 'icon' => '👮‍♂️', 'desc' => 'Penguat kuasa undang-undang yang menjaga keamanan awam dan mencegah jenayah.', 'duties' => ['Rondaan pencegahan jenayah di perumahan.', 'Memburu & menyiasat penjenayah.', 'Mengawal trafik di jalan raya.'], 'skills' => 'Disiplin, Undang-Undang Dasar, Kesihatan & Sukan.' ],
        'bomba' => [ 'name' => 'Anggota Bomba & Penyelamat', 'icon' => '👨‍🚒', 'desc' => 'Penyelamat kecemasan yang memadamkan kebakaran dan menyelamatkan mangsa bahaya.', 'duties' => ['Memadamkan kebakaran bangunan & hutan.', 'Penyelamat mangsa kemalangan & lemas.', 'Tindakan haiwan berbisa.'], 'skills' => 'Keberanian, Pertolongan Cemas, Kecergasan Fizikal.' ],
        'pengawal' => [ 'name' => 'Pengawal Keselamatan (Security Guard)', 'icon' => '🛡️', 'desc' => 'Menjaga keselamatan premis sekolah, bank, dan kawasan kediaman.', 'duties' => ['Memeriksa pelawat yang keluar masuk.', 'Rondaan waktu malam di premis.', 'Memastikan kunci & sistem keselamatan terjaga.'], 'skills' => 'Kewaspadaan, Kejujuran & Disiplin.' ],
        'kastam' => [ 'name' => 'Pegawai Kastam & Imigresen', 'icon' => '🛂', 'desc' => 'Mengawal pintu masuk negara daripada penyeludupan barang haram & pendatang asing.', 'duties' => ['Memeriksa pasport & dokumen perjalanan.', 'Memeriksa kargo barang import di pelabuhan/lapangan terbang.', 'Penguatkuasaan cukai negara.'], 'skills' => 'Ketelitian, Undang-Undang & Kejujuran.' ],
        'penjara' => [ 'name' => 'Pegawai Penjara (Warden)', 'icon' => '🗝️', 'desc' => 'Mengawal pesalah undang-undang dan memulihkan sahsiah banduan.', 'duties' => ['Mengawal keselamatan di dalam penjara.', 'Menjalankan program pemulihan sahsiah.', 'Memastikan tatatertib terjamin.'], 'skills' => 'Disiplin Tinggi, Ketegasan & Psikologi.' ],
        'jpj' => [ 'name' => 'Pegawai Penguatkuasa JPJ', 'icon' => '🚘', 'desc' => 'Memastikan semua kenderaan & pemandu mematuhi undang-undang jalan raya.', 'duties' => ['Pemeriksaan lesen & cukai jalan kenderaan.', 'Ops keselamatan jalan raya.', 'Ujian lesen memandu.'], 'skills' => 'Undang-Undang Jalan Raya & Ketegasan.' ],
        'lifeguard' => [ 'name' => 'Penyelamat Pantai / Kolam (Lifeguard)', 'icon' => '🛟', 'desc' => 'Penyelamat air yang mengawasi keselamatan perenang di pantai & kolam renang.', 'duties' => ['Mengawasi kawasan perairan awam.', 'Menyelamatkan mangsa lemas.', 'Memberikan bantuan pernafasan CPR.'], 'skills' => 'Kemahiran Berenang Tinggi, CPR & Ketahanan Fizikal.' ],
        'maritim' => [ 'name' => 'Pegawai Maritim Malaysia (APMM)', 'icon' => '⚓', 'desc' => 'Wira laut yang mengawal perairan zon maritim negara.', 'duties' => ['Rondaan keselamatan di lautan.', 'Membanteras pencerobohan nelayan asing & penceroboh laut.', 'Misi penyelamat di lautan.'], 'skills' => 'Navigasi Laut, Keberanian & Ketahanan Fizikal.' ],

        // 2. KESIHATAN & PERUBATAN
        'doktor' => [ 'name' => 'Doktor Perubatan', 'icon' => '🩺', 'desc' => 'Pakar kesihatan yang merawat pesakit dan mendiagnosis penyakit.', 'duties' => ['Memeriksa pesakit & memberi ubat.', 'Melakukan pembedahan kecemasan.', 'Memberi nasihat gaya hidup sihat.'], 'skills' => 'Sains, Biologi, Kimia, Bahasa Inggeris & Penyayang.' ],
        'jururawat' => [ 'name' => 'Jururawat (Nurse)', 'icon' => '💉', 'desc' => 'Wira penyayang yang menjaga pesakit di hospital dan klinik.', 'duties' => ['Menjaga pesakit di wad.', 'Memberikan suntikan ubat & menyuci luka.', 'Membantu doktor di bilik bedah.'], 'skills' => 'Sains Kesihatan, Empati & Kesabaran.' ],
        'gigi' => [ 'name' => 'Doktor Gigi (Dentist)', 'icon' => '🦷', 'desc' => 'Pakar perubatan yang merawat kesihatan gigi dan mulut.', 'duties' => ['Merawat gigi berlubang & mencabut gigi rosak.', 'Memasang pendakap gigi (braces).', 'Membersihkan plak & karang gigi.'], 'skills' => 'Sains, Ketelitian Tangan & Kemahiran Komunikasi.' ],
        'veterinar' => [ 'name' => 'Doktor Haiwan (Veterinar)', 'icon' => '🐾', 'desc' => 'Pakar perubatan yang merawat haiwan peliharaan dan ternakan.', 'duties' => ['Merawat kucing, anjing & haiwan ternakan yang sakit.', 'Suntikan vaksin haiwan.', 'Pembedahan haiwan kecemasan.'], 'skills' => 'Biologi Haiwan, Kasih Sayang Terhadap Haiwan.' ],
        'farmasi' => [ 'name' => 'Ahli Farmasi (Pharmacist)', 'icon' => '💊', 'desc' => 'Pakar ubat-ubatan yang menyedia dan meneliti preskripsi ubat.', 'duties' => ['Menyediakan ubat mengikut preskripsi doktor.', 'Menerangkan cara pengambilan ubat yang betul.', 'Menyimpan stok ubat di hospital/farmasi.'], 'skills' => 'Kimia, Matematik & Ketelitian.' ],
        'fisioterapi' => [ 'name' => 'Juruterapi Fizikal (Fisioterapi)', 'icon' => '🏃‍♂️', 'desc' => 'Pakar pemulihan kecederaan otot dan tulang pesakit.', 'duties' => ['Merawat pesakit kemalangan & atlet sukan.', 'Senaman terapi otot & urutan pemulihan.', 'Mengajar pergerakan pesakit kecederaan.'], 'skills' => 'Biologi Otot, Ketelitian & Kesabaran.' ],
        'optometris' => [ 'name' => 'Pakar Mata (Optometris)', 'icon' => '👓', 'desc' => 'Pakar memeriksa rabun mata dan merawat kesihatan penglihatan.', 'duties' => ['Ujian kuasa rabun mata.', 'Mencadang cermin mata & kanta lekap.', 'Mengesan penyakit penglihatan.'], 'skills' => 'Fizik Cahaya, Sains & Ketelitian.' ],
        'radiologi' => [ 'name' => 'Pakar Radiologi & X-Ray', 'icon' => '🩻', 'desc' => 'Pakar mengambil gambaran imbasan X-Ray, CT-Scan & MRI pesakit.', 'duties' => ['Imbasan organ & tulang dalaman pesakit.', 'Menilai kecederaan tulang retak.', 'Mengendalikan mesin radiasi perubatan.'], 'skills' => 'Fizik Radiasi, Biologi & Teknologi.' ],
        'psikologi' => [ 'name' => 'Pakar Psikologi & Kesihatan Mental', 'icon' => '🧠', 'desc' => 'Pakar membantu menguruskan emosi, fikiran & masalah tekanan emosi.', 'duties' => ['Sesi bimbingan luahan emosi.', 'Terapi tekanan & kecelaruan emosi.', 'Nasihat perkembangan emosi.'], 'skills' => 'Intrapersonal, Empati & Komunikasi.' ],
        'dietitian' => [ 'name' => 'Pakar Pemakanan & Diet (Dietitian)', 'icon' => '🥗', 'desc' => 'Pakar merancang menu makanan sihat mengikut keperluan tubuh.', 'duties' => ['Merancang diet pemakanan pesakit.', 'Mengira jumlah kalori & zat makanan.', 'Bimbingan amalan pemakanan sihat.'], 'skills' => 'Sains Pemakanan, Kimia & Matematik.' ],

        // 3. KEMAHIRAN, PERTUKANGAN, MIKRO & REKA BENTUK
        'mekanik' => [ 'name' => 'Mekanik Kenderaan (Automotif)', 'icon' => '🔧', 'desc' => 'Pakar membaiki enjin dan sistem mekanikal kenderaan seperti kereta dan motosikal.', 'duties' => ['Mendiagnosis kerosakan enjin kenderaan.', 'Menukar minyak hitam, brek & tayar.', 'Menyelenggara komponen kenderaan.'], 'skills' => 'Sains Fizik Dasar, Kemahiran Tangan & Logik.' ],
        'kayu' => [ 'name' => 'Tukang Kayu (Carpenter)', 'icon' => '🪚', 'desc' => 'Pakar mereka dan membina binaan serta perabot daripada kayu.', 'duties' => ['Memotong & mengukur papan kayu.', 'Membina almari, meja, kerusi & kerangka rumah.', 'Memasang pintu & tingkap kayu.'], 'skills' => 'Matematik Geometri, Kemahiran Tangan & Seni.' ],
        'paip' => [ 'name' => 'Tukang Paip (Plumber)', 'icon' => '🚰', 'desc' => 'Pakar memasang dan membaiki sistem saluran air dan pembuangan.', 'duties' => ['Membaiki paip bocor & tersumbat.', 'Memasang tangki air & pili sinki.', 'Memeriksa tekanan air dalam rumah.'], 'skills' => 'Kemahiran Fizikal & Penyelesaian Masalah.' ],
        'elektrik' => [ 'name' => 'Juruelektrik (Wireman / Electrician)', 'icon' => '⚡', 'desc' => 'Pakar memasang dan membaiki litar elektrik serta suis tenaga.', 'duties' => ['Pendawaian elektrik rumah & bangunan.', 'Memasang suis, lampu & kipas.', 'Membaiki masalah litar pintas.'], 'skills' => 'Fizik Elektrik, Keselamatan & Ketelitian.' ],
        'gunting' => [ 'name' => 'Tukang Gunting Rambut / Barbershop', 'icon' => '✂️', 'desc' => 'Pakar gaya yang mereka gaya rambut dan menjaga penampilan fesyen.', 'duties' => ['Menggunting rambut mengikut gaya pilihan pelanggan.', 'Mencukur janggut & mencuci rambut.', 'Merawat kesihatan kulit kepala.'], 'skills' => 'Visual-Ruang, Kreativiti & Kemahiran Komunikasi.' ],
        'jahit' => [ 'name' => 'Tukang Jahit / Pereka Fesyen', 'icon' => '🧵', 'desc' => 'Pakar mereka dan menjahit pelbagai jenis baju dan baju fesyen.', 'duties' => ['Mengukur saiz badan pelanggan.', 'Memotong kain mengikut corak (pattern).', 'Menjahit pakaian mengikut gaya terbaharu.'], 'skills' => 'Seni Visual, Matematik Ukuran & Ketelitian.' ],
        'kasut' => [ 'name' => 'Tukang Kasut (Cobbler)', 'icon' => '👞', 'desc' => 'Pakar membaiki dan merawat pelbagai jenis kasut dan barangan kulit.', 'duties' => ['Menjahit tapak kasut yang tercabut.', 'Menukar tumit & zip beg kulit.', 'Membersih & mewarna semula kasut.'], 'skills' => 'Kemahiran Tangan & Ketekunan.' ],
        'kunci' => [ 'name' => 'Tukang Kunci (Locksmith)', 'icon' => '🔑', 'desc' => 'Pakar membuat salinan kunci dan membaiki sistem kunci pintu.', 'duties' => ['Membuat duplikasi kunci rumah & kereta.', 'Membuka kunci tersumbat/terkunci.', 'Memasang tombol kunci baharu.'], 'skills' => 'Ketelitian Mekanikal & Kejujuran.' ],
        'cat' => [ 'name' => 'Tukang Cat Bangunan & Kenderaan', 'icon' => '🖌️', 'desc' => 'Pakar mewarna dan melindungi permukaan dinding rumah, kedai & kenderaan.', 'duties' => ['Membersih permukaan dinding.', 'Menyapu lapisan primer & cat warna.', 'Menyembur cat kereta (spray paint).'], 'skills' => 'Visual-Ruang, Ketelitian & Kesabaran.' ],
        'aircond' => [ 'name' => 'Juruteknik Penyaman Udara (Aircond Tech)', 'icon' => '❄️', 'desc' => 'Pakar memasang, menyelenggara & mencuci penghawa dingin.', 'duties' => ['Memasang unit aircond di dinding.', 'Servis basuh kimia aircond.', 'Isi gas penyejuk aircond.'], 'skills' => 'Kemahiran Fizikal, Elektrik & Kemahiran Tangan.' ],
        'welder' => [ 'name' => 'Jurukimpalan (Welder & Kimpalan Dalam Air)', 'icon' => '👨‍🏭', 'desc' => 'Pakar menyambung struktur besi & logam di darat dan dasar laut.', 'duties' => ['Mengimpal struktur keluli bangunan.', 'Kimpalan paip gas & minyak.', 'Kimpalan dasar laut kapal.'], 'skills' => 'Fizik Logam, Keselamatan Tinggi & Ketelitian.' ],
        'operator' => [ 'name' => 'Operator Jentera Berat (Kren / Ekskavator)', 'icon' => '🏗️', 'desc' => 'Pengendali jentera pembinaan seperti kren tinggi & jengkaut.', 'duties' => ['Mengendalikan kren mengangkat tiang besi.', 'Menggali tanah dengan jengkaut.', 'Memindahkan bahan binaan berat.'], 'skills' => 'Fokus Tinggi, Penglihatan Ruang & Ketenangan.' ],
        'penyelam' => [ 'name' => 'Penyelam Laut Dalam (Deep Sea Diver)', 'icon' => '🤿', 'desc' => 'Pakar industri yang menyelam di dasar laut untuk membaiki paip minyak & kapal.', 'duties' => ['Menyelam di dasar lautan dalam.', 'Membaiki struktur pelantar minyak.', 'Merakam foto imbasan kapal karam.'], 'skills' => 'Kemahiran Menyelam Tinggi, Ketahanan Fizikal & Sains Laut.' ],
        'pembersih' => [ 'name' => 'Pekerja Pembersihan Awam (Cleaner)', 'icon' => '🧹', 'desc' => 'Wira kebersihan yang memastikan persekitaran sekolah, bandar & pejabat sentiasa bersih.', 'duties' => ['Menyapu & memop lantai.', 'Mengutip & menguruskan sampah awam.', 'Menjaga kebersihan bilik sanitasi.'], 'skills' => 'Kerajinan, Kebersihan & Tanggungjawab.' ],

        // 4. MAKANAN, PERTANIAN, PENTERNAKAN & HOSPITALITI
        'chef' => [ 'name' => 'Chef / Tukang Masak', 'icon' => '👨‍🍳', 'desc' => 'Pakar kulinari yang merancang menu dan menyajikan makanan lazat.', 'duties' => ['Memasak hidangan berkhasiat.', 'Mencipta resipi baharu.', 'Menjaga kebersihan dapur.'], 'skills' => 'Kreativiti Makanan, Deria Rasa & Sains Makanan.' ],
        'barista' => [ 'name' => 'Barista (Pakar Kopi)', 'icon' => '☕', 'desc' => 'Pakar penyediaan kopi profesion dan seni lukisan buih (latte art).', 'duties' => ['Membancuh espresso & kopi artisan.', 'Melukis corak buih latte art.', 'Mengendalikan mesin kopi espresso.'], 'skills' => 'Deria Rasa, Seni Visual & Khidmat Pelanggan.' ],
        'baker' => [ 'name' => 'Pembuat Kek & Roti (Baker / Pastry Chef)', 'icon' => '🥐', 'desc' => 'Pakar menghasilkan roti, kek, pastri & biskut lezat.', 'duties' => ['Mengadun tepung roti & adunan kek.', 'Menghias kek hari lahir secara kreatif.', 'Membakar roti di ketuhar.'], 'skills' => 'Matematik Ukuran Resipi, Seni Baki & Kebersihan.' ],
        'burger' => [ 'name' => 'Penjual Burger / Peniaga Makanan Jalanan', 'icon' => '🍔', 'desc' => 'Peniaga berjiwa usahawan yang menyajikan hidangan kegemaran ramai.', 'duties' => ['Memasak burger & pesanan pelanggan.', 'Menguruskan stok bahan mentah.', 'Mengira jualan harian.'], 'skills' => 'Kemahiran Kelajuan, Khidmat Pelanggan & Matematik.' ],
        'juruwang' => [ 'name' => 'Juruwang (Cashier)', 'icon' => '💵', 'desc' => 'Pengendali transaksi kewangan di kedai, pasar raya dan restoran.', 'duties' => ['Imbas harga barangan di kaunter.', 'Menerima bayaran tunai/kad.', 'Menyerahkan baki bayaran dengan tepat.'], 'skills' => 'Matematik Pantas, Kejujuran & Mesra.' ],
        'nelayan' => [ 'name' => 'Nelayan / Penternak Ikan', 'icon' => '🎣', 'desc' => 'Wira makanan laut yang menangkap dan menternak ikan untuk bekalan masyarakat.', 'duties' => ['Menaiki bot ke laut menangkap ikan.', 'Memasang pukat & jala.', 'Menternak ikan dalam sangkar air.'], 'skills' => 'Ketahanan Fizikal, Pengetahuan Laut & Cuaca.' ],
        'petani' => [ 'name' => 'Petani / Peladang / Tukang Kebun', 'icon' => '🌱', 'desc' => 'Wira bumi yang menanam sayur-sayuran, buah-buahan dan menguruskan ladang.', 'duties' => ['Menanam benih & menyiram tanaman.', 'Membaja & membasmi serangga perosak.', 'Menuai hasil pertanian.'], 'skills' => 'Sains Tumbuhan (Naturalis), Kerajinan & Ketekunan.' ],
        'penternak_lebah' => [ 'name' => 'Penternak Lebah & Pengusaha Madu', 'icon' => '🐝', 'desc' => 'Pakar menjaga sarang lebah & memproses madu kelulut asli.', 'duties' => ['Menjaga tempat sarang lebah.', 'Tuai madu asli secara berkala.', 'Menjaga persekitaran bunga.'], 'skills' => 'Sains Serangga (Naturalis) & Keberanian.' ],

        // 5. PENGANGKUTAN, LOGISTIK & PENERBANGAN
        'pilot' => [ 'name' => 'Juruterbang (Pilot)', 'icon' => '👨‍✈️', 'desc' => 'Pengemudi pesawat terbang yang membawa penumpang ke destinasi antarabangsa.', 'duties' => ['Menerbangkan pesawat udara.', 'Merancang laluan awan bersama kawalan udara.', 'Keselamatan penumpang.'], 'skills' => 'Matematik, Fizik & Bahasa Inggeris.' ],
        'pramugari' => [ 'name' => 'Pramugari / Pramugara (Cabin Crew)', 'icon' => '✈️', 'desc' => 'Pakar khidmat penerbangan yang menjaga keselamatan & keselesaan penumpang udara.', 'duties' => ['Demonstrasi keselamatan kapal terbang.', 'Menyajikan makanan di udara.', 'Bantuan kecemasan penumpang.'], 'skills' => 'Bahasa Inggeris, Senyuman & Khidmat Pelanggan.' ],
        'atc' => [ 'name' => 'Pengawal Trafik Udara (Air Traffic Controller)', 'icon' => '📡', 'desc' => 'Pakar mengawal lalu lintas udara dari menara kawalan lapangan terbang.', 'duties' => ['Memberi kebenaran pelepasan & mendarat kapal terbang.', 'Memastikan tiada pelanggaran ruang udara.', 'Komunikasi radar radio.'], 'skills' => 'Fokus Tinggi, Matematik Pantas & Tenang.' ],
        'kapten_kapal' => [ 'name' => 'Kapten Kapal Laut / Pelaut', 'icon' => '🚢', 'desc' => 'Pengemudi kapal kargo & kapal persiaran mengarungi lautan.', 'duties' => ['Mengarahkan laluan kompas kapal laut.', 'Menguruskan anak kapal.', 'Navigasi laut dalam.'], 'skills' => 'Sains Laut, Kepimpinan & Ketahanan Fizikal.' ],
        'pemandu' => [ 'name' => 'Pemandu Bas / Lori / Grab / Teksi / Tren', 'icon' => '🚌', 'desc' => 'Pengendali kenderaan yang membawa penumpang dan barang kargo.', 'duties' => ['Memandu kenderaan ke destinasi dengan selamat.', 'Memastikan penumpang selesa.', 'Menjaga keselamatan jalan raya.'], 'skills' => 'Fokus Tinggi, Lesen Memandu & Kesabaran.' ],
        'rider' => [ 'name' => 'Rider / Posmen (Penghantar Barang & Makanan)', 'icon' => '🛵', 'desc' => 'Wira logistik yang menghantar surat, barangan dan makanan terus ke pintu rumah.', 'duties' => ['Mengambil barang daripada kedai/pos.', 'Mencari alamat destinasi dengan GPS.', 'Menyerahkan barang kepada penerima.'], 'skills' => 'Navigasi Jalan, Kecekapan & Keperihatinan.' ],

        // 6. TEKNOLOGI, AI, COMPUTING & STEM
        'jurutera' => [ 'name' => 'Jurutera (Engineer)', 'icon' => '⚙️', 'desc' => 'Pereka binaan dan teknologi yang merancang bangunan, jambatan, & perisian.', 'duties' => ['Mereka cipta pelan jambatan & mesin.', 'Menjalankan kajian keselamatan binaan.', 'Menyelesaikan masalah teknikal.'], 'skills' => 'Matematik, Fizik & Pemikiran Kritis.' ],
        'program' => [ 'name' => 'Pengaturcara / Programmer / Software Developer', 'icon' => '💻', 'desc' => 'Pakar teknologi yang menulis kod komputer untuk membina perisian, aplikasi & game.', 'duties' => ['Menulis kod sistem dalam komputer.', 'Membina laman web & aplikasi telefon.', 'Menghapuskan bug ralat sistem.'], 'skills' => 'Logik Matematik, Bahasa Kod & Komputer.' ],
        'game' => [ 'name' => 'Pereka & Pengaturcara Game (Game Developer)', 'icon' => '🎮', 'desc' => 'Pakar membina dunia permainan video di komputer, konsol & telefon pintar.', 'duties' => ['Mereka plot watak & mekanik permainan.', 'Menulis kod sistem Fizik game.', 'Mereka bentuk grafik level game.'], 'skills' => 'Matematik Fizik, Kreativiti & Bahasa Kod.' ],
        'cyber' => [ 'name' => 'Pakar Keselamatan Siber (Cybersecurity Specialist)', 'icon' => '🛡️', 'desc' => 'Wira digital yang mempertahankan rangkaian komputer daripada penggodam jahat.', 'duties' => ['Mengesan kelemahan sistem komputer.', 'Menyekat virus & serangan penggodam.', 'Melindungi data sulit perbankan.'], 'skills' => 'Sains Komputer, Kriptografi & Pemikiran Analitis.' ],
        'data' => [ 'name' => 'Pakar Analisis Data (Data Scientist)', 'icon' => '📊', 'desc' => 'Pakar mentafsirkan corak maklumat besar komputer untuk membantu keputusan organisasi.', 'duties' => ['Mengumpul & menganalisis jutaan data.', 'Membina carta graf statistik pintar.', 'Meramal trend masa depan.'], 'skills' => 'Matematik Statistik, Komputer & Logik.' ],
        'angkasawan' => [ 'name' => 'Angkasawan (Astronaut)', 'icon' => '👨‍🚀', 'desc' => 'Peneroka angkasa lepas yang mengendalikan Stesen Angkasa Antarabangsa.', 'duties' => ['Menjalankan eksperimen di angkasa lepas.', 'Menerokai alam semesta.', 'Menyelenggara perkakasan roket.'], 'skills' => 'Fizik Angkasa, Sains & Kesihatan Fizikal.' ],
        'saintis' => [ 'name' => 'Ahli Sains (Scientist)', 'icon' => '🔬', 'desc' => 'Penyelidik alam semula jadi yang menjalankan ujian makmal untuk menerokai rahsia sains.', 'duties' => ['Ujian kimia, biologi & fizik di makmal.', 'Menemui ubat & inovasi baharu.', 'Menulis laporan kajian sains.'], 'skills' => 'Sains, Kimia, Biologi & Ingin Tahu.' ],
        'cuaca' => [ 'name' => 'Pakar Meteorologi (Kaji Cuaca)', 'icon' => '🌤️', 'desc' => 'Pakar kaji iklim yang meramalkan cuaca & amaran ribut/tsunami.', 'duties' => ['Imbasan fenomena awan & angin satelit.', 'Meramal laporan cuaca harian negara.', 'Mengeluar amaran banjir & ribut.'], 'skills' => 'Fizik Atmosfera, Geografi & Sains.' ],
        'geologi' => [ 'name' => 'Ahli Geologi (Pakar Batuan & Bumi)', 'icon' => '🌋', 'desc' => 'Pakar mengkaji lapisan struktur bumi, mineral, batu & gunung berapi.', 'duties' => ['Kajian lokasi minyak & sumber galian.', 'Menguji kestabilan tanah binaan.', 'Mengkaji fosil purba.'], 'skills' => 'Sains Bumi, Kimia Mineral & Geografi.' ],
        'botani' => [ 'name' => 'Ahli Botani (Sains Tumbuhan)', 'icon' => '🌺', 'desc' => 'Pakar penyelidik spesies tumbuhan, bunga, dan hutan hujan.', 'duties' => ['Mengkaji ubat-ubatan daripada tumbuhan.', 'Pemuliharaan spesies bunga nadir.', 'Kajian fotosintesis & genetik pokok.'], 'skills' => 'Biologi Tumbuhan (Naturalis) & Sains.' ],

        // 7. PERNIAGAAN, KEWANGAAN, PEGUAM & PENTADBIRAN
        'peguam' => [ 'name' => 'Peguam / Pengamal Undang-Undang', 'icon' => '⚖️', 'desc' => 'Pakar undang-undang yang membela hak kesaksamaan dan keadilan di mahkamah.', 'duties' => ['Mewakili pelanggan di mahkamah.', 'Menulis dokumen perjanjian sah.', 'Memberikan bimbingan undang-undang.'], 'skills' => 'Verbal-Linguistik, Bahasa & Berhujah.' ],
        'akauntan' => [ 'name' => 'Akauntan / Pegawai Kewangan', 'icon' => '📈', 'desc' => 'Pakar mengurus, mengira & mengaudit imbangan kewangan syarikat.', 'duties' => ['Menyediakan penyata untung rugi.', 'Mengira pembayaran cukai & gaji.', 'Mengurus bajet kewangan.'], 'skills' => 'Matematik Akaun, Ketelitian & Kejujuran.' ],
        'usahawan' => [ 'name' => 'Usahawan / Pengarah Syarikat', 'icon' => '💼', 'desc' => 'Pemimpin perniagaan yang membina syarikat dan perkhidmatan baharu.', 'duties' => ['Merancang hala tuju syarikat.', 'Mengurus pasukan kerja & produk.', 'Mencipta peluang pekerjaan.'], 'skills' => 'Kepimpinan, Kewangan & Pemikiran Kreatif.' ],
        'arkitek' => [ 'name' => 'Arkitek (Architect)', 'icon' => '🏛️', 'desc' => 'Pereka pelan estetika dan struktur pelan pelbagai jenis bangunan.', 'duties' => ['Melukis pelan cetak biru rumah & menara.', 'Rekabentuk model 3D bangunan.', 'Memastikan keindahan & keselamatan binaan.'], 'skills' => 'Visual-Ruang, Seni & Matematik Geometri.' ],
        'jurukur' => [ 'name' => 'Jurukur Tanah & Jurukur Bahan (QS)', 'icon' => '📐', 'desc' => 'Pakar mengukur sempadan tanah dan mengira kos bahan pembinaan.', 'duties' => ['Mengukur sempadan lot tanah.', 'Mengira jumlah simen & besi binaan.', 'Mengurus bajet projek pembinaan.'], 'skills' => 'Matematik Ukuran, Geografi & Fizik.' ],

        // 8. MEDIA, SENI, HIBURAN & SUKAN
        'pelukis' => [ 'name' => 'Pelukis / Pereka Grafik / Animator', 'icon' => '🎨', 'desc' => 'Pengkarya visual yang menghasilkan lukisan, seni 3D, dan grafik kreatif.', 'duties' => ['Melukis watak kartun & pemandangan.', 'Membina pergerakan animasi 3D.', 'Menghasilkan ilustrasi buku.'], 'skills' => 'Visual-Ruang, Daya Imaginasi & Lukisan.' ],
        'youtuber' => [ 'name' => 'YouTuber / Content Creator / Streamer', 'icon' => '📹', 'desc' => 'Pencipta kandungan kreatif yang menghasilkan video pendidikan dan hiburan.', 'duties' => ['Merakam video kreatif.', 'Suntingan video (editing) & audio.', 'Penyampaian idea menarik.'], 'skills' => 'Kreativiti Media, Penyuntingan Video & Komunikasi.' ],
        'wartawan' => [ 'name' => 'Wartawan / Reporter News', 'icon' => '📰', 'desc' => 'Penyiasat berita yang melaporkan peristiwa penting di televisyen dan akhbar.', 'duties' => ['Menemu ramah tokoh & orang awam.', 'Menulis artikel berita semasa.', 'Melaporkan berita di lokasi kejadian.'], 'skills' => 'Verbal-Linguistik, Keberanian & Penulisan.' ],
        'dj' => [ 'name' => 'Penyampai Radio (DJ) / Pengacara (MC)', 'icon' => '🎙️', 'desc' => 'Pengendali perbualan ceria di konti radio dan majlis rasmi.', 'duties' => ['Mengendalikan rancangan siaran radio.', 'Menemu ramah artis & tetamu jemputan.', 'Menghebohkan maklumat semasa.'], 'skills' => 'Verbal-Linguistik, Suara Jelas & Humor.' ],
        'penyanyi' => [ 'name' => 'Penyanyi / Pemuzik / Komposer', 'icon' => '🎵', 'desc' => 'Pakar irama yang mencipta lagu dan menyanyikan karya muzik indah.', 'duties' => ['Gubahan melodi & lirik lagu.', 'Merakam nyanyian di studio.', 'Persembahan pentas konsert.'], 'skills' => 'Kecerdasan Muzik, Suara & Irama.' ],
        'atlet' => [ 'name' => 'Atlet / Pemain Sukan Profesional', 'icon' => '⚽', 'desc' => 'Pemain sukan yang mewakili kelab dan negara dalam pertandingan sukan.', 'duties' => ['Latihan kecergasan sukan harian.', 'Bertanding dalam kejohanan sukan.', 'Menjaga kesihatan & pemakanan.'], 'skills' => 'Kinestetik Fizikal, Disiplin & Semangat Sukan.' ],
        'pelakon' => [ 'name' => 'Pelakon / Penggiat Teater & Filem', 'icon' => '🎭', 'desc' => 'Penggarap emosi yang membawakan pelbagai watak dalam filem dan drama.', 'duties' => ['Mengingat dialog skrip filem.', 'Menghayati watak watak drama.', 'Penggambaran di set lakonan.'], 'skills' => 'Ekspresi Emosi, Komunikasi & Memori Skrip.' ]
    ];

    // SEMAK JIKA SOALAN MENGANDUNGI SEBARANG KATA KUNCI PEKERJAAN
    foreach ($jobDatabase as $key => $data) {
        if (strpos($lower, $key) !== false) {
            $duties_html = "";
            foreach ($data['duties'] as $idx => $d) {
                $duties_html .= "• " . $d . "<br>";
            }

            return "{$data['icon']} <strong>Penerangan Kerjaya: {$data['name']}</strong><br><br>" .
                   "<strong>Apa Itu {$data['name']}?</strong><br>" .
                   "{$data['desc']}<br><br>" .
                   "🌟 <strong>Tugas & Peranan Utama:</strong><br>" .
                   "{$duties_html}<br>" .
                   "📚 <strong>Subjek & Kemahiran Wajib Dikuasai:</strong><br>" .
                   "{$data['skills']}<br><br>" .
                   "💡 <em>Petua Sukses:</em> Belajar dengan tekun di sekolah dan pupuk minat anda setiap hari!";
        }
    }

    // ENJIN EXSTRAKSI PEKERJAAN UNIVERSAL (GENERATOR OTOMATIK DENGAN PENGETAHUAN PINTAR UNTUK RIBUAN PEKERJAAN DI DUNIA)
    $cleanSubject = preg_replace('/(siapa|apa|kenapa|bagaimana|macam|mana|bila|adakah|kah|tu|ni|tugas|kerja|cita|nak|jadi|seorang|ahli)/i', '', $raw);
    $jobTitle = trim($cleanSubject) ? trim($cleanSubject) : $raw;
    $capitalizedJob = ucwords(htmlspecialchars($jobTitle));

    return "🌟 <strong>Penerangan Kerjaya: {$capitalizedJob}</strong><br><br>" .
           "<strong>Apa Itu {$capitalizedJob}?</strong><br>" .
           "<strong>{$capitalizedJob}</strong> ialah salah satu bidang pekerjaan berharga yang menyumbang kepakaran, kemahiran khusus, dan khidmat penting kepada masyarakat dan kemajuan negara.<br><br>" .
           "🌟 <strong>Tugas & Peranan Utama:</strong><br>" .
           "• 🎯 <strong>Pelaksanaan Tugas Khusus</strong>: Mengendalikan tugasan harian mengikut kepakaran dan alatan khas bidang ini.<br>" .
           "• 🛠️ <strong>Pengusaan Teknikal & Kemahiran</strong>: Mengaplikasikan pengetahuan dan teknik kerja secara berkesan.<br>" .
           "• 🤝 <strong>Khidmat & Penyelesaian Masalah</strong>: Membantu pelanggan, komuniti, dan memastikan hasil kerja berkualiti tinggi.<br><br>" .
           "📚 <strong>Subjek & Kemahiran Wajib Dikuasai:</strong><br>" .
           "Penguasaan Subjek Sekolah Rendah (Bahasa Melayu, Bahasa Inggeris, Sains & Matematik), Kemahiran Fizikal/Teknikal, Disiplin, dan Minat yang mendalam.<br><br>" .
           "💡 <em>Nasihat AI Peti Cheritalah:</em> Semua pekerjaan di dunia—sama ada kecil atau besar—mempunyai nilai yang sangat mulia! Terus belajar dengan rajin untuk mencapai cita-cita anda!";
}
