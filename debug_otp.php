<?php
// Debug 2FA settings

use App\Models\ErpSetting;
use Illuminate\Support\Facades\DB;

require_once 'bootstrap/app.php';
$app = new Illuminate\Foundation\Application(getcwd());
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

echo "=== Database Records ===\n";
$settings = DB::table('erp_settings')
    ->where('key', 'like', 'security_two%')
    ->get();

foreach ($settings as $s) {
    echo "{$s->key}: {$s->value} (type: {$s->type})\n";
}

echo "\n=== ErpSetting::get() Results ===\n";
echo "security_two_factor_enabled: " . (ErpSetting::isEnabled('security_two_factor_enabled') ? 'true' : 'false') . "\n";
echo "security_two_factor_roles: " . json_encode(ErpSetting::asArray('security_two_factor_roles')) . "\n";
