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
    <h2>Rekap Jam Kerja Bulanan</h2>
    <div>Periode: {{ $startDate }} s.d {{ $endDate }}</div>
    <div>Karyawan: {{ $employeeName ?? $employeeId }}</div>

    <table class="summary-table" style="margin-top: 8px;">
        <tr>
            <td><strong>Target (menit)</strong>: {{ $slotSummary['target_minutes'] ?? '-' }}</td>
            <td><strong>Slot Approved (menit)</strong>: {{ $slotSummary['slot_minutes'] }}</td>
            <td><strong>Kehadiran (menit)</strong>: {{ $slotSummary['attendance_minutes'] }}</td>
            <td><strong>Sisa (menit)</strong>: {{ $slotSummary['remaining'] ?? '-' }}</td>
        </tr>
    </table>

    <h3 style="margin-top: 16px;">Detail Slot Approved</h3>
    <table>
        <thead>
            <tr>
                <th>Task</th>
                <th>Slot</th>
                <th>Menit</th>
                <th>Approved</th>
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
                <tr><td colspan="4" class="text-center">Belum ada slot approved pada rentang ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 style="margin-top: 16px;">Detail Kehadiran</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Durasi (menit)</th>
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
                <tr><td colspan="4" class="text-center">Belum ada data kehadiran pada rentang ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>