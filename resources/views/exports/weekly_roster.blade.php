<table>
    <thead>
    <tr>
        <th>Date</th>
        <th>Slot</th>
        <th>Time</th>
        <th>Employees</th>
        <th>Status</th>
        <th>Notes</th>
    </tr>
    </thead>
    <tbody>
    @foreach($roster->entries->sortBy(['date','slot_index']) as $e)
        <tr>
            <td>{{ $e->date->toDateString() }}</td>
            <td>{{ $e->status === 'off' ? 'OFF' : $e->slot_index + 1 }}</td>
            <td>
                @if($e->status === 'off')
                    Day off
                @elseif(isset($slotMap[$e->slot_index]))
                    {{ $slotMap[$e->slot_index]['start'] ?? '?' }} - {{ $slotMap[$e->slot_index]['end'] ?? '?' }}
                @else
                    -
                @endif
            </td>
            <td>{{ $e->user->name ?? '-' }}</td>
            <td>{{ $e->status }}</td>
            <td>{{ $e->notes }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
