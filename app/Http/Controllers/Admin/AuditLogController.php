<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('audit_logs')) {
            return view('admin.audit-logs.index', ['logs' => collect()]);
        }

        $query = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select('audit_logs.*', 'users.name as user_name', 'users.email as user_email')
            ->orderBy('audit_logs.timestamp', 'desc');

        if ($q = $request->get('q')) {
            $query->where('audit_logs.action', 'like', "%{$q}%")
                  ->orWhere('audit_logs.module', 'like', "%{$q}%")
                  ->orWhere('users.name', 'like', "%{$q}%");
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.audit-logs.index', compact('logs'));
    }

    public function show($id)
    {
        if (!Schema::hasTable('audit_logs')) abort(404);

        $log = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select('audit_logs.*', 'users.name as user_name', 'users.email as user_email')
            ->where('audit_logs.id', $id)
            ->first();

        if (!$log) abort(404);

        return view('admin.audit-logs.show', compact('log'));
    }
}
