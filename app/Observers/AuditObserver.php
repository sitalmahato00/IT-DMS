<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    protected function getRequestMeta()
    {
        $ip = null;
        $ua = null;
        try {
            $ip = request()->ip();
            $ua = request()->userAgent();
        } catch (\Exception $e) {
            // running outside HTTP context
        }

        return [
            'ip' => $ip,
            'ua' => $ua,
            'user_id' => Auth::id(),
        ];
    }

    protected function shouldIgnore(Model $model)
    {
        return $model instanceof AuditLog;
    }

    public function created(Model $model)
    {
        if ($this->shouldIgnore($model)) {
            return;
        }

        $meta = $this->getRequestMeta();

        AuditLog::create([
            'timestamp' => now(),
            'user_id' => $meta['user_id'],
            'action' => 'create',
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'old_values' => null,
            'new_values' => $model->getAttributes(),
            'ip_address' => $meta['ip'],
            'user_agent' => $meta['ua'],
        ]);
    }

    public function updated(Model $model)
    {
        if ($this->shouldIgnore($model)) {
            return;
        }

        $meta = $this->getRequestMeta();

        AuditLog::create([
            'timestamp' => now(),
            'user_id' => $meta['user_id'],
            'action' => 'update',
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'old_values' => $model->getOriginal(),
            'new_values' => $model->getAttributes(),
            'ip_address' => $meta['ip'],
            'user_agent' => $meta['ua'],
        ]);
    }

    public function deleted(Model $model)
    {
        if ($this->shouldIgnore($model)) {
            return;
        }

        $meta = $this->getRequestMeta();

        AuditLog::create([
            'timestamp' => now(),
            'user_id' => $meta['user_id'],
            'action' => 'delete',
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'old_values' => $model->getOriginal(),
            'new_values' => null,
            'ip_address' => $meta['ip'],
            'user_agent' => $meta['ua'],
        ]);
    }
}

