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

        // Compute a human-friendly details string for each log row
        $logs->getCollection()->transform(function ($log) {
            $old = [];
            $new = [];
            if (!empty($log->old_values)) {
                if (is_array($log->old_values)) {
                    $old = $log->old_values;
                } else {
                    $decodedOld = json_decode($log->old_values, true);
                    $old = is_array($decodedOld) ? $decodedOld : [];
                }
            }
            if (!empty($log->new_values)) {
                if (is_array($log->new_values)) {
                    $new = $log->new_values;
                } else {
                    $decodedNew = json_decode($log->new_values, true);
                    $new = is_array($decodedNew) ? $decodedNew : [];
                }
            }

            $nameKeys = ['name','title','subject_name','course_name','exam_name','file_name','gallery_title'];
            $displayName = null;
            foreach ($nameKeys as $k) {
                if (isset($new[$k]) && $new[$k]) { $displayName = $new[$k]; break; }
                if (isset($old[$k]) && $old[$k]) { $displayName = $old[$k]; break; }
            }

            $entity = $log->model_type ? class_basename($log->model_type) : ($log->module ?? 'Record');
            $action = $log->action ?? 'action';

            $details = '';
            if ($action === 'create') {
                $details = $displayName ? "Created {$entity}: {$displayName}" : "Created {$entity}";
            } elseif ($action === 'update') {
                // list changed fields if possible
                $changes = [];
                foreach ($new as $k => $v) {
                    $oldVal = $old[$k] ?? null;
                    if ($oldVal !== $v) {
                        $changes[] = "{$k}: " . (is_scalar($oldVal) ? $oldVal : json_encode($oldVal)) . " → " . (is_scalar($v) ? $v : json_encode($v));
                    }
                }
                if ($displayName && count($changes)) {
                    $details = "Updated {$entity} {$displayName} — " . implode(', ', array_slice($changes, 0, 3));
                } elseif (count($changes)) {
                    $details = "Updated {$entity}: " . implode(', ', array_slice($changes, 0, 3));
                } else {
                    $details = "Updated {$entity}";
                }
            } elseif ($action === 'delete') {
                $details = $displayName ? "Deleted {$entity}: {$displayName}" : "Deleted {$entity}";
            } else {
                $details = $displayName ? "{$entity}: {$displayName}" : ($log->module ?? ucfirst($action));
            }

            $log->details = $details;
            return $log;
        });

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
