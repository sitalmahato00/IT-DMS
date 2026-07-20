<?php

namespace App\Notifications\Concerns;

use App\Models\ErpSetting;

trait UsesNotificationEmailSettings
{
    protected function notificationEmailEnabled(string $key): bool
    {
        if (!ErpSetting::isEnabled('notification_email_enabled', true)) {
            return false;
        }

        return ErpSetting::isEnabled($key, true);
    }
}

