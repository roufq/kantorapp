@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Verify Your Email Address</div>

            <div class="card-body">
                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success" role="alert">
                        A new verification link has been sent to your email address.
                    </div>
                @endif

                <p>Thanks for signing in! Before getting started, please verify your email address by clicking on the link we just emailed to you. If you didn’t receive the email, we will gladly send you another.</p>

                <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">Resend Verification Email</button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="d-inline ms-2">
                    @csrf
                    <button type="submit" class="btn btn-link">Log Out</button>
                </form>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection

