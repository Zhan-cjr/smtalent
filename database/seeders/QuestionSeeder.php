<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $catNumerik = QuestionCategory::where('name', 'Numerik & Hitungan Cepat')->first();
        $catLogika = QuestionCategory::where('name', 'Logika & Penalaran')->first();
        $catVerbal = QuestionCategory::where('name', 'Verbal & Kosakata')->first();
        $catKetelitian = QuestionCategory::where('name', 'Ketelitian & Ketepatan')->first();
        $catPerilaku = QuestionCategory::where('name', 'Sikap & Perilaku Kerja (Skala 1-5)')->first();

        $catPramuniaga = QuestionCategory::where('name', 'Situasi Kerja Pramuniaga')->first();
        $catBarista = QuestionCategory::where('name', 'Situasi Kerja Barista')->first();
        $catKasir = QuestionCategory::where('name', 'Situasi Kerja Kasir')->first();
        $catAdmin = QuestionCategory::where('name', 'Situasi Kerja Admin')->first();
        $catSales = QuestionCategory::where('name', 'Situasi Kerja Sales')->first();

        $posPramuniaga = Position::where('code', 'PRM')->first();
        $posBarista = Position::where('code', 'BAR')->first();
        $posKasir = Position::where('code', 'KSR')->first();
        $posAdmin = Position::where('code', 'ADM')->first();
        $posSales = Position::where('code', 'SLS')->first();

        // ==========================================
        // 1. SOAL UMUM: NUMERIK & KETELITIAN (15 Soal)
        // ==========================================
        $generalNumericQuestions = [
            [
                'q' => 'Sebuah baju seharga Rp 200.000 mendapatkan diskon 20%. Jika pembeli membayar dengan uang Rp 200.000, berapa uang kembalian yang harus diberikan?',
                'cat' => $catNumerik->id,
                'pos' => null,
                'type' => 'multiple_choice',
                'opts' => [
                    ['key' => 'A', 'text' => 'Rp 20.000', 'correct' => false],
                    ['key' => 'B', 'text' => 'Rp 40.000', 'correct' => true],
                    ['key' => 'C', 'text' => 'Rp 50.000', 'correct' => false],
                    ['key' => 'D', 'text' => 'Rp 160.000', 'correct' => false],
                ],
            ],
            [
                'q' => 'Toko memiliki persediaan 120 kotak susu. Hari ini terjual 35% dari total stok. Berapa sisa kotak susu di toko sekarang?',
                'cat' => $catNumerik->id,
                'pos' => null,
                'type' => 'multiple_choice',
                'opts' => [
                    ['key' => 'A', 'text' => '78 kotak', 'correct' => true],
                    ['key' => 'B', 'text' => '42 kotak', 'correct' => false],
                    ['key' => 'C', 'text' => '82 kotak', 'correct' => false],
                    ['key' => 'D', 'text' => '75 kotak', 'correct' => false],
                ],
            ],
            [
                'q' => 'Lanjutkan deret angka berikut: 4, 8, 16, 32, ...',
                'cat' => $catNumerik->id,
                'pos' => null,
                'type' => 'multiple_choice',
                'opts' => [
                    ['key' => 'A', 'text' => '48', 'correct' => false],
                    ['key' => 'B', 'text' => '60', 'correct' => false],
                    ['key' => 'C', 'text' => '64', 'correct' => true],
                    ['key' => 'D', 'text' => '72', 'correct' => false],
                ],
            ],
            [
                'q' => 'Harga 1 lusin gelas adalah Rp 72.000. Berapakah harga 5 buah gelas?',
                'cat' => $catNumerik->id,
                'pos' => null,
                'type' => 'multiple_choice',
                'opts' => [
                    ['key' => 'A', 'text' => 'Rp 25.000', 'correct' => false],
                    ['key' => 'B', 'text' => 'Rp 30.000', 'correct' => true],
                    ['key' => 'C', 'text' => 'Rp 35.000', 'correct' => false],
                    ['key' => 'D', 'text' => 'Rp 28.000', 'correct' => false],
                ],
            ],
            [
                'q' => 'Lanjutkan deret angka berikut: 100, 95, 85, 70, 50, ...',
                'cat' => $catNumerik->id,
                'pos' => null,
                'type' => 'multiple_choice',
                'opts' => [
                    ['key' => 'A', 'text' => '35', 'correct' => false],
                    ['key' => 'B', 'text' => '30', 'correct' => false],
                    ['key' => 'C', 'text' => '25', 'correct' => true],
                    ['key' => 'D', 'text' => '20', 'correct' => false],
                ],
            ],
            [
                'q' => 'Manakah pasangan Kode SKU dan Nama Barang di bawah ini yang TIDAK IDENTIK / Berbeda satu karakter?',
                'cat' => $catKetelitian->id,
                'pos' => null,
                'type' => 'multiple_choice',
                'opts' => [
                    ['key' => 'A', 'text' => 'SKU-88219-A = SKU-88219-A', 'correct' => false],
                    ['key' => 'B', 'text' => 'SKU-99412-B = SKU-99412-B', 'correct' => false],
                    ['key' => 'C', 'text' => 'SKU-77315-X = SKU-7731S-X', 'correct' => true],
                    ['key' => 'D', 'text' => 'SKU-55102-K = SKU-55102-K', 'correct' => false],
                ],
            ],
            [
                'q' => 'Bandingkan kedua kelompok angka: [839201948] dan [839201948]. Apakah kedua kelompok angka tersebut sama persis?',
                'cat' => $catKetelitian->id,
                'pos' => null,
                'type' => 'multiple_choice',
                'opts' => [
                    ['key' => 'A', 'text' => 'Sama Persis', 'correct' => true],
                    ['key' => 'B', 'text' => 'Berbeda pada digit ke-4', 'correct' => false],
                    ['key' => 'C', 'text' => 'Berbeda pada digit terakhir', 'correct' => false],
                    ['key' => 'D', 'text' => 'Berbeda pada digit ke-7', 'correct' => false],
                ],
            ],
            [
                'q' => 'Cek barcode: 899277201923. Di antara pilihan berikut, mana yang memiliki kesalahan penulisan angka?',
                'cat' => $catKetelitian->id,
                'pos' => null,
                'type' => 'multiple_choice',
                'opts' => [
                    ['key' => 'A', 'text' => '899277201923', 'correct' => false],
                    ['key' => 'B', 'text' => '899277201923', 'correct' => false],
                    ['key' => 'C', 'text' => '899277201823', 'correct' => true],
                    ['key' => 'D', 'text' => '899277201923', 'correct' => false],
                ],
            ],
        ];

        // ==========================================
        // 2. SOAL UMUM: LOGIKA & VERBAL (12 Soal)
        // ==========================================
        $generalLogicVerbalQuestions = [
            [
                'q' => 'Semua karyawan toko wajib mengenakan seragam rapi saat jam operasional. Budi adalah karyawan toko yang sedang bertugas hari ini. Kesimpulan:',
                'cat' => $catLogika->id,
                'pos' => null,
                'type' => 'multiple_choice',
                'opts' => [
                    ['key' => 'A', 'text' => 'Budi boleh tidak berseragam jika cuaca panas.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Budi wajib mengenakan seragam rapi hari ini.', 'correct' => true],
                    ['key' => 'C', 'text' => 'Budi hanya perlu berseragam jika ada supervisor.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Budi bukan bagian dari karyawan operasional.', 'correct' => false],
                ],
            ],
            [
                'q' => 'PRAMUNIAGA : TOKO = APOTEKER : ...',
                'cat' => $catVerbal->id,
                'pos' => null,
                'type' => 'multiple_choice',
                'opts' => [
                    ['key' => 'A', 'text' => 'Obat', 'correct' => false],
                    ['key' => 'B', 'text' => 'Apotek', 'correct' => true],
                    ['key' => 'C', 'text' => 'Dokter', 'correct' => false],
                    ['key' => 'D', 'text' => 'Pasien', 'correct' => false],
                ],
            ],
            [
                'q' => 'Sinonim kata "INTEGRITAS" yang paling sesuai dalam lingkungan kerja adalah:',
                'cat' => $catVerbal->id,
                'pos' => null,
                'type' => 'multiple_choice',
                'opts' => [
                    ['key' => 'A', 'text' => 'Kejujuran dan ketulusan', 'correct' => true],
                    ['key' => 'B', 'text' => 'Kecepatan kerja', 'correct' => false],
                    ['key' => 'C', 'text' => 'Kepandaian berhitung', 'correct' => false],
                    ['key' => 'D', 'text' => 'Kemampuan berbicara', 'correct' => false],
                ],
            ],
            [
                'q' => 'Antonim / lawan kata dari kata "EFISIEN" adalah:',
                'cat' => $catVerbal->id,
                'pos' => null,
                'type' => 'multiple_choice',
                'opts' => [
                    ['key' => 'A', 'text' => 'Tepat guna', 'correct' => false],
                    ['key' => 'B', 'text' => 'Boros / Tidak efektif', 'correct' => true],
                    ['key' => 'C', 'text' => 'Praktis', 'correct' => false],
                    ['key' => 'D', 'text' => 'Cepat', 'correct' => false],
                ],
            ],
            [
                'q' => 'Jika hari ini hujan, maka jalanan licin. Hari ini jalanan tidak licin. Kesimpulannya:',
                'cat' => $catLogika->id,
                'pos' => null,
                'type' => 'multiple_choice',
                'opts' => [
                    ['key' => 'A', 'text' => 'Hari ini mendung', 'correct' => false],
                    ['key' => 'B', 'text' => 'Hari ini tidak hujan', 'correct' => true],
                    ['key' => 'C', 'text' => 'Jalanan baru diperbaiki', 'correct' => false],
                    ['key' => 'D', 'text' => 'Hujan terjadi kemarin', 'correct' => false],
                ],
            ],
        ];

        // ==========================================
        // 3. SOAL SIKAP & PERILAKU KERJA (Skala Likert 1-5, 12 Soal)
        // ==========================================
        $behaviorQuestions = [
            ['q' => 'Saya selalu datang ke tempat kerja minimal 15 menit sebelum shift dimulai.', 'aspect' => 'Disiplin'],
            ['q' => 'Saya tetap menyapa dan tersenyum ramah kepada pelanggan meskipun kondisi sedang lelah atau toko sedang sangat padat.', 'aspect' => 'Customer Service'],
            ['q' => 'Saya selalu memeriksa kembali detail pekerjaan saya (seperti harga barang, takaran, atau uang kembalian) untuk memastikan tidak ada kekeliruan.', 'aspect' => 'Ketelitian'],
            ['q' => 'Saya bersedia membantu rekan kerja yang sedang kewalahan menyelesaikan tugasnya tanpa menunggu diminta oleh atasan.', 'aspect' => 'Kerja Sama'],
            ['q' => 'Jika saya melakukan kesalahan saat bertugas, saya berani mengakuinya dan segera mencari solusi perbaikan.', 'aspect' => 'Integritas'],
            ['q' => 'Saya dapat mengendalikan emosi dengan tenang saat menghadapi pelanggan yang marah atau mengajukan komplain dengan nada tinggi.', 'aspect' => 'Customer Service'],
            ['q' => 'Saya selalu menaati Standard Operating Procedure (SOP) kerja meskipun sedang tidak diawasi oleh pimpinan.', 'aspect' => 'Disiplin'],
            ['q' => 'Saya bersemangat untuk mempelajari hal-hal baru dan menerima masukan membangun demi peningkatan kualitas kerja saya.', 'aspect' => 'Sikap Kerja'],
            ['q' => 'Saya mampu menyusun prioritas pekerjaan dengan baik saat ada beberapa tugas yang harus diselesaikan bersamaan.', 'aspect' => 'Problem Solving'],
            ['q' => 'Saya merawat dan menjaga kebersihan seluruh peralatan serta area kerja sebagai tanggung jawab utama.', 'aspect' => 'Tanggung Jawab'],
        ];

        // ==========================================
        // 4. SOAL KHUSUS PRAMUNIAGA (20 Soal Pilihan Ganda & Situasi Kerja)
        // ==========================================
        $pramuniagaQuestions = [
            [
                'q' => 'Saat Anda sedang merapikan rak display, seorang pelanggan menghampiri Anda dan menanyakan lokasi produk minyak goreng. Tindakan terbaik Anda adalah:',
                'cat' => $catPramuniaga->id,
                'pos' => $posPramuniaga->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Menunjuk arah lorong dengan tangan sambil terus merapikan rak.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Menghentikan sejenak pekerjaan, menyapa ramah, dan mengantarkan pelanggan langsung ke rak minyak goreng.', 'correct' => true],
                    ['key' => 'C', 'text' => 'Menyuruh pelanggan mencari sendiri di bagian belakang toko.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Menjawab singkat tanpa melihat ke arah pelanggan.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Prinsip FIFO (First In First Out) dalam penataan barang di toko berarti:',
                'cat' => $catPramuniaga->id,
                'pos' => $posPramuniaga->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Barang yang pertama kali masuk gudang/paling dekat kadaluarsa harus dipajang paling depan untuk dijual lebih dulu.', 'correct' => true],
                    ['key' => 'B', 'text' => 'Barang yang paling mahal diletakkan paling depan.', 'correct' => false],
                    ['key' => 'C', 'text' => 'Barang baru diletakkan menumpuk barang lama.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Barang yang ukurannya paling besar diletakkan di bagian depan.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Pelanggan menemukan produk biskuit kaleng yang kemasannya sedikit penyok dan meminta potongan harga khusus kepada Anda. Respon yang tepat sesuai SOP adalah:',
                'cat' => $catPramuniaga->id,
                'pos' => $posPramuniaga->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Langsung memberikan diskon 50% tanpa izin supervisor.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Meminta maaf, mengambilkan produk yang kondisinya sempurna dari stok, dan menginfokan aturan toko dengan sopan.', 'correct' => true],
                    ['key' => 'C', 'text' => 'Memarahi pelanggan karena memilih kaleng yang rusak.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Mengabaikan permintaan pelanggan.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Terdapat selisih harga antara label di rak (Rp 25.000) dengan harga yang terbaca di sistem kasir (Rp 30.000). Tindakan segera yang harus dilakukan pramuniaga adalah:',
                'cat' => $catPramuniaga->id,
                'pos' => $posPramuniaga->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Menyalahkan bagian kasir atas kesalahan input.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Segera mengecek dan memperbarui price tag di rak serta berkoordinasi dengan kasir/supervisor.', 'correct' => true],
                    ['key' => 'C', 'text' => 'Mencopot semua label harga di toko agar tidak diprotes pembeli.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Membiarkan perbedaan tersebut sampai ada pembeli lain yang komplain.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Saat toko sedang sangat ramai pengunjung dan lorong sempit terhalang kardus unboxing stok, apa yang sebaiknya Anda lakukan?',
                'cat' => $catPramuniaga->id,
                'pos' => $posPramuniaga->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Membiarkan kardus di lorong hingga jam operasional toko selesai.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Segera memindahkan kardus ke area gudang/belakang agar tidak mengganggu kenyamanan dan keselamatan pembeli.', 'correct' => true],
                    ['key' => 'C', 'text' => 'Menyuruh pembeli berputar mencari jalan lain.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Duduk di atas kardus sambil beristirahat.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Seorang pembeli ragu memilih antara dua merk deterjen. Sebagai pramuniaga yang profesional, Anda sebaiknya:',
                'cat' => $catPramuniaga->id,
                'pos' => $posPramuniaga->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Menjelaskan keunggulan dan promo masing-masing merk dengan ramah untuk membantu pelanggan menentukan pilihan.', 'correct' => true],
                    ['key' => 'B', 'text' => 'Menyuruh pembeli memilih yang paling mahal saja.', 'correct' => false],
                    ['key' => 'C', 'text' => 'Meninggalkan pembeli karena membuang-buang waktu.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Mengatakan bahwa kedua deterjen tersebut kualitasnya buruk.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Di area display buah segar, Anda melihat ada beberapa buah yang mulai layu dan membusuk. Tindakan yang benar adalah:',
                'cat' => $catPramuniaga->id,
                'pos' => $posPramuniaga->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Membiarkannya agar rak tetap terlihat penuh.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Melakukan sortasi (pemisahan) buah rusak sesuai SOP retur/afkir dan membersihkan display rak.', 'correct' => true],
                    ['key' => 'C', 'text' => 'Menutupi buah busuk dengan buah segar di atasnya.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Menjualnya secara diam-diam ke pembeli pertama.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Ketika melihat seorang anak kecil menangis terpisah dari orang tuanya di dalam area toko, langkah awal Anda adalah:',
                'cat' => $catPramuniaga->id,
                'pos' => $posPramuniaga->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Mendekati dan menenangkan anak, lalu segera berkoordinasi dengan bagian informasi/security untuk pengumuman.', 'correct' => true],
                    ['key' => 'B', 'text' => 'Menyuruh anak keluar dari toko.', 'correct' => false],
                    ['key' => 'C', 'text' => 'Mengabaikannya karena bukan tugas bagian display barang.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Memfoto anak dan mengunggahnya ke media sosial pribadi.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Pramuniaga melihat indikasi seseorang memasukkan barang kosmetik ke dalam saku jaket tanpa membawanya ke keranjang belanja. Sikap tepat Anda:',
                'cat' => $catPramuniaga->id,
                'pos' => $posPramuniaga->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Langsung meneriaki pencuri di depan umum.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Mendekati dengan ramah menawarkan keranjang belanja ("Mari kak saya bantu bawakan keranjangnya") dan mengabari security secara rahasia.', 'correct' => true],
                    ['key' => 'C', 'text' => 'Memukul orang tersebut.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Pura-pura tidak melihat agar tidak repot.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Pada akhir jam operasional (closing), tugas utama pramuniaga sebelum pulang meliputi:',
                'cat' => $catPramuniaga->id,
                'pos' => $posPramuniaga->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Langsung pulang tanpa memeriksa area.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Facing out (merapikan display), menyapu area toko, mematikan peralatan listrik non-esensial, dan memastikan toko steril.', 'correct' => true],
                    ['key' => 'C', 'text' => 'Menghitung uang kasir milik kasir.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Membuka kardus stok baru di tengah lorong.', 'correct' => false],
                ],
            ],
        ];

        // ==========================================
        // 5. SOAL KHUSUS BARISTA (20 Soal Pilihan Ganda & Situasi Kerja)
        // ==========================================
        $baristaQuestions = [
            [
                'q' => 'Resep standar Iced Americano di kafe membutuhkan 1 shot espresso (30ml) + 150ml air mineral + es batu. Jika ada pesanan 3 Iced Americano, berapa total volume espresso yang harus diekstrak?',
                'cat' => $catBarista->id,
                'pos' => $posBarista->id,
                'opts' => [
                    ['key' => 'A', 'text' => '60 ml', 'correct' => false],
                    ['key' => 'B', 'text' => '90 ml', 'correct' => true],
                    ['key' => 'C', 'text' => '120 ml', 'correct' => false],
                    ['key' => 'D', 'text' => '150 ml', 'correct' => false],
                ],
            ],
            [
                'q' => 'Setelah melakukan proses frothing / steaming susu untuk caffe latte, apa yang WAJIB segera dilakukan oleh seorang barista pada steam wand?',
                'cat' => $catBarista->id,
                'pos' => $posBarista->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Membiarkan sisa susu mengering di pipa steam wand.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Mengelap pipa steam wand dengan kain microfiber khusus dan melakukan purging (menyemburkan uap sesaat) untuk membersihkan sisa susu di dalam pipa.', 'correct' => true],
                    ['key' => 'C', 'text' => 'Mematikan mesin espresso secara total.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Merendam steam wand ke dalam air kopi.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Pelanggan mengeluhkan minuman latte buatannya terasa asam dan dingin karena pesanannya terlambat diantar. Sikap profesional Anda adalah:',
                'cat' => $catBarista->id,
                'pos' => $posBarista->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Membantah pelanggan dan mengatakan bahwa rasa asam memang karakteristik biji kopinya.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Meminta maaf dengan tulus, segera membuatkan minuman baru yang segar dan panas sesuai preferensi pelanggan, serta memberikan penjelasan sopan.', 'correct' => true],
                    ['key' => 'C', 'text' => 'Menyalahkan waiter yang terlambat mengantar minuman.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Menyuruh pelanggan memanaskan sendiri minumannya.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Hasil ekstraksi espresso keluar terlalu cepat (kurang dari 15 detik untuk 30ml) dan rasanya encer (under-extracted). Langkah kalibrasi grinder kopi yang tepat adalah:',
                'cat' => $catBarista->id,
                'pos' => $posBarista->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Mengubah setelan gilingan grinder menjadi lebih halus (finer).', 'correct' => true],
                    ['key' => 'B', 'text' => 'Mengubah setelan gilingan grinder menjadi lebih kasar (coarser).', 'correct' => false],
                    ['key' => 'C', 'text' => 'Mengurangi tekanan tamping menjadi sangat ringan.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Menambahkan es batu ke dalam portafilter.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Saat jam sibuk (peak hour) terjadi antrean 15 pesanan bersamaan. Strategi kerja barista yang paling efisien adalah:',
                'cat' => $catBarista->id,
                'pos' => $posBarista->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Bekerja secara panik dan melompati pesanan sesuka hati.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Menerapkan sistem batching (misal: menyiapkan cup, es, dan sirup sekaligus untuk pesanan sejenis) sambil tetap menjaga urutan nomor antrean struk.', 'correct' => true],
                    ['key' => 'C', 'text' => 'Menutup kafe sementara sampai antrean habis.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Mengurangi takaran bahan agar proses pembuatan lebih cepat selesai.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Pelanggan memesan Iced Matcha Latte dengan catatan "Less Sugar (50% gula) & Oatmilk (ganti susu oat)". Yang harus dipastikan barista adalah:',
                'cat' => $catBarista->id,
                'pos' => $posBarista->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Menggunakan takaran sirup gula 50% dari standar, memakai susu oatmilk, dan memeriksa ulang label cup sebelum diserahkan.', 'correct' => true],
                    ['key' => 'B', 'text' => 'Membuat seperti biasa dengan susu sapi karena oatmilk lebih mahal.', 'correct' => false],
                    ['key' => 'C', 'text' => 'Tidak memberikan gula sama sekali (0%).', 'correct' => false],
                    ['key' => 'D', 'text' => 'Mengabaikan catatan khusus pembeli.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Berapa suhu ideal pemanasan susu (steaming milk) untuk minuman latte agar manis alami susu keluar sempurna tanpa rasa gosong/rusak?',
                'cat' => $catBarista->id,
                'pos' => $posBarista->id,
                'opts' => [
                    ['key' => 'A', 'text' => '30°C - 40°C', 'correct' => false],
                    ['key' => 'B', 'text' => '60°C - 65°C', 'correct' => true],
                    ['key' => 'C', 'text' => '90°C - 100°C', 'correct' => false],
                    ['key' => 'D', 'text' => '120°C - 130°C', 'correct' => false],
                ],
            ],
            [
                'q' => 'Ketika melakukan opening bar kafe di pagi hari, urutan persiapan barista yang paling tepat adalah:',
                'cat' => $catBarista->id,
                'pos' => $posBarista->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Langsung duduk menunggu pelanggan pertama datang.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Menyalakan mesin espresso & grinder, cek stok bahan (biji kopi, susu, sirup), sanitasi area bar, dan melakukan kalibrasi rasa espresso (dial-in kopi).', 'correct' => true],
                    ['key' => 'C', 'text' => 'Memasak makanan berat di dapur.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Menghitung uang kasir tanpa membersihkan bar.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Tumpahan sirup karamel dan susu mengenai meja bar saat pembuatan minuman. Kapan waktu yang tepat untuk membersihkannya?',
                'cat' => $catBarista->id,
                'pos' => $posBarista->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Nanti malam saat kafe tutup.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Segera dilap dengan lap sanitasi khusus meja agar tidak lengket, higienis, dan tidak mengotori cup minuman berikutnya.', 'correct' => true],
                    ['key' => 'C', 'text' => 'Besok pagi sebelum buka.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Dibiarkan mengering sendiri.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Metode seduh manual (manual brew) V60 mengutamakan variabel berikut, KECUALI:',
                'cat' => $catBarista->id,
                'pos' => $posBarista->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Ukuran gilingan biji kopi (grind size)', 'correct' => false],
                    ['key' => 'B', 'text' => 'Suhu air panas yang konsisten', 'correct' => false],
                    ['key' => 'C', 'text' => 'Rasio perbandingan berat kopi dan air (brew ratio)', 'correct' => false],
                    ['key' => 'D', 'text' => 'Mencampurkan bubuk kopi instan sachet', 'correct' => true],
                ],
            ],
        ];

        // ==========================================
        // 6. SOAL KHUSUS KASIR, ADMIN, & SALES (10 Soal per Kategori)
        // ==========================================
        $kasirQuestions = [
            [
                'q' => 'Total belanjaan pelanggan Rp 137.500. Pelanggan membayar dengan uang pecahan Rp 150.000 (3 lembar Rp 50.000). Berapakah uang kembalian yang tepat?',
                'cat' => $catKasir->id,
                'pos' => $posKasir->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Rp 12.500', 'correct' => true],
                    ['key' => 'B', 'text' => 'Rp 13.500', 'correct' => false],
                    ['key' => 'C', 'text' => 'Rp 15.000', 'correct' => false],
                    ['key' => 'D', 'text' => 'Rp 22.500', 'correct' => false],
                ],
            ],
            [
                'q' => 'Jika terjadi selisih kas fisik lebih sedikit (short) sebesar Rp 10.000 saat proses tutup kasir (closing balance), langkah pertama yang harus dilakukan kasir adalah:',
                'cat' => $catKasir->id,
                'pos' => $posKasir->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Menyembunyikan selisih dan memalsukan laporan kasir.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Menghitung ulang uang fisik secara teliti, mencocokkan kembali struk transaksi sistem, dan melaporkan secara jujur ke atasan/supervisor.', 'correct' => true],
                    ['key' => 'C', 'text' => 'Menuduh kasir shift sebelumnya.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Mematikan mesin kasir dan langsung pulang.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Pelanggan melakukan pembayaran menggunakan QRIS, namun di ponsel pelanggan statusnya "Berhasil" sementara di mesin EDC/POS statusnya masih "Pending/Belum Masuk". Respon kasir sesuai SOP:',
                'cat' => $catKasir->id,
                'pos' => $posKasir->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Meminta maaf dengan ramah, mengecek mutasi riwayat merchant settlement, dan jika belum masuk meminta pelanggan menunggu sejenak untuk konfirmasi bank.', 'correct' => true],
                    ['key' => 'B', 'text' => 'Menyuruh pelanggan membayar tunai tanpa mengecek sistem.', 'correct' => false],
                    ['key' => 'C', 'text' => 'Membiarkan pelanggan membawa barang tanpa verifikasi struk resmi.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Memarahi pelanggan.', 'correct' => false],
                ],
            ],
        ];

        $adminQuestions = [
            [
                'q' => 'Di aplikasi Microsoft Excel atau Google Sheets, rumus (formula) yang digunakan untuk menjumlahkan nilai dalam rentang sel A1 sampai A10 adalah:',
                'cat' => $catAdmin->id,
                'pos' => $posAdmin->id,
                'opts' => [
                    ['key' => 'A', 'text' => '=COUNT(A1:A10)', 'correct' => false],
                    ['key' => 'B', 'text' => '=SUM(A1:A10)', 'correct' => true],
                    ['key' => 'C', 'text' => '=AVERAGE(A1:A10)', 'correct' => false],
                    ['key' => 'D', 'text' => '=TOTAL(A1:A10)', 'correct' => false],
                ],
            ],
            [
                'q' => 'Tugas administrasi yang memiliki tingkat urgensi TINGGI dan dampak PENTING (High Urgency, High Importance) sebaiknya diselesaikan dengan cara:',
                'cat' => $catAdmin->id,
                'pos' => $posAdmin->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Dikerjakan pertama kali sesegera mungkin dengan fokus penuh.', 'correct' => true],
                    ['key' => 'B', 'text' => 'Ditunda sampai akhir bulan.', 'correct' => false],
                    ['key' => 'C', 'text' => 'Diberikan ke orang lain tanpa instruksi.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Dihapus dari daftar tugas.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Dalam pengarsipan dokumen fisik maupun digital, tujuan utama dari penamaan file yang rapi dan terstruktur (misal: INVOICE_202610_PT_ABC.pdf) adalah:',
                'cat' => $catAdmin->id,
                'pos' => $posAdmin->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Mempermudah pencarian kembali dokumen saat dibutuhkan secara cepat dan akurat.', 'correct' => true],
                    ['key' => 'B', 'text' => 'Membuat ukuran file menjadi lebih besar.', 'correct' => false],
                    ['key' => 'C', 'text' => 'Agar komputer terlihat sibuk.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Hanya sekedar formalitas tanpa fungsi.', 'correct' => false],
                ],
            ],
        ];

        $salesQuestions = [
            [
                'q' => 'Calon pelanggan berkata: "Produk Anda bagus, tetapi harganya terlalu mahal dibanding kompetitor." Respon penanganan keberatan (objection handling) yang paling efektif adalah:',
                'cat' => $catSales->id,
                'pos' => $posSales->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Menjelek-jelekkan merk kompetitor.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Menghargai pendapat pelanggan, lalu menjelaskan nilai tambah (value), kualitas bahan, layanan purna jual, dan garansi resmi yang sebanding dengan investasinya.', 'correct' => true],
                    ['key' => 'C', 'text' => 'Langsung membatalkan penawaran dan pergi.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Mengatakan kepada pelanggan bahwa mereka tidak mampu membeli.', 'correct' => false],
                ],
            ],
            [
                'q' => 'Langkah pertama dalam siklus penjualan (Sales Cycle) sebelum melakukan presentasi produk adalah:',
                'cat' => $catSales->id,
                'pos' => $posSales->id,
                'opts' => [
                    ['key' => 'A', 'text' => 'Langsung meminta uang pembayaran.', 'correct' => false],
                    ['key' => 'B', 'text' => 'Melakukan identifikasi kebutuhan dan kualifikasi calon prospek pembeli (Needs Analysis).', 'correct' => true],
                    ['key' => 'C', 'text' => 'Mengirim tagihan faktur.', 'correct' => false],
                    ['key' => 'D', 'text' => 'Memaksa pembeli menandatangani kontrak.', 'correct' => false],
                ],
            ],
        ];

        // Insert All Multiple Choice Questions
        $allMCQs = array_merge(
            $generalNumericQuestions,
            $generalLogicVerbalQuestions,
            $pramuniagaQuestions,
            $baristaQuestions,
            $kasirQuestions,
            $adminQuestions,
            $salesQuestions
        );

        foreach ($allMCQs as $item) {
            $question = Question::create([
                'category_id' => $item['cat'],
                'position_id' => $item['pos'] ?? null,
                'type' => 'multiple_choice',
                'question_text' => $item['q'],
                'weight' => 1.00,
                'is_active' => true,
            ]);

            foreach ($item['opts'] as $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_key' => $opt['key'],
                    'option_text' => $opt['text'],
                    'is_correct' => $opt['correct'],
                ]);
            }
        }

        // Insert Likert Scale Questions
        foreach ($behaviorQuestions as $item) {
            Question::create([
                'category_id' => $catPerilaku->id,
                'position_id' => null, // Berlaku untuk semua posisi
                'type' => 'likert_scale',
                'question_text' => $item['q'],
                'aspect' => $item['aspect'],
                'weight' => 1.00,
                'is_active' => true,
            ]);
        }
    }
}
