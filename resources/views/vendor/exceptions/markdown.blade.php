# {{ $exception->class() }} - {!! $exception->title() !!}

{!! $exception->message() !!}

PHP {{ PHP_VERSION }}

Laravel {{ app()->version() }}

{{ $exception->request()->httpHost() }}

## Stack Trace
@foreach($exception->frames() as $index => $frame)
{{ $index }} - {{ $frame->file() }}:{{ $frame->line() }}
@endforeach

## Request

{{ $exception->request()->method() }} {{ \Illuminate\Support\Str::start($exception->request()->path(), '/') }}

## Headers
@php $headers = $exception->requestHeaders(); @endphp
@if(empty($headers))
No header data available.
@else
@foreach($headers as $key => $value)
* **{{ $key }}**: {!! $value !!}
@endforeach
@endif

## Route Context
@php $routeContext = $exception->applicationRouteContext(); @endphp
@if(empty($routeContext))
No routing data available.
@else
@foreach($routeContext as $name => $value)
{{ $name }}: {!! $value !!}
@endforeach
@endif

## Route Parameters
@if ($routeParametersContext = $exception->applicationRouteParametersContext())
{!! $routeParametersContext !!}
@else
No route parameter data available.
@endif

## Database Queries
@php $queries = $exception->applicationQueries(); @endphp
@if(empty($queries))
No database queries detected.
@else
@foreach ($queries as $query)
* {{ $query['connectionName'] }} - {!! $query['sql'] !!} ({{ $query['time'] }} ms)
@endforeach
@endif
