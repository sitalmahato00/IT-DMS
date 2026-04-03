<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('audit_logs')) {
            return view('admin.audit-logs.index', [
                'logs' => collect(),
                'userTabs' => collect(),
                'actionOptions' => [],
                'filters' => $this->normalizeAuditFilters($request),
                'perPage' => (int) $request->get('per_page', 10),
            ]);
        }

        $query = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select(
                'audit_logs.*',
                'users.name as user_name',
                'users.email as user_email',
                'users.role as user_role',
                'users.profile_photo_path as user_photo_path'
            )
            ->orderByDesc('audit_logs.timestamp')
            ->orderByDesc('audit_logs.id');

        $filters = $this->normalizeAuditFilters($request);

        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($where) use ($q) {
                $where->where('audit_logs.action', 'like', "%{$q}%")
                    ->orWhere('audit_logs.module', 'like', "%{$q}%")
                    ->orWhere('audit_logs.description', 'like', "%{$q}%")
                    ->orWhere('users.name', 'like', "%{$q}%")
                    ->orWhere('users.email', 'like', "%{$q}%")
                    ->orWhere('audit_logs.ip_address', 'like', "%{$q}%");
            });
        }

        if (!empty($filters['action'])) {
            $query->where('audit_logs.action', $filters['action']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('audit_logs.user_id', $filters['user_id']);
        }

        $perPage = in_array((int) $filters['per_page'], [10, 20, 50], true) ? (int) $filters['per_page'] : 10;
        $logs = $query->paginate($perPage)->withQueryString();

        $logs->getCollection()->transform(fn ($log) => $this->decorateAuditLog($log));

        return view('admin.audit-logs.index', [
            'logs' => $logs,
            'userTabs' => $this->getAuditUserTabs($filters),
            'actionOptions' => $this->getAuditActionOptions(),
            'filters' => $filters,
            'perPage' => $perPage,
        ]);
    }

    public function show($id)
    {
        if (!Schema::hasTable('audit_logs')) abort(404);

        $log = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select(
                'audit_logs.*',
                'users.name as user_name',
                'users.email as user_email',
                'users.role as user_role',
                'users.profile_photo_path as user_photo_path'
            )
            ->where('audit_logs.id', $id)
            ->first();

        if (!$log) abort(404);

        return view('admin.audit-logs.show', [
            'log' => $this->decorateAuditLog($log),
            'userTabs' => $this->getAuditUserTabs($this->normalizeAuditFilters(request())),
            'actionOptions' => $this->getAuditActionOptions(),
            'filters' => $this->normalizeAuditFilters(request()),
        ]);
    }

    private function normalizeAuditFilters(Request $request): array
    {
        return [
            'q' => trim((string) $request->get('q', '')),
            'action' => trim((string) $request->get('action', '')),
            'user_id' => trim((string) $request->get('user_id', '')),
            'per_page' => (int) $request->get('per_page', 10),
        ];
    }

    private function getAuditUserTabs(array $filters = []): \Illuminate\Support\Collection
    {
        if (!Schema::hasTable('audit_logs')) {
            return collect([
                [
                    'id' => null,
                    'name' => 'All Users',
                    'email' => '',
                    'role' => 'all',
                    'photo_url' => null,
                    'count' => 0,
                    'active' => empty($filters['user_id']),
                ],
            ]);
        }

        $tabs = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select(
                'audit_logs.user_id',
                'users.name as user_name',
                'users.email as user_email',
                'users.role as user_role',
                'users.profile_photo_path as user_photo_path',
                DB::raw('COUNT(audit_logs.id) as log_count')
            )
            ->groupBy('audit_logs.user_id', 'users.name', 'users.email', 'users.role', 'users.profile_photo_path')
            ->orderByDesc('log_count')
            ->limit(6)
            ->get()
            ->map(fn ($user) => [
                'id' => $user->user_id,
                'name' => $user->user_name ?? 'System',
                'email' => $user->user_email ?? '',
                'role' => $user->user_role ?? 'system',
                'photo_url' => $this->resolveStoredPhotoUrl($user->user_photo_path ?? null),
                'count' => (int) $user->log_count,
                'active' => (string) ($filters['user_id'] ?? '') === (string) $user->user_id,
            ]);

        return collect([
            [
                'id' => null,
                'name' => 'All Users',
                'email' => '',
                'role' => 'all',
                'photo_url' => null,
                'count' => (int) DB::table('audit_logs')->count(),
                'active' => empty($filters['user_id']),
            ],
        ])->concat($tabs);
    }

    private function getAuditActionOptions(): array
    {
        if (!Schema::hasTable('audit_logs')) {
            return [];
        }

        return DB::table('audit_logs')
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action')
            ->filter()
            ->mapWithKeys(fn ($action) => [$action => Str::headline(str_replace('_', ' ', $action))])
            ->toArray();
    }

    private function decorateAuditLog(object $log): object
    {
        $old = $this->decodeAuditValues($log->old_values ?? null);
        $new = $this->decodeAuditValues($log->new_values ?? null);
        $entity = $log->model_type ? class_basename($log->model_type) : ($log->module ?? 'Record');
        $displayName = $this->resolveAuditDisplayName($old, $new);
        $changes = $this->resolveAuditChanges($log->action ?? '', $old, $new);
        $browser = $this->parseBrowserInfo($log->user_agent ?? '');

        $log->old_values = $old;
        $log->new_values = $new;
        $log->entity_name = $entity;
        $log->display_name = $displayName;
        $log->details = $this->buildAuditSummary($log, $entity, $displayName, $changes);
        $log->changes = $changes;
        $log->timestamp_label = $log->timestamp ? \Carbon\Carbon::parse($log->timestamp)->format('Y-m-d H:i') : null;
        $log->timestamp_human = $log->timestamp ? \Carbon\Carbon::parse($log->timestamp)->diffForHumans() : null;
        $log->action_label = $log->formatted_action ?? Str::headline((string) ($log->action ?? 'action'));
        $log->user_role_label = Str::headline((string) ($log->user_role ?? 'System'));
        $log->user_photo_url = $this->resolveStoredPhotoUrl($log->user_photo_path ?? null);
        $log->user_initials = $this->getInitials($log->user_name ?? 'System');
        $log->browser_name = $browser['name'];
        $log->browser_platform = $browser['platform'];
        $log->browser_icon = $browser['icon'];
        $log->raw_user_agent = $log->user_agent ?? '';

        return $log;
    }

    private function decodeAuditValues(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    private function resolveAuditDisplayName(array $old, array $new): ?string
    {
        $nameKeys = ['name', 'title', 'subject_name', 'course_name', 'exam_name', 'file_name', 'gallery_title', 'full_name'];
        foreach ($nameKeys as $key) {
            if (!empty($new[$key])) {
                return (string) $new[$key];
            }
            if (!empty($old[$key])) {
                return (string) $old[$key];
            }
        }

        return null;
    }

    private function resolveAuditChanges(string $action, array $old, array $new): array
    {
        $changes = [];
        $keys = array_values(array_unique(array_merge(array_keys($old), array_keys($new))));

        foreach ($keys as $key) {
            $before = $old[$key] ?? null;
            $after = $new[$key] ?? null;
            if ($action === 'create' && $after !== null && $after !== '') {
                $changes[] = ['field' => $key, 'label' => Str::headline(str_replace(['_', '-'], ' ', $key)), 'before' => null, 'after' => $after];
            } elseif ($action === 'delete' && $before !== null && $before !== '') {
                $changes[] = ['field' => $key, 'label' => Str::headline(str_replace(['_', '-'], ' ', $key)), 'before' => $before, 'after' => null];
            } elseif ($before !== $after) {
                $changes[] = ['field' => $key, 'label' => Str::headline(str_replace(['_', '-'], ' ', $key)), 'before' => $before, 'after' => $after];
            }
        }

        return $changes;
    }

    private function buildAuditSummary(object $log, string $entity, ?string $displayName, array $changes): string
    {
        $action = strtolower((string) ($log->action ?? 'action'));

        if ($action === 'create') {
            return $displayName ? "Created {$entity}: {$displayName}" : "Created {$entity}";
        }

        if ($action === 'update') {
            $snippets = collect($changes)->take(3)->map(function ($change) {
                $before = $this->stringifyAuditValue($change['before']);
                $after = $this->stringifyAuditValue($change['after']);
                return "{$change['field']}: {$before} → {$after}";
            })->filter()->values()->all();

            if ($displayName && $snippets) {
                return "Updated {$entity} {$displayName} — " . implode(', ', $snippets);
            }

            return $snippets ? "Updated {$entity}: " . implode(', ', $snippets) : "Updated {$entity}";
        }

        if ($action === 'delete') {
            return $displayName ? "Deleted {$entity}: {$displayName}" : "Deleted {$entity}";
        }

        return $displayName ? "{$entity}: {$displayName}" : (string) ($log->module ?? Str::headline($action));
    }

    private function stringifyAuditValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '—';
    }

    private function parseBrowserInfo(string $userAgent): array
    {
        $browser = 'Browser';
        $platform = 'Windows';
        $icon = 'bi-browser-chrome';

        $browserMap = [
            'Edg' => ['name' => 'Edge', 'icon' => 'bi-browser-edge'],
            'OPR' => ['name' => 'Opera', 'icon' => 'bi-browser-opera'],
            'Chrome' => ['name' => 'Chrome', 'icon' => 'bi-browser-chrome'],
            'Firefox' => ['name' => 'Firefox', 'icon' => 'bi-browser-firefox'],
            'Safari' => ['name' => 'Safari', 'icon' => 'bi-browser-safari'],
        ];

        foreach ($browserMap as $needle => $meta) {
            if (str_contains($userAgent, $needle)) {
                $browser = $meta['name'];
                $icon = $meta['icon'];
                break;
            }
        }

        if (str_contains($userAgent, 'Windows')) {
            $platform = 'Windows';
        } elseif (str_contains($userAgent, 'Macintosh')) {
            $platform = 'macOS';
        } elseif (str_contains($userAgent, 'Linux')) {
            $platform = 'Linux';
        } elseif (str_contains($userAgent, 'Android')) {
            $platform = 'Android';
        } elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) {
            $platform = 'iOS';
        }

        return [
            'name' => $browser,
            'platform' => $platform,
            'icon' => $icon,
        ];
    }

    private function resolveStoredPhotoUrl(?string $path): ?string
    {
        $path = is_string($path) ? trim($path) : '';

        if ($path === '') {
            return null;
        }

        if (preg_match('/^(https?:|data:|blob:)/i', $path)) {
            return $path;
        }

        $normalized = str_replace('\\', '/', $path);
        $normalized = ltrim($normalized, '/');

        if (str_starts_with($normalized, 'storage/')) {
            $normalized = substr($normalized, 8);
        }

        $normalized = ltrim($normalized, '/');

        if ($normalized === '') {
            return null;
        }

        return Storage::url($normalized);
    }

    private function getInitials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $initials = collect($parts)->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->join('');

        return $initials !== '' ? strtoupper($initials) : 'A';
    }
}
