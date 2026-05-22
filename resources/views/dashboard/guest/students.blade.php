@extends('layouts.dashboard')

@section('title', 'Absensi Anak - TK Ibnul Qoyyim')
@section('page_title', 'Absensi Anak')

@php
    $overview = collect($studentOverview ?? []);
    $studentsList = collect($students ?? []);
    $selectedId = (int) ($selectedStudentId ?? 0);
    $windowDays = (int) ($attendanceWindowDays ?? 60);

    $allowedWindows = [30, 60, 90, 180];

    $tabs = $studentsList->map(fn ($s) => [
        'key' => (string) $s->id_student,
        'label' => $s->name,
        'url' => route('dashboard.students', ['student_id' => $s->id_student, 'attendance_window' => $windowDays]),
    ])->all();

    // Tab "Semua" sebagai default
    array_unshift($tabs, [
        'key' => '0',
        'label' => 'Semua',
        'url' => route('dashboard.students', ['attendance_window' => $windowDays]),
    ]);
@endphp

@section('content')

<x-ui.page-header title="Absensi Anak" />

@if($overview->isEmpty())
    <x-ui.empty-state icon="📭" message="Belum ada data anak yang tertaut." />
@else
    @if(count($tabs) > 2)
        <x-ui.tab-bar :tabs="$tabs" :active="(string) $selectedId" />
    @endif

    <div class="ui-toolbar">
        <form method="GET" action="{{ route('dashboard.students') }}">
            <input type="hidden" name="student_id" value="{{ $selectedId }}">
            <label class="ui-form-label" style="display:inline-block; margin-right: var(--ui-space-sm); margin-bottom: 0;">Periode Absensi:</label>
            <select name="attendance_window" class="ui-select" onchange="this.form.submit()" style="width: auto;">
                @foreach($allowedWindows as $w)
                    <option value="{{ $w }}" @selected($windowDays === $w)>{{ $w }} hari terakhir</option>
                @endforeach
            </select>
        </form>
    </div>

    @foreach($overview as $row)
        @php
            $student = $row['student'];
            $attSum = $row['attendance_summary'] ?? ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
            $recent = $row['recent_attendance'] ?? [];
            $statusLabel = $row['student_status_label'] ?? '-';
            $genderLabel = $row['gender_label'] ?? '-';
            $statusBadge = match (strtolower($statusLabel)) {
                'aktif' => 'success',
                'ditolak', 'rejected' => 'danger',
                default => 'neutral',
            };
        @endphp

        <div class="ui-section">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: var(--ui-space-md); flex-wrap: wrap; gap: var(--ui-space-sm);">
                <div>
                    <h3 class="ui-section__title" style="margin: 0;">{{ $student->name ?? '-' }}</h3>
                    <div class="ui-stat-card__hint" style="margin-top: var(--ui-space-xs);">
                        {{ $genderLabel }} · Grup {{ $student->group ?? '-' }}
                    </div>
                </div>
                <x-ui.badge :variant="$statusBadge">{{ $statusLabel }}</x-ui.badge>
            </div>

            <div style="display: flex; flex-wrap: wrap; gap: var(--ui-space-lg); padding: var(--ui-space-md); background: var(--surface-hover); border-radius: var(--ui-radius-sm); margin-bottom: var(--ui-space-md);">
                <div><strong style="color: var(--color-primary-dark);">{{ (int) ($attSum['hadir'] ?? 0) }}</strong> Hadir</div>
                <div><strong style="color: var(--color-info);">{{ (int) ($attSum['izin'] ?? 0) }}</strong> Izin</div>
                <div><strong style="color: var(--color-warning);">{{ (int) ($attSum['sakit'] ?? 0) }}</strong> Sakit</div>
                <div><strong style="color: var(--color-danger);">{{ (int) ($attSum['alpa'] ?? 0) }}</strong> Alpa</div>
                <div style="color: var(--color-muted); margin-left: auto;">{{ $windowDays }} hari terakhir</div>
            </div>

            <h4 style="margin: 0 0 var(--ui-space-sm) 0; font-size: var(--ui-font-xs); font-weight: 600; color: var(--color-muted); text-transform: uppercase; letter-spacing: 0.5px;">Riwayat Terbaru</h4>

            @if(empty($recent))
                <x-ui.empty-state icon="📭" message="Belum ada riwayat absensi." />
            @else
                <div class="ui-table-wrapper" style="box-shadow: none; border: none;">
                    <table class="ui-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent as $att)
                                @php
                                    $vrt = match ($att['status'] ?? '') {
                                        'hadir' => 'success',
                                        'izin' => 'info',
                                        'sakit' => 'warning',
                                        'alpa' => 'danger',
                                        default => 'neutral',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $att['date'] ?? '-' }}</td>
                                    <td><x-ui.badge :variant="$vrt">{{ $att['status_label'] ?? '-' }}</x-ui.badge></td>
                                    <td style="color: var(--color-muted);">{{ $att['information'] ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endforeach
@endif

@endsection
