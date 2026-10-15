@include('partials.header', ['title' => 'Profile Setting'])

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="title-header option-title">
                        <h5>Profile Info</h5>
                    </div>
                    <div class="row">
                        <div class="mb-4 col-lg-6 col-md-6 col-sm-12">
                            <label class="form-label-title mb-1">Name</label>
                            <input class="form-control" type="text" value="{{ $name }}" readonly>
                        </div>
                        <div class="mb-4 col-lg-6 col-md-6 col-sm-12">
                            <label class="form-label-title mb-1">Email</label>
                            <input class="form-control" type="text" value="{{ $email }}" readonly>
                        </div>
                    </div>

                    <hr class="my-2">

                    <div class="title-header option-title">
                        <h5>Change Password</h5>
                    </div>

                    @if (session('notice'))
                        <div class="alert alert-success">{{ session('notice') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    <form class="theme-form" method="POST" action="{{ $passwordRoute }}" autocomplete="off">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="mb-4 col-lg-6 col-md-6 col-sm-12">
                                <label class="form-label-title mb-1" for="current_password">Current Password</label>
                                <input class="form-control" type="password" name="current_password" id="current_password" required>
                            </div>
                            <div class="mb-4 col-lg-6 col-md-6 col-sm-12">
                                <label class="form-label-title mb-1" for="new_password">New Password</label>
                                <input class="form-control" type="password" name="new_password" id="new_password" required minlength="6">
                            </div>
                            <div class="mb-4 col-lg-6 col-md-6 col-sm-12">
                                <label class="form-label-title mb-1" for="new_password_confirmation">Confirm New Password</label>
                                <input class="form-control" type="password" name="new_password_confirmation" id="new_password_confirmation" required minlength="6">
                            </div>
                            <div class="mb-4 col-lg-6 col-md-6 col-sm-12">
                                <label class="form-label-title mb-1">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-md btn-primary">Update Password</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')
