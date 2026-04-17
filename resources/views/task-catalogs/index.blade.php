@extends('layouts.appnew')
@section('content')
<div class="bg-light p-3 mb-3 rounded border d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
        <h3 class="mb-1">Task Catalog</h3>
        <p class="text-muted mb-0">Jobdesk: {{ $jobdesk->name }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('jobdesks.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
        <a href="{{ route('jobdesks.catalogs.create', $jobdesk) }}" class="btn btn-primary btn-sm">Add Task</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Task Catalog List</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Name</th>
                    <th>Unit</th>
                    <th>Value</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($catalogs as $catalog)
                    <tr>
                        <td>{{ $loop->iteration + ($catalogs->currentPage()-1)*$catalogs->perPage() }}</td>
                        <td>{{ $catalog->name }}</td>
                        <td>{{ ucfirst($catalog->unit) }}</td>
                        <td>{{ $catalog->value }}</td>
                        <td>{{ ucfirst($catalog->task_type) }}</td>
                        <td>
                            <span class="badge {{ $catalog->is_active ? 'badge-success' : 'badge-secondary' }}">
                                {{ $catalog->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('jobdesks.catalogs.edit', [$jobdesk, $catalog]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('jobdesks.catalogs.destroy', [$jobdesk, $catalog]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this task catalog?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No task catalogs yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $catalogs->links() }}
    </div>
</div>
@endsection
