<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\\Contracts\\Console\\Kernel')->bootstrap();
$validIds = \App\Models\WeeklyRoster::pluck('id')->all();
$deleted = \App\Models\WeeklyRosterEntry::whereNotIn('weekly_roster_id',$validIds)->delete();
echo "Deleted orphan entries: {$deleted}\n";
