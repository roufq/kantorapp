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
                        let currentLat, currentLng;

                        function getLocation() {
                            if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(function(position) {
                                    currentLat = position.coords.latitude;
                                    currentLng = position.coords.longitude;
                                    const location = currentLat + ', ' + currentLng;

                                    // For check-in form
                                    const latitudeInput = document.getElementById('latitude');
                                    const longitudeInput = document.getElementById('longitude');
                                    const locationDisplay = document.getElementById('locationDisplay');
                                    const checkinBtn = document.getElementById('checkinBtn');

                                    if (latitudeInput) latitudeInput.value = currentLat;
                                    if (longitudeInput) longitudeInput.value = currentLng;
                                    if (locationDisplay) locationDisplay.textContent = location;
                                    if (checkinBtn) checkinBtn.disabled = false;

                                    // For check-out form
                                    const checkoutLatitudeInput = document.getElementById('checkout_latitude');
                                    const checkoutLongitudeInput = document.getElementById('checkout_longitude');
                                    const checkoutLocationDisplay = document.getElementById('checkoutLocationDisplay');
                                    const checkoutBtn = document.getElementById('checkoutBtn');

                                    if (checkoutLatitudeInput) checkoutLatitudeInput.value = currentLat;
                                    if (checkoutLongitudeInput) checkoutLongitudeInput.value = currentLng;
                                    if (checkoutLocationDisplay) checkoutLocationDisplay.textContent = location;
                                    if (checkoutBtn) checkoutBtn.disabled = false;
                                }, function(error) {
                                    alert('Error getting location: ' + error.message);
                                });
                            } else {
                                alert('Geolocation is not supported by this browser.');
                            }
                        }

                        // Auto-get location when page loads for check-out if user is already checked in
                        @if($todayAttendance && !$todayAttendance->check_out_time)
                        window.onload = function() {
                            getLocation();
                        };
                        @endif
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
