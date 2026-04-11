@extends('layouts.dashboard')

@section('title', 'Absensi Guru - TK Ibnul Qoyyim')
@section('page_title', 'Absensi Guru')

@section('content')

<div class="admin-teacher-attendance-page">
    <div class="admin-section-header">
        <p class="admin-section-subtitle">Kelola data absensi guru</p>
    </div>

    @include('components.dashboard.admin.section-header', [
        'type' => 'teacher-attendance',
        'search' => $search ?? '',
        'status' => $status ?? 'all',
        'date_from' => $date_from ?? '',
        'date_to' => $date_to ?? '',
        'exportUrl' => route('admin.teacher-attendance.export', request()->query()),
        'resetUrl' => route('admin.teacher-attendance.index'),
    ])

    @include('components.dashboard.admin.management-table', [
        'type' => 'teacher-attendance',
        'items' => $attendances ?? collect(),
    ])

    @if($attendances)
        @include('components.dashboard.admin.pagination-controls', [
            'items' => $attendances,
            'search' => $search ?? '',
            'status' => $status ?? 'all',
            'date_from' => $date_from ?? '',
            'date_to' => $date_to ?? '',
            'per_page' => $per_page ?? 10,
        ])
    @endif
</div>

@include('components.dashboard.admin.admin-scripts')

@endsection
