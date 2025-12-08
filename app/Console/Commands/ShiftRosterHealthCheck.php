<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WeeklyRosterEntry;
use App\Models\ShiftAssignment;

class ShiftRosterHealthCheck extends Command
{
    protected $signature = 'shift:roster-health {--fix-dupes : Hapus entri duplikat per user+date+slot_index, sisakan terbaru}';
    protected $description = 'Audit roster: duplikat, slot out-of-range, entri tanpa assignment/slot.';

    public function handle(): int
    {
        $this->info('Menjalankan audit roster...');
        $fixDupes = (bool) $this->option('fix-dupes');

        $issues = [
            'duplicates' => [],
            'slot_out_of_range' => [],
            'missing_assignment' => [],
            'assignment_mismatch' => [],
            'missing_slots' => [],
            'shift_assignment_duplicates' => [],
        ];

        // Duplikat per user+date+slot_index
        $groups = WeeklyRosterEntry::selectRaw('weekly_roster_id, user_id, date, slot_index, COUNT(*) as cnt, GROUP_CONCAT(id ORDER BY id DESC) as ids')
            ->groupBy('weekly_roster_id', 'user_id', 'date', 'slot_index')
            ->havingRaw('cnt > 1')
            ->get();
        foreach ($groups as $g) {
            $ids = array_map('intval', explode(',', $g->ids));
            $issues['duplicates'][] = [
                'weekly_roster_id' => $g->weekly_roster_id,
                'user_id' => $g->user_id,
                'date' => $g->date,
                'slot_index' => $g->slot_index,
                'ids' => $ids,
            ];
            if ($fixDupes && count($ids) > 1) {
                $keep = array_shift($ids); // keep latest (descending order)
                WeeklyRosterEntry::whereIn('id', $ids)->delete();
                $this->warn("Duplikat dibersihkan, simpan id {$keep}, hapus " . implode(',', $ids));
            }
        }

        // Slot out-of-range dan missing assignment/mismatch
        $entries = WeeklyRosterEntry::with(['roster.locationShift', 'shiftAssignment'])->get();
        foreach ($entries as $entry) {
            $slots = $entry->roster?->locationShift?->normalizedSlots() ?? [];
            if (empty($slots)) {
                $issues['missing_slots'][] = ['entry_id' => $entry->id, 'roster_id' => $entry->weekly_roster_id];
            } elseif ($entry->slot_index >= count($slots)) {
                $issues['slot_out_of_range'][] = $entry->id;
            }

            if ($entry->status === 'scheduled' && !$entry->shift_assignment_id) {
                $issues['missing_assignment'][] = $entry->id;
            } elseif ($entry->shiftAssignment && $entry->shiftAssignment->user_id != $entry->user_id) {
                $issues['assignment_mismatch'][] = $entry->id;
            }
        }

        // Duplikat shift_assignment per user+date
        $assignDupes = ShiftAssignment::selectRaw('user_id, date, COUNT(*) as cnt, GROUP_CONCAT(id ORDER BY id DESC) as ids')
            ->groupBy('user_id', 'date')
            ->havingRaw('cnt > 1')
            ->get();
        foreach ($assignDupes as $d) {
            $ids = array_map('intval', explode(',', $d->ids));
            $issues['shift_assignment_duplicates'][] = [
                'user_id' => $d->user_id,
                'date' => $d->date,
                'ids' => $ids,
            ];
            if ($fixDupes && count($ids) > 1) {
                $keep = array_shift($ids);
                ShiftAssignment::whereIn('id', $ids)->delete();
                $this->warn("Shift assignment duplikat dibersihkan, simpan id {$keep}, hapus " . implode(',', $ids));
            }
        }

        foreach ($issues as $key => $list) {
            $this->line($key . ': ' . count($list));
        }

        if (count($issues['missing_slots']) > 0) {
            $sample = collect($issues['missing_slots'])->take(20)->map(function ($item) {
                return 'entry_id=' . $item['entry_id'] . ' roster_id=' . $item['roster_id'];
            })->implode(', ');
            $this->warn('Detail missing_slots (maks 20): ' . $sample);
        }

        $hasIssues = collect($issues)->flatten()->count() > 0;
        if ($hasIssues) {
            $this->warn('Audit selesai dengan temuan. Periksa detail di atas.');
            return Command::FAILURE;
        }

        $this->info('Audit selesai. Tidak ditemukan masalah.');
        return Command::SUCCESS;
    }
}
