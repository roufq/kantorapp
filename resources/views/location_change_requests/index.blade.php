@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Location Change Requests</h1>

    @if(auth()->user()->hasRole('Karyawan'))
    <a href="{{ route('location-change-requests.create') }}" class="btn btn-primary mb-3">Create New Request</a>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                @if(auth()->user()->hasRole('Admin Lokasi'))
                <th>Employee</th>
                @endif
                <th>Original Location</th>
                <th>Target Location</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
            <tr>
                <td>{{ $request->request_date }}</td>
                @if(auth()->user()->hasRole('Admin Lokasi'))
                <td>{{ $request->user->name }}</td>
                @endif
                <td>{{ $request->originalLocation->name }}</td>
                <td>{{ $request->targetLocation->name }}</td>
                <td>{{ $request->reason }}</td>
                <td>{{ $request->status }}</td>
                <td>
                    @if(auth()->user()->hasRole('Admin Lokasi') && $request->status === 'pending')
                    <form action="{{ route('location-change-requests.updateStatus', $request) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn btn-success btn-sm">Approve</button>
                    </form>
                    <form action="{{ route('location-change-requests.updateStatus', $request) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
