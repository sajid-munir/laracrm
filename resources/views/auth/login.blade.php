@include('partials.auth-header', ['title' => 'Sign In'])

<h4 class="mb-1">Sign In</h4>
<p class="text-muted mb-4">Admin and Agent can sign in with their registered email.</p>

@if ($errors->any())
    <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
@endif

<form class="theme-form" method="POST" action="{{ route('login') }}" autocomplete="off">
    @csrf

    <div class="mb-3">
        <label class="form-label-title mb-1" for="email">Email Address</label>
        <input class="form-control" type="email" name="email" id="email" value="{{ old('email') }}" required autofocus>
    </div>

    <div class="mb-2">
        <label class="form-label-title mb-1" for="password">Password</label>
        <div class="password-field">
            <input class="form-control" type="password" name="password" id="password" required>
            <i class="ri-eye-line toggle-password" id="togglePassword"></i>
        </div>
    </div>

    <div class="forgot-password-link">
        <a href="{{ route('password.request') }}">Forgot Password?</a>
    </div>

    <button type="submit" class="btn btn-theme w-100 mt-2">Login</button>
</form>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        var input = document.getElementById('password');
        var isHidden = input.getAttribute('type') === 'password';
        input.setAttribute('type', isHidden ? 'text' : 'password');
        this.classList.toggle('ri-eye-line', !isHidden);
        this.classList.toggle('ri-eye-off-line', isHidden);
    });
</script>

@include('partials.auth-footer')
