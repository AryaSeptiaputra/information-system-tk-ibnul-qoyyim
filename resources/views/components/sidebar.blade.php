@php
    // ===== MENU CONFIGURATION PER ROLE =====
    // Define menu structure for each role
    $menus = [
        'super_admin' => [
            ['icon' => '📊', 'label' => 'Dashboard', 'route' => '#', 'key' => 'dashboard'],
            ['icon' => '📝', 'label' => 'Pendaftaran', 'route' => '#', 'key' => 'registrations'],
            ['icon' => '👥', 'label' => 'Data Siswa', 'route' => '#', 'key' => 'students'],
            ['icon' => '📚', 'label' => 'Kelas & Kurikulum', 'route' => '#', 'key' => 'classes'],
            ['icon' => '👨‍🏫', 'label' => 'Data Guru', 'route' => '#', 'key' => 'teachers'],
            ['icon' => '💰', 'label' => 'Keuangan', 'route' => '#', 'key' => 'finance'],
            ['icon' => '📊', 'label' => 'Laporan', 'route' => '#', 'key' => 'reports'],
            ['icon' => '⚙️', 'label' => 'Pengaturan', 'route' => '#', 'key' => 'settings'],
        ],
        'headmaster' => [
            ['icon' => '📊', 'label' => 'Dashboard', 'route' => '#', 'key' => 'dashboard'],
            ['icon' => '📝', 'label' => 'Pendaftaran', 'route' => '#', 'key' => 'registrations'],
            ['icon' => '👥', 'label' => 'Data Siswa', 'route' => '#', 'key' => 'students'],
            ['icon' => '👨‍🏫', 'label' => 'Data Guru', 'route' => '#', 'key' => 'teachers'],
            ['icon' => '📊', 'label' => 'Laporan', 'route' => '#', 'key' => 'reports'],
        ],
        'administration' => [
            ['icon' => '📊', 'label' => 'Dashboard', 'route' => '#', 'key' => 'dashboard'],
            ['icon' => '📝', 'label' => 'Pendaftaran', 'route' => '#', 'key' => 'registrations'],
            ['icon' => '👥', 'label' => 'Data Siswa', 'route' => '#', 'key' => 'students'],
            ['icon' => '💰', 'label' => 'Keuangan', 'route' => '#', 'key' => 'finance'],
        ],
        'teacher' => [
            ['icon' => '📊', 'label' => 'Dashboard', 'route' => '#', 'key' => 'dashboard'],
            ['icon' => '👥', 'label' => 'Data Siswa', 'route' => '#', 'key' => 'students'],
            ['icon' => '📝', 'label' => 'Nilai & Rapor', 'route' => '#', 'key' => 'grades'],
            ['icon' => '📚', 'label' => 'Materi Ajar', 'route' => '#', 'key' => 'materials'],
        ],
        'guest' => [
            ['icon' => '📊', 'label' => 'Dashboard', 'route' => '#', 'key' => 'dashboard'],
        ],
    ];

    // Get menu for current user role
    $userRole = $userRole ?? 'guest';
    $currentMenu = $menus[$userRole] ?? $menus['guest'];
@endphp

<style>
    /* ===== SIDEBAR STYLING ===== */
    .sidebar-header {
        padding: 24px 20px;
        border-bottom: 2px solid var(--green-light);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sidebar-logo {
        font-size: 32px;
    }

    .sidebar-brand {
        flex: 1;
    }

    .sidebar-brand-title {
        font-family: 'Fredoka One', cursive;
        font-size: 16px;
        color: var(--green-dark);
        line-height: 1.2;
    }

    .sidebar-brand-subtitle {
        font-size: 11px;
        color: var(--gray);
        font-weight: 700;
    }

    .sidebar-menu {
        padding: 20px 12px;
        list-style: none;
    }

    .sidebar-menu-section {
        margin-bottom: 20px;
    }

    .sidebar-menu-title {
        font-size: 11px;
        font-weight: 800;
        color: var(--gray);
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 12px 16px;
        margin-bottom: 8px;
    }

    .sidebar-menu-item {
        list-style: none;
        margin-bottom: 6px;
    }

    .sidebar-menu-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 12px;
        color: var(--dark);
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s;
    }

    .sidebar-menu-link:hover {
        background: var(--green-light);
        color: var(--green-dark);
    }

    .sidebar-menu-link.active {
        background: linear-gradient(135deg, var(--green), var(--green-dark));
        color: white;
        font-weight: 800;
        box-shadow: 0 4px 12px rgba(46,204,113,0.3);
    }

    .sidebar-menu-icon {
        font-size: 20px;
        min-width: 24px;
    }

    .sidebar-footer {
        padding: 20px 12px;
        border-top: 2px solid var(--green-light);
        margin-top: auto;
    }

    .sidebar-logout-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--green);
        background: white;
        border-radius: 12px;
        color: var(--green-dark);
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        text-align: left;
    }

    .sidebar-logout-btn:hover {
        background: var(--green-light);
    }

    .sidebar-logout-btn:active {
        transform: scale(0.98);
    }

    /* Mobile sidebar adjustments */
    @media (max-width: 600px) {
        .sidebar {
            box-shadow: -2px 0 8px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 20px 16px;
        }

        .sidebar-menu {
            padding: 16px 8px;
        }

        .sidebar-menu-link {
            padding: 10px 14px;
            font-size: 13px;
        }
    }
</style>

<!-- SIDEBAR COMPONENT -->
<aside class="sidebar">
    <!-- SIDEBAR HEADER -->
    <div class="sidebar-header">
        <div class="sidebar-logo">🕌</div>
        <div class="sidebar-brand">
            <div class="sidebar-brand-title">TK Ibnul</div>
            <div class="sidebar-brand-subtitle">Qoyyim Sulawesi</div>
        </div>
    </div>

    <!-- SIDEBAR MENU -->
    <ul class="sidebar-menu">
        @foreach($currentMenu as $item)
            <li class="sidebar-menu-item">
                <a href="{{ $item['route'] }}" class="sidebar-menu-link {{ $currentRoute === $item['key'] ? 'active' : '' }}">
                    <span class="sidebar-menu-icon">{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <!-- SIDEBAR FOOTER -->
    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="sidebar-logout-btn">
                <span style="font-size: 20px;">🚪</span>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
