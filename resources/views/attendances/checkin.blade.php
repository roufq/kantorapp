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
                            <input type="hidden" id="latitude" name="latitude">
                            <input type="hidden" id="longitude" name="longitude">
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <div id="locationDisplay" class="form-control" readonly>Click "Get GPS Location" to fetch your current location</div>
                                <button type="button" class="btn btn-secondary mt-1" onclick="getLocation()">Get GPS Location</button>
                            </div>
                            <button type="submit" class="btn btn-primary" id="checkinBtn" disabled>Check In</button>
                        </form>
                    @endif

                    @if($todayAttendance && !$todayAttendance->check_out_time)
                        <hr>

                        {{-- Check Out Form --}}
                        <form id="checkoutForm" method="POST" action="{{ route('attendance.checkout') }}">
                            @csrf
                            <input type="hidden" id="checkout_latitude" name="latitude">
                            <input type="hidden" id="checkout_longitude" name="longitude">
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <div id="checkoutLocationDisplay" class="form-control" readonly>Click "Get GPS Location" to fetch your current location</div>
                                <button type="button" class="btn btn-secondary mt-1" onclick="getLocation()">Get GPS Location</button>
                            </div>
                            <button type="submit" class="btn btn-warning" id="checkoutBtn" disabled>Check Out</button>
                        </form>
                    @endif

                    <script>
                        function getLocation() {
                            if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(function(position) {
                                    const lat = position.coords.latitude;
                                    const lng = position.coords.longitude;
                                    const location = lat + ', ' + lng;

                                    // For check-in form
                                    document.getElementById('latitude').value = lat;
                                    document.getElementById('longitude').value = lng;
                                    document.getElementById('locationDisplay').textContent = location;
                                    document.getElementById('checkinBtn').disabled = false;

                                    // For check-out form
                                    document.getElementById('checkout_latitude').value = lat;
                                    document.getElementById('checkout_longitude').value = lng;
                                    document.getElementById('checkoutLocationDisplay').textContent = location;
                                    document.getElementById('checkoutBtn').disabled = false;
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
