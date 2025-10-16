@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Attendance Management') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h4>Check In / Check Out</h4>

                    @if($todayAttendance && !$todayAttendance->check_out_time)
                        <div class="alert alert-info">You checked in at {{ $todayAttendance->check_in_time->format('H:i:s') }}</div>
                    @elseif($todayAttendance && $todayAttendance->check_out_time)
                        <div class="alert alert-success">You have completed your attendance for today. Checked out at {{ $todayAttendance->check_out_time->format('H:i:s') }}</div>
                    @else
                        {{-- Check In Form --}}
                        <form id="checkinForm" method="POST" action="{{ route('attendance.checkin.post') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="location" class="form-label">Location (Optional)</label>
                                <input type="text" class="form-control" id="location" name="location" readonly>
                                <button type="button" class="btn btn-secondary mt-1" onclick="getLocation()">Get GPS Location</button>
                            </div>
                            <button type="submit" class="btn btn-primary">Check In</button>
                        </form>
                    @endif

                    @if($todayAttendance && !$todayAttendance->check_out_time)
                        <hr>

                        {{-- Check Out Form --}}
                        <form id="checkoutForm" method="POST" action="{{ route('attendance.checkout') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="checkout_location" class="form-label">Location (Optional)</label>
                                <input type="text" class="form-control" id="checkout_location" name="location" readonly>
                                <button type="button" class="btn btn-secondary mt-1" onclick="getLocation()">Get GPS Location</button>
                            </div>
                            <button type="submit" class="btn btn-warning">Check Out</button>
                        </form>
                    @endif

                    <script>
                        function getLocation() {
                            if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(function(position) {
                                    const location = position.coords.latitude + ', ' + position.coords.longitude;
                                    document.getElementById('location').value = location;
                                    document.getElementById('checkout_location').value = location;
                                }, function(error) {
                                    alert('Error getting location: ' + error.message);
                                });
                            } else {
                                alert('Geolocation is not supported by this browser.');
                            }
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
