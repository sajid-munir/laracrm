@include('partials.auth-header', ['title' => 'Forgot Password'])

<h4 class="mb-1">Forgot Password</h4>
<p class="text-muted mb-4">Enter your email address to get a password reset link.</p>

@if ($errors->any())
    <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
@endif

@if (session('status'))
    <div class="alert alert-success" role="alert">{{ session('status') }}</div>
    <p class="text-muted mb-0">Check your inbox (and spam folder). The link expires in 1 hour.</p>
@else
    <form class="theme-form" method="POST" action="{{ route('password.email') }}" autocomplete="off">
        @csrf

        <div class="mb-3">
            <label class="form-label-title mb-1" for="email">Email Address</label>
            <input class="form-control" type="email" name="email" id="email" value="{{ old('email') }}" required autofocus>
        </div>

        <button type="submit" class="btn btn-theme w-100 mt-2">Send Reset Link</button>
    </form>
@endif

<p class="text-center mt-4 mb-0">
    <a href="{{ route('login') }}">Back to Login</a>
</p>

@include('partials.auth-footer')
