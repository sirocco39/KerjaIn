<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
    <link rel="icon" href="{{ asset('Image/Icon/Icon Kerjain.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <title>
        {{ __('admin/master.admin_title') }}
    </title>
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link id="pagestyle" href="{{ asset('assets/css/material-dashboard.css') }}" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


</head>


<body class="g-sidenav-show bg-gray-100">
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand px-4 py-3 m-0" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('Image/Icon/Icon Kerjain.png') }}" class="navbar-brand-img" width="26" height="26" alt="main_logo">
                <span class="ms-1 text-sm text-dark">{{ __('admin/master.kerjain_admin') }}</span>
            </a>
        </div>
        <hr class="horizontal dark mt-0 mb-2">
        <div class="collapse navbar-collapse w-auto " id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active bg-gradient-primary text-white' : 'text-dark' }}" href="{{route('admin.dashboard')}}" id="dashboard-link">
                        <i class="material-symbols-rounded opacity-5">dashboard</i>
                        <span class="nav-link-text ms-1">{{ __('admin/master.dashboard') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.verifications.*') ? 'active bg-gradient-primary text-white' : 'text-dark' }}" href="{{ route('admin.verifications.index') }}" id="verifications-link">
                        <i class="material-symbols-rounded opacity-5">table_view</i>
                        <span class="nav-link-text ms-1">{{ __('admin/master.verifications') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active bg-gradient-primary text-white' : 'text-dark' }}" href="{{ route('admin.users.index') }}" id="users-link">
                        <i class="material-symbols-rounded opacity-5">receipt_long</i>
                        <span class="nav-link-text ms-1">{{ __('admin/master.users') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active bg-gradient-primary text-white' : 'text-dark' }}" href="{{ route('admin.reports.index') }}" id="reports-link">
                        <i class="material-symbols-rounded opacity-5">view_in_ar</i>
                        <span class="nav-link-text ms-1">{{ __('admin/master.reports') }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active bg-gradient-primary text-white' : 'text-dark' }}" href="{{ route('admin.transactions.index') }}" id="transactions-link">
                        <i class="material-symbols-rounded opacity-5">format_textdirection_r_to_l</i>
                        <span class="nav-link-text ms-1">{{ __('admin/master.earnings') }}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="sidenav-footer position-absolute w-100 bottom-0 ">
            <div class="mx-3">
                <a class="btn bg-gradient-dark w-100" id="logout-btn" href="{{ route('logout') }}" type="button"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    {{ __('admin/master.logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </aside>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        {{-- Main breadcrumb item, "Admin" --}}
                        <li class="breadcrumb-item text-sm">
                            <a class="opacity-5 text-dark" href="{{ route('admin.dashboard') }}">
                                {{ $breadcrumbs['mainPageTitle'] ?? __('admin/master.admin_breadcrumb') }}
                            </a>
                        </li>
                        {{-- Current page breadcrumb item --}}
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
                            {{ $breadcrumbs['currentPageTitle'] ?? '' }}
                        </li>
                        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
                            {{ $breadcrumbs['currentSectionTitle'] ?? '' }}
                        </li>
                    </ol>
                    {{-- Current section title --}}
                    <h6 class="font-weight-bolder mb-0">{{ $breadcrumbs['currentSectionTitle'] ?? '' }}</h6>
                </nav>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                        {{-- Search bar or other nav items --}}
                    </div>
                    <ul class="navbar-nav justify-content-end">
                        <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                                <div class="sidenav-toggler-inner">
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item dropdown d-flex align-items-center">
                            <a class="nav-link text-dark d-flex align-items-center" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="material-symbols-rounded opacity-5">language</i>
                                <span class="nav-link-text ms-1">{{ __('admin/master.language') }}</span>
                            </a>

                            <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                                <li><a class="dropdown-item" href="{{ route('language.switch', 'id') }}">{{ __('admin/master.indonesia') }}</a></li>
                                <li><a class="dropdown-item" href="{{ route('language.switch', 'en') }}">{{ __('admin/master.english') }}</a></li>
                            </ul>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link text-body font-weight-bold px-0" id="logout" href="{{ route('logout') }}" type="button"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-user me-sm-1"></i>
                                <span class="d-sm-inline d-none">{{ __('admin/master.logout') }}</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        @yield('content')
        <footer class="footer pt-3 ">
            <div class="container-fluid">
                <div class="row align-items-center justify-content-lg-between">
                    <div class="col-lg-6 mb-lg-0 mb-4">
                        <div class="copyright text-center text-sm text-muted text-lg-start">
                            © <script>
                                document.write(new Date().getFullYear())
                            </script>,
                            {{ __('admin/master.proudly_powered') }} <i class="fa fa-tools"></i> {{ __('admin/master.by_kerjain') }}
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                            <li class="nav-item">
                                <a href="{{ route('admin.dashboard') }}" class="nav-link text-muted">{{ __('admin/master.dashboard') }}</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link text-muted">{{ __('admin/master.about_kerjain') }}</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link text-muted">{{ __('admin/master.support') }}</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link pe-0 text-muted">{{ __('admin/master.terms_of_service') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </footer>
    </main>
    {{-- MODAL SECTION --}}
    @yield('modal')

    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>

    <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/Chart.extension.js') }}"></script>

    <script src="{{ asset('assets/js/material-dashboard.min.js') }}"></script>


    @stack('scripts')
</body>

</html>