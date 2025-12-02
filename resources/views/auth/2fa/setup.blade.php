@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Setup Two-Factor Authentication</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Add an extra layer of security to your account by enabling two-factor authentication.</p>

                    <form action="{{ route('2fa.setup.post') }}" method="POST">
                        @csrf
                        @php
                            $methods = $availableMethods ?? ['email', 'app'];
                        @endphp

                        <div class="mb-3">
                            <label for="method" class="form-label">Authentication Method</label>
                            <select name="method" id="method" class="form-control" required>
                                <option value="">Select a method</option>
                                @if(in_array('app', $methods))
                                    <option value="app">Authenticator App (Recommended)</option>
                                @endif
                                @if(in_array('email', $methods))
                                    <option value="email">Email</option>
                                @endif
                                @if(in_array('sms', $methods))
                                    <option value="sms">SMS</option>
                                @endif
                            </select>
                            @if(!in_array('sms', $methods))
                                <small class="text-muted">SMS is unavailable because SMS provider is not configured.</small>
                            @endif
                        </div>

                        <div class="mb-3" id="phone-field" style="display: none;">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="tel" name="phone_number" id="phone_number" class="form-control"
                                   placeholder="+1234567890">
                            <small class="form-text text-muted">Required for SMS authentication</small>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Continue</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Skip for Now</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('method').addEventListener('change', function() {
    const phoneField = document.getElementById('phone-field');
    if (this.value === 'sms') {
        phoneField.style.display = 'block';
        document.getElementById('phone_number').required = true;
    } else {
        phoneField.style.display = 'none';
        document.getElementById('phone_number').required = false;
    }
});
</script>
@endsection
