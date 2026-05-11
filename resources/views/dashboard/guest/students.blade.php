@extends('layouts.dashboard')

@section('title', 'Data Murid & Absensi')
@section('page_title', 'Data Murid & Absensi')

@section('content')
    @include('components.dashboard.guest-students', [
        'studentOverview' => $studentOverview ?? collect(),
        'attendanceWindowDays' => $attendanceWindowDays ?? 60,
        'announcements' => $announcements ?? [],
        'students' => $students ?? collect(),
        'selectedStudentId' => $selectedStudentId ?? 0,
    ])
@endsection
