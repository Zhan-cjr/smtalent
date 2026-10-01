<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Seleksi - {{ $candidate->name }}</title>
    <style>
        @page { margin: 25px 30px; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            font-size: 10px;
            color: #64748b;
            margin: 2px 0 0 0;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background-color: #d1fae5; color: #065f46; }
        .badge-warning { background-color: #fef3c7; color: #92400e; }
        .badge-danger { background-color: #fee2e2; color: #991b1b; }
        .badge-primary { background-color: #e0e7ff; color: #3730a3; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        th, td {
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 9px;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #e2e8f0;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin: 14px 0 8px 0;
            text-transform: uppercase;
        }
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 12px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-indigo { color: #4f46e5; }
        .footer {
            margin-top: 25px;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <table class="header">
        <tr>
            <td>
                <h1>LEMBAR HASIL EVALUASI PSIKOTES & WAWANCARA</h1>
                <p>{{ $companyName }} | Divisi Rekrutmen & Pengembangan SDM</p>
            </td>
            <td class="text-right">
                <span class="badge {{ $candidate->final_status === 'LOLOS' ? 'badge-success' : ($candidate->final_status === 'CADANGAN' ? 'badge-warning' : 'badge-primary') }}">
                    STATUS: {{ $candidate->final_status ?? $candidate->status }}
                </span>
                @if($candidate->final_rank)
                    <p style="margin-top: 4px; font-weight: bold; font-size: 12px; color: #4f46e5;">RANKING: #{{ $candidate->final_rank }}</p>
                @endif
            </td>
        </tr>
    </table>

    <!-- Data Kandidat -->
    <div class="section-title">1. Data Pribadi Kandidat</div>
    <table class="table-bordered">
        <tr>
            <td width="20%" class="font-bold">Nama Lengkap</td>
            <td width="30%">{{ $candidate->name }}</td>
            <td width="20%" class="font-bold">Posisi Dilamar</td>
            <td width="30%">{{ $candidate->vacancy?->position?->name }}</td>
        </tr>
        <tr>
            <td class="font-bold">Email</td>
            <td>{{ $candidate->email }}</td>
            <td class="font-bold">Lowongan Batch</td>
            <td>{{ $candidate->vacancy?->title }}</td>
        </tr>
        <tr>
            <td class="font-bold">No. Telepon / WA</td>
            <td>{{ $candidate->phone }}</td>
            <td class="font-bold">NIK / KTP</td>
            <td>{{ $candidate->nik ?: '-' }}</td>
        </tr>
        <tr>
            <td class="font-bold">Pendidikan</td>
            <td>{{ $candidate->education ?: '-' }}</td>
            <td class="font-bold">Tanggal Cetak</td>
            <td>{{ date('d F Y - H:i') }} WIB</td>
        </tr>
    </table>

    <!-- Ringkasan Nilai Akhir -->
    <div class="section-title">2. Rekapitulasi Skor Akhir Seleksi (Formula {{ $psychotestWeight }}% : {{ $interviewWeight }}%)</div>
    <table class="table-bordered" style="background-color: #f1f5f9;">
        <thead>
            <tr>
                <th class="text-center">Komponen Penilaian</th>
                <th class="text-center">Nilai Asli (0-100)</th>
                <th class="text-center">Bobot Persentase</th>
                <th class="text-center">Poin Terbobot</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="font-bold">Tes Psikotes & Logika Kerja</td>
                <td class="text-center font-bold">{{ $candidate->final_psychotest_score ?? '0.00' }}</td>
                <td class="text-center">{{ $psychotestWeight }}%</td>
                <td class="text-center font-bold text-indigo">{{ round(floatval($candidate->final_psychotest_score ?? 0) * ($psychotestWeight / 100), 2) }}</td>
            </tr>
            <tr>
                <td class="font-bold">Wawancara Kompetensi HRD (8 Aspek)</td>
                <td class="text-center font-bold">{{ $candidate->final_interview_score ?? '0.00' }}</td>
                <td class="text-center">{{ $interviewWeight }}%</td>
                <td class="text-center font-bold text-indigo">{{ round(floatval($candidate->final_interview_score ?? 0) * ($interviewWeight / 100), 2) }}</td>
            </tr>
            <tr style="background-color: #e2e8f0; font-size: 12px;">
                <td colspan="3" class="font-bold text-right">TOTAL NILAI AKHIR:</td>
                <td class="text-center font-bold" style="color: #1e1b4b; font-size: 14px;">{{ $candidate->final_score ?? '0.00' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Rincian Skor Psikotes -->
    @php
        $latestAttempt = $candidate->testAttempts->last();
    @endphp
    @if($latestAttempt)
        <div class="section-title">3. Rincian Skor Psikotes</div>
        <table class="table-bordered">
            <tr>
                <td width="25%" class="font-bold">Total Soal: {{ $latestAttempt->total_questions }}</td>
                <td width="25%" class="font-bold" style="color: #059669;">Jawaban Benar: {{ $latestAttempt->total_correct }}</td>
                <td width="25%" class="font-bold" style="color: #dc2626;">Jawaban Salah: {{ $latestAttempt->total_wrong }}</td>
                <td width="25%" class="font-bold" style="color: #d97706;">Tidak Dijawab: {{ $latestAttempt->total_unanswered }}</td>
            </tr>
        </table>

        @if(!empty($latestAttempt->aspect_scores_json))
            <p style="font-size: 9px; font-weight: bold; margin: 4px 0;">Indikator Sikap Kerja (Skala 1-5):</p>
            <table class="table-bordered">
                <thead>
                    <tr>
                        @foreach($latestAttempt->aspect_scores_json as $aspect => $d)
                            <th class="text-center">{{ $aspect }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach($latestAttempt->aspect_scores_json as $aspect => $d)
                            <td class="text-center font-bold">{{ $d['score_100'] ?? $d['average'] }} / 100</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        @endif
    @endif

    <!-- Rincian Wawancara 8 Aspek -->
    @php
        $latestItw = $candidate->interviews->last();
    @endphp
    @if($latestItw && $latestItw->scores->isNotEmpty())
        <div class="section-title">4. Rincian Penilaian Wawancara HRD (8 Aspek)</div>
        <table class="table-bordered">
            <thead>
                <tr>
                    @foreach($latestItw->scores as $sc)
                        <th class="text-center">{{ $sc->aspect_name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach($latestItw->scores as $sc)
                        <td class="text-center font-bold" style="color: #7c3aed;">{{ $sc->score }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>

        <div class="summary-box">
            <p><strong>Rekomendasi Interviewer:</strong> {{ $latestItw->recommendation ?? '-' }}</p>
            @if($latestItw->strengths)
                <p><strong>Kelebihan:</strong> {{ $latestItw->strengths }}</p>
            @endif
            @if($latestItw->weaknesses)
                <p><strong>Catatan Pengembangan:</strong> {{ $latestItw->weaknesses }}</p>
            @endif
        </div>
    @endif

    <!-- Footer Signature -->
    <table style="margin-top: 30px;">
        <tr>
            <td width="60%"></td>
            <td width="40%" class="text-center">
                <p>Disetujui Oleh,</p>
                <div style="height: 50px;"></div>
                <p class="font-bold"><u>Tim HRD & Talent Acquisition</u></p>
                <p>{{ $companyName }}</p>
            </td>
        </tr>
    </table>

    <div class="footer">
        * Dokumen ini dibuat otomatis oleh Sistem Psikotes & Seleksi Karyawan sebagai alat bantu evaluasi rekrutmen objektif.
    </div>
</body>
</html>
