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

        /* ---- Sidebar ---- */
        .portal-sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            z-index: 1030;
            display: flex;
            flex-direction: column;
            transition: left .25s ease;
        }

        .portal-sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 18px;
            border-bottom: 1px solid #eef2f7;
            text-decoration: none;
        }

        .portal-sidebar-brand img {
            height: 42px;
            width: auto;
        }

        .portal-sidebar-brand .title {
            color: #012970;
            font-weight: 700;
            font-size: 13px;
            line-height: 1.3;
        }

        .portal-sidebar-nav {
            list-style: none;
            padding: 12px;
            margin: 0;
            flex: 1;
        }

        .portal-sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #444;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 4px;
            transition: background-color .15s ease, color .15s ease;
        }

        .portal-sidebar-nav a i {
            font-size: 18px;
        }

        .portal-sidebar-nav a:hover {
            background: #f6f9ff;
            color: #012970;
        }

        .portal-sidebar-nav a.active {
            background: #eef2ff;
            color: #012970;
            font-weight: 600;
        }

        .portal-sidebar-footer {
            padding: 14px;
            border-top: 1px solid #eef2f7;
        }

        .portal-sidebar-footer .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Mobile: sidebar hidden off-canvas until toggled */
        @media (max-width: 991px) {
            .portal-sidebar {
                left: -260px;
                box-shadow: 0 0 30px rgba(0,0,0,.15);
            }

            .sidebar-open .portal-sidebar {
                left: 0;
            }
        }

        /* ---- Topbar (mobile toggle + guest login) ---- */
        .portal-topbar {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-toggle-btn {
            font-size: 1.4rem;
            color: #374151;
            cursor: pointer;
            display: none;
        }

        @media (max-width: 991px) {
            .sidebar-toggle-btn {
                display: inline-block;
            }
        }

        /* ---- Content area ---- */
        .portal-content-wrap {
            margin-left: 260px;
            min-height: 100vh;
        }

        @media (max-width: 991px) {
            .portal-content-wrap {
                margin-left: 0;
            }
        }

        #main {
            padding: 30px 20px 40px;
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Dim overlay behind sidebar on mobile when open */
        .sidebar-backdrop {
            display: none;
        }

        @media (max-width: 991px) {
            .sidebar-open .sidebar-backdrop {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,.35);
                z-index: 1020;
            }
        }
    </style>
</head>
<body>

    @if ($portalUser)
        @php $roles = $portalUser['roles'] ?? []; @endphp

        <div class="sidebar-backdrop" onclick="document.body.classList.remove('sidebar-open')"></div>

        <aside class="portal-sidebar">
            <a href="{{ route('engagements.index') }}" class="portal-sidebar-brand">
                <img src="{{ asset('images/main_logo.png') }}" alt="IAZ Logo">
                <div class="title">
                    Institute of Architects<br>of Zimbabwe
                </div>
            </a>

            <ul class="portal-sidebar-nav">
                <li>
                    <a href="{{ route('engagements.index') }}" class="{{ request()->routeIs('engagements.*') ? 'active' : '' }}">
                        <i class="ri-file-list-3-line"></i> My Engagements
                    </a>
                </li>
                @if (in_array('client', $roles))
                    <li>
                        <a href="{{ route('architects.index') }}" class="{{ request()->routeIs('architects.*') ? 'active' : '' }}">
                            <i class="ri-search-line"></i> Search Architects
                        </a>
                    </li>
                @endif
                @if (in_array('architect', $roles) || in_array('client', $roles))
                    <li>
                        <a href="{{ route('plan-applications.index') }}" class="{{ request()->routeIs('plan-applications.*') ? 'active' : '' }}">
                            <i class="ri-file-paper-2-line"></i> Plan Applications
                        </a>
                    </li>
                @endif
                @if (in_array('council', $roles))
                    <li>
                        <a href="{{ route('council.index') }}" class="{{ request()->routeIs('council.*') ? 'active' : '' }}">
                            <i class="ri-shield-check-line"></i> Council
                        </a>
                    </li>
                @endif
            </ul>

            <div class="portal-sidebar-footer">
                <div class="d-flex align-items-center justify-content-between">
                    <span class="user-name">{{ $portalUser['name'] ?? $portalUser['username'] }}</span>
                    <a href="{{ route('portal.logout') }}" class="btn btn-outline-danger btn-sm">Logout</a>
                </div>
            </div>
        </aside>
    @endif

    <div class="portal-content-wrap">
        <div class="portal-topbar">
            <i class="ri-menu-line sidebar-toggle-btn" onclick="document.body.classList.toggle('sidebar-open')"></i>

            @unless ($portalUser)
                <a href="{{ route('engagements.index') }}" class="d-flex align-items-center text-decoration-none">
                    <img src="{{ asset('images/main_logo.png') }}" alt="IAZ Logo" style="height: 36px;" class="me-2">
                    <span class="fw-bold" style="color:#012970;">Institute of Architects of Zimbabwe</span>
                </a>
                <a href="{{ route('portal.login') }}" class="btn btn-outline-primary btn-sm">Portal Login</a>
            @endunless
        </div>

        <!-- Main Content -->
        <main id="main">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
