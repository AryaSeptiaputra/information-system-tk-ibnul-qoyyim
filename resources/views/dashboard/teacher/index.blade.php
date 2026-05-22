@extends('layouts.dashboard')

@section('title', 'Dashboard Saya - TK Ibnul Qoyyim')
@section('page_title', 'Dashboard Saya')

@php
    $rp = static fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
    $statusLabel = ['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'];
@endphp

@section('content')

<x-ui.page-header :title="'Selamat datang, ' . ($teacher?->name ?? $user?->name ?? 'Guru')" />

@if(!$teacher)
    <x-ui.empty-state message="Akun Anda belum tertaut ke data guru. Hubungi admin." />
@else
    {{-- 3 StatCard ringkas --}}
    <div class="ui-stat-card-grid" style="margin-bottom: var(--ui-space-lg);">
        <x-ui.stat-card
            label="Honor Bulan Ini"
            :value="$honorThisMonth ? $rp($honorThisMonth->amount) : 'Belum ada'"
            :hint="$honorThisMonth ? ($honorThisMonth->payment_date ? 'Paid ' . $honorThisMonth->payment_date?->format('d M') : 'Unpaid') : 'Honor belum di-generate'"
        />
        <x-ui.stat-card
            label="Absensi Bulan Ini"
            :value="$attendanceCounts['hadir'] . ' Hadir'"
            :hint="$attendanceCounts['izin'] . ' izin · ' . $attendanceCounts['sakit'] . ' sakit · ' . $attendanceCounts['late'] . ' telat'"
        />
        <x-ui.stat-card
            label="Kelas Saya"
            :value="$classesCount . ' kelas'"
            :hint="$studentsCount . ' murid'"
        />
    </div>

    {{-- Aksi cepat --}}
    <div class="ui-section">
        <h3 class="ui-section__title">Aksi Cepat</h3>
        <div style="display: flex; gap: var(--ui-space-sm); flex-wrap: wrap;">
            <a href="{{ route('admin.my-attendance.index') }}" class="ui-btn ui-btn--primary">
                🕒 Absen Sekarang
            </a>
            <a href="{{ route('admin.my-honor.index') }}" class="ui-btn ui-btn--secondary">
                📋 Lihat Honor Saya
            </a>
            @if(Route::has('admin.teacher.students'))
                <a href="{{ route('admin.teacher.students') }}" class="ui-btn ui-btn--secondary">
                    🧒 Murid Kelas Saya
                </a>
            @endif
        </div>
    </div>

    {{-- Riwayat absensi 7 hari --}}
    <div class="ui-section">
        <h3 class="ui-section__title">Absensi Saya (7 Hari Terakhir)</h3>

        @if($recentAttendance->isEmpty())
            <x-ui.empty-state icon="📭" message="Belum ada riwayat absensi 7 hari terakhir." />
        @else
            <div class="ui-table-wrapper" style="box-shadow: none; border: none;">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Check-in</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAttendance as $row)
                            @php
                                $checkInTz = $row->check_in_time?->copy()->setTimezone($tz);
                                $statusVariant = match ($row->status) {
                                    'hadir' => 'success',
                                    'izin' => 'info',
                                    'sakit' => 'warning',
                                    'alpa' => 'danger',
                                    default => 'neutral',
                                };
                            @endphp
                            <tr>
                                <td>{{ $row->date?->translatedFormat('D, d M') ?? '-' }}</td>
                                <td><x-ui.badge :variant="$statusVariant">{{ $statusLabel[$row->status] ?? $row->status }}</x-ui.badge></td>
                                <td>
                                    {{ $checkInTz?->format('H:i') ?? '-' }}
                                    @if($row->is_late)
                                        <x-ui.badge variant="warning">Telat {{ (int) $row->late_minutes }} mnt</x-ui.badge>
                                    @endif
                                </td>
                                <td style="color: var(--color-muted);">
                                    @if($row->attachment_path)
                                        <a href="{{ asset($row->attachment_path) }}" target="_blank">Lihat bukti</a>
                                    @else
                                        {{ $row->information ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endif

@endsection
