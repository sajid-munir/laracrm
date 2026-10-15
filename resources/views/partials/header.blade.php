{{--
    Top of every signed-in page: <head>, top bar and sidebar. Opens .page-body; partials.footer closes it.
    Usage: @include('partials.header', ['title' => 'All Leads'])
--}}
@php
    $isAgent = auth()->user() instanceof \App\Models\SalesAgent;
    $displayName = $isAgent ? auth()->user()->employee_name : auth()->user()->name;
    $homeUrl = $isAgent ? route('agent.leads') : route('dashboard');
    $profileUrl = $isAgent ? route('agent.profile') : route('profile');
@endphp
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/images/logo/favicon.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('assets/images/logo/favicon.png') }}" type="image/x-icon">
    <title>{{ $isAgent ? 'Agent Panel' : 'Admin Panel' }} - {{ $title }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/vendors/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/vendors/feather-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/vendors/scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/crm.css') }}">

    {{-- Loaded here so each page can put its own <script> anywhere in the body. --}}
    <script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/crm.js') }}"></script>
</head>

<body>
<div class="page-wrapper compact-wrapper" id="pageWrapper">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-wrapper m-0">
            <div class="header-logo-wrapper p-0">
                <div class="logo-wrapper">
                    <a href="{{ $homeUrl }}">
                        <img class="img-fluid main-logo" src="{{ asset('assets/images/logo/logo.png') }}" alt="logo">
                        <img class="img-fluid white-logo" src="{{ asset('assets/images/logo/logo.png') }}" alt="logo">
                    </a>
                </div>
                <div class="toggle-sidebar">
                    <i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i>
                    <a href="{{ $homeUrl }}">
                        <img src="{{ asset('assets/images/logo/logo.png') }}" class="img-fluid" alt="">
                    </a>
                </div>
            </div>
            <div class="nav-right col-6 pull-right right-header p-0">
                <ul class="nav-menus">
                    <li>
                        <div class="mode"><i class="ri-moon-line"></i></div>
                    </li>
                    <li class="profile-nav onhover-dropdown pe-0 me-0">
                        <div class="media profile-media">
                            <img class="user-profile rounded-circle" src="{{ asset('assets/images/user.png') }}" alt="">
                            <div class="user-name-hide media-body">
                                <span>{{ $displayName }}</span>
                                <p class="mb-0 font-roboto">{{ $isAgent ? 'Agent' : 'Admin' }} <i class="middle ri-arrow-down-s-line"></i></p>
                            </div>
                        </div>
                        <ul class="profile-dropdown onhover-show-div">
                            @unless ($isAgent)
                                <li><a href="{{ route('users.index') }}"><i data-feather="users"></i><span>Users</span></a></li>
                            @endunless
                            <li><a href="{{ $profileUrl }}"><i data-feather="settings"></i><span>Settings</span></a></li>
                            <li><a data-bs-toggle="modal" data-bs-target="#logoutModal" href="javascript:void(0)"><i data-feather="log-out"></i><span>Log out</span></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="page-body-wrapper">
        <!-- Sidebar -->
        <div class="sidebar-wrapper">
            <div id="sidebarEffect"></div>
            <div>
                <div class="logo-wrapper logo-wrapper-center">
                    <a href="{{ $homeUrl }}">
                        <img class="img-fluid for-white" src="{{ asset('assets/images/logo/logo.png') }}" alt="logo">
                    </a>
                    <div class="back-btn"><i class="fa fa-angle-left"></i></div>
                    <div class="toggle-sidebar"><i class="ri-apps-line status_toggle middle sidebar-toggle"></i></div>
                </div>
                <div class="logo-icon-wrapper">
                    <a href="{{ $homeUrl }}">
                        <img class="img-fluid main-logo main-white" src="{{ asset('assets/images/logo/favicon.png') }}" alt="logo">
                        <img class="img-fluid main-logo main-dark" src="{{ asset('assets/images/logo/favicon.png') }}" alt="logo">
                    </a>
                </div>
                <nav class="sidebar-main">
                    <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
                    <div id="sidebar-menu">
                        <ul class="sidebar-links" id="simple-bar">
                            <li class="back-btn"></li>

                            @if ($isAgent)
                                <li class="sidebar-list">
                                    <a class="sidebar-link sidebar-title link-nav" href="{{ route('agent.leads') }}">
                                        <i class="ri-user-received-line"></i><span>My Leads</span>
                                    </a>
                                </li>
                            @else
                                <li class="sidebar-list">
                                    <a class="sidebar-link sidebar-title link-nav" href="{{ route('dashboard') }}">
                                        <i class="ri-home-line"></i><span>Dashboard</span>
                                    </a>
                                </li>
                                <li class="sidebar-list">
                                    <a href="{{ route('users.index') }}"><i class="ri-user-3-line"></i><span>Users</span></a>
                                </li>
                                <li class="sidebar-list">
                                    <a href="{{ route('agents.index') }}"><i class="ri-team-line"></i><span>Agents</span></a>
                                </li>
                                <li class="sidebar-list">
                                    <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                                        <i class="ri-user-received-line"></i><span>Leads</span>
                                    </a>
                                    <ul class="sidebar-submenu">
                                        <li><a href="{{ route('leads.index') }}">All Leads</a></li>
                                        <li><a href="{{ route('leads.assigned') }}">Assigned Leads</a></li>
                                        <li><a href="{{ route('leads.unassigned') }}">Unassigned Leads</a></li>
                                    </ul>
                                </li>
                            @endif

                            <li class="sidebar-list">
                                <a href="{{ $profileUrl }}"><i class="ri-settings-line"></i><span>Settings</span></a>
                            </li>
                            <li class="sidebar-list">
                                <a data-bs-toggle="modal" data-bs-target="#logoutModal" href="javascript:void(0)">
                                    <i class="ri-logout-box-line"></i><span>Log out</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
                </nav>
            </div>
        </div>

        <div class="page-body">
