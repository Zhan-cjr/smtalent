<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Position;
use App\Models\Setting;
use App\Models\Vacancy;
use App\Services\AuditLogService;
use App\Services\RankingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected RankingService $rankingService
    ) {}

    public function index(): View
    {
        $vacancies = Vacancy::with('position')->get();
        $positions = Position::all();

        return view('hrd.reports.index', compact('vacancies', 'positions'));
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $vacancyId = $request->query('vacancy_id');
        $positionId = $request->query('position_id');
        $finalStatus = $request->query('final_status');

        $candidates = $this->rankingService->getFinalRankings(
            $vacancyId ? intval($vacancyId) : null,
            $positionId ? intval($positionId) : null,
            $finalStatus
        );

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Hasil Seleksi');

        // Header
        $headers = [
            'Rank', 'Nama Kandidat', 'Email', 'No Telepon', 'Posisi',
            'Nilai Psikotes (60%)', 'Nilai Interview (40%)', 'Nilai Akhir', 'Status Seleksi', 'Keputusan Final',
        ];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        $rankNum = 1;
        foreach ($candidates as $c) {
            $sheet->fromArray([
                $rankNum++,
                $c->name,
                $c->email,
                $c->phone,
                $c->vacancy?->position?->name,
                $c->final_psychotest_score ?? 0,
                $c->final_interview_score ?? 0,
                $c->final_score ?? 0,
                $c->status,
                $c->final_status ?? '-',
            ], null, 'A'.$row);

            $row++;
        }

        AuditLogService::log('EXPORT_REPORT', 'Mengekspor laporan hasil seleksi ke Excel.');

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="Rekap_Hasil_Seleksi_'.date('Ymd_His').'.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    public function exportPsychotestExcel(Request $request): StreamedResponse
    {
        $vacancyId = $request->query('vacancy_id');
        $positionId = $request->query('position_id');
        $status = $request->query('status');

        $candidates = $this->rankingService->getPsychotestRankings(
            $vacancyId ? intval($vacancyId) : null,
            $positionId ? intval($positionId) : null,
            $status
        );

        $passingGrade = floatval(Setting::get('psychotest_passing_grade', 65));

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Hasil Psikotes');

        // Header
        $headers = [
            'Rank', 'Nama Kandidat', 'Email', 'No Telepon', 'Posisi Jabatan',
            'Durasi Pengerjaan', 'Nilai Psikotes', 'Passing Grade', 'Hasil Kelulusan', 'Status Kandidat',
        ];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        $rankNum = 1;
        foreach ($candidates as $c) {
            $isPassed = ($c->final_psychotest_score >= $passingGrade);
            $duration = $c->latestAttempt ? round($c->latestAttempt->duration_seconds_used / 60).' menit' : '-';

            $sheet->fromArray([
                $rankNum++,
                $c->name,
                $c->email,
                $c->phone,
                $c->vacancy?->position?->name ?? $c->vacancy?->title ?? '-',
                $duration,
                $c->final_psychotest_score ?? 0,
                $passingGrade,
                $isPassed ? 'LULUS' : 'TIDAK LULUS',
                $c->status,
            ], null, 'A'.$row);

            $row++;
        }

        AuditLogService::log('EXPORT_REPORT', 'Mengekspor laporan hasil tes psikotes ke Excel.');

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="Rekap_Hasil_Psikotes_'.date('Ymd_His').'.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    public function exportCandidatePdf(Candidate $candidate): Response
    {
        $candidate->load([
            'user',
            'vacancy.position',
            'testAttempts.testPackage',
            'interviews.interviewer',
            'interviews.scores',
        ]);

        $companyName = Setting::get('company_name', 'PT Rekrutmen Cipta Karir Indonesia');
        $psychotestWeight = Setting::get('psychotest_weight', 60);
        $interviewWeight = Setting::get('interview_weight', 40);

        $pdf = Pdf::loadView('hrd.reports.candidate-pdf', compact(
            'candidate',
            'companyName',
            'psychotestWeight',
            'interviewWeight'
        ))->setPaper('a4', 'portrait');

        AuditLogService::log('EXPORT_PDF', "Mencetak PDF hasil seleksi kandidat: {$candidate->name}");

        return $pdf->download("Hasil_Seleksi_{$candidate->name}.pdf");
    }
}
