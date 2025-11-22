<?php

namespace App\Exports;

use App\Models\Overtime;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OvertimeExport implements FromQuery, WithHeadings, WithMapping
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
            'Employee Name',
            'Date',
            'Start Time',
            'End Time',
            'Duration (Minutes)',
            'Reason',
            'Status',
            'Selected Masters',
            'Approval Details',
        ];
    }

    public function map($overtime): array
    {
        // Get selected masters names
        $selectedMasters = [];
        if ($overtime->selected_masters) {
            $masters = \App\Models\User::whereIn('id', $overtime->selected_masters)->pluck('name')->toArray();
            $selectedMasters = implode(', ', $masters);
        }

        // Get approval details
        $approvalDetails = [];
        foreach ($overtime->approvals as $approval) {
            $approvalDetails[] = $approval->master->name . ': ' . ucfirst($approval->status);
        }
        $approvalDetailsStr = implode('; ', $approvalDetails);

        return [
            $overtime->user->name,
            $overtime->date ? $overtime->date->format('Y-m-d') : 'N/A',
            $overtime->start_time_wib ?? 'N/A',
            $overtime->end_time_wib ?? 'N/A',
            $overtime->duration_hours ? number_format($overtime->duration_minutes, 0) : 'N/A',
            $overtime->reason ?? 'N/A',
            ucfirst($overtime->status),
            $selectedMasters,
            $approvalDetailsStr,
        ];
    }
}
