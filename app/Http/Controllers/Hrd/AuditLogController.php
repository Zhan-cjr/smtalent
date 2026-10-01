<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $action = $request->query('action');
        $query = AuditLog::with('user');

        if ($action) {
            $query->where('action', $action);
        }

        $logs = $query->latest('created_at')->paginate(20)->withQueryString();
        $distinctActions = AuditLog::distinct()->pluck('action');

        return view('hrd.audit-logs.index', compact('logs', 'distinctActions', 'action'));
    }
}
