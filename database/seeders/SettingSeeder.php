<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'psychotest_weight', 'value' => '60', 'description' => 'Bobot persentase penilaian psikotes dalam seleksi akhir (%)'],
            ['key' => 'interview_weight', 'value' => '40', 'description' => 'Bobot persentase penilaian interview dalam seleksi akhir (%)'],
            ['key' => 'default_passing_grade', 'value' => '70', 'description' => 'Standar nilai kelulusan psikotes default'],
            ['key' => 'default_duration_minutes', 'value' => '50', 'description' => 'Durasi waktu default pengerjaan psikotes (menit)'],
            ['key' => 'company_name', 'value' => 'PT Rekrutmen Cipta Karir Indonesia', 'description' => 'Nama perusahaan penyelenggara rekrutmen'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
