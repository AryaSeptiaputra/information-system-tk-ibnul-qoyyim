<!-- Management Table Component - Reusable -->
@php
    $isUserTable = $type === 'user';
    $isTeacherTable = $type === 'teacher';
    $isRegistrationTable = $type === 'registration';
    $isParentTable = $type === 'parent';
    $isStudentTable = $type === 'student';
    $isClassTable = $type === 'class';
    $isStudentAttendanceTable = $type === 'student-attendance';
    $isTeacherAttendanceTable = $type === 'teacher-attendance';
    $isTeacherHonorTable = $type === 'teacher-honor';
    $isFacilityTable = $type === 'facility';

    $isPaymentTable = $type === 'payment';
    $isStudentPaymentTable = $type === 'student-payment';

    $showDelete = $showDelete ?? ($isUserTable || $isTeacherTable);

    // We intentionally keep the row information minimal; full details are shown in a modal.
    $columnCount = match (true) {
        $isUserTable => 6,
        $isTeacherTable => 7,
        $isRegistrationTable => 6,
        $isParentTable => 5,
        $isStudentTable => 6,
        $isClassTable => 5,
        $isStudentAttendanceTable => 5,
        $isPaymentTable => 7,
        $isStudentPaymentTable => 7,
        $isTeacherHonorTable => 6,
        default => 4,
    };
@endphp

<div class="admin-table-wrapper admin-table-wrapper-fixed-10">
    <table class="admin-management-table {{ ($isUserTable || $isTeacherTable || $isRegistrationTable) ? 'admin-management-table-centered' : '' }} {{ $isParentTable ? 'admin-management-table-center-ends' : '' }} {{ $isStudentTable ? 'admin-management-table-center-student-cols' : '' }} {{ $isClassTable ? 'admin-management-table-class-cols' : '' }} {{ $isStudentAttendanceTable ? 'admin-management-table-student-attendance-cols' : '' }} {{ $isTeacherAttendanceTable ? 'admin-management-table-teacher-attendance-cols' : '' }} {{ $isTeacherHonorTable ? 'admin-management-table-teacher-honor-cols' : '' }} {{ $isPaymentTable ? 'admin-management-table-payment-cols' : '' }}">
        <thead class="admin-table-header">
            <tr>
                @if($isUserTable)
                    <th class="col-id">No</th>
                    <th class="col-name">Nama</th>
                    <th class="col-email">Email</th>
                    <th class="col-role">Peran</th>
                    <th class="col-status">Status</th>
                    <th class="col-actions">Aksi</th>
                @elseif($isTeacherTable)
                    <th class="col-id">No</th>
                    <th class="col-name">Nama</th>
                    <th class="col-education">Pendidikan</th>
                    <th class="col-phone">Telepon</th>
                    <th class="col-email">Email</th>
                    <th class="col-status">Status</th>
                    <th class="col-actions">Aksi</th>
                @elseif($isRegistrationTable)
                    <th class="col-id">No</th>
                    <th class="col-name">Nama Calon</th>
                    <th class="col-group">Grup</th>
                    <th class="col-deadline">Deadline</th>
                    <th class="col-status">Status</th>
                    <th class="col-actions">Aksi</th>
                @elseif($isStudentTable)
                    <th class="col-id">No</th>
                    <th class="col-name">Nama</th>
                    <th class="col-gender">Gender</th>
                    <th class="col-group">Grup</th>
                    <th class="col-status">Status</th>
                    <th class="col-actions">Aksi</th>
                @elseif($isParentTable)
                    <th class="col-id">No</th>
                    <th class="col-father">Nama Ayah</th>
                    <th class="col-mother">Nama Ibu</th>
                    <th class="col-student">Nama Siswa</th>
                    <th class="col-actions">Aksi</th>
                @elseif($isClassTable)
                    <th class="col-id">No</th>
                    <th class="col-name">Nama Kelas</th>
                    <th class="col-status">Tahun Ajaran</th>
                    <th class="col-students">Murid</th>
                    <th class="col-actions">Aksi</th>
                @elseif($isStudentAttendanceTable)
                    <th class="col-id">Tanggal</th>
                    <th class="col-name">Nama Murid</th>
                    <th class="col-class">Kelas</th>
                    <th class="col-status">Status</th>
                    <th class="col-actions">Aksi</th>
                @elseif($isTeacherAttendanceTable)
                    <th class="col-id">Tanggal</th>
                    <th class="col-name">Nama Guru</th>
                    <th class="col-status">Status</th>
                    <th class="col-actions">Aksi</th>
                @elseif($isTeacherHonorTable)
                    <th class="col-id">ID</th>
                    <th class="col-name">Guru</th>
                    <th class="col-period">Periode</th>
                    <th class="col-amount">Total Honor</th>
                    <th class="col-status">Status</th>
                    <th class="col-actions">Aksi</th>
                @elseif($isFacilityTable)
                    <th class="col-id">ID</th>
                    <th class="col-name">Fasilitas</th>
                    <th class="col-status">Status</th>
                    <th class="col-actions">Aksi</th>
                @elseif($isPaymentTable)
                    <th class="col-id">No</th>
                    <th class="col-name">Nama</th>
                    <th class="col-type">Jenis</th>
                    <th class="col-period">Mode Periode</th>
                    <th class="col-amount">Default</th>
                    <th class="col-status">Status</th>
                    <th class="col-actions">Aksi</th>
                @elseif($isStudentPaymentTable)
                    <th class="col-id">ID</th>
                    <th class="col-name">Murid</th>
                    <th class="col-type">Payment</th>
                    <th class="col-period">Periode</th>
                    <th class="col-amount">Final</th>
                    <th class="col-status">Status</th>
                    <th class="col-actions">Aksi</th>
                @else
                    <th class="col-id">ID</th>
                    <th class="col-name">Nama</th>
                    <th class="col-status">Status</th>
                    <th class="col-actions">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody class="admin-table-body">
            @forelse($items as $item)
                @php
                    $itemId = match (true) {
                        $isPaymentTable => $item->id_payment,
                        $isStudentPaymentTable => $item->id_student_payment,
                        $isStudentAttendanceTable, $isTeacherAttendanceTable => $item->id_attendance,
                        $isTeacherHonorTable => $item->id_honors,
                        $isRegistrationTable => $item->id_registration,
                        $isParentTable => $item->id_parents,
                        $isStudentTable => $item->id_student,
                        $isClassTable => $item->id_class,
                        $isTeacherTable => $item->id_teacher,
                        $isFacilityTable => $item->id,
                        default => $item->id,
                    };
                @endphp
                <tr class="admin-table-row" data-item-id="{{ $itemId }}">
                    @if($isUserTable)
                        @php
                            $baseNumber = method_exists($items, 'firstItem') ? ((int)($items->firstItem() ?? 1)) : 1;
                            $rowNumber = $baseNumber + (int)$loop->index;
                            $userStatusValue = $item->status ?? 'active';
                            $isActiveUser = $userStatusValue === 'active';
                            $userStatusLabel = $isActiveUser ? 'Aktif' : 'Nonaktif';
                            $userStatusBadgeClass = $isActiveUser ? 'admin-badge-success' : 'admin-badge-inactive';
                        @endphp
                        <td class="col-id">{{ $rowNumber }}</td>
                        <td class="col-name">
                            <span class="admin-avatar-small" title="{{ $item->name }}">{{ substr($item->name, 0, 1) }}</span>
                            {{ $item->name }}
                        </td>
                        <td class="col-email">{{ $item->email ?? '-' }}</td>
                        <td class="col-role">
                            <span class="admin-badge admin-badge-{{ str_replace('_', '-', $item->role) }}">
                                {{ ucfirst(str_replace('_', ' ', $item->role)) }}
                            </span>
                        </td>
                        <td class="col-status"><span class="admin-badge {{ $userStatusBadgeClass }}">{{ $userStatusLabel }}</span></td>
                    @elseif($isTeacherTable)
                        @php
                            $baseNumber = method_exists($items, 'firstItem') ? ((int)($items->firstItem() ?? 1)) : 1;
                            $rowNumber = $baseNumber + (int)$loop->index;
                            $teacherStatusValue = $item->status ?? 'active';
                            $isActiveTeacher = $teacherStatusValue === 'active';
                            $statusLabel = $isActiveTeacher ? 'Aktif' : 'Nonaktif';
                            $statusBadgeClass = $isActiveTeacher ? 'admin-badge-success' : 'admin-badge-inactive';

                            $teacherEducation = $item->education ?? '-';
                            $teacherPhone = $item->phone_num ?? ($item->user?->phone_num ?? '-');
                            $teacherEmail = $item->email ?? ($item->user?->email ?? '-');
                        @endphp
                        <td class="col-id">{{ $rowNumber }}</td>
                        <td class="col-name">
                            <span class="admin-avatar-small" title="{{ $item->name }}">{{ substr($item->name, 0, 1) }}</span>
                            {{ $item->name }}
                        </td>
                        <td class="col-education">{{ $teacherEducation }}</td>
                        <td class="col-phone">{{ $teacherPhone }}</td>
                        <td class="col-email">{{ $teacherEmail }}</td>
                        <td class="col-status">
                            <span class="admin-badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span>
                        </td>
                    @elseif($isRegistrationTable)
                        @php
                            $candidateName = data_get($item->candidate_data, 'name', '-');
                            $deadline = $item->payment_deadline ? $item->payment_deadline->format('Y-m-d') : '-';
                            $statusValue = $item->status ?? 'pending';
                            $statusLabel = match($statusValue) {
                                'pending' => 'Pending',
                                'approved_awaiting_payment' => 'Menunggu Pembayaran',
                                'pending_due' => 'Jatuh Tempo',
                                'active' => 'Active',
                                'rejected' => 'Rejected',
                                default => $statusValue,
                            };
                            $statusBadgeClass = match($statusValue) {
                                'active' => 'admin-badge-success',
                                'rejected' => 'admin-badge-danger',
                                'pending_due' => 'admin-badge-warning',
                                'approved_awaiting_payment' => 'admin-badge-warning',
                                default => 'admin-badge-warning',
                            };

                            $baseNumber = method_exists($items, 'firstItem') ? ((int)($items->firstItem() ?? 1)) : 1;
                            $rowNumber = $baseNumber + (int)$loop->index;
                        @endphp
                        <td class="col-id">{{ $rowNumber }}</td>
                        <td class="col-name">
                            <span class="admin-avatar-small" title="{{ $candidateName }}">{{ substr($candidateName, 0, 1) }}</span>
                            {{ $candidateName }}
                        </td>
                        <td class="col-group">{{ $item->group ?? '-' }}</td>
                        <td class="col-deadline">{{ $deadline }}</td>
                        <td class="col-status"><span class="admin-badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span></td>
                    @elseif($isStudentTable)
                        @php
                            $baseNumber = method_exists($items, 'firstItem') ? ((int)($items->firstItem() ?? 1)) : 1;
                            $rowNumber = $baseNumber + (int)$loop->index;

                            $statusValue = $item->status ?? 'pending_payment';
                            $statusLabel = match($statusValue) {
                                'aktif', 'active' => 'Aktif',
                                'non-aktif', 'inactive' => 'Nonaktif',
                                'pending_payment' => 'Belum Aktif',
                                'lulus' => 'Lulus',
                                'pindah' => 'Pindah',
                                'rejected' => 'Ditolak',
                                default => $statusValue,
                            };
                            $statusBadgeClass = match($statusValue) {
                                'aktif', 'active' => 'admin-badge-success',
                                'non-aktif', 'inactive' => 'admin-badge-inactive',
                                'rejected' => 'admin-badge-danger',
                                'lulus' => 'admin-badge-info',
                                'pindah' => 'admin-badge-warning',
                                default => 'admin-badge-warning',
                            };

                            $genderValue = $item->gender ?? null;
                            $genderLabel = match($genderValue) {
                                'pria' => 'Pria',
                                'perempuan' => 'Perempuan',
                                null => '-',
                                default => $genderValue,
                            };
                        @endphp
                        <td class="col-id">{{ $rowNumber }}</td>
                        <td class="col-name">
                            <span class="admin-avatar-small" title="{{ $item->name }}">{{ substr($item->name, 0, 1) }}</span>
                            {{ $item->name }}
                        </td>
                        <td class="col-gender">{{ $genderLabel }}</td>
                        <td class="col-group">{{ $item->group ?? '-' }}</td>
                        <td class="col-status"><span class="admin-badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span></td>
                    @elseif($isParentTable)
                        @php
                            $baseNumber = method_exists($items, 'firstItem') ? ((int)($items->firstItem() ?? 1)) : 1;
                            $rowNumber = $baseNumber + (int)$loop->index;
                            $studentNames = ($item->students ?? collect())
                                ->pluck('name')
                                ->filter()
                                ->values()
                                ->implode(', ');
                        @endphp
                        <td class="col-id">{{ $rowNumber }}</td>
                        <td class="col-father">{{ $item->father_name ?? '-' }}</td>
                        <td class="col-mother">{{ $item->mother_name ?? '-' }}</td>
                        <td class="col-student">{{ $studentNames !== '' ? $studentNames : '-' }}</td>
                    @elseif($isClassTable)
                        @php
                            $baseNumber = method_exists($items, 'firstItem') ? ((int)($items->firstItem() ?? 1)) : 1;
                            $rowNumber = $baseNumber + (int)$loop->index;
                        @endphp
                        <td class="col-id">{{ $rowNumber }}</td>
                        <td class="col-name">{{ $item->class_name ?? '-' }}</td>
                        <td class="col-status">{{ $item->school_year ?? '-' }}</td>
                        <td class="col-students">{{ (int)($item->students_count ?? 0) }}/{{ $item->max_students ?? '-' }}</td>
                    @elseif($isStudentAttendanceTable)
                        @php
                            $statusValue = $item->status ?? 'hadir';
                            $statusLabel = match($statusValue) {
                                'hadir' => 'Hadir',
                                'izin' => 'Izin',
                                'sakit' => 'Sakit',
                                'alpa' => 'Alpa',
                                default => $statusValue,
                            };
                            $statusBadgeClass = match($statusValue) {
                                'hadir' => 'admin-badge-success',
                                'izin' => 'admin-badge-info',
                                'sakit' => 'admin-badge-warning',
                                'alpa' => 'admin-badge-danger',
                                default => 'admin-badge-warning',
                            };

                            $student = $item->student;
                            $studentClassName = $student?->classes?->first()?->class_name ?? '-';
                        @endphp
                        <td class="col-id">{{ $item->date?->format('Y-m-d') ?? '-' }}</td>
                        <td class="col-name">{{ $item->student?->name ?? '-' }}</td>
                        <td class="col-class">{{ $studentClassName }}</td>
                        <td class="col-status"><span class="admin-badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span></td>
                    @elseif($isTeacherAttendanceTable)
                        @php
                            $statusValue = $item->status ?? 'hadir';
                            $statusLabel = match($statusValue) {
                                'hadir' => 'Hadir',
                                'izin' => 'Izin',
                                'sakit' => 'Sakit',
                                'alpa' => 'Alpa',
                                default => $statusValue,
                            };
                            $statusBadgeClass = match($statusValue) {
                                'hadir' => 'admin-badge-success',
                                'izin' => 'admin-badge-info',
                                'sakit' => 'admin-badge-warning',
                                'alpa' => 'admin-badge-danger',
                                default => 'admin-badge-warning',
                            };
                        @endphp
                        <td class="col-id">{{ $item->date?->format('Y-m-d') ?? '-' }}</td>
                        <td class="col-name">{{ $item->teacher?->name ?? '-' }}</td>
                        <td class="col-status"><span class="admin-badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span></td>
                    @elseif($isTeacherHonorTable)
                        @php
                            $teacherName = $item->teacher?->name ?? '-';
                            $periodLabel = sprintf('%02d/%d', (int)($item->month ?? 0), (int)($item->year ?? 0));
                            $amountLabel = 'Rp ' . number_format((float)($item->amount ?? 0), 0, ',', '.');

                            $isPaid = (bool)($item->payment_date);
                            $statusLabel = $isPaid ? 'Paid' : 'Unpaid';
                            $statusBadgeClass = $isPaid ? 'admin-badge-success' : 'admin-badge-warning';
                        @endphp
                        <td class="col-id">{{ $item->id_honors }}</td>
                        <td class="col-name">
                            <span class="admin-avatar-small" title="{{ $teacherName }}">{{ $teacherName !== '-' ? substr($teacherName, 0, 1) : '-' }}</span>
                            {{ $teacherName }}
                        </td>
                        <td class="col-period">{{ $periodLabel }}</td>
                        <td class="col-amount">{{ $amountLabel }}</td>
                        <td class="col-status"><span class="admin-badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span></td>
                    @elseif($isFacilityTable)
                        @php
                            $isActive = (bool)($item->is_active ?? true);
                            $statusLabel = $isActive ? 'Aktif' : 'Nonaktif';
                            $statusBadgeClass = $isActive ? 'admin-badge-success' : 'admin-badge-inactive';

                            $qty = (int)($item->quantity ?? 0);
                            $cond = $item->condition ?? '-';
                            $img = $item->image_path ?? null;
                        @endphp
                        <td class="col-id">{{ $item->id }}</td>
                        <td class="col-name">
                            {{ $item->name ?? '-' }}
                            <div class="form-label-hint">
                                Jumlah: {{ $qty }} • Kondisi: {{ $cond }}@if($img) • {{ $img }}@endif
                            </div>
                        </td>
                        <td class="col-status"><span class="admin-badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span></td>
                    @elseif($isPaymentTable)
                        @php
                            $baseNumber = method_exists($items, 'firstItem') ? ((int)($items->firstItem() ?? 1)) : 1;
                            $rowNumber = $baseNumber + (int)$loop->index;

                            $mode = (string)($item->period_mode ?? 'one_time');
                            $modeLabel = match($mode) {
                                'monthly' => 'Bulanan',
                                'school_year' => 'Tahun Ajaran',
                                default => 'Sekali Bayar',
                            };
                            $amountLabel = 'Rp ' . number_format((float)($item->default_amount ?? 0), 0, ',', '.');

                            $isActive = (bool)($item->is_active ?? true);
                            $statusLabel = $isActive ? 'Aktif' : 'Nonaktif';
                            $statusBadgeClass = $isActive ? 'admin-badge-success' : 'admin-badge-inactive';
                        @endphp
                        <td class="col-id">{{ $rowNumber }}</td>
                        <td class="col-name">{{ $item->name ?? '-' }}</td>
                        <td class="col-type">{{ $item->jenis_payment ?? '-' }}</td>
                        <td class="col-period">{{ $modeLabel }}</td>
                        <td class="col-amount">{{ $amountLabel }}</td>
                        <td class="col-status"><span class="admin-badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span></td>
                    @elseif($isStudentPaymentTable)
                        @php
                            $studentName = $item->student?->name ?? '-';
                            $paymentName = $item->payment?->name ?? '-';
                            $amountLabel = 'Rp ' . number_format((float)($item->final_amount ?? 0), 0, ',', '.');

                            $statusValue = $item->status ?? 'pending';
                            $statusLabel = match($statusValue) {
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                default => $statusValue,
                            };
                            $statusBadgeClass = match($statusValue) {
                                'paid' => 'admin-badge-success',
                                'failed' => 'admin-badge-danger',
                                default => 'admin-badge-warning',
                            };
                        @endphp
                        <td class="col-id">{{ $item->id_student_payment }}</td>
                        <td class="col-name">
                            <span class="admin-avatar-small" title="{{ $studentName }}">{{ $studentName !== '-' ? substr($studentName, 0, 1) : '-' }}</span>
                            {{ $studentName }}
                        </td>
                        <td class="col-type">{{ $paymentName }}</td>
                        <td class="col-period">{{ $item->payment_period ?? '-' }}</td>
                        <td class="col-amount">{{ $amountLabel }}</td>
                        <td class="col-status"><span class="admin-badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span></td>
                    @else
                        @php
                            $statusValue = $item->status ?? 'pending_payment';
                            $statusLabel = match($statusValue) {
                                'pending_payment' => 'Pending Payment',
                                'aktif' => 'Aktif',
                                'non-aktif' => 'Nonaktif',
                                'lulus' => 'Lulus',
                                'pindah' => 'Pindah',
                                'rejected' => 'Rejected',
                                default => $statusValue,
                            };
                            $statusBadgeClass = match($statusValue) {
                                'aktif' => 'admin-badge-success',
                                'non-aktif' => 'admin-badge-inactive',
                                'lulus' => 'admin-badge-info',
                                'pindah' => 'admin-badge-warning',
                                'rejected' => 'admin-badge-danger',
                                default => 'admin-badge-warning',
                            };
                        @endphp
                        <td class="col-id">{{ $item->id_student }}</td>
                        <td class="col-name">
                            <span class="admin-avatar-small" title="{{ $item->name }}">{{ substr($item->name, 0, 1) }}</span>
                            {{ $item->name }}
                        </td>
                        <td class="col-status"><span class="admin-badge {{ $statusBadgeClass }}">{{ $statusLabel }}</span></td>
                    @endif
                    <td class="col-actions">
                        <button
                            type="button"
                            class="admin-action-view-btn"
                            data-modal-open="view-{{ $type }}-modal"
                            data-item-id="{{ $itemId }}"
                        >
                            👁️ Lihat
                        </button>
                    </td>
                </tr>
            @empty
                <tr class="admin-table-empty">
                    <td colspan="{{ $columnCount }}" class="text-center">
                        <p class="text-empty">Tidak ada data ditemukan</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
