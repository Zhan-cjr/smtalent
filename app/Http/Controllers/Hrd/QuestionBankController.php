<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\QuestionOption;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestionBankController extends Controller
{
    public function index(Request $request): View
    {
        $categoryId = $request->query('category_id');
        $positionId = $request->query('position_id');
        $type = $request->query('type');
        $search = $request->query('search');

        $query = Question::with(['category', 'position', 'options']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($positionId !== null && $positionId !== '') {
            if ($positionId === 'general') {
                $query->whereNull('position_id');
            } else {
                $query->where('position_id', $positionId);
            }
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($search) {
            $query->where('question_text', 'like', '%'.$search.'%');
        }

        $questions = $query->latest()->paginate(15)->withQueryString();
        $categories = QuestionCategory::all();
        $positions = Position::where('is_active', true)->get();

        return view('hrd.questions.index', compact('questions', 'categories', 'positions', 'categoryId', 'positionId', 'type', 'search'));
    }

    public function create(): View
    {
        $categories = QuestionCategory::all();
        $positions = Position::where('is_active', true)->get();

        return view('hrd.questions.create', compact('categories', 'positions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'category_id' => ['required', 'exists:question_categories,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'type' => ['required', 'in:multiple_choice,likert_scale'],
            'question_text' => ['required', 'string'],
            'aspect' => ['nullable', 'string', 'max:100'],
            'weight' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'options' => ['nullable', 'array'],
            'correct_option' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request) {
            $question = Question::create([
                'category_id' => $request->category_id,
                'position_id' => $request->position_id ?: null,
                'type' => $request->type,
                'question_text' => $request->question_text,
                'aspect' => $request->aspect,
                'weight' => $request->weight,
                'is_active' => $request->boolean('is_active', true),
            ]);

            if ($request->type === 'multiple_choice' && $request->has('options')) {
                foreach ($request->options as $key => $text) {
                    if (! empty(trim($text))) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_key' => $key,
                            'option_text' => $text,
                            'is_correct' => ($request->correct_option === $key),
                        ]);
                    }
                }
            }

            AuditLogService::log('CREATE_QUESTION', "Menambah soal baru ID: {$question->id}");
        });

        return redirect()->route('hrd.questions.index')->with('success', 'Soal berhasil ditambahkan ke Bank Soal.');
    }

    public function edit(Question $question): View
    {
        $question->load('options');
        $categories = QuestionCategory::all();
        $positions = Position::where('is_active', true)->get();

        return view('hrd.questions.edit', compact('question', 'categories', 'positions'));
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        $request->validate([
            'category_id' => ['required', 'exists:question_categories,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'type' => ['required', 'in:multiple_choice,likert_scale'],
            'question_text' => ['required', 'string'],
            'aspect' => ['nullable', 'string', 'max:100'],
            'weight' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'options' => ['nullable', 'array'],
            'correct_option' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request, $question) {
            $question->update([
                'category_id' => $request->category_id,
                'position_id' => $request->position_id ?: null,
                'type' => $request->type,
                'question_text' => $request->question_text,
                'aspect' => $request->aspect,
                'weight' => $request->weight,
                'is_active' => $request->boolean('is_active', true),
            ]);

            if ($request->type === 'multiple_choice' && $request->has('options')) {
                $question->options()->delete();
                foreach ($request->options as $key => $text) {
                    if (! empty(trim($text))) {
                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_key' => $key,
                            'option_text' => $text,
                            'is_correct' => ($request->correct_option === $key),
                        ]);
                    }
                }
            }

            AuditLogService::log('UPDATE_QUESTION', "Mengubah soal ID: {$question->id}");
        });

        return redirect()->route('hrd.questions.index')->with('success', 'Soal berhasil diperbarui.');
    }

    public function duplicate(Question $question): RedirectResponse
    {
        DB::transaction(function () use ($question) {
            $newQ = $question->replicate();
            $newQ->question_text = '[Salinan] '.$newQ->question_text;
            $newQ->save();

            foreach ($question->options as $opt) {
                $newOpt = $opt->replicate();
                $newOpt->question_id = $newQ->id;
                $newOpt->save();
            }

            AuditLogService::log('DUPLICATE_QUESTION', "Menduplikasi soal ID {$question->id} ke ID {$newQ->id}");
        });

        return redirect()->route('hrd.questions.index')->with('success', 'Soal berhasil diduplikasi.');
    }

    public function destroy(Question $question): RedirectResponse
    {
        $id = $question->id;
        $question->delete();
        AuditLogService::log('DELETE_QUESTION', "Menghapus soal ID: {$id}");

        return redirect()->route('hrd.questions.index')->with('success', 'Soal berhasil dihapus dari Bank Soal.');
    }

    public function exportExcel(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Bank Soal');

        // Header
        $headers = ['ID', 'Kategori', 'Posisi', 'Tipe', 'Pertanyaan', 'Aspek', 'Bobot', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Kunci Jawaban', 'Status Aktif'];
        $sheet->fromArray($headers, null, 'A1');

        $questions = Question::with(['category', 'position', 'options'])->get();
        $row = 2;

        foreach ($questions as $q) {
            $optA = $q->options->firstWhere('option_key', 'A')?->option_text ?? '';
            $optB = $q->options->firstWhere('option_key', 'B')?->option_text ?? '';
            $optC = $q->options->firstWhere('option_key', 'C')?->option_text ?? '';
            $optD = $q->options->firstWhere('option_key', 'D')?->option_text ?? '';
            $correct = $q->options->firstWhere('is_correct', true)?->option_key ?? '';

            $sheet->fromArray([
                $q->id,
                $q->category?->name,
                $q->position?->name ?? 'Umum',
                $q->type,
                $q->question_text,
                $q->aspect,
                $q->weight,
                $optA,
                $optB,
                $optC,
                $optD,
                $correct,
                $q->is_active ? 'Aktif' : 'Nonaktif',
            ], null, 'A'.$row);

            $row++;
        }

        AuditLogService::log('EXPORT_QUESTION_BANK', 'Mengekspor Bank Soal ke Excel.');

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="Bank_Soal_Psikotes_'.date('Ymd_His').'.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
