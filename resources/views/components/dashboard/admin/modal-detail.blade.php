<!-- Dynamic Detail Modal - Reusable (Read-only) -->
@php
    // Ensure optional variables exist to avoid "Undefined variable" when using nullsafe operator.
    $user = $user ?? null;
    $teacher = $teacher ?? null;
    $registration = $registration ?? null;
    $parent = $parent ?? null;
    $student = $student ?? null;
    $schoolClass = $schoolClass ?? null;
    $studentAttendance = $studentAttendance ?? null;
    $teacherAttendance = $teacherAttendance ?? null;
    $teacherHonor = $teacherHonor ?? null;
    $facility = $facility ?? null;
    $payment = $payment ?? null;
    $studentPayment = $studentPayment ?? null;

    $classStudents = $classStudents ?? collect();
    $classTeachers = $classTeachers ?? collect();
    $availableStudents = $availableStudents ?? collect();
    $availableTeachers = $availableTeachers ?? collect();

    /**
     * Supported types: user, teacher, registration, parent, student, class, payment, student-payment
     */
    $modalId = "view-{$type}-modal";
    $entity = $user ?? $teacher ?? $registration ?? $parent ?? $student ?? $schoolClass ?? $studentAttendance ?? $teacherAttendance ?? $teacherHonor ?? $facility ?? $payment ?? $studentPayment ?? null;

    $label = match($type) {
        'user' => 'Pengguna',
        'teacher' => 'Guru',
        'registration' => 'Pendaftaran',
        'parent' => 'Orang Tua',
        'student' => 'Murid',
        'class' => 'Kelas',
        'student-attendance' => 'Absensi Murid',
        'teacher-attendance' => 'Absensi Guru',
        'teacher-honor' => 'Honor Guru',
        'facility' => 'Sarana & Prasarana',
        'payment' => 'Payment',
        'student-payment' => 'Tagihan Murid',
        default => 'Data',
    };

    $title = 'Detail ' . $label;

    $detailCardCount = match($type) {
        // These types render multiple cards/blocks in the detail modal.
        'registration' => 5,
        'teacher' => 2,
        'parent' => 3,
        'student' => 1,
        'class' => 3,
        'student-attendance' => 2,
        'teacher-attendance' => 2,
        'payment' => 2,
        'student-payment' => 2,
        default => 1,
    };

    $modalWidthClass = $detailCardCount <= 1 ? ' modal-admin-compact' : '';

    $registrationCandidate = ($type === 'registration') ? ($registration?->candidate_data ?? []) : [];
    $legacyParentData = ($type === 'registration') ? ($registration?->parent_data ?? []) : [];
    if (is_string($legacyParentData)) {
        $decodedLegacyParentData = json_decode($legacyParentData, true);
        $legacyParentData = is_array($decodedLegacyParentData) ? $decodedLegacyParentData : [];
    }

    $registrationParents = ($type === 'registration')
        ? (($registration?->parents_data ?? null) ?: $legacyParentData)
        : [];

    // Backward compatibility: some flows store keys like father_phone/father_job
    $fatherPhone = ($type === 'registration')
        ? ($registrationParents['father_phone_num']
            ?? $registrationParents['father_phone']
            ?? $registrationParents['father_phone_number']
            ?? null)
        : null;
    $motherPhone = ($type === 'registration')
        ? ($registrationParents['mother_phone_num']
            ?? $registrationParents['mother_phone']
            ?? $registrationParents['mother_phone_number']
            ?? null)
        : null;
    $fatherJob = ($type === 'registration')
        ? ($registrationParents['father_occupation']
            ?? $registrationParents['father_job']
            ?? null)
        : null;
    $motherJob = ($type === 'registration')
        ? ($registrationParents['mother_occupation']
            ?? $registrationParents['mother_job']
            ?? null)
        : null;

    $registrationStatusValue = ($type === 'registration') ? ($registration?->status ?? 'pending') : null;
    $registrationStatusLabel = ($type === 'registration')
        ? match($registrationStatusValue) {
            'pending' => 'Pending',
            'approved_awaiting_payment' => 'Menunggu Pembayaran',
            'pending_due' => 'Jatuh Tempo',
            'active' => 'Active',
            'rejected' => 'Rejected',
            default => $registrationStatusValue,
        }
        : null;

    $entityId = match ($type) {
        'student-attendance' => $studentAttendance?->id_attendance,
        'teacher-attendance' => $teacherAttendance?->id_attendance,
        'teacher-honor' => $teacherHonor?->id_honors,
        'facility' => $facility?->id,
        'payment' => $payment?->id_payment,
        'student-payment' => $studentPayment?->id_student_payment,
        'class' => $schoolClass?->id_class,
        'student' => $student?->id_student,
        'parent' => $parent?->id_parents,
        'registration' => $registration?->id_registration,
        'teacher' => $teacher?->id_teacher,
        'user' => $user?->id,
        default => null,
    };

    $defaultEntityNameForDelete = $user?->name
        ?? $teacher?->name
        ?? data_get($registration?->candidate_data, 'name', null)
        ?? $parent?->father_name
        ?? $student?->name
        ?? ($teacherHonor?->teacher?->name ? ($teacherHonor->teacher->name . ' - ' . sprintf('%02d/%d', (int)($teacherHonor->month ?? 0), (int)($teacherHonor->year ?? 0))) : null)
        ?? $facility?->name
        ?? $schoolClass?->class_name
        ?? ($studentAttendance?->student?->name ? ($studentAttendance->student->name . ' - ' . ($studentAttendance->date?->format('Y-m-d') ?? '-')) : null)
        ?? ($teacherAttendance?->teacher?->name ? ($teacherAttendance->teacher->name . ' - ' . ($teacherAttendance->date?->format('Y-m-d') ?? '-')) : null)
        ?? 'data';

    $entityNameForDelete = match ($type) {
        'payment' => $payment?->name ?? $defaultEntityNameForDelete,
        'student-payment' => (($studentPayment?->student?->name)
                ? ($studentPayment->student->name . ' - ' . ($studentPayment?->payment?->name ?? '-') . ' - ' . ($studentPayment?->payment_period ?? '-'))
                : null)
            ?? $defaultEntityNameForDelete,
        default => $defaultEntityNameForDelete,
    };

    $currentRole = auth()->user()?->role ?? 'guest';

    // Role-based controls for mutating actions inside detail modals.
    $canManageFinance = in_array($currentRole, ['superadmin', 'administration'], true);
    $canManageAttendance = in_array($currentRole, ['superadmin', 'administration', 'teacher'], true);
    $canManageGeneral = in_array($currentRole, ['superadmin', 'administration'], true);

    $canManageThisType = match ($type) {
        'user' => $currentRole === 'superadmin',
        'student-attendance', 'teacher-attendance' => $canManageAttendance,
        'payment', 'student-payment', 'teacher-honor' => $canManageFinance,
        default => $canManageGeneral,
    };
@endphp

<div id="{{ $modalId }}" class="modal-overlay" hidden aria-hidden="true" data-modal-type="{{ $type }}">
    <div class="modal modal-admin{{ $modalWidthClass }}" role="dialog" aria-modal="true" aria-labelledby="modal-detail-title">
        <div class="modal-header">
            <div>
                <div class="modal-eyebrow">Informasi Lengkap</div>
                <h2 id="modal-detail-title" class="modal-title">{{ $title }}</h2>
            </div>

            <button type="button" class="modal-close" data-modal-close="close-detail" aria-label="Tutup">✕</button>
        </div>

        <div class="modal-body">
            @if($type === 'registration')
                <div class="registration-detail-row-one">
                    <div class="registration-detail-block">
                        <h3>Data Calon Murid</h3>
                        @php
                            $registrationBirthDateRaw = $registrationCandidate['birth_date'] ?? null;
                            $registrationBirthDateLabel = '-';
                            if ($registrationBirthDateRaw) {
                                if (is_string($registrationBirthDateRaw)) {
                                    $registrationBirthDateLabel = trim(explode(' ', $registrationBirthDateRaw)[0]);
                                } elseif (is_object($registrationBirthDateRaw) && method_exists($registrationBirthDateRaw, 'format')) {
                                    $registrationBirthDateLabel = $registrationBirthDateRaw->format('Y-m-d');
                                } else {
                                    $registrationBirthDateLabel = (string)$registrationBirthDateRaw;
                                }
                            }
                        @endphp
                        <div class="registration-detail-row"><span>ID Pendaftaran</span><strong>{{ $registration?->id_registration ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $registrationCandidate['name'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>TTL</span><strong>{{ $registrationCandidate['birth_place'] ?? '-' }}, {{ $registrationBirthDateLabel }}</strong></div>
                        <div class="registration-detail-row"><span>Gender</span><strong>
                            @php
                                $gender = $registrationCandidate['gender'] ?? null;
                            @endphp
                            {{ $gender === 'pria' ? 'Pria' : ($gender === 'perempuan' ? 'Perempuan' : '-') }}
                        </strong></div>
                        <div class="registration-detail-row"><span>Grup</span><strong>{{ $registration?->group ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Status</span><strong>{{ $registrationStatusLabel ?? '-' }}</strong></div>
                    </div>

                    <div class="registration-detail-block">
                        <h3>Data Ayah</h3>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $registrationParents['father_name'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>No. HP</span><strong>{{ $fatherPhone ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Pekerjaan</span><strong>{{ $fatherJob ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Alamat</span><strong>{{ $registrationParents['father_address'] ?? '-' }}</strong></div>
                    </div>

                    <div class="registration-detail-block">
                        <h3>Data Ibu</h3>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $registrationParents['mother_name'] ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>No. HP</span><strong>{{ $motherPhone ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Pekerjaan</span><strong>{{ $motherJob ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Alamat</span><strong>{{ $registrationParents['mother_address'] ?? '-' }}</strong></div>
                    </div>
                </div>

                <div class="registration-detail-divider"></div>

                <div class="registration-detail-row-two">
                    <div class="registration-detail-block">
                        <h3>Deadline & Catatan</h3>
                        <div class="registration-detail-row"><span>Reject reason</span><strong>{{ $registration?->reject_reason ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Deadline pembayaran</span><strong>{{ $registration?->payment_deadline ? $registration->payment_deadline->format('Y-m-d') : '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Masa tenggang</span><strong>{{ $registration?->grace_period_until ? $registration->grace_period_until->format('Y-m-d') : '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Bayar terlambat</span><strong>{{ ($registration?->paid_late ?? false) ? 'Ya' : 'Tidak' }}</strong></div>
                        <div class="registration-detail-row"><span>Dibuat</span><strong>{{ $registration?->created_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Diperbarui</span><strong>{{ $registration?->updated_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                    </div>

                    <div class="registration-detail-block">
                        <h3>Akun Orang Tua (User)</h3>
                        <div class="registration-detail-row"><span>ID User</span><strong>{{ $registration?->id_user ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $registration?->user?->name ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Email</span><strong>{{ $registration?->user?->email ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Telepon</span><strong>{{ $registration?->user?->phone_num ?? '-' }}</strong></div>
                    </div>
                </div>

            @elseif($type === 'user')
                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        <h3>Profil Pengguna</h3>
                        <div class="registration-detail-row"><span>ID</span><strong>{{ $user?->id ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $user?->name ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Email</span><strong>{{ $user?->email ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Email Diverifikasi</span><strong>{{ $user?->email_verified_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Telepon</span><strong>{{ $user?->phone_num ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Peran</span><strong>{{ $user?->role ? ucfirst(str_replace('_', ' ', $user->role)) : '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Status</span><strong>{{ ($user?->status ?? 'active') === 'active' ? 'Aktif' : 'Nonaktif' }}</strong></div>
                        <div class="registration-detail-row"><span>Dibuat</span><strong>{{ $user?->created_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Diperbarui</span><strong>{{ $user?->updated_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                    </div>
                </div>

            @elseif($type === 'teacher')
                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        <h3>Profil Guru</h3>
                        <div class="registration-detail-row"><span>ID Guru</span><strong>{{ $teacher?->id_teacher ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>ID User</span><strong>{{ $teacher?->id_user ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $teacher?->name ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Status</span><strong>{{ ($teacher?->status ?? 'active') === 'active' ? 'Aktif' : 'Nonaktif' }}</strong></div>
                        <div class="registration-detail-row"><span>Pendidikan</span><strong>{{ $teacher?->education ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Telepon</span><strong>{{ $teacher?->phone_num ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Email</span><strong>{{ $teacher?->email ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Dibuat</span><strong>{{ $teacher?->created_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Diperbarui</span><strong>{{ $teacher?->updated_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                    </div>

                    <div class="registration-detail-block">
                        <h3>Akun (User)</h3>
                        <div class="registration-detail-row"><span>ID User</span><strong>{{ $teacher?->id_user ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $teacher?->user?->name ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Email</span><strong>{{ $teacher?->user?->email ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Email Diverifikasi</span><strong>{{ $teacher?->user?->email_verified_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Telepon</span><strong>{{ $teacher?->user?->phone_num ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Peran</span><strong>{{ $teacher?->user?->role ? ucfirst(str_replace('_', ' ', $teacher->user->role)) : '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Status</span><strong>{{ ($teacher?->user?->status ?? 'active') === 'active' ? 'Aktif' : 'Nonaktif' }}</strong></div>
                        <div class="registration-detail-row"><span>Dibuat</span><strong>{{ $teacher?->user?->created_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Diperbarui</span><strong>{{ $teacher?->user?->updated_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                    </div>
                </div>

            @elseif($type === 'parent')
                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        <h3>Data Orang Tua</h3>
                        <div class="registration-detail-row"><span>ID</span><strong>{{ $parent?->id_parents ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nama Ayah</span><strong>{{ $parent?->father_name ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>HP Ayah</span><strong>{{ $parent?->father_phone_num ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Pekerjaan Ayah</span><strong>{{ $parent?->father_occupation ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Alamat Ayah</span><strong>{{ $parent?->father_address ?? '-' }}</strong></div>
                    </div>

                    <div class="registration-detail-block">
                        <h3>Data Ibu</h3>
                        <div class="registration-detail-row"><span>Nama Ibu</span><strong>{{ $parent?->mother_name ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>HP Ibu</span><strong>{{ $parent?->mother_phone_num ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Pekerjaan Ibu</span><strong>{{ $parent?->mother_occupation ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Alamat Ibu</span><strong>{{ $parent?->mother_address ?? '-' }}</strong></div>
                    </div>
                </div>

                <div class="registration-detail-divider"></div>

                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        <h3>Akun Orang Tua (User)</h3>
                        <div class="registration-detail-row"><span>ID User</span><strong>{{ $parent?->id_user ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $parent?->user?->name ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Email</span><strong>{{ $parent?->user?->email ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Telepon</span><strong>{{ $parent?->user?->phone_num ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Dibuat</span><strong>{{ $parent?->created_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                    </div>
                </div>

            @elseif($type === 'payment')
                @php
                    $isActive = (bool)($payment?->is_active ?? true);
                    $statusLabel = $isActive ? 'Aktif' : 'Nonaktif';

                    $periodLabel = match(($payment?->period_mode ?? 'one_time')) {
                        'one_time' => 'One Time (ONCE)',
                        'monthly' => 'Bulanan (YYYY-MM)',
                        'school_year' => 'Tahun Ajaran (YYYY/YYYY)',
                        default => (string)($payment?->period_mode ?? '-'),
                    };

                    $template = $payment?->detail_fee_template;
                    if (!is_array($template)) {
                        $template = [];
                    }
                    $templateTotal = 0;
                @endphp

                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        <h3>Data Payment</h3>
                        <div class="registration-detail-row"><span>ID</span><strong>{{ $payment?->id_payment ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $payment?->name ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Jenis</span><strong>{{ $payment?->jenis_payment ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Mode Periode</span><strong>{{ $periodLabel }}</strong></div>
                        <div class="registration-detail-row"><span>Default Amount</span><strong>Rp {{ number_format((float)($payment?->default_amount ?? 0), 0, ',', '.') }}</strong></div>
                        <div class="registration-detail-row"><span>Status</span><strong>{{ $statusLabel }}</strong></div>
                        <div class="registration-detail-row"><span>Start Date</span><strong>{{ $payment?->start_date?->format('Y-m-d') ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>End Date</span><strong>{{ $payment?->end_date?->format('Y-m-d') ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Dibuat</span><strong>{{ $payment?->created_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Diperbarui</span><strong>{{ $payment?->updated_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                    </div>

                    <div class="registration-detail-block">
                        <h3>Komponen Biaya</h3>

                        @forelse($template as $row)
                            @php
                                $label = trim((string)($row['label'] ?? 'Komponen'));
                                if ($label === '') $label = 'Komponen';
                                $qty = (int)($row['qty'] ?? 1);
                                if ($qty <= 0) $qty = 1;
                                $amount = (float)($row['amount'] ?? 0);
                                $subtotal = $amount * $qty;
                                $templateTotal += $subtotal;
                            @endphp
                            <div class="registration-detail-row">
                                <span>{{ $label }} × {{ $qty }}</span>
                                <strong>Rp {{ number_format((float)$subtotal, 0, ',', '.') }}</strong>
                            </div>
                        @empty
                            <div class="registration-detail-row"><span>Komponen</span><strong>-</strong></div>
                        @endforelse

                        @if(!empty($template))
                            <div class="registration-detail-divider"></div>
                            <div class="registration-detail-row"><span>Total Komponen</span><strong>Rp {{ number_format((float)$templateTotal, 0, ',', '.') }}</strong></div>
                        @endif
                    </div>
                </div>

            @elseif($type === 'student-payment')
                @php
                    $sp = $studentPayment;
                    $statusValue = $sp?->status ?? 'pending';
                    $statusLabel = match($statusValue) {
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        default => $statusValue,
                    };

                    $installments = $sp?->installments;
                    if (!($installments instanceof \Illuminate\Support\Collection)) {
                        $installments = collect($installments ?? []);
                    }
                    $hasInstallments = $installments->count() > 0;

                    $methodValue = $sp?->payment_method;
                    $methodLabel = match($methodValue) {
                        'transfer_bank' => 'Transfer Bank',
                        'e_wallet' => 'E-Wallet',
                        'cash' => 'Cash',
                        'qris' => 'QRIS',
                        null, '' => '-',
                        default => (string)$methodValue,
                    };

                    $snapshot = $sp?->detail_fee_snapshot;
                    if (!is_array($snapshot)) {
                        $snapshot = [];
                    }
                    $snapshotTotal = 0;

                    $headerProofs = collect($sp?->proofs ?? []);
                    $latestHeaderProof = $headerProofs->first();
                    $latestHeaderProofPath = $latestHeaderProof?->file_path ?? ($sp?->proof_file ?? null);

                    $latestMethodDetail = null;
                    if ($latestHeaderProof && ($latestHeaderProof->payment_method_label ?? null)) {
                        $extra = [];
                        $extraLabel = trim((string)($latestHeaderProof->payment_method_label ?? ''));
                        $extraAcc = trim((string)($latestHeaderProof->payment_method_account_number ?? ''));
                        $extraName = trim((string)($latestHeaderProof->payment_method_account_name ?? ''));
                        if ($extraLabel !== '') $extra[] = $extraLabel;
                        if ($extraAcc !== '') $extra[] = $extraAcc;
                        if ($extraName !== '') $extra[] = 'a.n ' . $extraName;
                        if (!empty($extra)) {
                            $latestMethodDetail = implode(' • ', $extra);
                        }
                    }
                @endphp

                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        <h3>Data Tagihan</h3>
                        <div class="registration-detail-row"><span>ID</span><strong>{{ $sp?->id_student_payment ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Murid</span><strong>{{ $sp?->student?->name ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Payment</span><strong>{{ $sp?->payment?->name ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Periode</span><strong>{{ $sp?->payment_period ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Status</span><strong>{{ $statusLabel }}</strong></div>
                        <div class="registration-detail-row"><span>Metode</span><strong>{{ $methodLabel }}</strong></div>
                        @if($latestMethodDetail)
                            <div class="registration-detail-row"><span>Detail Metode</span><strong>{{ $latestMethodDetail }}</strong></div>
                        @endif

                        <div class="registration-detail-row"><span>Total</span><strong>Rp {{ number_format((float)($sp?->total_amount ?? 0), 0, ',', '.') }}</strong></div>
                        <div class="registration-detail-row"><span>Diskon</span><strong>Rp {{ number_format((float)($sp?->discount_amount ?? 0), 0, ',', '.') }}</strong></div>
                        <div class="registration-detail-row"><span>Final</span><strong>Rp {{ number_format((float)($sp?->final_amount ?? 0), 0, ',', '.') }}</strong></div>
                        <div class="registration-detail-row"><span>Paid At</span><strong>{{ $sp?->paid_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                        <div class="registration-detail-row">
                            <span>Bukti Bayar</span>
                            <strong>
                                @if($latestHeaderProofPath)
                                    <a href="{{ asset($latestHeaderProofPath) }}" target="_blank" rel="noopener">Lihat</a>
                                @else
                                    -
                                @endif
                            </strong>
                        </div>

                        @if($headerProofs->count() > 0)
                            <div class="registration-detail-row"><span>Riwayat Bukti</span><strong>{{ $headerProofs->count() }} file</strong></div>

                            @foreach($headerProofs as $p)
                                @php
                                    $pStatus = (string)($p?->status ?? 'pending');
                                    $pStatusLabel = match($pStatus) {
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                        default => $pStatus,
                                    };
                                    $pMethod = $p?->payment_method;
                                    $pMethodLabel = match($pMethod) {
                                        'transfer_bank' => 'Transfer Bank',
                                        'e_wallet' => 'E-Wallet',
                                        'cash' => 'Cash',
                                        'qris' => 'QRIS',
                                        null, '' => '-',
                                        default => (string)$pMethod,
                                    };

                                    $pExtra = [];
                                    $pExtraLabel = trim((string)($p?->payment_method_label ?? ''));
                                    $pExtraAcc = trim((string)($p?->payment_method_account_number ?? ''));
                                    $pExtraName = trim((string)($p?->payment_method_account_name ?? ''));
                                    if ($pExtraLabel !== '') $pExtra[] = $pExtraLabel;
                                    if ($pExtraAcc !== '') $pExtra[] = $pExtraAcc;
                                    if ($pExtraName !== '') $pExtra[] = 'a.n ' . $pExtraName;
                                    $pMethodDisplay = !empty($pExtra) ? ($pMethodLabel . ' • ' . implode(' • ', $pExtra)) : $pMethodLabel;

                                    $pUploadedAt = $p?->created_at?->format('Y-m-d H:i') ?? '-';
                                    $pUploader = $p?->uploadedBy?->name ?? '-';
                                    $pUploaderLabel = $pUploader !== '-' ? ('oleh ' . $pUploader) : null;
                                @endphp

                                <div class="registration-detail-row">
                                    <span>
                                        #{{ $loop->iteration }} • {{ $pUploadedAt }} • {{ $pMethodDisplay }} • {{ $pStatusLabel }}
                                        @if($pUploaderLabel)
                                            • {{ $pUploaderLabel }}
                                        @endif
                                    </span>
                                    <strong class="admin-proof-actions">
                                        @if($p?->file_path)
                                            <a href="{{ asset($p->file_path) }}" target="_blank" rel="noopener">Lihat</a>
                                        @else
                                            <span>-</span>
                                        @endif

                                        @if($pStatus === 'pending' && $canManageFinance)
                                            <form class="admin-modal-form admin-inline-form" method="POST" action="{{ route('admin.student-payments.proofs.approve', $p) }}">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="admin-btn admin-btn-submit">Approve</button>
                                            </form>

                                            <form class="admin-modal-form admin-inline-form" method="POST" action="{{ route('admin.student-payments.proofs.reject', $p) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="admin_note" class="form-input" placeholder="Alasan penolakan" required />
                                                <button type="submit" class="admin-btn admin-btn-cancel">Reject</button>
                                            </form>
                                        @endif
                                    </strong>
                                </div>
                                @if(($p?->admin_note ?? null))
                                    <div class="registration-detail-row"><span>Catatan Admin</span><strong>{{ $p->admin_note }}</strong></div>
                                @endif
                            @endforeach
                        @endif
                        <div class="registration-detail-row"><span>Dibuat</span><strong>{{ $sp?->created_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Diperbarui</span><strong>{{ $sp?->updated_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                    </div>

                    <div class="registration-detail-block">
                        <h3>Rincian Biaya (Snapshot)</h3>
                        @forelse($snapshot as $row)
                            @php
                                $label = trim((string)($row['label'] ?? 'Komponen'));
                                if ($label === '') $label = 'Komponen';
                                $qty = (int)($row['qty'] ?? 1);
                                if ($qty <= 0) $qty = 1;
                                $amount = (float)($row['amount'] ?? 0);
                                $subtotal = $amount * $qty;
                                $snapshotTotal += $subtotal;
                            @endphp
                            <div class="registration-detail-row">
                                <span>{{ $label }} × {{ $qty }}</span>
                                <strong>Rp {{ number_format((float)$subtotal, 0, ',', '.') }}</strong>
                            </div>
                        @empty
                            <div class="registration-detail-row"><span>Komponen</span><strong>-</strong></div>
                        @endforelse

                        @if(!empty($snapshot))
                            <div class="registration-detail-divider"></div>
                            <div class="registration-detail-row"><span>Total Snapshot</span><strong>Rp {{ number_format((float)$snapshotTotal, 0, ',', '.') }}</strong></div>
                        @endif
                    </div>
                </div>

                @php
                    $installments = $sp?->installments;
                    if (!($installments instanceof \Illuminate\Support\Collection)) {
                        $installments = collect($installments ?? []);
                    }

                    $hasInstallments = $installments->count() > 0;
                    $hasPaidInstallments = $hasInstallments && $installments->contains(fn($i) => ($i->status ?? 'pending') === 'paid');
                    $allPaidInstallments = $hasInstallments && $installments->every(fn($i) => ($i->status ?? 'pending') === 'paid');
                @endphp

                <div class="registration-detail-divider"></div>

                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        <h3>Cicilan</h3>

                        @if(!$hasInstallments)
                            <div class="registration-detail-row"><span>Status Cicilan</span><strong>-</strong></div>
                            <div class="registration-detail-row"><span>Jumlah Cicilan</span><strong>{{ $sp?->installment_count ?? '-' }}</strong></div>

                            @if($canManageFinance && $statusValue !== 'paid' && (float)($sp?->final_amount ?? 0) > 0)
                                <div class="registration-detail-row">
                                    <span>Aksi</span>
                                    <strong>
                                        <button
                                            type="button"
                                            class="admin-btn admin-btn-submit"
                                            data-modal-open="custom-installment-plan"
                                            data-modal-url="{{ route('admin.student-payments.installments.create', $sp) }}"
                                        >
                                            Atur Cicilan
                                        </button>
                                    </strong>
                                </div>
                            @endif
                        @else
                            <div class="registration-detail-row"><span>Status Cicilan</span><strong>{{ $allPaidInstallments ? 'Lunas' : 'Berjalan' }}</strong></div>
                            <div class="registration-detail-row"><span>Jumlah Cicilan</span><strong>{{ $installments->count() }}</strong></div>

                            @if($canManageFinance && !$hasPaidInstallments && $statusValue !== 'paid')
                                <div class="registration-detail-row">
                                    <span>Aksi</span>
                                    <strong>
                                        <button
                                            type="button"
                                            class="admin-btn admin-btn-cancel"
                                            data-modal-open="custom-installment-reset"
                                            data-modal-url="{{ route('admin.student-payments.installments.reset.confirm', $sp) }}"
                                        >
                                            Reset Cicilan
                                        </button>
                                    </strong>
                                </div>
                            @endif

                            <div class="registration-detail-divider"></div>

                            @foreach($installments as $ins)
                                @php
                                    $insStatusValue = $ins?->status ?? 'pending';
                                    $insStatusLabel = $insStatusValue === 'paid' ? 'Paid' : 'Pending';

                                    $insProofs = collect($ins?->proofs ?? []);
                                    $latestInsProof = $insProofs->first();
                                    $latestInsProofPath = $latestInsProof?->file_path ?? ($ins?->proof_file ?? null);
                                @endphp

                                <div class="registration-detail-row">
                                    <span>Cicilan #{{ $ins?->installment_number ?? '-' }} ({{ $ins?->due_date?->format('Y-m-d') ?? '-' }}) - {{ $insStatusLabel }}</span>
                                    <strong>Rp {{ number_format((float)($ins?->installment_amount ?? 0), 0, ',', '.') }}</strong>
                                </div>
                                <div class="registration-detail-row"><span>Paid At</span><strong>{{ $ins?->paid_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                                <div class="registration-detail-row">
                                    <span>Bukti Bayar</span>
                                    <strong>
                                        @if($latestInsProofPath)
                                            <a href="{{ asset($latestInsProofPath) }}" target="_blank" rel="noopener">Lihat</a>
                                        @else
                                            -
                                        @endif
                                    </strong>
                                </div>

                                @if($insProofs->count() > 0)
                                    <div class="registration-detail-row"><span>Riwayat Bukti</span><strong>{{ $insProofs->count() }} file</strong></div>

                                    @foreach($insProofs as $p)
                                        @php
                                            $pStatus = (string)($p?->status ?? 'pending');
                                            $pStatusLabel = match($pStatus) {
                                                'pending' => 'Menunggu',
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Ditolak',
                                                default => $pStatus,
                                            };
                                            $pMethod = $p?->payment_method;
                                            $pMethodLabel = match($pMethod) {
                                                'transfer_bank' => 'Transfer Bank',
                                                'e_wallet' => 'E-Wallet',
                                                'cash' => 'Cash',
                                                'qris' => 'QRIS',
                                                null, '' => '-',
                                                default => (string)$pMethod,
                                            };

                                            $pExtra = [];
                                            $pExtraLabel = trim((string)($p?->payment_method_label ?? ''));
                                            $pExtraAcc = trim((string)($p?->payment_method_account_number ?? ''));
                                            $pExtraName = trim((string)($p?->payment_method_account_name ?? ''));
                                            if ($pExtraLabel !== '') $pExtra[] = $pExtraLabel;
                                            if ($pExtraAcc !== '') $pExtra[] = $pExtraAcc;
                                            if ($pExtraName !== '') $pExtra[] = 'a.n ' . $pExtraName;
                                            $pMethodDisplay = !empty($pExtra) ? ($pMethodLabel . ' • ' . implode(' • ', $pExtra)) : $pMethodLabel;

                                            $pUploadedAt = $p?->created_at?->format('Y-m-d H:i') ?? '-';
                                            $pUploader = $p?->uploadedBy?->name ?? '-';
                                            $pUploaderLabel = $pUploader !== '-' ? ('oleh ' . $pUploader) : null;
                                        @endphp

                                        <div class="registration-detail-row">
                                            <span>
                                                #{{ $loop->iteration }} • {{ $pUploadedAt }} • {{ $pMethodDisplay }} • {{ $pStatusLabel }}
                                                @if($pUploaderLabel)
                                                    • {{ $pUploaderLabel }}
                                                @endif
                                            </span>
                                            <strong class="admin-proof-actions">
                                                @if($p?->file_path)
                                                    <a href="{{ asset($p->file_path) }}" target="_blank" rel="noopener">Lihat</a>
                                                @else
                                                    <span>-</span>
                                                @endif

                                                @if($canManageFinance && $pStatus === 'pending' && ($ins?->status ?? 'pending') !== 'paid' && $statusValue !== 'paid')
                                                    <form class="admin-modal-form admin-inline-form" method="POST" action="{{ route('admin.student-payments.proofs.approve', $p) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="admin-btn admin-btn-submit">Approve</button>
                                                    </form>

                                                    <form class="admin-modal-form admin-inline-form" method="POST" action="{{ route('admin.student-payments.proofs.reject', $p) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="text" name="admin_note" class="form-input" placeholder="Alasan penolakan" required />
                                                        <button type="submit" class="admin-btn admin-btn-cancel">Reject</button>
                                                    </form>
                                                @endif
                                            </strong>
                                        </div>
                                        @if(($p?->admin_note ?? null))
                                            <div class="registration-detail-row"><span>Catatan Admin</span><strong>{{ $p->admin_note }}</strong></div>
                                        @endif
                                    @endforeach
                                @endif
                                <div class="registration-detail-row">
                                    <span>Aksi</span>
                                    <strong>
                                        @if($canManageFinance && ($ins?->status ?? 'pending') === 'pending' && $statusValue !== 'paid')
                                            <button
                                                type="button"
                                                class="admin-btn admin-btn-submit"
                                                data-modal-open="custom-installment-pay"
                                                data-modal-url="{{ route('admin.student-payments.installments.pay', [$sp, $ins]) }}"
                                            >
                                                Bayar
                                            </button>
                                        @else
                                            -
                                        @endif
                                    </strong>
                                </div>

                                @if(!$loop->last)
                                    <div class="registration-detail-divider"></div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>

            @elseif($type === 'student')
                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        <h3>Profil Murid</h3>
                        <div class="registration-detail-row"><span>ID</span><strong>{{ $student?->id_student ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $student?->name ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>TTL</span><strong>{{ $student?->birth_place ?? '-' }}, {{ $student?->birth_date?->format('Y-m-d') ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Gender</span><strong>{{ $student?->gender ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Grup</span><strong>{{ $student?->group ?? '-' }}</strong></div>
                        @php
                            $studentStatusValue = $student?->status ?? null;
                            $studentStatusLabel = match($studentStatusValue) {
                                'aktif', 'active' => 'Aktif',
                                'non-aktif', 'inactive' => 'Nonaktif',
                                'pending_payment' => 'Belum Aktif',
                                'lulus' => 'Lulus',
                                'pindah' => 'Pindah',
                                'rejected' => 'Ditolak',
                                null => '-',
                                default => $studentStatusValue,
                            };
                        @endphp
                        <div class="registration-detail-row"><span>Status</span><strong>{{ $studentStatusLabel }}</strong></div>
                        <div class="registration-detail-row"><span>Dibuat</span><strong>{{ $student?->created_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                    </div>
                </div>

            @elseif($type === 'class')
                <div class="registration-detail-grid registration-detail-grid-odd-last-full">
                    <div class="registration-detail-block">
                        <h3>Data Kelas</h3>
                        <div class="registration-detail-row"><span>ID</span><strong>{{ $schoolClass?->id_class ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nama Kelas</span><strong>{{ $schoolClass?->class_name ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Tahun Ajaran</span><strong>{{ $schoolClass?->school_year ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Kapasitas Maksimal</span><strong>{{ $schoolClass?->max_students ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Jumlah Murid</span><strong>{{ $schoolClass?->students_count ?? 0 }}</strong></div>
                        <div class="registration-detail-row"><span>Jumlah Guru</span><strong>{{ $schoolClass?->teachers_count ?? 0 }}</strong></div>
                        <div class="registration-detail-row"><span>Dibuat</span><strong>{{ $schoolClass?->created_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                    </div>
                </div>

                <div class="registration-detail-divider"></div>

                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        <h3>Murid di Kelas Ini</h3>

                        @if($canManageThisType)
                            <form class="admin-modal-form" method="POST" action="{{ route('admin.classes.students.attach', $schoolClass) }}">
                                @csrf
                                <div class="form-group">
                                    <label for="class-add-student" class="form-label">
                                        Tambah Murid
                                                <span class="form-label-hint">(centang lebih dari 1)</span>
                                    </label>
                                           @if($availableStudents->isEmpty())
                                               <div class="registration-detail-row"><span>Tidak ada murid tersedia</span><strong>-</strong></div>
                                           @else
                                               <div class="admin-checkbox-list" id="class-add-student">
                                                   @foreach($availableStudents as $s)
                                                       <label class="admin-checkbox-item">
                                                           <input type="checkbox" name="id_student[]" value="{{ $s->id_student }}">
                                                           <span>{{ $s->name }} (ID: {{ $s->id_student }})</span>
                                                       </label>
                                                   @endforeach
                                               </div>
                                           @endif
                                </div>
                                <button type="submit" class="admin-btn admin-btn-submit" {{ $availableStudents->isEmpty() ? 'disabled' : '' }}>Tambah</button>
                            </form>

                            <div class="registration-detail-divider"></div>
                        @endif

                        @forelse($classStudents as $s)
                            <div class="registration-detail-row">
                                <span>{{ $s->name ?? '-' }}</span>
                                <strong>
                                    @if($canManageThisType)
                                        <form class="admin-modal-form admin-inline-form" method="POST" action="{{ route('admin.classes.students.detach', [$schoolClass, $s]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-btn admin-btn-cancel">Hapus</button>
                                        </form>
                                    @else
                                        -
                                    @endif
                                </strong>
                            </div>
                        @empty
                            <div class="registration-detail-row"><span>Belum ada murid di kelas ini</span><strong>-</strong></div>
                        @endforelse
                    </div>

                    <div class="registration-detail-block">
                        <h3>Guru di Kelas Ini</h3>

                        @if($canManageThisType)
                            <form class="admin-modal-form" method="POST" action="{{ route('admin.classes.teachers.attach', $schoolClass) }}">
                                @csrf
                                <div class="form-group">
                                           <label for="class-add-teacher" class="form-label">
                                               Tambah Guru
                                               <span class="form-label-hint">(centang lebih dari 1)</span>
                                           </label>
                                           @if($availableTeachers->isEmpty())
                                               <div class="registration-detail-row"><span>Tidak ada guru tersedia</span><strong>-</strong></div>
                                           @else
                                               <div class="admin-checkbox-list" id="class-add-teacher">
                                                   @foreach($availableTeachers as $t)
                                                       <label class="admin-checkbox-item">
                                                           <input type="checkbox" name="id_teacher[]" value="{{ $t->id_teacher }}">
                                                           <span>{{ $t->name }} (ID: {{ $t->id_teacher }})</span>
                                                       </label>
                                                   @endforeach
                                               </div>
                                           @endif
                                </div>
                                <button type="submit" class="admin-btn admin-btn-submit" {{ $availableTeachers->isEmpty() ? 'disabled' : '' }}>Tambah</button>
                            </form>

                            <div class="registration-detail-divider"></div>
                        @endif

                        @forelse($classTeachers as $t)
                            <div class="registration-detail-row">
                                <span>{{ $t->name ?? '-' }}</span>
                                <strong>
                                    @if($canManageThisType)
                                        <form class="admin-modal-form admin-inline-form" method="POST" action="{{ route('admin.classes.teachers.detach', [$schoolClass, $t]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-btn admin-btn-cancel">Hapus</button>
                                        </form>
                                    @else
                                        -
                                    @endif
                                </strong>
                            </div>
                        @empty
                            <div class="registration-detail-row"><span>Belum ada guru di kelas ini</span><strong>-</strong></div>
                        @endforelse
                    </div>
                </div>

            @elseif($type === 'student-attendance')
                @php
                    $statusValue = $studentAttendance?->status ?? 'hadir';
                    $statusLabel = match($statusValue) {
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpa' => 'Alpa',
                        default => $statusValue,
                    };
                @endphp

                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        <h3>Data Absensi</h3>
                        <div class="registration-detail-row"><span>ID</span><strong>{{ $studentAttendance?->id_attendance ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Tanggal</span><strong>{{ $studentAttendance?->date?->format('Y-m-d') ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Status</span><strong>{{ $statusLabel }}</strong></div>
                        <div class="registration-detail-row"><span>Keterangan</span><strong>{{ $studentAttendance?->information ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Dibuat</span><strong>{{ $studentAttendance?->created_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                    </div>

                    <div class="registration-detail-block">
                        <h3>Data Murid</h3>
                        <div class="registration-detail-row"><span>ID Murid</span><strong>{{ $student?->id_student ?? $studentAttendance?->id_student ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $student?->name ?? '-' }}</strong></div>
                    </div>
                </div>

            @elseif($type === 'teacher-attendance')
                @php
                    $statusValue = $teacherAttendance?->status ?? 'hadir';
                    $statusLabel = match($statusValue) {
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpa' => 'Alpa',
                        default => $statusValue,
                    };

                    $teacherClassesLabel = ($teacher?->classes ?? collect())
                        ->pluck('class_name')
                        ->filter()
                        ->values()
                        ->implode(', ');
                @endphp

                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        <h3>Data Absensi</h3>
                        <div class="registration-detail-row"><span>ID</span><strong>{{ $teacherAttendance?->id_attendance ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Tanggal</span><strong>{{ $teacherAttendance?->date?->format('Y-m-d') ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Status</span><strong>{{ $statusLabel }}</strong></div>
                        <div class="registration-detail-row"><span>Keterangan</span><strong>{{ $teacherAttendance?->information ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Dibuat</span><strong>{{ $teacherAttendance?->created_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                    </div>

                    <div class="registration-detail-block">
                        <h3>Data Guru</h3>
                        <div class="registration-detail-row"><span>ID Guru</span><strong>{{ $teacher?->id_teacher ?? $teacherAttendance?->id_teacher ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $teacher?->name ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Status</span><strong>{{ ($teacher?->status ?? null) ? ucfirst($teacher->status) : '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Pendidikan</span><strong>{{ $teacher?->education ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Telepon</span><strong>{{ $teacher?->phone_num ?? ($teacher?->user?->phone_num ?? '-') }}</strong></div>
                        <div class="registration-detail-row"><span>Email</span><strong>{{ $teacher?->email ?? ($teacher?->user?->email ?? '-') }}</strong></div>
                        <div class="registration-detail-row"><span>Mengajar Kelas</span><strong>{{ $teacherClassesLabel !== '' ? $teacherClassesLabel : '-' }}</strong></div>
                    </div>
                </div>
            @elseif($type === 'teacher-honor')
                @php
                    $teacherName = $teacher?->name ?? $teacherHonor?->teacher?->name ?? '-';
                    $periodLabel = sprintf('%02d/%d', (int)($teacherHonor?->month ?? 0), (int)($teacherHonor?->year ?? 0));
                    $isPaid = (bool)($teacherHonor?->payment_date);
                    $statusLabel = $isPaid ? 'Paid' : 'Unpaid';
                @endphp

                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        <h3>Data Honor</h3>
                        <div class="registration-detail-row"><span>ID</span><strong>{{ $teacherHonor?->id_honors ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Guru</span><strong>{{ $teacherName }}</strong></div>
                        <div class="registration-detail-row"><span>Periode</span><strong>{{ $periodLabel }}</strong></div>
                        <div class="registration-detail-row"><span>Status</span><strong>{{ $statusLabel }}</strong></div>
                        <div class="registration-detail-row"><span>Tanggal Pembayaran</span><strong>{{ $teacherHonor?->payment_date?->format('Y-m-d') ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nominal</span><strong>Rp {{ number_format((float)($teacherHonor?->amount ?? 0), 0, ',', '.') }}</strong></div>
                        <div class="registration-detail-row"><span>Hadir</span><strong>{{ (int)($teacherHonor?->attendance_count ?? 0) }}</strong></div>
                        <div class="registration-detail-row"><span>Izin</span><strong>{{ (int)($teacherHonor?->permission_count ?? 0) }}</strong></div>
                        <div class="registration-detail-row"><span>Sakit</span><strong>{{ (int)($teacherHonor?->sickness_count ?? 0) }}</strong></div>
                        <div class="registration-detail-row"><span>Alpa</span><strong>{{ (int)($teacherHonor?->absence_count ?? 0) }}</strong></div>
                        <div class="registration-detail-row"><span>Dibuat</span><strong>{{ $teacherHonor?->created_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                    </div>
                </div>

            @elseif($type === 'facility')
                @php
                    $isActive = (bool)($facility?->is_active ?? true);
                    $statusLabel = $isActive ? 'Aktif' : 'Nonaktif';
                @endphp

                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        <h3>Data Fasilitas</h3>
                        <div class="registration-detail-row"><span>ID</span><strong>{{ $facility?->id ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Nama</span><strong>{{ $facility?->name ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Jumlah</span><strong>{{ (int)($facility?->quantity ?? 0) }}</strong></div>
                        <div class="registration-detail-row"><span>Kondisi</span><strong>{{ $facility?->condition ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Loc Path Gambar</span><strong>{{ $facility?->image_path ?? '-' }}</strong></div>
                        <div class="registration-detail-row"><span>Status</span><strong>{{ $statusLabel }}</strong></div>
                        <div class="registration-detail-row"><span>Dibuat</span><strong>{{ $facility?->created_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                        @if($facility?->description)
                            <div class="registration-detail-row"><span>Deskripsi</span><strong>{{ $facility->description }}</strong></div>
                        @endif
                    </div>
                </div>

            @else
                <div class="registration-detail-grid">
                    <div class="registration-detail-block">
                        @if($type === 'user')
                            @php
                                $fallbackUser = $user ?? $entity;
                            @endphp
                            <h3>Profil Pengguna</h3>
                            <div class="registration-detail-row"><span>ID</span><strong>{{ $fallbackUser?->id ?? '-' }}</strong></div>
                            <div class="registration-detail-row"><span>Nama</span><strong>{{ $fallbackUser?->name ?? '-' }}</strong></div>
                            <div class="registration-detail-row"><span>Email</span><strong>{{ $fallbackUser?->email ?? '-' }}</strong></div>
                            <div class="registration-detail-row"><span>Email Diverifikasi</span><strong>{{ $fallbackUser?->email_verified_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                            <div class="registration-detail-row"><span>Telepon</span><strong>{{ $fallbackUser?->phone_num ?? '-' }}</strong></div>
                            <div class="registration-detail-row"><span>Peran</span><strong>{{ $fallbackUser?->role ? ucfirst(str_replace('_', ' ', $fallbackUser->role)) : '-' }}</strong></div>
                            <div class="registration-detail-row"><span>Status</span><strong>{{ ($fallbackUser?->status ?? 'active') === 'active' ? 'Aktif' : 'Nonaktif' }}</strong></div>
                            <div class="registration-detail-row"><span>Dibuat</span><strong>{{ $fallbackUser?->created_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                            <div class="registration-detail-row"><span>Diperbarui</span><strong>{{ $fallbackUser?->updated_at?->format('Y-m-d H:i') ?? '-' }}</strong></div>
                        @else
                            <h3>Data</h3>
                            <div class="registration-detail-row"><span>ID</span><strong>{{ $entity?->id ?? '-' }}</strong></div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="modal-footer">
            @if($type === 'student-payment' && $studentPayment && $canManageFinance)
                @php
                    $spStatusValueFooter = (string)($studentPayment->status ?? 'pending');
                    $spInstallmentsFooter = collect($studentPayment->installments ?? []);
                    $spHasInstallmentsFooter = $spInstallmentsFooter->count() > 0;
                @endphp

                @if(!$spHasInstallmentsFooter)
                    <form class="admin-modal-form admin-detail-status-form" method="POST" action="{{ route('admin.student-payments.update', $studentPayment) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group admin-detail-status-form-action">
                            <label for="student-payment-status-action" class="form-label admin-detail-status-form-label">Aksi</label>
                            <select id="student-payment-status-action" name="status" class="form-input form-select">
                                <option value="paid" @selected($spStatusValueFooter === 'paid')>✅ Paid</option>
                                <option value="pending" @selected($spStatusValueFooter === 'pending')>⏳ Pending</option>
                                <option value="failed" @selected($spStatusValueFooter === 'failed')>⛔ Failed</option>
                            </select>
                        </div>

                        <button type="submit" class="admin-btn admin-btn-submit">Simpan</button>
                    </form>
                @endif
            @endif

            @if($type === 'registration' && $canManageThisType && in_array(($registrationStatusValue ?? null), ['pending', 'approved_awaiting_payment', 'pending_due'], true))
                <form class="admin-modal-form admin-detail-status-form" method="POST" action="{{ route('admin.registrations.update', $registration) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group admin-detail-status-form-action">
                        <label for="registration-status-action" class="form-label admin-detail-status-form-label">Aksi</label>
                        <select id="registration-status-action" name="status" class="form-input form-select" data-reject-reason-toggle>
                            <option value="active" selected>✅ Approve</option>
                            <option value="rejected">⛔ Tolak</option>
                        </select>
                    </div>

                    <div class="form-group admin-detail-status-form-reject" data-reject-reason-group hidden>
                        <label for="registration-reject-reason" class="form-label admin-detail-status-form-label">Alasan Penolakan</label>
                        <input id="registration-reject-reason" type="text" name="reject_reason" class="form-input" placeholder="Masukkan alasan penolakan">
                    </div>

                    <button type="submit" class="admin-btn admin-btn-submit">Simpan</button>
                </form>
            @endif

            @if($type !== 'registration' && $entityId && $canManageThisType)
                <button
                    type="button"
                    class="admin-btn admin-btn-submit"
                    data-modal-open="edit-{{ $type }}-modal"
                    data-item-id="{{ $entityId }}"
                >
                    ✏️ Edit
                </button>
            @endif

            @if(in_array($type, ['user', 'teacher', 'student-attendance', 'teacher-attendance', 'facility', 'payment', 'student-payment'], true) && $entityId && $canManageThisType)
                <button
                    type="button"
                    class="admin-btn admin-btn-cancel"
                    data-confirm-delete
                    data-delete-type="{{ $type }}"
                    data-item-id="{{ $entityId }}"
                    data-item-name="{{ $entityNameForDelete }}"
                >
                    🗑️ Hapus
                </button>
            @endif
            <button type="button" class="admin-btn admin-btn-cancel" data-modal-close="close-detail">Tutup</button>
        </div>
    </div>
</div>
