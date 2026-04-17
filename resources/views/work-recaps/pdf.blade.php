<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h2 { margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f5f5f5; }
        .summary-table td { border: none; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h2>Monthly Working Hours Recap</h2>
    <div>Period: {{ $startDate }} to {{ $endDate }}</div>
    <div>Employee: {{ $employeeName ?? $employeeId }}</div>

    <table class="summary-table" style="margin-top: 8px;">
        <tr>
            <td><strong>Target (minutes)</strong>: {{ $slotSummary['target_minutes'] ?? '-' }}</td>
            <td><strong>Approved Slot (minutes)</strong>: {{ $slotSummary['slot_minutes'] }}</td>
            <td><strong>Attendance (minutes)</strong>: {{ $slotSummary['attendance_minutes'] }}</td>
            <td><strong>Remaining (minutes)</strong>: {{ $slotSummary['remaining'] ?? '-' }}</td>
        </tr>
    </table>

    <h3 style="margin-top: 16px;">Approved Slot Details</h3>
    <table>
        <thead>
            <tr>
                <th>Task</th>
                <th>Slot</th>
                <th>Minutes</th>
                <th>Approved at</th>
            </tr>
        </thead>
        <tbody>
            @forelse($slotDetails as $slot)
                <tr>
                    <td>{{ $slot->task->title ?? 'Task #'.$slot->task_id }}</td>
                    <td>{{ $slot->name }}</td>
                    <td class="text-right">{{ $slot->minutes }}</td>
                    <td>{{ optional($slot->approved_at)->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">No approved slots in this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 style="margin-top: 16px;">Attendance Details</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Duration (minutes)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendanceDetails as $att)
                <tr>
                    <td>{{ optional($att->check_in_time)->format('d M Y') }}</td>
                    <td>{{ optional($att->check_in_time)->format('H:i') }}</td>
                    <td>{{ optional($att->check_out_time)->format('H:i') }}</td>
                    <td class="text-right">{{ $att->duration_minutes ?? 0 }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center">No attendance data in this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>