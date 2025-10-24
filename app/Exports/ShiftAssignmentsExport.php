<?php

namespace App\Exports;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ShiftAssignmentsExport implements FromCollection, WithHeadings, WithMapping
{
    /** @var \Illuminate\Database\Eloquent\Builder */
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return ['Date', 'User ID', 'User Name', 'Shift ID', 'Shift Name', 'Status', 'Notes'];
    }

    public function map($row): array
    {
        return [
            optional($row->date)->format('Y-m-d'),
            $row->user_id,
            optional($row->user)->name,
            $row->shift_id,
            optional($row->shift)->name,
            $row->status,
            $row->notes,
        ];
    }
}
