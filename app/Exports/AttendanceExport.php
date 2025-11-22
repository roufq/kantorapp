<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'User Name',
            'Employee Name',
            'Check In Time',
            'Check Out Time',
            'Location',
            'Is Late',
            'Approval Status',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->user->name,
            $attendance->user->employee->nama ?? 'N/A',
            $attendance->check_in_time ? $attendance->check_in_time->format('Y-m-d H:i:s') : 'N/A',
            $attendance->check_out_time ? $attendance->check_out_time->format('Y-m-d H:i:s') : 'Not checked out',
            $attendance->location ?? 'N/A',
            $attendance->is_late ? 'Yes' : 'No',
            $attendance->approval_status ?? 'N/A',
        ];
    }
}
