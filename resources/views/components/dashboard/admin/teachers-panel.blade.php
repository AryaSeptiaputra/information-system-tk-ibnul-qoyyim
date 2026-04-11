<!-- Teachers Management Section -->
<div id="admin-teachers-panel" class="admin-section" hidden aria-hidden="true">
    <!-- Section Title -->
    <div class="admin-section-header">
        <h2>Manajemen Guru</h2>
        <p class="admin-section-subtitle">Kelola data guru dan pendidik</p>
    </div>

    <!-- Search, Filter & Actions -->
    @include('components.dashboard.admin.section-header', [
        'type' => 'teacher',
        'search' => $search ?? '',
        'status' => $status ?? 'all',
        'exportUrl' => route('admin.teachers.export'),
        'resetUrl' => route('admin.teachers.index'),
    ])

    <!-- Table -->
    @include('components.dashboard.admin.management-table', [
        'type' => 'teacher',
        'items' => $teachers ?? collect(),
    ])

    <!-- Pagination -->
    @if(($teachers ?? null) && $teachers->total() > 0)
        @include('components.dashboard.admin.pagination-controls', [
            'items' => $teachers,
            'search' => $search ?? '',
            'status' => $status ?? 'all',
            'per_page' => $per_page ?? 10,
        ])
    @endif

    <!-- Add/Edit Modal Forms -->
    @include('components.dashboard.admin.modal-form', [
        'type' => 'teacher',
        'action' => 'create',
        'teacher' => null,
        'users' => $availableUsers ?? collect(),
    ])

    @include('components.dashboard.admin.modal-form', [
        'type' => 'teacher',
        'action' => 'edit',
        'teacher' => null,
        'users' => $availableUsers ?? collect(),
    ])
</div>
