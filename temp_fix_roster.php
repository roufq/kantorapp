<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\\Contracts\\Console\\Kernel')->bootstrap();
$ids = [2,3];
foreach ($ids as $id) {
    if ($r = \App\Models\WeeklyRoster::find($id)) {
        $r->entries()->delete();
        $r->delete();
        echo "Deleted roster {$id}\n";
    } else {
        echo "Roster {$id} not found\n";
    }
}
