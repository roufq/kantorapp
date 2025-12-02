<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceRecapExport implements FromArray, WithHeadings, WithMapping
{
    private array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'User',
            'Location',
            'Total Days',
            'Holiday',
            'Weekly Off',
            'Leave',
            'Working Days',
            'Present',
            'Alfa',
        ];
    }

    public function map($row): array
    {
        return [
            $row['user']->name,
            optional($row['location'])->name,
            $row['totalDays'],
            $row['holidayDays'],
            $row['weeklyOffDays'],
            $row['leaveDays'],
            $row['workingDays'],
            $row['presentDays'],
            $row['alphaDays'],
        ];
    }
}
