<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('admin/pilihan.title') }}</title>
    <link href="https:
    <style>
        main {
            display: flex;
            justify-content: center;
            
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .card-choice {
            margin: 5px;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
            cursor: pointer;
            width: 300x;
        }

        .card-choice:hover {
            transform: translateY(-5px);
        }

        .card-choice.requester {
            background-color: #d3fa0d;
        }

        .card-choice.taker {
            background-color: #309fff;
        }

        .card-choice.admin {
            background-color: #294287;
        }

        .card-choice h5 {
            margin-top: 15px;
            font-weight: bold;
        }

        .card-choice a {
            text-decoration: none;
            color: inherit;
        }

        .navbar-brand img {
            height: 40px;
            width: auto;
        }
    </style>
</head>


<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/job-req/home" id="logoNavbarLink">
            <img src="{{ asset('Image/Logo/Logo Kerjain - LightBackground.png') }}" alt="Logo Kerjain"
                id="logoNavbar">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger">{{ __('admin/pilihan.logout') }}</button>
        </form>
    </div>
</nav>

<body>

    <div class="container-fluid p-0">
        @if(session('custom_info_alert'))
        
        <div class="alert alert-info alert-dismissible fade show text-center w-100 rounded-0 mb-0" role="alert">
            {{ session('custom_info_alert') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="text mb-2">
                    <h1>{{ __('admin/pilihan.welcome_message_title') }}</h1>
                    <p class="lead">{{ __('admin/pilihan.welcome_message_lead') }}</p>
                </div>

                <div class="row justify-content-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-reset"> 
                        <div class="card-choice admin" style="color: white">
                            <h3><i class="bi bi-gear-fill"></i></h3>
                            <h5>{{ __('admin/pilihan.admin_page_title') }}</h5>
                            <p>{{ __('admin/pilihan.admin_page_description') }}</p>
                        </div>
                    </a>
                    <a href="{{ route('job-req.home') }}" class="text-decoration-none text-reset">
                        <div class="card-choice requester">
                            <h3><i class="bi bi-briefcase-fill"></i></h3>
                            <h5>{{ __('admin/pilihan.job_req_page_title') }}</h5>
                            <p>{{ __('admin/pilihan.job_req_page_description') }}</p>
                        </div>
                    </a>
                    <a href="{{ route('job-taker.home') }}" class="text-decoration-none text-reset"> 
                        <div class="card-choice taker" style="color: white">
                            <h3><i class="bi bi-person-workspace"></i></h3>
                            <h5>{{ __('admin/pilihan.job_taker_page_title') }}</h5>
                            <p>{{ __('admin/pilihan.job_taker_page_description') }}</p>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script src="https:
    <link rel="stylesheet" href="https:
</body>

</html>