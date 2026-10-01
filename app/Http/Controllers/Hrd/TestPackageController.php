<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Question;
use App\Models\TestPackage;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestPackageController extends Controller
{
    public function index(): View
    {
        $packages = TestPackage::with('position')->withCount('questions', 'attempts')->get();

        return view('hrd.test-packages.index', compact('packages'));
    }

    public function create(): View
    {
        $positions = Position::where('is_active', true)->get();
        $questions = Question::with('category')->where('is_active', true)->get();

        return view('hrd.test-packages.create', compact('positions', 'questions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'position_id' => ['nullable', 'exists:positions,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'total_questions' => ['required', 'integer', 'min:1'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'passing_grade' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_randomized' => ['nullable', 'boolean'],
            'is_options_randomized' => ['nullable', 'boolean'],
            'show_result_to_candidate' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'question_ids' => ['nullable', 'array'],
        ]);

        $validated['is_randomized'] = $request->boolean('is_randomized', true);
        $validated['is_options_randomized'] = $request->boolean('is_options_randomized', true);
        $validated['show_result_to_candidate'] = $request->boolean('show_result_to_candidate', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        $package = TestPackage::create($validated);

        if ($request->has('question_ids')) {
            $package->questions()->sync($request->question_ids);
        } else {
            // Default auto sync pertanyaan sesuai posisi & pertanyaan umum
            $generalQuestions = Question::whereNull('position_id')->where('is_active', true)->get();
            $positionQuestions = $package->position_id
                ? Question::where('position_id', $package->position_id)->where('is_active', true)->get()
                : collect();
            $package->questions()->sync($generalQuestions->merge($positionQuestions)->pluck('id'));
        }

        AuditLogService::log('CREATE_PACKAGE', "Membuat Paket Psikotes: {$package->name}");

        return redirect()->route('hrd.test-packages.index')->with('success', 'Paket psikotes berhasil dibuat.');
    }

    public function edit(TestPackage $testPackage): View
    {
        $testPackage->load('questions');
        $positions = Position::where('is_active', true)->get();
        $questions = Question::with('category')->where('is_active', true)->get();
        $assignedIds = $testPackage->questions->pluck('id')->toArray();

        return view('hrd.test-packages.edit', compact('testPackage', 'positions', 'questions', 'assignedIds'));
    }

    public function update(Request $request, TestPackage $testPackage): RedirectResponse
    {
        $validated = $request->validate([
            'position_id' => ['nullable', 'exists:positions,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'total_questions' => ['required', 'integer', 'min:1'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'passing_grade' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_randomized' => ['nullable', 'boolean'],
            'is_options_randomized' => ['nullable', 'boolean'],
            'show_result_to_candidate' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'question_ids' => ['nullable', 'array'],
        ]);

        $validated['is_randomized'] = $request->boolean('is_randomized', true);
        $validated['is_options_randomized'] = $request->boolean('is_options_randomized', true);
        $validated['show_result_to_candidate'] = $request->boolean('show_result_to_candidate', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        $testPackage->update($validated);

        if ($request->has('question_ids')) {
            $testPackage->questions()->sync($request->question_ids);
        }

        AuditLogService::log('UPDATE_PACKAGE', "Mengubah Paket Psikotes: {$testPackage->name}");

        return redirect()->route('hrd.test-packages.index')->with('success', 'Paket psikotes berhasil diperbarui.');
    }

    public function destroy(TestPackage $testPackage): RedirectResponse
    {
        $name = $testPackage->name;
        $testPackage->delete();
        AuditLogService::log('DELETE_PACKAGE', "Menghapus Paket Psikotes: {$name}");

        return redirect()->route('hrd.test-packages.index')->with('success', 'Paket psikotes berhasil dihapus.');
    }
}
