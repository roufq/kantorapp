@php
    $connectionName = $connectionName ?? ($query['connectionName'] ?? '');
    $sql = $sql ?? ($query['sql'] ?? '');
    $time = $time ?? ($query['time'] ?? '');
@endphp
* {{ $connectionName }} - {!! $sql !!} ({{ $time }} ms)
