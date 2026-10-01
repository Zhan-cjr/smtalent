<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Question;
use App\Models\TestPackage;
use Illuminate\Database\Seeder;

class TestPackageSeeder extends Seeder
{
    public function run(): void
    {
        $positions = Position::all();

        foreach ($positions as $position) {
            $package = TestPackage::updateOrCreate(
                ['position_id' => $position->id],
                [
                    'name' => 'Paket Psikotes & Seleksi '.$position->name,
                    'description' => 'Paket evaluasi komprehensif terdiri dari 70% soal umum (numerik, logika, ketelitian, perilaku kerja) dan 30% simulasi kerja '.$position->name,
                    'total_questions' => 40,
                    'duration_minutes' => 50,
                    'passing_grade' => 70.00,
                    'is_randomized' => true,
                    'is_options_randomized' => true,
                    'show_result_to_candidate' => false,
                    'is_active' => true,
                ]
            );

            // Assign questions to package
            $generalQuestions = Question::whereNull('position_id')->get();
            $positionQuestions = Question::where('position_id', $position->id)->get();
            $allAssigned = $generalQuestions->merge($positionQuestions);

            $package->questions()->sync($allAssigned->pluck('id'));
        }
    }
}
