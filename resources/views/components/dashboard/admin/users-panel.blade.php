<!-- Users Management Section -->
<div id="admin-users-panel" class="admin-section" hidden aria-hidden="true">
    <!-- Section Title -->
    <div class="admin-section-header">
        <h2>Manajemen Pengguna</h2>
        <p class="admin-section-subtitle">Kelola data pengguna sistem</p>
    </div>

    <!-- Search, Filter & Actions -->
    @include('components.dashboard.admin.section-header', [
        'type' => 'user',
        'search' => $search ?? '',
        'role' => $role ?? 'all',
        'status' => $status ?? 'all',
        'exportUrl' => route('admin.users.export'),
        'resetUrl' => route('admin.users.index'),
    ])

    <!-- Table -->
    @include('components.dashboard.admin.management-table', [
        'type' => 'user',
        'items' => $users ?? collect(),
    ])

    <!-- Pagination -->
    @if(($users ?? null) && $users->total() > 0)
        @include('components.dashboard.admin.pagination-controls', [
            'items' => $users,
            'search' => $search ?? '',
            'role' => $role ?? 'all',
            'status' => $status ?? 'all',
            'per_page' => $per_page ?? 10,
        ])
    @endif

    <!-- Add/Edit Modal Forms -->
    @include('components.dashboard.admin.modal-form', [
        'type' => 'user',
        'action' => 'create',
        'user' => null,
    ])

    @include('components.dashboard.admin.modal-form', [
        'type' => 'user',
        'action' => 'edit',
        'user' => null,
    ])
</div>
