@extends('layouts.dashboard')

@section('title', 'Data Murid - TK Ibnul Qoyyim')
@section('page_title', 'Data Murid')

@section('content')

<div class="admin-students-page">
    <div class="admin-section-header">
        <p class="admin-section-subtitle">Kelola data murid</p>
    </div>

    @include('components.dashboard.admin.section-header', [
        'type' => 'student',
        'search' => $search ?? '',
        'status' => $status ?? 'all',
        'group' => $group ?? 'all',
        'gender' => $gender ?? 'all',
        'exportUrl' => route('admin.students.export', request()->query()),
        'resetUrl' => route('admin.students.index'),
    ])

    @include('components.dashboard.admin.management-table', [
        'type' => 'student',
        'items' => $students ?? collect(),
        'showDelete' => false,
    ])

    @if($students && $students->total() > 0)
        @include('components.dashboard.admin.pagination-controls', [
            'items' => $students,
            'search' => $search ?? '',
            'status' => $status ?? 'all',
            'group' => $group ?? 'all',
            'gender' => $gender ?? 'all',
            'per_page' => $per_page ?? 10,
        ])
    @endif
</div>

@include('components.dashboard.admin.admin-scripts')

@endsection
