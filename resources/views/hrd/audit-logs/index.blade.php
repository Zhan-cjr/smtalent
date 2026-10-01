@extends('layouts.hrd', [
    'title' => 'Audit Log Aktivitas',
    'headerTitle' => 'Audit Log & Riwayat Aktivitas',
    'headerSubtitle' => 'Pencatatan riwayat autentikasi, pengerjaan psikotes, penilaian, dan keputusan HRD'
])

@section('content')
<div class="space-y-6">
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Riwayat Aktivitas Sistem ({{ $logs->total() }})</h3>
            <p class="text-xs text-slate-500">Mencatat IP Address, User Agent, dan aksi pengguna secara kronologis</p>
        </div>

        <form action="{{ route('hrd.audit-logs.index') }}" method="GET" class="flex items-center gap-2">
            <select name="action" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-medium focus:border-indigo-500" onchange="this.form.submit()">
                <option value="">Semua Aksi</option>
                @foreach($distinctActions as $act)
                    <option value="{{ $act }}" {{ $action === $act ? 'selected' : '' }}>{{ $act }}</option>
                @endforeach
            </select>
            @if($action)
                <a href="{{ route('hrd.audit-logs.index') }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold" title="Reset">✕</a>
            @endif
        </form>
    </div>

    <!-- Log Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <th class="py-3.5 px-6">Waktu Kejadian</th>
                        <th class="py-3.5 px-6">Pengguna</th>
                        <th class="py-3.5 px-6">Aksi (Action)</th>
                        <th class="py-3.5 px-6">Keterangan / Rincian</th>
                        <th class="py-3.5 px-6 text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4 px-6 text-xs text-slate-500 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $log->created_at->format('d M Y - H:i:s') }}</div>
                                <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900 text-xs">{{ $log->user?->name ?? 'Sistem / Tamu' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $log->user?->email }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-600 max-w-md">
                                {{ $log->description }}
                            </td>
                            <td class="py-4 px-6 text-right font-mono text-xs text-slate-400">
                                {{ $log->ip_address ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-xs text-slate-400 font-medium">Belum ada catatan log aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
