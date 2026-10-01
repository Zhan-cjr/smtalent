<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            [
                'code' => 'PRM',
                'name' => 'Pramuniaga',
                'description' => 'Bertanggung jawab melayani pelanggan di toko, penataan display barang (visual merchandising), kebersihan area toko, dan pengecekan stok/label harga.',
                'is_active' => true,
            ],
            [
                'code' => 'BAR',
                'name' => 'Barista',
                'description' => 'Bertanggung jawab meracik minuman kopi dan non-kopi sesuai SOP, menjaga kebersihan mesin espresso & bar, serta memberikan layanan ramah kepada pelanggan kafe.',
                'is_active' => true,
            ],
            [
                'code' => 'KSR',
                'name' => 'Kasir',
                'description' => 'Bertanggung jawab atas transaksi pembayaran tunai/non-tunai, ketepatan perhitungan uang kasir, pencetakan struk, dan laporan tutup kasir harian.',
                'is_active' => true,
            ],
            [
                'code' => 'ADM',
                'name' => 'Admin',
                'description' => 'Bertanggung jawab atas pencatatan administrasi operasional, arsip dokumen, pengolahan spreadsheet data, dan korespondensi kantor.',
                'is_active' => true,
            ],
            [
                'code' => 'SLS',
                'name' => 'Sales',
                'description' => 'Bertanggung jawab mencapai target penjualan, prospek pelanggan baru, presentasi produk, dan negosiasi penawaran bisnis.',
                'is_active' => true,
            ],
        ];

        foreach ($positions as $pos) {
            Position::updateOrCreate(['code' => $pos['code']], $pos);
        }
    }
}
