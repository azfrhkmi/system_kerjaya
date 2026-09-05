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

    // PENGKALAN DATA PEKERJAAN UNIVERSAL (DARI SEECIL-KECIL HINGGA SEBESAR-BESAR PEKERJAAN DI DUNIA)
    $jobDatabase = [
        // 1. KESELAMATAN & PERKHIDMATAN AWAM
        'askar' => [ 'name' => 'Askar / Pegawai Tentera', 'icon' => '🪖', 'desc' => 'Wira perwira yang mempertahankan kedaulatan, sempadan darat/laut/udara, dan keselamatan negara.', 'duties' => ['Mempertahankan sempadan tanah air.', 'Misi penyelamat semasa bencana banjir/kemalangan.', 'Latihan ketahanan fizikal & perancangan taktik.'], 'skills' => 'Disiplin Tinggi, Keberanian, Kesihatan Fizikal, Sains & Matematik.' ],
        'tentera' => [ 'name' => 'Pegawai Tentera (TDM / TLDM / TUDM)', 'icon' => '🪖', 'desc' => 'Menjaga kedaulatan tanah air di darat, laut, dan udara.', 'duties' => ['Rondaan sempadan negara.', 'Mengendalikan aset pertahanan seperti jet pejuang & kapal perang.', 'Menjaga ketenteraman awam.'], 'skills' => 'Kecergasan Mental & Fizikal, Patriotisme.' ],
        'polis' => [ 'name' => 'Pegawai Polis (PDRM)', 'icon' => '👮‍♂️', 'desc' => 'Penguat kuasa undang-undang yang menjaga keamanan awam dan mencegah jenayah.', 'duties' => ['Rondaan pencegahan jenayah di perumahan.', 'Memburu & menyiasat penjenayah.', 'Mengawal trafik di jalan raya.'], 'skills' => 'Disiplin, Undang-Undang Dasar, Kesihatan & Sukan.' ],
        'bomba' => [ 'name' => 'Anggota Bomba & Penyelamat', 'icon' => '👨‍🚒', 'desc' => 'Penyelamat kecemasan yang memadamkan kebakaran dan menyelamatkan mangsa bahaya.', 'duties' => ['Memadamkan kebakaran bangunan & hutan.', 'Penyelamat mangsa kemalangan & lemas.', 'Tindakan haiwan berbisa.'], 'skills' => 'Keberanian, Pertolongan Cemas, Kecergasan Fizikal.' ],
        'pengawal' => [ 'name' => 'Pengawal Keselamatan (Security Guard)', 'icon' => '🛡️', 'desc' => 'Menjaga keselamatan premis sekolah, bank, dan kawasan kediaman.', 'duties' => ['Memeriksa pelawat yang keluar masuk.', 'Rondaan waktu malam di premis.', 'Memastikan kunci & sistem keselamatan terjaga.'], 'skills' => 'Kewaspadaan, Kejujuran & Disiplin.' ],

        // 2. KESIHATAN & RAWATAN
        'doktor' => [ 'name' => 'Doktor Perubatan', 'icon' => '🩺', 'desc' => 'Pakar kesihatan yang merawat pesakit dan mendiagnosis penyakit.', 'duties' => ['Memeriksa pesakit & memberi ubat.', 'Melakukan pembedahan kecemasan.', 'Memberi nasihat gaya hidup sihat.'], 'skills' => 'Sains, Biologi, Kimia, Bahasa Inggeris & Penyayang.' ],
        'jururawat' => [ 'name' => 'Jururawat (Nurse)', 'icon' => '💉', 'desc' => 'Wira penyayang yang menjaga pesakit di hospital dan klinik.', 'duties' => ['Menjaga pesakit di wad.', 'Memberikan suntikan ubat & menyuci luka.', 'Membantu doktor di bilik bedah.'], 'skills' => 'Sains Kesihatan, Empati & Kesabaran.' ],
        'gigi' => [ 'name' => 'Doktor Gigi (Dentist)', 'icon' => '🦷', 'desc' => 'Pakar perubatan yang merawat kesihatan gigi dan mulut.', 'duties' => ['Merawat gigi berlubang & mencabut gigi rosak.', 'Memasang pendakap gigi (braces).', 'Membersihkan plak & karang gigi.'], 'skills' => 'Sains, Ketelitian Tangan & Kemahiran Komunikasi.' ],
        'veterinar' => [ 'name' => 'Doktor Haiwan (Veterinar)', 'icon' => '🐾', 'desc' => 'Pakar perubatan yang merawat haiwan peliharaan dan ternakan.', 'duties' => ['Merawat kucing, anjing & haiwan ternakan yang sakit.', 'Suntikan vaksin haiwan.', 'Pembedahan haiwan kecemasan.'], 'skills' => 'Biologi Haiwan, Kasih Sayang Terhadap Haiwan.' ],
        'farmasi' => [ 'name' => 'Ahli Farmasi (Pharmacist)', 'icon' => '💊', 'desc' => 'Pakar ubat-ubatan yang menyedia dan meneliti preskripsi ubat.', 'duties' => ['Menyediakan ubat mengikut preskripsi doktor.', 'Menerangkan cara pengambilan ubat yang betul.', 'Menyimpan stok ubat di hospital/farmasi.'], 'skills' => 'Kimia, Matematik & Ketelitian.' ],

        // 3. KEMAHIRAN, PERTUKANGAN & REKA BENTUK
        'mekanik' => [ 'name' => 'Mekanik Kenderaan (Automotif)', 'icon' => '🔧', 'desc' => 'Pakar membaiki enjin dan sistem mekanikal kenderaan seperti kereta dan motosikal.', 'duties' => ['Mendiagnosis kerosakan enjin kenderaan.', 'Menukar minyak hitam, brek & tayar.', 'Menyelenggara komponen kenderaan.'], 'skills' => 'Sains Fizik Dasar, Kemahiran Tangan & Logik.' ],
        'kayu' => [ 'name' => 'Tukang Kayu (Carpenter)', 'icon' => '🪚', 'desc' => 'Pakar mereka dan membina binaan serta perabot daripada kayu.', 'duties' => ['Memotong & mengukur papan kayu.', 'Membina almari, meja, kerusi & kerangka rumah.', 'Memasang pintu & tingkap kayu.'], 'skills' => 'Matematik Geometri, Kemahiran Tangan & Seni.' ],
        'paip' => [ 'name' => 'Tukang Paip (Plumber)', 'icon' => '🚰', 'desc' => 'Pakar memasang dan membaiki sistem saluran air dan pembuangan.', 'duties' => ['Membaiki paip bocor & tersumbat.', 'Memasang tangki air & pili sinki.', 'Memeriksa tekanan air dalam rumah.'], 'skills' => 'Kemahiran Fizikal & Penyelesaian Masalah.' ],
        'elektrik' => [ 'name' => 'Juruelektrik (Wireman / Electrician)', 'icon' => '⚡', 'desc' => 'Pakar memasang dan membaiki litar elektrik serta suis tenaga.', 'duties' => ['Pendawaian elektrik rumah & bangunan.', 'Memasang suis, lampu & kipas.', 'Membaiki masalah litar pintas.'], 'skills' => 'Fizik Elektrik, Keselamatan & Ketelitian.' ],
        'gunting' => [ 'name' => 'Tukang Gunting Rambut / Barbershop', 'icon' => '✂️', 'desc' => 'Pakar gaya yang mereka gaya rambut dan menjaga penampilan fesyen.', 'duties' => ['Menggunting rambut mengikut gaya pilihan pelanggan.', 'Mencukur janggut & mencuci rambut.', 'Merawat kesihatan kulit kepala.'], 'skills' => 'Visual-Ruang, Kreativiti & Kemahiran Komunikasi.' ],
        'jahit' => [ 'name' => 'Tukang Jahit / Pereka Fesyen', 'icon' => '🧵', 'desc' => 'Pakar mereka dan menjahit pelbagai jenis baju dan baju fesyen.', 'duties' => ['Mengukur saiz badan pelanggan.', 'Memotong kain mengikut corak (pattern).', 'Menjahit pakaian mengikut gaya terbaharu.'], 'skills' => 'Seni Visual, Matematik Ukuran & Ketelitian.' ],
        'kasut' => [ 'name' => 'Tukang Kasut (Cobbler)', 'icon' => '👞', 'desc' => 'Pakar membaiki dan merawat pelbagai jenis kasut dan barangan kulit.', 'duties' => ['Menjahit tapak kasut yang tercabut.', 'Menukar tumit & zip beg kulit.', 'Membersih & mewarna semula kasut.'], 'skills' => 'Kemahiran Tangan & Ketekunan.' ],
        'kunci' => [ 'name' => 'Tukang Kunci (Locksmith)', 'icon' => '🔑', 'desc' => 'Pakar membuat salinan kunci dan membaiki sistem kunci pintu.', 'duties' => ['Membuat duplikasi kunci rumah & kereta.', 'Membuka kunci tersumbat/terkunci.', 'Memasang tombol kunci baharu.'], 'skills' => 'Ketelitian Mekanikal & Kejujuran.' ],
        'pembersih' => [ 'name' => 'Pekerja Pembersihan Awam (Cleaner)', 'icon' => '🧹', 'desc' => 'Wira kebersihan yang memastikan persekitaran sekolah, bandar & pejabat sentiasa bersih.', 'duties' => ['Menyapu & memop lantai.', 'Mengutip & menguruskan sampah awam.', 'Menjaga kebersihan bilik sanitasi.'], 'skills' => 'Kerajinan, Kebersihan & Tanggungjawab.' ],

        // 4. MAKANAN, PERTANIAN & PENTERNAKAN
        'chef' => [ 'name' => 'Chef / Tukang Masak', 'icon' => '👨‍🍳', 'desc' => 'Pakar kulinari yang merancang menu dan menyajikan makanan lazat.', 'duties' => ['Memasak hidangan berkhasiat.', 'Mencipta resipi baharu.', 'Menjaga kebersihan dapur.'], 'skills' => 'Kreativiti Makanan, Deria Rasa & Sains Makanan.' ],
        'burger' => [ 'name' => 'Penjual Burger / Peniaga Makanan Jalanan', 'icon' => '🍔', 'desc' => 'Peniaga berjiwa usahawan yang menyajikan hidangan kegemaran ramai.', 'duties' => ['Memasak burger & pesanan pelanggan.', 'Menguruskan stok bahan mentah.', 'Mengira jualan harian.'], 'skills' => 'Kemahiran Kelajuan, Khidmat Pelanggan & Matematik.' ],
        'juruwang' => [ 'name' => 'Juruwang (Cashier)', 'icon' => '💵', 'desc' => 'Pengendali transaksi kewangan di kedai, pasar raya dan restoran.', 'duties' => ['Imbas harga barangan di kaunter.', 'Menerima bayaran tunai/kad.', 'Menyerahkan baki bayaran dengan tepat.'], 'skills' => 'Matematik Pantas, Kejujuran & Mesra.' ],
        'nelayan' => [ 'name' => 'Nelayan / Penternak Ikan', 'icon' => '🎣', 'desc' => 'Wira makanan laut yang menangkap dan menternak ikan untuk bekalan masyarakat.', 'duties' => ['Menaiki bot ke laut menangkap ikan.', 'Memasang pukat & jala.', 'Menternak ikan dalam sangkar air.'], 'skills' => 'Ketahanan Fizikal, Pengetahuan Laut & Cuaca.' ],
        'petani' => [ 'name' => 'Petani / Peladang / Tukang Kebun', 'icon' => '🌱', 'desc' => 'Wira bumi yang menanam sayur-sayuran, buah-buahan dan menguruskan ladang.', 'duties' => ['Menanam benih & menyiram tanaman.', 'Membaja & membasmi serangga perosak.', 'Menuai hasil pertanian.'], 'skills' => 'Sains Tumbuhan (Naturalis), Kerajinan & Ketekunan.' ],

        // 5. PENGANGKUTAN & LOGISTIK
        'pilot' => [ 'name' => 'Juruterbang (Pilot)', 'icon' => '👨‍✈️', 'desc' => 'Pengemudi pesawat terbang yang membawa penumpang ke destinasi antarabangsa.', 'duties' => ['Menerbangkan pesawat udara.', 'Merancang laluan awan bersama kawalan udara.', 'Keselamatan penumpang.'], 'skills' => 'Matematik, Fizik & Bahasa Inggeris.' ],
        'pemandu' => [ 'name' => 'Pemandu Bas / Lori / Grab / Teksi / Tren', 'icon' => '🚌', 'desc' => 'Pengendali kenderaan yang membawa penumpang dan barang kargo.', 'duties' => ['Memandu kenderaan ke destinasi dengan selamat.', 'Memastikan penumpang selesa.', 'Menjaga keselamatan jalan raya.'], 'skills' => 'Fokus Tinggi, Lesen Memandu & Kesabaran.' ],
        'rider' => [ 'name' => 'Rider / Posmen (Penghantar Barang & Makanan)', 'icon' => '🛵', 'desc' => 'Wira logistik yang menghantar surat, barangan dan makanan terus ke pintu rumah.', 'duties' => ['Mengambil barang daripada kedai/pos.', 'Mencari alamat destinasi dengan GPS.', 'Menyerahkan barang kepada penerima.'], 'skills' => 'Navigasi Jalan, Kecekapan & Keperihatinan.' ],

        // 6. TEKNOLOGI, AI & STEM
        'jurutera' => [ 'name' => 'Jurutera (Engineer)', 'icon' => '⚙️', 'desc' => 'Pereka binaan dan teknologi yang merancang bangunan, jambatan, & perisian.', 'duties' => ['Mereka cipta pelan jambatan & mesin.', 'Menjalankan kajian keselamatan binaan.', 'Menyelesaikan masalah teknikal.'], 'skills' => 'Matematik, Fizik & Pemikiran Kritis.' ],
        'program' => [ 'name' => 'Pengaturcara / Programmer / Software Developer', 'icon' => '💻', 'desc' => 'Pakar teknologi yang menulis kod komputer untuk membina perisian, aplikasi & game.', 'duties' => ['Menulis kod sistem dalam komputer.', 'Membina laman web & aplikasi telefon.', 'Menghapuskan bug ralat sistem.'], 'skills' => 'Logik Matematik, Bahasa Kod & Komputer.' ],
        'cyber' => [ 'name' => 'Pakar Keselamatan Siber (Cybersecurity Specialist)', 'icon' => '🛡️', 'desc' => 'Wira digital yang mempertahankan rangkaian komputer daripada penggodam jahat.', 'duties' => ['Mengesan kelemahan sistem komputer.', 'Menyekat virus & serangan penggodam.', 'Melindungi data sulit perbankan.'], 'skills' => 'Sains Komputer, Kriptografi & Pemikiran Analitis.' ],
        'angkasawan' => [ 'name' => 'Angkasawan (Astronaut)', 'icon' => '👨‍🚀', 'desc' => 'Peneroka angkasa lepas yang mengendalikan Stesen Angkasa Antarabangsa.', 'duties' => ['Menjalankan eksperimen di angkasa lepas.', 'Menerokai alam semesta.', 'Menyelenggara perkakasan roket.'], 'skills' => 'Fizik Angkasa, Sains & Kesihatan Fizikal.' ],

        // 7. MEDIA, SENI & HIBURAN
        'pelukis' => [ 'name' => 'Pelukis / Pereka Grafik / Animator', 'icon' => '🎨', 'desc' => 'Pengkarya visual yang menghasilkan lukisan, seni 3D, dan grafik kreatif.', 'duties' => ['Melukis watak kartun & pemandangan.', 'Membina pergerakan animasi 3D.', 'Menghasilkan ilustrasi buku.'], 'skills' => 'Visual-Ruang, Daya Imaginasi & Lukisan.' ],
        'youtuber' => [ 'name' => 'YouTuber / Content Creator / Streamer', 'icon' => '📹', 'desc' => 'Pencipta kandungan kreatif yang menghasilkan video pendidikan dan hiburan.', 'duties' => ['Merakam video kreatif.', 'Suntingan video (editing) & audio.', 'Penyampaian idea menarik.'], 'skills' => 'Kreativiti Media, Penyuntingan Video & Komunikasi.' ],
        'wartawan' => [ 'name' => 'Wartawan / Reporter News', 'icon' => '📰', 'desc' => 'Penyiasat berita yang melaporkan peristiwa penting di televisyen dan akhbar.', 'duties' => ['Menemu ramah tokoh & orang awam.', 'Menulis artikel berita semasa.', 'Melaporkan berita di lokasi kejadian.'], 'skills' => 'Verbal-Linguistik, Keberanian & Penulisan.' ],
        'wartawan_foto' => [ 'name' => 'Jurufoto / Videografer', 'icon' => '📸', 'desc' => 'Pakar lensa yang merakam gambar dan video momen penting.', 'duties' => ['Merakam foto profesional.', 'Mengatur pencahayaan kamera.', 'Suntingan warna foto digital.'], 'skills' => 'Visual-Ruang, Kemahiran Kamera & Seni.' ]
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

    // JIKA TIADA KATA KUNCI TERSEDIA, GUNAKAN ENJIN EXSTRAKSI PEKERJAAN UNIVERSAL (GENERATOR OTOMATIK UNTUK APA SAHAJA PEKERJAAN DI DUNIA!)
    $cleanSubject = preg_replace('/(siapa|apa|kenapa|bagaimana|macam|mana|bila|adakah|kah|tu|ni|tugas|kerja|cita|nak|jadi)/i', '', $raw);
    $jobTitle = trim($cleanSubject) ? trim($cleanSubject) : $raw;
    $capitalizedJob = ucwords(htmlspecialchars($jobTitle));

    return "🌟 <strong>Penerangan Kerjaya: {$capitalizedJob}</strong><br><br>" .
           "<strong>Apa Itu {$capitalizedJob}?</strong><br>" .
           "{$capitalizedJob} ialah salah satu pekerjaan penting yang menyumbang kemahiran dan perkhidmatan berharga kepada masyarakat dan negara.<br><br>" .
           "🌟 <strong>Tugas & Peranan Utama:</strong><br>" .
           "• 🎯 <strong>Melaksanakan Tugas Khusus</strong>: Menggunakan kemahiran khas untuk menyelesaikan tugasan harian.<br>" .
           "• 🛠️ <strong>Penggunaan Alat & Kemahiran</strong>: Mengendalikan alatan serta teknik kerja secara profesional.<br>" .
           "• 🤝 <strong>Khidmat Masyarakat</strong>: Membantu pelanggan, komuniti, dan memastikan hasil kerja berkualiti tinggi.<br><br>" .
           "📚 <strong>Subjek & Kemahiran Wajib Dikuasai:</strong><br>" .
           "Pengetahuan Akademik Sekolah Rendah (Bahasa Melayu, Bahasa Inggeris, Sains, Matematik), Kemahiran Fizikal/Teknikal, Disiplin, dan Minat yang mendalam.<br><br>" .
           "💡 <em>Nasihat AI Peti Cheritalah:</em> Semua pekerjaan di dunia—sama ada besar atau kecil—mempunyai nilai yang sangat mulia! Terus belajar dengan rajin untuk mencapai cita-cita anda!";
}
