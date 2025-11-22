<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== LOCATIONS ===\n";
$locations = App\Models\Location::with('shifts')->get();
foreach($locations as $l) {
    echo $l->name . ' (ID:' . $l->id . ', schedule_type:' . ($l->schedule_type ?? 'null') . ', shift_enabled:' . ($l->shift_enabled ? 'true' : 'false') . ') - Shifts: ' . $l->shifts->pluck('code')->join(', ') . PHP_EOL;
}

echo "\n=== SHIFTS ===\n";
$shifts = App\Models\Shift::all();
foreach($shifts as $s) {
    echo $s->code . ' - ' . $s->name . PHP_EOL;
}
