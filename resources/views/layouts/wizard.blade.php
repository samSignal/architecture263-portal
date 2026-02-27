<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f6f9ff;
            color: #444444;
        }
        
        .wizard-header {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 4px 10px rgba(0,0,0,.03);
            padding: 15px 0;
            margin-bottom: 30px;
        }

        .logo-img {
            height: 50px;
            width: auto;
        }

        .wizard-title {
            color: #012970;
            font-weight: 700;
            margin-bottom: 0;
        }
        
        /* Centered main content for wizard */
        #main {
            padding: 0 20px 40px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0px 0 30px rgba(1, 41, 112, 0.1);
            margin-bottom: 30px;
            background-color: #fff;
        }
        
        .pagetitle h1 {
            font-size: 24px;
            margin-bottom: 0;
            font-weight: 600;
            color: #012970;
        }
    </style>
</head>
<body>

    <!-- Simple Header -->
    <header class="wizard-header">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ route('plan-approval.index') }}" class="d-flex align-items-center text-decoration-none">
                <img src="{{ asset('images/main_logo.png') }}" alt="IAZ Logo" class="logo-img me-3">
                <div class="d-none d-md-block">
                    <h5 class="wizard-title">Institute of Architects of Zimbabwe</h5>
                    <small class="text-muted">Plan Approval Application</small>
                </div>
            </a>
            <div>
                @if(request()->cookie('portal_token'))
                    <a href="{{ route('portal.logout') }}" class="btn btn-outline-danger btn-sm">Logout</a>
                @else
                    <a href="{{ route('portal.login') }}" class="btn btn-outline-primary btn-sm">Portal Login</a>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main id="main">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
