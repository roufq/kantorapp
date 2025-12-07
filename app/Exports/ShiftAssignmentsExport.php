<?php

namespace App\Exports;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ShiftAssignmentsExport implements FromQuery, WithHeadings, WithMapping
{
    /** @var \Illuminate\Database\Eloquent\Builder */
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query->orderBy('date', 'desc');
    }

    public function headings(): array
    {
        return ['Date', 'Location', 'User ID', 'User Name', 'Shift ID', 'Shift Name', 'Slots', 'Status', 'Notes'];
    }

    public function map($row): array
    {
        $slots = $row->locationShift ? $row->locationShift->normalizedSlots() : (optional($row->shift) ? $row->shift->normalizedSlots() : []);
        $slotText = collect($slots)->map(function ($s) {
            return $s['start'] . ' - ' . $s['end'] . (!empty($s['days']) ? ' (' . implode(',', $s['days']) . ')' : '');
        })->implode('; ');

        return [
            optional($row->date)->format('Y-m-d'),
            optional($row->location)->name,
            $row->user_id,
            optional($row->user)->name,
            $row->shift_id,
            optional($row->shift)->name,
            $slotText,
            $row->status,
            $row->notes,
        ];
    }
}
