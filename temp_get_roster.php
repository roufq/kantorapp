<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\\Contracts\\Console\\Kernel')->bootstrap();
$r4 = \App\Models\WeeklyRoster::find(4);
var_export($r4 ? $r4->only(['id','location_shift_id']) : null);
