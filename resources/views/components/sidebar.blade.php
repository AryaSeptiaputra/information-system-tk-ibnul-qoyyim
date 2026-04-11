@php
    // ===== MENU CONFIGURATION PER ROLE =====
    // Define menu structure for each role
    $menus = [
        'superadmin' => [
            [
                'group' => 'Utama',
                'items' => [
                    ['icon' => '📊', 'label' => 'Dashboard', 'route' => route('admin.dashboard'), 'key' => 'admin.dashboard'],
                ],
            ],
            [
                'group' => 'Administrasi',
                'items' => [
                    ['icon' => '👥', 'label' => 'Manajemen Pengguna', 'route' => route('admin.users.index'), 'key' => 'admin.users.index'],
                    ['icon' => '📝', 'label' => 'Manajemen Pendaftaran', 'route' => route('admin.registrations.index'), 'key' => 'admin.registrations.index'],
                ],
            ],
            [
                'group' => 'Murid',
                'items' => [
                    ['icon' => '👨‍👩‍👧‍👦', 'label' => 'Data Orang Tua', 'route' => route('admin.parents.index'), 'key' => 'admin.parents.index'],
                    ['icon' => '🧒', 'label' => 'Data Murid', 'route' => route('admin.students.index'), 'key' => 'admin.students.index'],
                    ['icon' => '📊', 'label' => 'Data Kelas', 'route' => route('admin.classes.index'), 'key' => 'admin.classes.index'],
                    ['icon' => '📝', 'label' => 'Absensi Murid', 'route' => route('admin.student-attendance.index'), 'key' => 'admin.student-attendance.index'],
                ],
            ],
            [
                'group' => 'Guru',
                'items' => [
                    ['icon' => '👨‍🏫', 'label' => 'Manajemen Guru', 'route' => route('admin.teachers.index'), 'key' => 'admin.teachers.index'],
                    ['icon' => '📝', 'label' => 'Absensi Guru', 'route' => route('admin.teacher-attendance.index'), 'key' => 'admin.teacher-attendance.index'],
                    ['icon' => '💰', 'label' => 'Honor Guru', 'route' => route('admin.teacher-honors.index'), 'key' => 'admin.teacher-honors.index'],
                ],
            ],
            [
                'group' => 'Sarpras',
                'items' => [
                    ['icon' => '🏗️', 'label' => 'Sarpras', 'route' => route('admin.facilities.index'), 'key' => 'admin.facilities.index'],
                ],
            ],
            [
                'group' => 'Keuangan',
                'items' => [
                    ['icon' => '🧾', 'label' => 'Master Payment', 'route' => route('admin.payments.index'), 'key' => 'admin.payments.index'],
                    ['icon' => '💳', 'label' => 'Tagihan Murid', 'route' => route('admin.student-payments.index'), 'key' => 'admin.student-payments.index'],
                ],
            ],
            [
                'group' => 'Pengaturan',
                'items' => [
                    ['icon' => '⚙️', 'label' => 'Info Pembayaran', 'route' => route('admin.settings.payment-info.edit'), 'key' => 'admin.settings.payment-info.edit'],
                ],
            ],
        ],
        'headmaster' => [
            [
                'group' => 'Utama',
                'items' => [
                    ['icon' => '📊', 'label' => 'Dashboard', 'route' => route('admin.dashboard'), 'key' => 'admin.dashboard'],
                ],
            ],
            [
                'group' => 'Administrasi',
                'items' => [
                    ['icon' => '📝', 'label' => 'Pendaftaran', 'route' => route('admin.registrations.index'), 'key' => 'admin.registrations.index'],
                ],
            ],
            [
                'group' => 'Data',
                'items' => [
                    ['icon' => '👨‍👩‍👧‍👦', 'label' => 'Data Orang Tua', 'route' => route('admin.parents.index'), 'key' => 'admin.parents.index'],
                    ['icon' => '👥', 'label' => 'Data Siswa', 'route' => route('admin.students.index'), 'key' => 'admin.students.index'],
                    ['icon' => '📊', 'label' => 'Data Kelas', 'route' => route('admin.classes.index'), 'key' => 'admin.classes.index'],
                    ['icon' => '👨‍🏫', 'label' => 'Data Guru', 'route' => route('admin.teachers.index'), 'key' => 'admin.teachers.index'],
                ],
            ],
            [
                'group' => 'Akademik',
                'items' => [
                    ['icon' => '📝', 'label' => 'Absensi Murid', 'route' => route('admin.student-attendance.index'), 'key' => 'admin.student-attendance.index'],
                    ['icon' => '📝', 'label' => 'Absensi Guru', 'route' => route('admin.teacher-attendance.index'), 'key' => 'admin.teacher-attendance.index'],
                ],
            ],
            [
                'group' => 'Keuangan',
                'items' => [
                    ['icon' => '🧾', 'label' => 'Master Payment', 'route' => route('admin.payments.index'), 'key' => 'admin.payments.index'],
                    ['icon' => '💳', 'label' => 'Tagihan Murid', 'route' => route('admin.student-payments.index'), 'key' => 'admin.student-payments.index'],
                    ['icon' => '💰', 'label' => 'Honor Guru', 'route' => route('admin.teacher-honors.index'), 'key' => 'admin.teacher-honors.index'],
                ],
            ],
            [
                'group' => 'Sarpras',
                'items' => [
                    ['icon' => '🏗️', 'label' => 'Sarpras', 'route' => route('admin.facilities.index'), 'key' => 'admin.facilities.index'],
                ],
            ],
            [
                'group' => 'Pengaturan',
                'items' => [
                    ['icon' => '⚙️', 'label' => 'Info Pembayaran', 'route' => route('admin.settings.payment-info.edit'), 'key' => 'admin.settings.payment-info.edit'],
                ],
            ],
        ],
        'administration' => [
            [
                'group' => 'Utama',
                'items' => [
                    ['icon' => '📊', 'label' => 'Dashboard', 'route' => route('admin.dashboard'), 'key' => 'admin.dashboard'],
                ],
            ],
            [
                'group' => 'Administrasi',
                'items' => [
                    ['icon' => '📝', 'label' => 'Pendaftaran', 'route' => route('admin.registrations.index'), 'key' => 'admin.registrations.index'],
                ],
            ],
            [
                'group' => 'Data',
                'items' => [
                    ['icon' => '👨‍👩‍👧‍👦', 'label' => 'Data Orang Tua', 'route' => route('admin.parents.index'), 'key' => 'admin.parents.index'],
                    ['icon' => '👥', 'label' => 'Data Siswa', 'route' => route('admin.students.index'), 'key' => 'admin.students.index'],
                    ['icon' => '📊', 'label' => 'Data Kelas', 'route' => route('admin.classes.index'), 'key' => 'admin.classes.index'],
                ],
            ],
            [
                'group' => 'Akademik',
                'items' => [
                    ['icon' => '📝', 'label' => 'Absensi Murid', 'route' => route('admin.student-attendance.index'), 'key' => 'admin.student-attendance.index'],
                    ['icon' => '📝', 'label' => 'Absensi Guru', 'route' => route('admin.teacher-attendance.index'), 'key' => 'admin.teacher-attendance.index'],
                ],
            ],
            [
                'group' => 'Keuangan',
                'items' => [
                    ['icon' => '🧾', 'label' => 'Master Payment', 'route' => route('admin.payments.index'), 'key' => 'admin.payments.index'],
                    ['icon' => '💳', 'label' => 'Tagihan Murid', 'route' => route('admin.student-payments.index'), 'key' => 'admin.student-payments.index'],
                ],
            ],
            [
                'group' => 'Pengaturan',
                'items' => [
                    ['icon' => '⚙️', 'label' => 'Info Pembayaran', 'route' => route('admin.settings.payment-info.edit'), 'key' => 'admin.settings.payment-info.edit'],
                ],
            ],
        ],
        'teacher' => [
            [
                'group' => 'Utama',
                'items' => [
                    ['icon' => '📊', 'label' => 'Dashboard', 'route' => route('admin.dashboard'), 'key' => 'admin.dashboard'],
                ],
            ],
            [
                'group' => 'Akademik',
                'items' => [
                    ['icon' => '👥', 'label' => 'Data Siswa', 'route' => route('admin.students.index'), 'key' => 'admin.students.index'],
                    ['icon' => '📊', 'label' => 'Data Kelas', 'route' => route('admin.classes.index'), 'key' => 'admin.classes.index'],
                    ['icon' => '📝', 'label' => 'Absensi Murid', 'route' => route('admin.student-attendance.index'), 'key' => 'admin.student-attendance.index'],
                    ['icon' => '📝', 'label' => 'Absensi Guru', 'route' => route('admin.teacher-attendance.index'), 'key' => 'admin.teacher-attendance.index'],
                    ['icon' => '💰', 'label' => 'Honor Saya', 'route' => route('admin.my-honor.index'), 'key' => 'admin.my-honor.index'],
                ],
            ],
            [
                'group' => 'Materi',
                'items' => [
                    ['icon' => '📚', 'label' => 'Materi Ajar', 'route' => '#', 'key' => 'materials'],
                ],
            ],
        ],
        'guest' => [
            [
                'group' => 'Utama',
                'items' => [
                    ['icon' => '📊', 'label' => 'Dashboard', 'route' => route('dashboard'), 'key' => 'dashboard'],
                    ['icon' => '👨‍👩‍👧‍👦', 'label' => 'Info Murid & Orang Tua', 'route' => route('dashboard.info'), 'key' => 'dashboard.info'],
                    ['icon' => '🧾', 'label' => 'Tagihan', 'route' => route('dashboard.bills'), 'key' => 'dashboard.bills'],
                ],
            ],
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


    .sidebar-nav {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
    }

    .sidebar-menu {
        padding: 20px 12px;
        list-style: none;
    }

    .sidebar-menu-item {
        list-style: none;
        margin-bottom: 6px;
    }

    .sidebar-group-title {
        padding: 10px 16px 8px;
        margin-top: 10px;
        font-size: 11px;
        font-weight: 900;
        color: var(--gray);
        letter-spacing: 0.08em;
        text-transform: uppercase;
        border-top: 1px solid var(--green-light);
    }

    .sidebar-group-title:first-child {
        margin-top: 0;
        border-top: none;
        padding-top: 0;
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

    /* Collapsed sidebar: icons only */
    body.sidebar-collapsed .sidebar-header {
        padding: 16px 12px;
        justify-content: space-between;
    }

    body.sidebar-collapsed .sidebar-brand {
        display: none;
    }

    body.sidebar-collapsed .sidebar-menu {
        padding: 16px 10px;
    }

    body.sidebar-collapsed .sidebar-menu-link {
        justify-content: center;
        padding: 12px;
    }

    body.sidebar-collapsed .sidebar-menu-text {
        display: none;
    }

    body.sidebar-collapsed .sidebar-group-title {
        display: none;
    }

    body.sidebar-collapsed .sidebar-logout-btn {
        justify-content: center;
    }

    body.sidebar-collapsed .sidebar-logout-text {
        display: none;
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

    .sidebar-logout-form {
        margin: 0;
    }

    .sidebar-logout-icon {
        font-size: 20px;
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

        body.sidebar-collapsed .sidebar-brand {
            display: block;
        }

        body.sidebar-collapsed .sidebar-menu-text,
        body.sidebar-collapsed .sidebar-logout-text {
            display: inline;
        }

        body.sidebar-collapsed .sidebar-group-title {
            display: block;
        }

    }
</style>

<!-- SIDEBAR COMPONENT -->
<aside class="sidebar">
    <!-- SIDEBAR HEADER -->
    <header class="sidebar-header">
        <div class="sidebar-logo">🕌</div>
        <div class="sidebar-brand">
            <div class="sidebar-brand-title">TK Ibnul Qoyyim</div>
            <div class="sidebar-brand-subtitle">Sulawesi</div>
        </div>
    </header>

    <!-- SIDEBAR MENU -->
    <nav aria-label="Menu" class="sidebar-nav">
        <ul class="sidebar-menu">
            @foreach($currentMenu as $group)
                <li class="sidebar-group-title">{{ $group['group'] }}</li>
                @foreach($group['items'] as $item)
                    <li class="sidebar-menu-item">
                        <a href="{{ $item['route'] }}" class="sidebar-menu-link {{ $currentRoute === $item['key'] ? 'active' : '' }}" title="{{ $item['label'] }}">
                            <span class="sidebar-menu-icon">{{ $item['icon'] }}</span>
                            <span class="sidebar-menu-text">{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            @endforeach
        </ul>
    </nav>

    <!-- SIDEBAR FOOTER -->
    <div class="sidebar-footer">
        <form action="{{ route('logout') }}" method="POST" class="sidebar-logout-form">
            @csrf
            <button type="submit" class="sidebar-logout-btn">
                <span class="sidebar-logout-icon">🚪</span>
                <span class="sidebar-logout-text">Logout</span>
            </button>
        </form>
    </div>
</aside>
