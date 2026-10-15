{{--
    Top of the sign-in, forgot password and reset password pages (no sidebar).
    Usage: @include('partials.auth-header', ['title' => 'Sign In'])
--}}
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/images/logo/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.png') }}" type="image/x-icon">
    <title>CRM - {{ $title }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/crm.css') }}">
</head>

<body class="auth-page">
<div class="auth-wrapper">
    <div class="auth-logo">
        <img src="{{ asset('assets/images/logo/logo.png') }}" alt="logo">
    </div>
    <div class="card auth-card">
        <div class="card-body p-4 p-sm-5">
