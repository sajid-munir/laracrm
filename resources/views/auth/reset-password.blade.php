@include('partials.auth-header', ['title' => 'Reset Password'])

<h4 class="mb-1">Reset Password</h4>

@if (! empty($success))
    <p class="text-muted mb-4">Your password has been reset successfully.</p>
    <a href="{{ route('login') }}" class="btn btn-theme w-100">Go to Login</a>
@elseif (! $isValid)
    <div class="alert alert-danger mt-3" role="alert">This reset link is invalid or has expired. Please request a new one.</div>
    <a href="{{ route('password.request') }}" class="btn btn-theme w-100">Request a New Link</a>
@else
    <p class="text-muted mb-4">Choose a new password for your account.</p>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
    @endif

    <form class="theme-form" method="POST" action="{{ route('password.update') }}" autocomplete="off">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label class="form-label-title mb-1" for="password">New Password</label>
            <input class="form-control" type="password" name="password" id="password" required minlength="6">
        </div>

        <div class="mb-3">
            <label class="form-label-title mb-1" for="password_confirmation">Confirm Password</label>
            <input class="form-control" type="password" name="password_confirmation" id="password_confirmation" required minlength="6">
        </div>

        <button type="submit" class="btn btn-theme w-100 mt-2">Reset Password</button>
    </form>
@endif

@include('partials.auth-footer')
