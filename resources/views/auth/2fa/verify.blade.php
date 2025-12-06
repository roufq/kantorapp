@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Two-Factor Authentication</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(auth()->user()->two_factor_method === 'email')
                        <p>Please enter the 6-digit code sent to your email address.</p>
                    @elseif(auth()->user()->two_factor_method === 'sms')
                        <p>Please enter the 6-digit code sent to your phone number.</p>
                    @else
                        <p>Please enter the 6-digit code from your authenticator app.</p>
                    @endif

                    <form action="{{ route('2fa.verify.post') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="code" class="form-label">Verification Code</label>
                            <input type="text" name="code" id="code" class="form-control"
                                   placeholder="Enter 6-digit code" maxlength="6" required>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Verify</button>
                        </div>
                    </form>

                    @if(auth()->user()->two_factor_enabled && auth()->user()->two_factor_backup_codes)
                        <div class="mt-4">
                            <p class="text-muted">Lost access to your {{ auth()->user()->two_factor_method }}?</p>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#backupModal" class="btn btn-sm btn-outline-secondary">
                                Use Backup Code
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Backup Code Modal -->
@if(auth()->user()->two_factor_enabled && auth()->user()->two_factor_backup_codes)
<div class="modal fade" id="backupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Use Backup Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('2fa.verify.post') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Enter one of your backup codes:</p>
                    <input type="text" name="code" class="form-control" placeholder="Enter backup code" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Verify</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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
