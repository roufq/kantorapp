<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\\Contracts\\Console\\Kernel')->bootstrap();
$ids=[4];
$deleted=\App\Models\WeeklyRosterEntry::whereIn('weekly_roster_id',$ids)->delete();
echo "Deleted entries: {$deleted}\n";
