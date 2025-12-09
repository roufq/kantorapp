<?php

namespace App\Exports;

use App\Models\WeeklyRoster;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class WeeklyRosterExport implements FromView
{
    public function __construct(private WeeklyRoster $roster)
    {
    }

    public function view(): View
    {
        $roster = $this->roster->load(['entries.user', 'location', 'locationShift.shift']);
        $slotMap = [];
        $slots = $roster->locationShift?->normalizedSlots() ?? [];
        foreach ($slots as $idx => $slot) {
            $slotMap[$idx] = $slot;
        }

        return view('exports.weekly_roster', compact('roster', 'slotMap'));
    }
}
