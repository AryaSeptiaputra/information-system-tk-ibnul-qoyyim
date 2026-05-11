@php
    $studentOverview = collect($studentOverview ?? []);
    $attendanceWindowDays = (int)($attendanceWindowDays ?? 60);
    $announcements = collect($announcements ?? []);
    $attendanceWindows = [30, 60, 90, 180];
    $students = collect($students ?? []);
    $selectedStudentId = (int)($selectedStudentId ?? 0);
@endphp

<style>
    .guest-students {
        background: white;
        border-radius: 16px;
        padding: 22px;
        border: 2px solid var(--green-light);
        margin-bottom: 24px;
    }

    .guest-students-title {
        font-family: 'Fredoka One', cursive;
        font-size: 18px;
        color: var(--dark);
        margin: 0 0 6px;
    }

    .guest-students-subtitle {
        color: var(--gray);
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .guest-students-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
        align-items: center;
    }

        .guest-students-filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .guest-students-filter-select {
            padding: 8px 12px;
            border-radius: 10px;
            border: 2px solid var(--green-light);
            background: white;
            font-size: 12px;
            font-weight: 800;
            color: var(--dark);
        }

    .guest-students-filter-label {
        font-size: 12px;
        font-weight: 800;
        color: var(--gray);
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .guest-students-filter-chip {
        padding: 6px 12px;
        border-radius: 999px;
        border: 2px solid var(--green-light);
        background: white;
        color: var(--dark);
        font-size: 12px;
        font-weight: 900;
        text-decoration: none;
    }

    .guest-students-filter-chip.active {
        background: var(--green-light);
        color: var(--green-dark);
    }

    .guest-students-announcements {
        background: var(--bg);
        border-radius: 14px;
        padding: 16px;
        border: 2px solid var(--green-light);
        margin-bottom: 16px;
    }

    .guest-students-announcements h3 {
        font-size: 14px;
        font-weight: 900;
        color: var(--dark);
        margin: 0 0 10px;
    }

    .guest-students-announcements-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 10px;
    }

    .guest-students-announcement {
        background: white;
        border-radius: 12px;
        padding: 12px;
        border: 2px solid var(--green-light);
    }

    .guest-students-announcement-title {
        font-weight: 900;
        color: var(--dark);
        font-size: 13px;
        margin-bottom: 6px;
    }

    .guest-students-announcement-meta {
        font-size: 11px;
        color: var(--gray);
        font-weight: 700;
        margin-bottom: 6px;
        display: flex;
        justify-content: space-between;
        gap: 8px;
    }

    .guest-students-announcement-body {
        font-size: 12px;
        color: var(--dark);
        font-weight: 700;
        line-height: 1.4;
    }

    .guest-students-grid {
        display: grid;
        gap: 16px;
    }

    .guest-student-card {
        border-radius: 14px;
        padding: 16px;
        border: 2px solid var(--green-light);
        background: var(--bg);
    }

    .guest-student-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .guest-student-name {
        font-weight: 900;
        color: var(--dark);
        font-size: 15px;
    }

    .guest-student-status {
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 900;
        background: white;
        border: 2px solid var(--green-light);
        color: var(--dark);
    }

    .guest-student-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 10px;
        margin-bottom: 14px;
    }

    .guest-student-meta-item {
        background: white;
        border-radius: 10px;
        padding: 10px 12px;
        border: 1px dashed var(--green-light);
    }

    .guest-student-meta-item span {
        display: block;
        font-size: 11px;
        color: var(--gray);
        font-weight: 800;
        margin-bottom: 4px;
    }

    .guest-student-meta-item strong {
        font-size: 12px;
        color: var(--dark);
        font-weight: 900;
    }

    .guest-student-sections {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 12px;
    }

    .guest-student-section {
        background: white;
        border-radius: 12px;
        padding: 12px 14px;
        border: 2px solid var(--green-light);
    }

    .guest-student-section h4 {
        font-size: 12px;
        font-weight: 900;
        color: var(--dark);
        margin: 0 0 8px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .guest-student-attendance-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 6px;
    }

    .guest-student-attendance-item {
        background: var(--bg);
        border-radius: 8px;
        padding: 8px;
        font-size: 12px;
        font-weight: 800;
        color: var(--dark);
        border: 1px solid var(--green-light);
        display: flex;
        justify-content: space-between;
        gap: 8px;
    }

    .guest-student-list {
        display: grid;
        gap: 6px;
    }

    .guest-student-list-item {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        font-size: 12px;
    }

    .guest-student-list-item span {
        color: var(--gray);
        font-weight: 700;
    }

    .guest-student-list-item strong {
        color: var(--dark);
        font-weight: 900;
        text-align: right;
    }

    .guest-student-issues {
        color: var(--dark);
        font-size: 12px;
        font-weight: 800;
        display: grid;
        gap: 6px;
    }

    .guest-student-issue-item {
        padding: 8px 10px;
        border-radius: 8px;
        background: #fff8e1;
        border: 1px solid #ffe7a0;
    }

    .guest-student-empty {
        font-size: 12px;
        color: var(--gray);
        font-weight: 700;
    }

    @media (max-width: 600px) {
        .guest-students {
            padding: 16px;
        }

        .guest-student-sections {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="guest-students">
    <div class="guest-students-title">Data Murid & Absensi</div>
    <div class="guest-students-subtitle">Ringkasan data murid, absensi {{ $attendanceWindowDays }} hari terakhir, dan tagihan berjalan.</div>

    <div class="guest-students-filter">
        <div class="guest-students-filter-label">Rentang Absensi</div>
        @foreach($attendanceWindows as $days)
            @php
                $attendanceParams = ['attendance_window' => $days];
                if ($selectedStudentId > 0) {
                    $attendanceParams['student_id'] = $selectedStudentId;
                }
            @endphp
            <a
                href="{{ route('dashboard.students', $attendanceParams) }}"
                class="guest-students-filter-chip {{ $attendanceWindowDays === $days ? 'active' : '' }}"
            >{{ $days }} Hari</a>
        @endforeach
    </div>

    <div class="guest-students-filter">
        <div class="guest-students-filter-label">Filter Murid</div>
        <form method="GET" action="{{ route('dashboard.students') }}" class="guest-students-filter-form">
            <input type="hidden" name="attendance_window" value="{{ $attendanceWindowDays }}">
            <select name="student_id" class="guest-students-filter-select" data-student-filter>
                <option value="">Semua Murid</option>
                @foreach($students as $student)
                    <option value="{{ $student->id_student }}" @selected($selectedStudentId === (int)$student->id_student)>
                        {{ $student->name ?? '-' }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    @if($announcements->count() > 0)
        <div class="guest-students-announcements">
            <h3>Pengumuman & Info Sekolah</h3>
            <div class="guest-students-announcements-grid">
                @foreach($announcements as $announcement)
                    <div class="guest-students-announcement">
                        <div class="guest-students-announcement-title">{{ $announcement['title'] ?? '-' }}</div>
                        <div class="guest-students-announcement-meta">
                            <span>{{ $announcement['tag'] ?? 'Info' }}</span>
                            <span>{{ $announcement['date'] ?? '-' }}</span>
                        </div>
                        <div class="guest-students-announcement-body">{{ $announcement['body'] ?? '-' }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if($studentOverview->count() === 0)
        <div class="guest-student-empty">Belum ada data murid. Silakan lakukan pendaftaran.</div>
    @else
        <div class="guest-students-grid">
            @foreach($studentOverview as $overview)
                @php
                    $student = $overview['student'] ?? null;
                    $attendanceSummary = $overview['attendance_summary'] ?? ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
                    $recentAttendance = collect($overview['recent_attendance'] ?? []);
                    $pendingPayments = collect($overview['pending_payments'] ?? []);
                    $paidPayments = collect($overview['paid_payments'] ?? []);
                    $issues = collect($overview['issues'] ?? []);
                @endphp

                <div class="guest-student-card">
                    <div class="guest-student-header">
                        <div class="guest-student-name">{{ $student?->name ?? '-' }}</div>
                        <div class="guest-student-status">{{ $overview['status_label'] ?? '-' }}</div>
                    </div>

                    <div class="guest-student-meta">
                        <div class="guest-student-meta-item">
                            <span>Kelompok</span>
                            <strong>{{ $student?->group ?? '-' }}</strong>
                        </div>
                        <div class="guest-student-meta-item">
                            <span>Jenis Kelamin</span>
                            <strong>{{ $overview['gender_label'] ?? '-' }}</strong>
                        </div>
                        <div class="guest-student-meta-item">
                            <span>Tanggal Lahir</span>
                            <strong>{{ $overview['birth_date'] ?? '-' }}</strong>
                        </div>
                    </div>

                    <div class="guest-student-sections">
                        <div class="guest-student-section">
                            <h4>Ringkasan Absensi</h4>
                            <div class="guest-student-attendance-grid">
                                <div class="guest-student-attendance-item"><span>Hadir</span><strong>{{ $attendanceSummary['hadir'] ?? 0 }}</strong></div>
                                <div class="guest-student-attendance-item"><span>Izin</span><strong>{{ $attendanceSummary['izin'] ?? 0 }}</strong></div>
                                <div class="guest-student-attendance-item"><span>Sakit</span><strong>{{ $attendanceSummary['sakit'] ?? 0 }}</strong></div>
                                <div class="guest-student-attendance-item"><span>Alpa</span><strong>{{ $attendanceSummary['alpa'] ?? 0 }}</strong></div>
                            </div>
                        </div>

                        <div class="guest-student-section">
                            <h4>Absensi Terakhir</h4>
                            @if($recentAttendance->count() === 0)
                                <div class="guest-student-empty">Belum ada data absensi.</div>
                            @else
                                <div class="guest-student-list">
                                    @foreach($recentAttendance as $row)
                                        <div class="guest-student-list-item">
                                            <span>{{ $row['date'] ?? '-' }}</span>
                                            <strong>{{ $row['status_label'] ?? '-' }}</strong>
                                        </div>
                                        @if(!empty($row['information']))
                                            <div class="guest-student-empty">Catatan: {{ $row['information'] }}</div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="guest-student-section">
                            <h4>Tagihan Berjalan</h4>
                            @if($pendingPayments->count() === 0)
                                <div class="guest-student-empty">Semua tagihan sudah lunas.</div>
                            @else
                                <div class="guest-student-list">
                                    @foreach($pendingPayments as $pay)
                                        <div class="guest-student-list-item">
                                            <span>{{ $pay['payment_name'] ?? '-' }} ({{ $pay['period'] ?? '-' }})</span>
                                            <strong>{{ $pay['amount_label'] ?? '-' }} • {{ $pay['status_label'] ?? '-' }}</strong>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="guest-student-section">
                            <h4>Riwayat Tagihan Lunas</h4>
                            @if($paidPayments->count() === 0)
                                <div class="guest-student-empty">Belum ada tagihan lunas.</div>
                            @else
                                <div class="guest-student-list">
                                    @foreach($paidPayments as $pay)
                                        <div class="guest-student-list-item">
                                            <span>{{ $pay['payment_name'] ?? '-' }} ({{ $pay['period'] ?? '-' }})</span>
                                            <strong>{{ $pay['amount_label'] ?? '-' }} • {{ $pay['paid_at'] ?? '-' }}</strong>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="guest-student-section">
                            <h4>Catatan & Isu</h4>
                            @if($issues->count() === 0)
                                <div class="guest-student-empty">Tidak ada catatan khusus.</div>
                            @else
                                <div class="guest-student-issues">
                                    @foreach($issues as $issue)
                                        <div class="guest-student-issue-item">{{ $issue }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    document.querySelectorAll('[data-student-filter]').forEach(function (select) {
        select.addEventListener('change', function () {
            const form = select.closest('form');
            if (form) form.submit();
        });
    });
</script>
