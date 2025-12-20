@extends('layouts.appnew')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Setup Authenticator App</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Step 1:</strong> Install an authenticator app like Google Authenticator, Authy, or Microsoft Authenticator on your phone.
                    </div>

                    <div class="alert alert-info">
                        <strong>Step 2:</strong> Scan the QR code below with your authenticator app:
                    </div>

                    <div class="text-center mb-4">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}" alt="QR Code" class="img-fluid">
                    </div>

                    <div class="alert alert-warning">
                        <strong>Can't scan the QR code?</strong> Enter this code manually: <code>{{ $qrCodeUrl }}</code>
                    </div>

                    <div class="alert alert-info">
                        <strong>Step 3:</strong> Enter the 6-digit code from your authenticator app below:
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('2fa.verify.post') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="code" class="form-label">Verification Code</label>
                            <input type="text" name="code" id="code" class="form-control"
                                   placeholder="Enter 6-digit code" maxlength="6" required>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Verify & Enable 2FA</button>
                            <a href="{{ route('2fa.setup') }}" class="btn btn-secondary">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-focus the code input
document.getElementById('code').focus();

// Auto-submit when 6 digits are entered
document.getElementById('code').addEventListener('input', function() {
    if (this.value.length === 6) {
        this.form.submit();
    }
});
</script>
@endsection
