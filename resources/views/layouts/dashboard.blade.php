<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - TK Ibnul Qoyyim')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ===== DASHBOARD LAYOUT ===== */
        body.dashboard {
            display: flex;
            min-height: 100vh;
        }

        .dashboard-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
            background: var(--bg);
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background: white;
            border-right: 2px solid var(--green-light);
            box-shadow: 2px 0 8px rgba(46,204,113,0.1);
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
            transition: transform 0.3s ease;
        }

        /* MAIN CONTENT */
        .dashboard-main {
            flex: 1;
            margin-left: 260px;
            display: flex;
            flex-direction: column;
        }

        .dashboard-topbar {
            background: white;
            border-bottom: 2px solid var(--green-light);
            padding: 16px 32px;
            box-shadow: 0 2px 8px rgba(46,204,113,0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-title {
            font-family: 'Fredoka One', cursive;
            font-size: 24px;
            color: var(--dark);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-weight: 800;
            color: var(--dark);
            font-size: 14px;
        }

        .user-role {
            font-size: 12px;
            color: var(--gray);
            font-weight: 600;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--green), var(--blue));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }

        .dashboard-content {
            flex: 1;
            padding: 32px;
            overflow-y: auto;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 900px) {
            .sidebar {
                width: 220px;
            }

            .dashboard-main {
                margin-left: 220px;
            }

            .dashboard-content {
                padding: 24px;
            }

            .dashboard-topbar {
                padding: 14px 24px;
            }
        }

        @media (max-width: 600px) {
            .sidebar {
                width: 100%;
                left: -100%;
                height: 100%;
                border-right: none;
                border-bottom: 2px solid var(--green-light);
            }

            .sidebar.active {
                left: 0;
            }

            .dashboard-main {
                margin-left: 0;
            }

            .dashboard-content {
                padding: 16px;
            }

            .dashboard-topbar {
                padding: 12px 16px;
            }

            .topbar-title {
                font-size: 18px;
            }

            .user-info {
                display: none;
            }
        }

        /* SCROLLBAR */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: var(--light);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--green-light);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--green);
        }

        .dashboard-content::-webkit-scrollbar {
            width: 8px;
        }

        .dashboard-content::-webkit-scrollbar-track {
            background: var(--light);
        }

        .dashboard-content::-webkit-scrollbar-thumb {
            background: var(--green-light);
            border-radius: 4px;
        }

        .dashboard-content::-webkit-scrollbar-thumb:hover {
            background: var(--green);
        }
    </style>
</head>
<body class="dashboard">
    <div class="dashboard-wrapper">
        <!-- SIDEBAR -->
        @include('components.sidebar', [
            'userRole' => auth()->user()->role ?? 'guest',
            'currentRoute' => request()->route()?->getName() ?? ''
        ])

        <!-- MAIN CONTENT -->
        <div class="dashboard-main">
            <!-- TOPBAR -->
            <div class="dashboard-topbar">
                <div class="topbar-left">
                    <h1 class="topbar-title">@yield('page_title', 'Dashboard')</h1>
                </div>
                <div class="topbar-right">
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name ?? 'User' }}</div>
                        <div class="user-role">{{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'guest')) }}</div>
                    </div>
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                </div>
            </div>

            <!-- CONTENT -->
            <div class="dashboard-content">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        // Mobile sidebar toggle (for future use)
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                sidebar.classList.toggle('active');
            }
        }
    </script>
</body>
</html>
