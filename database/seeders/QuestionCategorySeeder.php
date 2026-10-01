<?php

namespace Database\Seeders;

use App\Models\QuestionCategory;
use Illuminate\Database\Seeder;

class QuestionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Numerik & Hitungan Cepat', 'type' => 'general', 'description' => 'Kemampuan berhitung cepat, persentase, harga, diskon, dan kembalian.'],
            ['name' => 'Logika & Penalaran', 'type' => 'general', 'description' => 'Kemampuan silogisme, pola urutan, dan analogi hubungan konsep.'],
            ['name' => 'Verbal & Kosakata', 'type' => 'general', 'description' => 'Kemampuan sinonim, antonim, dan pemahaman instruksi teks.'],
            ['name' => 'Ketelitian & Ketepatan', 'type' => 'general', 'description' => 'Pengecekan kode SKU, kesamaan data angka/karakter, dan deteksi kesalahan.'],
            ['name' => 'Sikap & Perilaku Kerja (Skala 1-5)', 'type' => 'work_behavior', 'description' => 'Integritas, disiplin, kerja sama, keramahan, dan tanggung jawab kerja.'],
            ['name' => 'Situasi Kerja Pramuniaga', 'type' => 'position_specific', 'description' => 'Studi kasus operasional toko, display barang, dan penanganan keluhan pembeli.'],
            ['name' => 'Situasi Kerja Barista', 'type' => 'position_specific', 'description' => 'Studi kasus operasional kafe, antrean minuman, SOP alat kopi, dan higienitas.'],
            ['name' => 'Situasi Kerja Kasir', 'type' => 'position_specific', 'description' => 'Ketelitian transaksi POS, selisih kasir, dan penanganan pembayaran.'],
            ['name' => 'Situasi Kerja Admin', 'type' => 'position_specific', 'description' => 'Pengolahan arsip data, verifikasi dokumen, dan prioritas tugas kantor.'],
            ['name' => 'Situasi Kerja Sales', 'type' => 'position_specific', 'description' => 'Strategi penawaran produk, negosiasi, dan handling keberatan calon pembeli.'],
        ];

        foreach ($categories as $cat) {
            QuestionCategory::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
