<!-- Dynamic Add/Edit Modal Form - Reusable -->
@php
    // Ensure optional variables exist to avoid "Undefined variable" notices.
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

    $position = $position ?? null;
    $allowanceType = $allowanceType ?? null;
    $teacherPosition = $teacherPosition ?? null;
    $positionAllowance = $positionAllowance ?? null;
    $teacherAttendanceRate = $teacherAttendanceRate ?? null;

    $payment = $payment ?? null;
    $studentPayment = $studentPayment ?? null;

    $students = $students ?? collect();
    $payments = $payments ?? collect();
    $teachers = $teachers ?? collect();
    $positions = $positions ?? collect();
    $allowanceTypes = $allowanceTypes ?? collect();

    $modalId = $action === 'create' ? "add-{$type}-modal" : "edit-{$type}-modal";
    $entity = $user ?? $teacher ?? $registration ?? $parent ?? $student ?? $schoolClass ?? $studentAttendance ?? $teacherAttendance ?? $teacherHonor ?? $facility ?? $position ?? $allowanceType ?? $teacherPosition ?? $positionAllowance ?? $teacherAttendanceRate ?? $payment ?? $studentPayment ?? null;
    $isEdit = $action === 'edit' && $entity;

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
        'position' => 'Posisi Guru',
        'teacher-position' => 'Penugasan Posisi',
        'allowance-type' => 'Jenis Tunjangan',
        'position-allowance' => 'Tunjangan Posisi',
        'teacher-attendance-rate' => 'Tarif Kehadiran',
        'facility' => 'Sarana & Prasarana',
        'payment' => 'Payment',
        'student-payment' => 'Tagihan Murid',
        default => 'Data',
    };

    $submitText = $isEdit ? 'Simpan Perubahan' : ('Tambah ' . $label);
    $modalTitle = $isEdit ? ('Edit ' . $label) : ('Tambah ' . $label);

    $formAction = match($type) {
        'user' => $isEdit ? route('admin.users.update', $user) : route('admin.users.store'),
        'teacher' => $isEdit ? route('admin.teachers.update', $teacher) : route('admin.teachers.store'),
        'registration' => $isEdit ? route('admin.registrations.update', $registration) : route('admin.registrations.store'),
        'parent' => $isEdit ? route('admin.parents.update', $parent) : route('admin.parents.store'),
        'student' => $isEdit ? route('admin.students.update', $student) : route('admin.students.store'),
        'class' => $isEdit ? route('admin.classes.update', $schoolClass) : route('admin.classes.store'),
        'student-attendance' => $isEdit ? route('admin.student-attendance.update', $studentAttendance) : route('admin.student-attendance.store'),
        'teacher-attendance' => $isEdit ? route('admin.teacher-attendance.update', $teacherAttendance) : route('admin.teacher-attendance.store'),
        'teacher-honor' => $isEdit ? route('admin.teacher-honors.update', $teacherHonor) : route('admin.teacher-honors.store'),
        'position' => $isEdit ? route('admin.positions.update', $position) : route('admin.positions.store'),
        'teacher-position' => $isEdit ? route('admin.teacher-positions.update', $teacherPosition) : route('admin.teacher-positions.store'),
        'allowance-type' => $isEdit ? route('admin.allowance-types.update', $allowanceType) : route('admin.allowance-types.store'),
        'position-allowance' => $isEdit ? route('admin.position-allowances.update', $positionAllowance) : route('admin.position-allowances.store'),
        'teacher-attendance-rate' => $isEdit ? route('admin.teacher-attendance-rates.update', $teacherAttendanceRate) : route('admin.teacher-attendance-rates.store'),
        'facility' => $isEdit ? route('admin.facilities.update', $facility) : route('admin.facilities.store'),
        'payment' => $isEdit ? route('admin.payments.update', $payment) : route('admin.payments.store'),
        'student-payment' => $isEdit ? route('admin.student-payments.update', $studentPayment) : route('admin.student-payments.store'),
        default => '#',
    };
@endphp

<div id="{{ $modalId }}" class="modal-overlay" hidden aria-hidden="true" data-modal-type="{{ $type }}">
    <div class="modal modal-admin modal-admin-form" role="dialog" aria-modal="true" aria-labelledby="modal-form-title">
        <!-- Modal Header -->
        <div class="modal-header">
            <h2 id="modal-form-title" class="modal-title">{{ $modalTitle }}</h2>
            <button type="button" class="modal-close" data-modal-close="close-modal" aria-label="Tutup">
                ✕
            </button>
        </div>

        <!-- Modal Body with Form -->
        <form id="admin-form-{{ $type }}-{{ $isEdit ? 'edit' : 'create' }}" class="admin-modal-form" method="POST" action="{{ $formAction }}" @if(in_array($type, ['facility', 'student-payment', 'teacher-attendance'], true)) enctype="multipart/form-data" @endif>
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="modal-body">
                <!-- USER FORM -->
                @if($type === 'user')
                    <!-- Name Field -->
                    <div class="form-group">
                        <label for="form-name" class="form-label">Nama Lengkap</label>
                        <input 
                            type="text" 
                            id="form-name" 
                            name="name" 
                            class="form-input"
                            value="{{ $user?->name ?? old('name') }}"
                            required
                            placeholder="Masukkan nama lengkap"
                        >
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="form-email" class="form-label">Email</label>
                        <input 
                            type="email" 
                            id="form-email" 
                            name="email" 
                            class="form-input"
                            value="{{ $user?->email ?? old('email') }}"
                            required
                            placeholder="example@email.com"
                        >
                    </div>

                    <!-- Phone Number Field -->
                    <div class="form-group">
                        <label for="form-phone" class="form-label">Nomor Telepon</label>
                        <input 
                            type="tel" 
                            id="form-phone" 
                            name="phone_num" 
                            class="form-input"
                            value="{{ $user?->phone_num ?? old('phone_num') }}"
                            required
                            placeholder="081234567890"
                        >
                    </div>

                    <!-- Role Field -->
                    <div class="form-group">
                        <label for="form-role" class="form-label">Peran</label>
                        <select 
                            id="form-role" 
                            name="role" 
                            class="form-input form-select"
                            required
                        >
                            <option value="">-- Pilih Peran --</option>
                            <option value="superadmin" @selected(($user?->role ?? old('role')) === 'superadmin')>Super Admin</option>
                            <option value="headmaster" @selected(($user?->role ?? old('role')) === 'headmaster')>Kepala Sekolah</option>
                            <option value="administration" @selected(($user?->role ?? old('role')) === 'administration')>Administrasi</option>
                            <option value="bendahara" @selected(($user?->role ?? old('role')) === 'bendahara')>Bendahara</option>
                            <option value="teacher" @selected(($user?->role ?? old('role')) === 'teacher')>Guru</option>
                            <option value="guest" @selected(($user?->role ?? old('role')) === 'guest')>Orang Tua</option>
                        </select>
                    </div>

                    <!-- Status Field -->
                    <div class="form-group">
                        <label for="form-status" class="form-label">Status</label>
                        <select
                            id="form-status"
                            name="status"
                            class="form-input form-select"
                        >
                            @php
                                $currentStatus = $user?->status ?? old('status') ?? 'active';
                            @endphp
                            <option value="active" @selected($currentStatus === 'active')>Aktif</option>
                            <option value="inactive" @selected($currentStatus === 'inactive')>Nonaktif</option>
                        </select>
                    </div>

                    <!-- Password Field (Required for Create, Optional for Edit) -->
                    <div class="form-group">
                        <label for="form-password" class="form-label">
                            Password
                            @if($isEdit)
                                <span class="form-label-hint">(kosongkan jika tidak ingin diubah)</span>
                            @endif
                        </label>
                        <input 
                            type="password" 
                            id="form-password" 
                            name="password" 
                            class="form-input"
                            @if(!$isEdit) required @endif
                            placeholder="Minimal 8 karakter"
                        >
                    </div>

                    <!-- Password Confirmation -->
                    <div class="form-group">
                        <label for="form-password-confirm" class="form-label">Konfirmasi Password</label>
                        <input 
                            type="password" 
                            id="form-password-confirm" 
                            name="password_confirmation" 
                            class="form-input"
                            @if(!$isEdit) required @endif
                            placeholder="Ulangi password"
                        >
                    </div>

                <!-- TEACHER FORM -->
                @elseif($type === 'teacher')
                    <!-- User Selection (for creating new teacher) -->
                    <div class="form-group">
                        <label for="form-id-user" class="form-label">Pilih Pengguna</label>
                        @if($isEdit)
                            <input type="hidden" name="id_user" value="{{ $teacher?->id_user ?? '' }}">
                        @endif
                        <select 
                            id="form-id-user" 
                            name="id_user" 
                            class="form-input form-select"
                            required
                            {{ $isEdit ? 'disabled' : '' }}
                        >
                            <option value="">-- Pilih Pengguna --</option>
                            @forelse($users ?? [] as $availableUser)
                                <option value="{{ $availableUser->id }}" @selected(($teacher?->id_user ?? old('id_user')) === $availableUser->id)>
                                    {{ $availableUser->name }} ({{ $availableUser->email }})
                                </option>
                            @empty
                                <option disabled>Tidak ada pengguna guru tersedia</option>
                            @endforelse
                        </select>
                        @error('id_user')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Teacher Name Field -->
                    <div class="form-group">
                        <label for="form-name" class="form-label">Nama Guru</label>
                        <input 
                            type="text" 
                            id="form-name" 
                            name="name" 
                            class="form-input"
                            value="{{ $teacher?->name ?? old('name') }}"
                            required
                            placeholder="Masukkan nama guru"
                        >
                        @error('name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-position" class="form-label">Jabatan</label>
                        <input
                            type="text"
                            id="form-position"
                            name="position"
                            class="form-input"
                            value="{{ $teacher?->position ?? old('position') }}"
                            placeholder="Contoh: Wali Kelas"
                        >
                        @error('position')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-nip" class="form-label">NIP (Opsional)</label>
                        <input
                            type="text"
                            id="form-nip"
                            name="nip"
                            class="form-input"
                            value="{{ $teacher?->nip ?? old('nip') }}"
                            placeholder="Nomor Induk Pegawai"
                        >
                        @error('nip')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-nuptk" class="form-label">NUPTK (Opsional)</label>
                        <input
                            type="text"
                            id="form-nuptk"
                            name="nuptk"
                            class="form-input"
                            value="{{ $teacher?->nuptk ?? old('nuptk') }}"
                            placeholder="Nomor Unik Pendidik"
                        >
                        @error('nuptk')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-birth-place" class="form-label">Tempat Lahir</label>
                        <input
                            type="text"
                            id="form-birth-place"
                            name="birth_place"
                            class="form-input"
                            value="{{ $teacher?->birth_place ?? old('birth_place') }}"
                            placeholder="Contoh: Makassar"
                        >
                        @error('birth_place')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-birth-date" class="form-label">Tanggal Lahir</label>
                        <input
                            type="date"
                            id="form-birth-date"
                            name="birth_date"
                            class="form-input"
                            value="{{ $teacher?->birth_date?->format('Y-m-d') ?? old('birth_date') }}"
                        >
                        @error('birth_date')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-start-work-date" class="form-label">Tanggal Mulai Kerja</label>
                        <input
                            type="date"
                            id="form-start-work-date"
                            name="start_work_date"
                            class="form-input"
                            value="{{ $teacher?->start_work_date?->format('Y-m-d') ?? old('start_work_date') }}"
                        >
                        @error('start_work_date')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Education Field -->
                    <div class="form-group">
                        <label for="form-education" class="form-label">Pendidikan</label>
                        <input 
                            type="text" 
                            id="form-education" 
                            name="education" 
                            class="form-input"
                            value="{{ $teacher?->education ?? old('education') }}"
                            required
                            placeholder="S1 Pendidikan, S2, etc"
                        >
                        @error('education')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status Field -->
                    <div class="form-group">
                        <label for="form-status" class="form-label">Status</label>
                        <select
                            id="form-status"
                            name="status"
                            class="form-input form-select"
                            required
                        >
                            <option value="active" @selected(($teacher?->status ?? old('status') ?? 'active') === 'active')>Aktif</option>
                            <option value="inactive" @selected(($teacher?->status ?? old('status') ?? 'active') === 'inactive')>Nonaktif</option>
                        </select>
                        @error('status')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Phone Number Field -->
                    <div class="form-group">
                        <label for="form-phone" class="form-label">Nomor Telepon</label>
                        <input 
                            type="tel" 
                            id="form-phone" 
                            name="phone_num" 
                            class="form-input"
                            value="{{ $teacher?->phone_num ?? old('phone_num') }}"
                            required
                            placeholder="081234567890"
                        >
                        @error('phone_num')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Field (Optional) -->
                    <div class="form-group">
                        <label for="form-email" class="form-label">Email (Opsional)</label>
                        <input 
                            type="email" 
                            id="form-email" 
                            name="email" 
                            class="form-input"
                            value="{{ $teacher?->email ?? old('email') }}"
                            placeholder="email@example.com"
                        >
                        @error('email')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                <!-- REGISTRATION FORM -->
                @elseif($type === 'registration')
                    @php
                        $candidate = $registration?->candidate_data ?? [];
                        $parents = $registration?->parents_data ?? [];
                    @endphp

                    @if($isEdit)
                        @php
                            $candidateName = $candidate['name'] ?? '-';
                            $userName = $registration?->user?->name ?? '-';
                            $userEmail = $registration?->user?->email ?? '-';
                            $currentStatus = $registration?->status ?? 'pending';
                            $currentStatusLabel = match($currentStatus) {
                                'pending' => 'Pending',
                                'approved_awaiting_payment' => 'Menunggu Pembayaran',
                                'pending_due' => 'Jatuh Tempo',
                                'active' => 'Active',
                                'rejected' => 'Rejected',
                                default => $currentStatus,
                            };

                            $pendingLike = in_array($currentStatus, ['pending', 'approved_awaiting_payment', 'pending_due'], true);
                        @endphp

                        <div class="profil-info-grid">
                            <div class="profil-info-item">
                                <div class="label">Calon</div>
                                <div class="value">{{ $candidateName }}</div>
                            </div>
                            <div class="profil-info-item">
                                <div class="label">User</div>
                                <div class="value">{{ $userName }} ({{ $userEmail }})</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="form-status" class="form-label">
                                Status
                                <span class="form-label-hint">(Pending → Menunggu Pembayaran / Tolak)</span>
                            </label>

                            @if($pendingLike)
                                <select id="form-status" name="status" class="form-input form-select" required>
                                    @if($currentStatus === 'pending')
                                        <option value="pending" selected>Pending</option>
                                        <option value="approved_awaiting_payment">Approve (Menunggu Pembayaran)</option>
                                    @else
                                        @if($currentStatus === 'pending_due')
                                            <option value="pending_due" selected>Jatuh Tempo</option>
                                            <option value="approved_awaiting_payment">Menunggu Pembayaran (Perpanjang)</option>
                                        @else
                                            <option value="approved_awaiting_payment" selected>Menunggu Pembayaran</option>
                                        @endif
                                    @endif
                                    <option value="rejected">Tolak</option>
                                </select>
                            @else
                                <input type="hidden" name="status" value="{{ $currentStatus }}">
                                <select id="form-status" class="form-input form-select" disabled>
                                    <option value="{{ $currentStatus }}" selected>{{ $currentStatusLabel }}</option>
                                </select>
                            @endif

                            @error('status')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        @if($pendingLike)
                            <div class="form-group">
                                <label for="form-reject-reason" class="form-label">
                                    Alasan Penolakan
                                    <span class="form-label-hint">(wajib jika status = Tolak)</span>
                                </label>
                                <input
                                    type="text"
                                    id="form-reject-reason"
                                    name="reject_reason"
                                    class="form-input"
                                    value="{{ $registration?->reject_reason ?? old('reject_reason') }}"
                                    placeholder="Contoh: Data tidak lengkap"
                                >
                                @error('reject_reason')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    @else
                        <div class="form-group">
                            <label for="form-id-user" class="form-label">Pilih User</label>
                            <select id="form-id-user" name="id_user" class="form-input form-select" required>
                                <option value="">-- Pilih User --</option>
                                @foreach($users ?? [] as $availableUser)
                                    <option value="{{ $availableUser->id }}" @selected(($registration?->id_user ?? old('id_user')) === $availableUser->id)>
                                        {{ $availableUser->name }} ({{ $availableUser->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_user')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="form-candidate-name" class="form-label">Nama Calon</label>
                            <input type="text" id="form-candidate-name" name="candidate_name" class="form-input" value="{{ $candidate['name'] ?? old('candidate_name') }}" required>
                            @error('candidate_name')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="form-candidate-birth-place" class="form-label">Tempat Lahir</label>
                            <input type="text" id="form-candidate-birth-place" name="candidate_birth_place" class="form-input" value="{{ $candidate['birth_place'] ?? old('candidate_birth_place') }}">
                            @error('candidate_birth_place')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="form-candidate-birth-date" class="form-label">Tanggal Lahir</label>
                            <input type="date" id="form-candidate-birth-date" name="candidate_birth_date" class="form-input" value="{{ $candidate['birth_date'] ?? old('candidate_birth_date') }}">
                            @error('candidate_birth_date')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="form-candidate-gender" class="form-label">Gender</label>
                            <select id="form-candidate-gender" name="candidate_gender" class="form-input form-select">
                                <option value="">-- Pilih Gender --</option>
                                <option value="pria" @selected(($candidate['gender'] ?? old('candidate_gender')) === 'pria')>Pria</option>
                                <option value="perempuan" @selected(($candidate['gender'] ?? old('candidate_gender')) === 'perempuan')>Perempuan</option>
                            </select>
                            @error('candidate_gender')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="form-group" class="form-label">Grup</label>
                            <input type="text" id="form-group" name="group" class="form-input" value="{{ $registration?->group ?? old('group') }}" placeholder="A / B">
                            @error('group')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Status
                                <span class="form-label-hint">(otomatis: Pending, deadline diisi saat Approve)</span>
                            </label>
                            <select class="form-input form-select" disabled>
                                <option value="pending" selected>Pending</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="form-father-name" class="form-label">Nama Ayah</label>
                            <input type="text" id="form-father-name" name="father_name" class="form-input" value="{{ $parents['father_name'] ?? old('father_name') }}">
                            @error('father_name')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="form-mother-name" class="form-label">Nama Ibu</label>
                            <input type="text" id="form-mother-name" name="mother_name" class="form-input" value="{{ $parents['mother_name'] ?? old('mother_name') }}">
                            @error('mother_name')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="form-father-phone" class="form-label">HP Ayah</label>
                            <input type="text" id="form-father-phone" name="father_phone_num" class="form-input" value="{{ $parents['father_phone_num'] ?? old('father_phone_num') }}">
                            @error('father_phone_num')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="form-mother-phone" class="form-label">HP Ibu</label>
                            <input type="text" id="form-mother-phone" name="mother_phone_num" class="form-input" value="{{ $parents['mother_phone_num'] ?? old('mother_phone_num') }}">
                            @error('mother_phone_num')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="form-father-occupation" class="form-label">Pekerjaan Ayah</label>
                            <input type="text" id="form-father-occupation" name="father_occupation" class="form-input" value="{{ $parents['father_occupation'] ?? old('father_occupation') }}">
                            @error('father_occupation')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="form-mother-occupation" class="form-label">Pekerjaan Ibu</label>
                            <input type="text" id="form-mother-occupation" name="mother_occupation" class="form-input" value="{{ $parents['mother_occupation'] ?? old('mother_occupation') }}">
                            @error('mother_occupation')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="form-father-address" class="form-label">Alamat Ayah</label>
                            <input type="text" id="form-father-address" name="father_address" class="form-input" value="{{ $parents['father_address'] ?? old('father_address') }}">
                            @error('father_address')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="form-mother-address" class="form-label">Alamat Ibu</label>
                            <input type="text" id="form-mother-address" name="mother_address" class="form-input" value="{{ $parents['mother_address'] ?? old('mother_address') }}">
                            @error('mother_address')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                <!-- PARENT FORM -->
                @elseif($type === 'parent')
                    <div class="form-group">
                        <label for="form-id-user" class="form-label">Pilih User</label>
                        @if($isEdit)
                            <input type="hidden" name="id_user" value="{{ $parent?->id_user ?? '' }}">
                        @endif
                        <select id="form-id-user" name="id_user" class="form-input form-select" required {{ $isEdit ? 'disabled' : '' }}>
                            <option value="">-- Pilih User --</option>
                            @foreach($users ?? [] as $availableUser)
                                <option value="{{ $availableUser->id }}" @selected(($parent?->id_user ?? old('id_user')) === $availableUser->id)>
                                    {{ $availableUser->name }} ({{ $availableUser->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_user')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-father-name" class="form-label">Nama Ayah</label>
                        <input type="text" id="form-father-name" name="father_name" class="form-input" value="{{ $parent?->father_name ?? old('father_name') }}">
                    </div>

                    <div class="form-group">
                        <label for="form-mother-name" class="form-label">Nama Ibu</label>
                        <input type="text" id="form-mother-name" name="mother_name" class="form-input" value="{{ $parent?->mother_name ?? old('mother_name') }}">
                    </div>

                    <div class="form-group">
                        <label for="form-father-phone" class="form-label">HP Ayah</label>
                        <input type="text" id="form-father-phone" name="father_phone_num" class="form-input" value="{{ $parent?->father_phone_num ?? old('father_phone_num') }}">
                    </div>

                    <div class="form-group">
                        <label for="form-mother-phone" class="form-label">HP Ibu</label>
                        <input type="text" id="form-mother-phone" name="mother_phone_num" class="form-input" value="{{ $parent?->mother_phone_num ?? old('mother_phone_num') }}">
                    </div>

                    <div class="form-group">
                        <label for="form-father-occupation" class="form-label">Pekerjaan Ayah</label>
                        <input type="text" id="form-father-occupation" name="father_occupation" class="form-input" value="{{ $parent?->father_occupation ?? old('father_occupation') }}">
                    </div>

                    <div class="form-group">
                        <label for="form-mother-occupation" class="form-label">Pekerjaan Ibu</label>
                        <input type="text" id="form-mother-occupation" name="mother_occupation" class="form-input" value="{{ $parent?->mother_occupation ?? old('mother_occupation') }}">
                    </div>

                    <div class="form-group">
                        <label for="form-father-address" class="form-label">Alamat Ayah</label>
                        <input type="text" id="form-father-address" name="father_address" class="form-input" value="{{ $parent?->father_address ?? old('father_address') }}">
                    </div>

                    <div class="form-group">
                        <label for="form-mother-address" class="form-label">Alamat Ibu</label>
                        <input type="text" id="form-mother-address" name="mother_address" class="form-input" value="{{ $parent?->mother_address ?? old('mother_address') }}">
                    </div>

                <!-- STUDENT FORM -->
                @elseif($type === 'student')
                    <div class="form-group">
                        <label for="form-parent" class="form-label">Orang Tua (Opsional)</label>
                        <select id="form-parent" name="id_parents" class="form-input form-select">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($parents ?? [] as $p)
                                @php
                                    $parentLabel = trim(($p->father_name ?? '') . ' / ' . ($p->mother_name ?? ''));
                                    $parentLabel = $parentLabel !== '/' ? $parentLabel : ('Orang Tua #' . $p->id_parents);
                                @endphp
                                <option value="{{ $p->id_parents }}" @selected(($student?->id_parents ?? old('id_parents')) === $p->id_parents)>
                                    #{{ $p->id_parents }} - {{ $parentLabel }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_parents')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-registration" class="form-label">Pendaftaran (Opsional)</label>
                        <select id="form-registration" name="id_registration" class="form-input form-select">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($registrations ?? [] as $reg)
                                @php $candidateName = $reg->candidate_data['name'] ?? ('Pendaftaran #' . $reg->id_registration); @endphp
                                <option value="{{ $reg->id_registration }}" @selected(($student?->id_registration ?? old('id_registration')) === $reg->id_registration)>
                                    #{{ $reg->id_registration }} - {{ $candidateName }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_registration')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-name" class="form-label">Nama Murid</label>
                        <input type="text" id="form-name" name="name" class="form-input" value="{{ $student?->name ?? old('name') }}" required>
                        @error('name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-birth-place" class="form-label">Tempat Lahir</label>
                        <input type="text" id="form-birth-place" name="birth_place" class="form-input" value="{{ $student?->birth_place ?? old('birth_place') }}">
                        @error('birth_place')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-birth-date" class="form-label">Tanggal Lahir</label>
                        <input type="date" id="form-birth-date" name="birth_date" class="form-input" value="{{ $student?->birth_date?->format('Y-m-d') ?? old('birth_date') }}">
                        @error('birth_date')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-gender" class="form-label">Gender</label>
                        <select id="form-gender" name="gender" class="form-input form-select">
                            <option value="">-- Pilih Gender --</option>
                            <option value="pria" @selected(($student?->gender ?? old('gender')) === 'pria')>Pria</option>
                            <option value="perempuan" @selected(($student?->gender ?? old('gender')) === 'perempuan')>Perempuan</option>
                        </select>
                        @error('gender')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-religion" class="form-label">Agama</label>
                        @php
                            $religionOptions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
                            $currentReligion = $student?->religion ?? old('religion');
                        @endphp
                        <select id="form-religion" name="religion" class="form-input form-select">
                            <option value="">-- Pilih Agama --</option>
                            @foreach($religionOptions as $religionOption)
                                <option value="{{ $religionOption }}" @selected($currentReligion === $religionOption)>{{ $religionOption }}</option>
                            @endforeach
                        </select>
                        @error('religion')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-group" class="form-label">Grup</label>
                        <input type="text" id="form-group" name="group" class="form-input" value="{{ $student?->group ?? old('group') }}" placeholder="A / B">
                        @error('group')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-status" class="form-label">Status</label>
                        @php $studentStatus = $student?->status ?? old('status') ?? 'pending_payment'; @endphp
                        <select id="form-status" name="status" class="form-input form-select" required>
                            <option value="pending_payment" @selected($studentStatus === 'pending_payment')>Belum Aktif</option>
                            <option value="aktif" @selected($studentStatus === 'aktif')>Aktif</option>
                            <option value="non-aktif" @selected($studentStatus === 'non-aktif')>Nonaktif</option>
                            <option value="lulus" @selected($studentStatus === 'lulus')>Lulus</option>
                            <option value="pindah" @selected($studentStatus === 'pindah')>Pindah</option>
                            <option value="rejected" @selected($studentStatus === 'rejected')>Rejected</option>
                        </select>
                        @error('status')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                @elseif($type === 'class')
                    <div class="form-group">
                        <label for="form-class-name" class="form-label">Nama Kelas</label>
                        <input
                            type="text"
                            id="form-class-name"
                            name="class_name"
                            class="form-input"
                            value="{{ $schoolClass?->class_name ?? old('class_name') }}"
                            required
                            placeholder="Contoh: Kelas A"
                        >
                        @error('class_name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-school-year" class="form-label">Tahun Ajaran</label>
                        <input
                            type="text"
                            id="form-school-year"
                            name="school_year"
                            class="form-input"
                            value="{{ $schoolClass?->school_year ?? old('school_year') }}"
                            required
                            placeholder="Contoh: 2025/2026"
                        >
                        @error('school_year')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-max-students" class="form-label">Kapasitas Maksimal Murid</label>
                        <input
                            type="number"
                            id="form-max-students"
                            name="max_students"
                            class="form-input"
                            value="{{ $schoolClass?->max_students ?? old('max_students') }}"
                            min="1"
                            step="1"
                            placeholder="Contoh: 30"
                        >
                        @error('max_students')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                @elseif($type === 'student-attendance')
                    <div class="form-group">
                        <label class="form-label">Murid</label>
                        @if($isEdit)
                            <select id="form-id-student" name="id_student" class="form-input form-select" required>
                                <option value="">-- Pilih Murid --</option>
                                @forelse($students ?? [] as $availableStudent)
                                    <option value="{{ $availableStudent->id_student }}" @selected(($studentAttendance?->id_student ?? old('id_student')) == $availableStudent->id_student)>
                                        {{ $availableStudent->name }} (ID: {{ $availableStudent->id_student }})
                                    </option>
                                @empty
                                    <option disabled>Tidak ada murid tersedia</option>
                                @endforelse
                            </select>
                        @else
                            @php
                                $oldSelectedStudents = old('id_student', []);
                                if (!is_array($oldSelectedStudents)) {
                                    $oldSelectedStudents = [$oldSelectedStudents];
                                }
                            @endphp
                            <div class="admin-checkbox-list" role="group" aria-label="Pilih murid">
                                @forelse($students ?? [] as $availableStudent)
                                    <label class="admin-checkbox-item">
                                        <input type="checkbox" name="id_student[]" value="{{ $availableStudent->id_student }}" @checked(in_array($availableStudent->id_student, $oldSelectedStudents))>
                                        <span>{{ $availableStudent->name }} (ID: {{ $availableStudent->id_student }})</span>
                                    </label>
                                @empty
                                    <div class="admin-checkbox-item" aria-disabled="true">
                                        <span>Tidak ada murid tersedia</span>
                                    </div>
                                @endforelse
                            </div>
                            <span class="form-label-hint">Pilih satu atau lebih murid untuk absensi sekaligus.</span>
                        @endif

                        @error('id_student')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                        @error('id_student.*')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-date" class="form-label">Tanggal</label>
                        <input type="date" id="form-date" name="date" class="form-input" value="{{ $studentAttendance?->date?->format('Y-m-d') ?? old('date') }}" required>
                        @error('date')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-status" class="form-label">Status</label>
                        @php $attendanceStatus = $studentAttendance?->status ?? old('status') ?? 'hadir'; @endphp
                        <select id="form-status" name="status" class="form-input form-select" required>
                            <option value="hadir" @selected($attendanceStatus === 'hadir')>Hadir</option>
                            <option value="izin" @selected($attendanceStatus === 'izin')>Izin</option>
                            <option value="sakit" @selected($attendanceStatus === 'sakit')>Sakit</option>
                            <option value="alpa" @selected($attendanceStatus === 'alpa')>Alpa</option>
                        </select>
                        @error('status')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-information" class="form-label">Keterangan</label>
                        <textarea id="form-information" name="information" class="form-input" rows="3" placeholder="Opsional">{{ $studentAttendance?->information ?? old('information') }}</textarea>
                        @error('information')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                @elseif($type === 'teacher-attendance')
                    <div class="form-group">
                        <label class="form-label">Guru</label>
                        @if($isEdit)
                            <select id="form-id-teacher" name="id_teacher" class="form-input form-select" required>
                                <option value="">-- Pilih Guru --</option>
                                @forelse($teachers ?? [] as $availableTeacher)
                                    <option value="{{ $availableTeacher->id_teacher }}" @selected(($teacherAttendance?->id_teacher ?? old('id_teacher')) == $availableTeacher->id_teacher)>
                                        {{ $availableTeacher->name }} (ID: {{ $availableTeacher->id_teacher }})
                                    </option>
                                @empty
                                    <option disabled>Tidak ada guru tersedia</option>
                                @endforelse
                            </select>
                        @else
                            @php
                                $oldSelectedTeachers = old('id_teacher', []);
                                if (!is_array($oldSelectedTeachers)) {
                                    $oldSelectedTeachers = [$oldSelectedTeachers];
                                }
                            @endphp
                            <div class="admin-checkbox-list" role="group" aria-label="Pilih guru">
                                @forelse($teachers ?? [] as $availableTeacher)
                                    <label class="admin-checkbox-item">
                                        <input type="checkbox" name="id_teacher[]" value="{{ $availableTeacher->id_teacher }}" @checked(in_array($availableTeacher->id_teacher, $oldSelectedTeachers))>
                                        <span>{{ $availableTeacher->name }} (ID: {{ $availableTeacher->id_teacher }})</span>
                                    </label>
                                @empty
                                    <div class="admin-checkbox-item" aria-disabled="true">
                                        <span>Tidak ada guru tersedia</span>
                                    </div>
                                @endforelse
                            </div>
                            <span class="form-label-hint">Pilih satu atau lebih guru untuk absensi sekaligus.</span>
                        @endif

                        @error('id_teacher')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                        @error('id_teacher.*')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-date" class="form-label">Tanggal</label>
                        <input type="date" id="form-date" name="date" class="form-input" value="{{ $teacherAttendance?->date?->format('Y-m-d') ?? old('date') }}" required>
                        @error('date')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-status" class="form-label">Status</label>
                        @php $attendanceStatus = $teacherAttendance?->status ?? old('status') ?? 'hadir'; @endphp
                        <select id="form-status" name="status" class="form-input form-select" required>
                            <option value="hadir" @selected($attendanceStatus === 'hadir')>Hadir</option>
                            <option value="izin" @selected($attendanceStatus === 'izin')>Izin</option>
                            <option value="sakit" @selected($attendanceStatus === 'sakit')>Sakit</option>
                            <option value="alpa" @selected($attendanceStatus === 'alpa')>Alpa</option>
                        </select>
                        @error('status')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($isEdit)
                        @php
                            $tzCheckIn = config('attendance.timezone', 'Asia/Makassar');
                            $checkInValue = $teacherAttendance?->check_in_time
                                ? $teacherAttendance->check_in_time->copy()->setTimezone($tzCheckIn)->format('Y-m-d\TH:i')
                                : old('check_in_time');
                        @endphp
                        <div class="form-group">
                            <label for="form-check-in-time" class="form-label">Jam Check-in (WITA)</label>
                            <input type="datetime-local" id="form-check-in-time" name="check_in_time" class="form-input" value="{{ $checkInValue }}">
                            <span class="form-label-hint">Kosongkan jika status izin/sakit/alpa. Batas tepat waktu: {{ config('attendance.late_after') }} WITA.</span>
                            @error('check_in_time')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="form-information" class="form-label">Keterangan</label>
                        <textarea id="form-information" name="information" class="form-input" rows="3" placeholder="Opsional">{{ $teacherAttendance?->information ?? old('information') }}</textarea>
                        @error('information')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-attachment" class="form-label">Bukti / Lampiran</label>
                        @if($isEdit && $teacherAttendance?->attachment_path)
                            <div style="margin-bottom:.5rem;">
                                <a href="{{ asset($teacherAttendance->attachment_path) }}" target="_blank" rel="noopener">Lihat bukti saat ini</a>
                            </div>
                        @endif
                        <input type="file" id="form-attachment" name="attachment" class="form-input" accept=".pdf,.jpg,.jpeg,.png">
                        <span class="form-label-hint">Wajib jika status = Izin. Format: PDF/JPG/PNG, max 2MB.</span>
                        @error('attachment')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                @elseif($type === 'teacher-honor')
                    @php
                        $attendanceCountValue = (int)($teacherHonor?->attendance_count ?? old('attendance_count') ?? 0);
                        $amountValue = (float)($teacherHonor?->amount ?? old('amount') ?? 0);
                        $rateValue = (float)($teacherHonor?->rate_snapshot ?? old('rate_snapshot') ?? 0);
                        $allowanceTotalValue = (float)($teacherHonor?->allowance_total ?? old('allowance_total') ?? 0);
                        $manualAdjustmentValue = (float)($teacherHonor?->manual_adjustment ?? old('manual_adjustment') ?? 0);
                        $defaultStart = now()->startOfMonth()->format('Y-m-d');
                        $defaultEnd = now()->endOfMonth()->format('Y-m-d');
                        $periodStartValue = $teacherHonor?->period_start?->format('Y-m-d') ?? old('period_start') ?? $defaultStart;
                        $periodEndValue = $teacherHonor?->period_end?->format('Y-m-d') ?? old('period_end') ?? $defaultEnd;
                    @endphp

                    <div class="form-group">
                        <label for="form-id-teacher" class="form-label">Guru</label>
                        @if($isEdit)
                            <input type="hidden" name="id_teacher" value="{{ $teacherHonor?->id_teacher ?? '' }}">
                        @endif
                        <select id="form-id-teacher" name="id_teacher" class="form-input form-select" required {{ $isEdit ? 'disabled' : '' }}>
                            <option value="">-- Pilih Guru --</option>
                            @foreach(($teachers ?? []) as $t)
                                @php
                                    $teacherName = $t->name ?? '-';
                                    $teacherStatus = $t->status ?? 'active';
                                @endphp
                                <option value="{{ $t->id_teacher }}" @selected((int)($teacherHonor?->id_teacher ?? old('id_teacher')) === (int)$t->id_teacher)>
                                    {{ $teacherName }} (ID: {{ $t->id_teacher }}){{ $teacherStatus === 'inactive' ? ' - Nonaktif' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_teacher')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-period-start" class="form-label">Periode Mulai</label>
                        <input
                            type="date"
                            id="form-period-start"
                            name="period_start"
                            class="form-input"
                            value="{{ $periodStartValue }}"
                            required
                        >
                        @error('period_start')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-period-end" class="form-label">Periode Akhir</label>
                        <input
                            type="date"
                            id="form-period-end"
                            name="period_end"
                            class="form-input"
                            value="{{ $periodEndValue }}"
                            required
                        >
                        @error('period_end')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="registration-detail-block admin-honor-recap-block" data-honor-recap-card>
                        <h3>Rekap Absensi</h3>
                        <div class="registration-detail-row"><span>Periode</span><strong data-honor-recap-period>-</strong></div>
                        <div class="registration-detail-row"><span>Hadir</span><strong data-honor-recap-hadir>0</strong></div>
                        <div class="registration-detail-row"><span>Izin</span><strong data-honor-recap-izin>0</strong></div>
                        <div class="registration-detail-row"><span>Sakit</span><strong data-honor-recap-sakit>0</strong></div>
                        <div class="registration-detail-row"><span>Alpa</span><strong data-honor-recap-alpa>0</strong></div>
                        <div class="registration-detail-row"><span>Total Pertemuan</span><strong data-honor-recap-total>0</strong></div>
                        <div class="registration-detail-row"><span>Rate per Hadir</span><strong data-honor-recap-rate>Rp 0</strong></div>
                        <div class="registration-detail-row"><span>Total Tunjangan</span><strong data-honor-recap-allowance>Rp 0</strong></div>
                        <div class="registration-detail-row"><span>Preview Total Honor</span><strong data-honor-recap-amount>Rp 0</strong></div>
                        <span class="form-label-hint" data-honor-recap-note>Pilih guru + periode untuk melihat rekap.</span>
                    </div>

                    <div class="form-group">
                        <label for="form-attendance-count" class="form-label">Jumlah Hadir</label>
                        <input
                            type="number"
                            id="form-attendance-count"
                            name="attendance_count"
                            class="form-input"
                            value="{{ $attendanceCountValue }}"
                            min="0"
                            step="1"
                            readonly
                        >
                        @error('attendance_count')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                        <span class="form-label-hint">Diisi otomatis dari absensi pada periode terpilih.</span>
                    </div>

                    <div class="form-group">
                        <label for="form-permission-count" class="form-label">Jumlah Izin</label>
                        <input
                            type="number"
                            id="form-permission-count"
                            name="permission_count"
                            class="form-input"
                            value="{{ $teacherHonor?->permission_count ?? old('permission_count') ?? 0 }}"
                            min="0"
                            step="1"
                            readonly
                        >
                        @error('permission_count')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-sickness-count" class="form-label">Jumlah Sakit</label>
                        <input
                            type="number"
                            id="form-sickness-count"
                            name="sickness_count"
                            class="form-input"
                            value="{{ $teacherHonor?->sickness_count ?? old('sickness_count') ?? 0 }}"
                            min="0"
                            step="1"
                            readonly
                        >
                        @error('sickness_count')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-absence-count" class="form-label">Jumlah Alpa</label>
                        <input
                            type="number"
                            id="form-absence-count"
                            name="absence_count"
                            class="form-input"
                            value="{{ $teacherHonor?->absence_count ?? old('absence_count') ?? 0 }}"
                            min="0"
                            step="1"
                            readonly
                        >
                        @error('absence_count')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-rate-per-attendance" class="form-label">Tarif per Hadir (Otomatis)</label>
                        <input
                            type="number"
                            id="form-rate-per-attendance"
                            class="form-input"
                            value="{{ $rateValue }}"
                            min="0"
                            step="0.01"
                            readonly
                        >
                        <span class="form-label-hint">Tarif otomatis diambil dari tabel tarif per guru.</span>
                    </div>

                    <div class="form-group">
                        <label for="form-allowance-total" class="form-label">Total Tunjangan (Otomatis)</label>
                        <input
                            type="number"
                            id="form-allowance-total"
                            class="form-input"
                            value="{{ $allowanceTotalValue }}"
                            min="0"
                            step="0.01"
                            readonly
                        >
                        <span class="form-label-hint">Total tunjangan berasal dari semua posisi aktif.</span>
                    </div>

                    <div class="form-group">
                        <label for="form-manual-adjustment" class="form-label">Penyesuaian Manual (+/-)</label>
                        <input
                            type="number"
                            id="form-manual-adjustment"
                            name="manual_adjustment"
                            class="form-input"
                            value="{{ $manualAdjustmentValue }}"
                            step="0.01"
                        >
                        @error('manual_adjustment')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                        <span class="form-label-hint">Gunakan untuk bonus/potongan tanpa mengubah data absensi.</span>
                    </div>

                    <div class="form-group">
                        <label for="form-amount" class="form-label">Total Honor (Otomatis)</label>
                        <input
                            type="number"
                            id="form-amount"
                            name="amount"
                            class="form-input"
                            value="{{ $amountValue }}"
                            min="0"
                            step="0.01"
                            readonly
                        >
                        @error('amount')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                        <span class="form-label-hint" data-honor-total-preview>Preview total honor: -</span>
                    </div>

                    <div class="form-group">
                        <label for="form-payment-date" class="form-label">Tanggal Pembayaran (Opsional)</label>
                        <input
                            type="date"
                            id="form-payment-date"
                            name="payment_date"
                            class="form-input"
                            value="{{ $teacherHonor?->payment_date?->format('Y-m-d') ?? old('payment_date') }}"
                        >
                        @error('payment_date')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                        <span class="form-label-hint">Kosongkan jika belum dibayar.</span>
                    </div>
                @elseif($type === 'position')
                    <div class="form-group">
                        <label for="form-name" class="form-label">Nama Posisi</label>
                        <input
                            type="text"
                            id="form-name"
                            name="name"
                            class="form-input"
                            value="{{ $position?->name ?? old('name') }}"
                            required
                            placeholder="Contoh: Kepala Sekolah"
                        >
                        @error('name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-description" class="form-label">Deskripsi (Opsional)</label>
                        <textarea id="form-description" name="description" class="form-input" rows="3" placeholder="Opsional">{{ $position?->description ?? old('description') }}</textarea>
                        @error('description')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-is-active" class="form-label">Status</label>
                        @php
                            $positionActive = (int)($position?->is_active ?? old('is_active') ?? 1);
                        @endphp
                        <select id="form-is-active" name="is_active" class="form-input form-select">
                            <option value="1" @selected($positionActive === 1)>Aktif</option>
                            <option value="0" @selected($positionActive === 0)>Nonaktif</option>
                        </select>
                        @error('is_active')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                @elseif($type === 'allowance-type')
                    <div class="form-group">
                        <label for="form-name" class="form-label">Nama Tunjangan</label>
                        <input
                            type="text"
                            id="form-name"
                            name="name"
                            class="form-input"
                            value="{{ $allowanceType?->name ?? old('name') }}"
                            required
                            placeholder="Contoh: Tunjangan Jabatan"
                        >
                        @error('name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-description" class="form-label">Deskripsi (Opsional)</label>
                        <textarea id="form-description" name="description" class="form-input" rows="3" placeholder="Opsional">{{ $allowanceType?->description ?? old('description') }}</textarea>
                        @error('description')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-is-active" class="form-label">Status</label>
                        @php
                            $typeActive = (int)($allowanceType?->is_active ?? old('is_active') ?? 1);
                        @endphp
                        <select id="form-is-active" name="is_active" class="form-input form-select">
                            <option value="1" @selected($typeActive === 1)>Aktif</option>
                            <option value="0" @selected($typeActive === 0)>Nonaktif</option>
                        </select>
                        @error('is_active')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                @elseif($type === 'teacher-position')
                    <div class="form-group">
                        <label for="form-id-teacher" class="form-label">Guru</label>
                        <select id="form-id-teacher" name="id_teacher" class="form-input form-select" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach(($teachers ?? []) as $t)
                                <option value="{{ $t->id_teacher }}" @selected((int)($teacherPosition?->id_teacher ?? old('id_teacher')) === (int)$t->id_teacher)>
                                    {{ $t->name ?? '-' }} (ID: {{ $t->id_teacher }}){{ ($t->status ?? 'active') === 'inactive' ? ' - Nonaktif' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_teacher')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-id-position" class="form-label">Posisi</label>
                        <select id="form-id-position" name="id_position" class="form-input form-select" required>
                            <option value="">-- Pilih Posisi --</option>
                            @foreach(($positions ?? []) as $p)
                                <option value="{{ $p->id_position }}" @selected((int)($teacherPosition?->id_position ?? old('id_position')) === (int)$p->id_position)>
                                    {{ $p->name ?? '-' }}{{ ($p->is_active ?? true) ? '' : ' - Nonaktif' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_position')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-effective-from" class="form-label">Periode Mulai</label>
                        <input
                            type="date"
                            id="form-effective-from"
                            name="effective_from"
                            class="form-input"
                            value="{{ $teacherPosition?->effective_from?->format('Y-m-d') ?? old('effective_from') }}"
                            required
                        >
                        @error('effective_from')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-effective-to" class="form-label">Periode Akhir (Opsional)</label>
                        <input
                            type="date"
                            id="form-effective-to"
                            name="effective_to"
                            class="form-input"
                            value="{{ $teacherPosition?->effective_to?->format('Y-m-d') ?? old('effective_to') }}"
                        >
                        @error('effective_to')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                @elseif($type === 'position-allowance')
                    <div class="form-group">
                        <label for="form-id-position" class="form-label">Posisi</label>
                        <select id="form-id-position" name="id_position" class="form-input form-select" required>
                            <option value="">-- Pilih Posisi --</option>
                            @foreach(($positions ?? []) as $p)
                                <option value="{{ $p->id_position }}" @selected((int)($positionAllowance?->id_position ?? old('id_position')) === (int)$p->id_position)>
                                    {{ $p->name ?? '-' }}{{ ($p->is_active ?? true) ? '' : ' - Nonaktif' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_position')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-id-allowance-type" class="form-label">Jenis Tunjangan</label>
                        <select id="form-id-allowance-type" name="id_allowance_type" class="form-input form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            @foreach(($allowanceTypes ?? []) as $t)
                                <option value="{{ $t->id_allowance_type }}" @selected((int)($positionAllowance?->id_allowance_type ?? old('id_allowance_type')) === (int)$t->id_allowance_type)>
                                    {{ $t->name ?? '-' }}{{ ($t->is_active ?? true) ? '' : ' - Nonaktif' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_allowance_type')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-amount" class="form-label">Nominal</label>
                        <input
                            type="number"
                            id="form-amount"
                            name="amount"
                            class="form-input"
                            value="{{ $positionAllowance?->amount ?? old('amount') }}"
                            min="0"
                            step="0.01"
                            required
                        >
                        @error('amount')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-effective-from" class="form-label">Periode Mulai</label>
                        <input
                            type="date"
                            id="form-effective-from"
                            name="effective_from"
                            class="form-input"
                            value="{{ $positionAllowance?->effective_from?->format('Y-m-d') ?? old('effective_from') }}"
                            required
                        >
                        @error('effective_from')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-effective-to" class="form-label">Periode Akhir (Opsional)</label>
                        <input
                            type="date"
                            id="form-effective-to"
                            name="effective_to"
                            class="form-input"
                            value="{{ $positionAllowance?->effective_to?->format('Y-m-d') ?? old('effective_to') }}"
                        >
                        @error('effective_to')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                @elseif($type === 'teacher-attendance-rate')
                    <div class="form-group">
                        <label for="form-id-teacher" class="form-label">Guru</label>
                        <select id="form-id-teacher" name="id_teacher" class="form-input form-select" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach(($teachers ?? []) as $t)
                                <option value="{{ $t->id_teacher }}" @selected((int)($teacherAttendanceRate?->id_teacher ?? old('id_teacher')) === (int)$t->id_teacher)>
                                    {{ $t->name ?? '-' }} (ID: {{ $t->id_teacher }}){{ ($t->status ?? 'active') === 'inactive' ? ' - Nonaktif' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_teacher')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-amount" class="form-label">Tarif per Hadir</label>
                        <input
                            type="number"
                            id="form-amount"
                            name="amount_per_attendance"
                            class="form-input"
                            value="{{ $teacherAttendanceRate?->amount_per_attendance ?? old('amount_per_attendance') }}"
                            min="0"
                            step="0.01"
                            required
                        >
                        @error('amount_per_attendance')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-effective-from" class="form-label">Periode Mulai</label>
                        <input
                            type="date"
                            id="form-effective-from"
                            name="effective_from"
                            class="form-input"
                            value="{{ $teacherAttendanceRate?->effective_from?->format('Y-m-d') ?? old('effective_from') }}"
                            required
                        >
                        @error('effective_from')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-effective-to" class="form-label">Periode Akhir (Opsional)</label>
                        <input
                            type="date"
                            id="form-effective-to"
                            name="effective_to"
                            class="form-input"
                            value="{{ $teacherAttendanceRate?->effective_to?->format('Y-m-d') ?? old('effective_to') }}"
                        >
                        @error('effective_to')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                @elseif($type === 'facility')
                    <div class="form-group">
                        <label for="form-name" class="form-label">Nama Fasilitas</label>
                        <input
                            type="text"
                            id="form-name"
                            name="name"
                            class="form-input"
                            value="{{ $facility?->name ?? old('name') }}"
                            required
                            placeholder="Contoh: Ruang Kelas Modern"
                        >
                        @error('name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-description" class="form-label">Deskripsi (Opsional)</label>
                        <textarea id="form-description" name="description" class="form-input" rows="3" placeholder="Opsional">{{ $facility?->description ?? old('description') }}</textarea>
                        @error('description')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-quantity" class="form-label">Jumlah</label>
                        <input
                            type="number"
                            id="form-quantity"
                            name="quantity"
                            class="form-input"
                            value="{{ $facility?->quantity ?? old('quantity') ?? 0 }}"
                            min="0"
                            step="1"
                            required
                        >
                        @error('quantity')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-condition" class="form-label">Kondisi (Opsional)</label>
                        @php
                            $conditionOptions = ['Baik', 'Rusak Ringan', 'Rusak Berat'];
                            $currentCondition = $facility?->condition ?? old('condition');
                        @endphp
                        <select id="form-condition" name="condition" class="form-input form-select">
                            <option value="">-- Pilih Kondisi --</option>
                            @foreach($conditionOptions as $conditionOption)
                                <option value="{{ $conditionOption }}" @selected($currentCondition === $conditionOption)>{{ $conditionOption }}</option>
                            @endforeach
                        </select>
                        @error('condition')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-fund-source" class="form-label">Sumber Dana (Opsional)</label>
                        <input
                            type="text"
                            id="form-fund-source"
                            name="fund_source"
                            class="form-input"
                            value="{{ $facility?->fund_source ?? old('fund_source') }}"
                            placeholder="Contoh: BOS"
                        >
                        @error('fund_source')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-acquisition-year" class="form-label">Tahun Perolehan (Opsional)</label>
                        <input
                            type="number"
                            id="form-acquisition-year"
                            name="acquisition_year"
                            class="form-input"
                            value="{{ $facility?->acquisition_year ?? old('acquisition_year') }}"
                            min="1900"
                            max="2100"
                            step="1"
                            placeholder="2025"
                        >
                        @error('acquisition_year')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-category" class="form-label">Kategori (Opsional)</label>
                        <input
                            type="text"
                            id="form-category"
                            name="category"
                            class="form-input"
                            value="{{ $facility?->category ?? old('category') }}"
                            placeholder="Contoh: Perabot"
                        >
                        @error('category')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-image" class="form-label">Gambar (Opsional)</label>
                        <input
                            type="file"
                            id="form-image"
                            name="image"
                            class="form-input"
                            accept="image/*"
                        >
                        @error('image')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                        <span class="form-label-hint">Upload gambar (JPG/PNG/WebP). Maks 2MB.</span>
                    </div>

                    <div class="form-group">
                        <label for="form-is-active" class="form-label">Tampilkan di Landing Page</label>
                        @php
                            $isActive = (int)($facility?->is_active ?? old('is_active') ?? 1);
                        @endphp
                        <select id="form-is-active" name="is_active" class="form-input form-select" required>
                            <option value="1" @selected($isActive === 1)>Ya (Aktif)</option>
                            <option value="0" @selected($isActive === 0)>Tidak (Nonaktif)</option>
                        </select>
                        @error('is_active')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                @elseif($type === 'payment')
                    @php
                        $initialRows = [];

                        // Prefer old input when present, otherwise use model template.
                        $oldJson = old('detail_fee_json');
                        if (is_string($oldJson) && trim($oldJson) !== '') {
                            $decoded = json_decode($oldJson, true);
                            if (is_array($decoded)) $initialRows = $decoded;
                        } elseif ($payment?->detail_fee_template !== null) {
                            $initialRows = is_array($payment->detail_fee_template) ? $payment->detail_fee_template : [];
                        }

                        // Normalize to array of rows.
                        if (!is_array($initialRows)) $initialRows = [];

                        $initialJsonCompact = json_encode($initialRows, JSON_UNESCAPED_UNICODE);
                        if (!is_string($initialJsonCompact)) $initialJsonCompact = '';
                        $initialJsonPretty = json_encode($initialRows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        if (!is_string($initialJsonPretty)) $initialJsonPretty = '';
                    @endphp

                    <div class="form-group">
                        <label for="form-name" class="form-label">Nama Payment</label>
                        <input
                            type="text"
                            id="form-name"
                            name="name"
                            class="form-input"
                            value="{{ $payment?->name ?? old('name') }}"
                            required
                            placeholder="Contoh: SPP Bulanan"
                        >
                        @error('name')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-jenis" class="form-label">Jenis Payment</label>
                        <input
                            type="text"
                            id="form-jenis"
                            name="jenis_payment"
                            class="form-input"
                            value="{{ $payment?->jenis_payment ?? old('jenis_payment') }}"
                            required
                            placeholder="Contoh: spp / daftar_ulang / seragam"
                        >
                        @error('jenis_payment')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-period-mode" class="form-label">Mode Periode</label>
                        <select id="form-period-mode" name="period_mode" class="form-input form-select" required>
                            @php
                                $pm = $payment?->period_mode ?? old('period_mode') ?? 'one_time';
                            @endphp
                            <option value="one_time" @selected($pm === 'one_time')>Sekali Bayar</option>
                            <option value="monthly" @selected($pm === 'monthly')>Bulanan</option>
                            <option value="school_year" @selected($pm === 'school_year')>Tahun Ajaran</option>
                        </select>
                        @error('period_mode')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                        <span class="form-label-hint">Menentukan format periode tagihan (ONCE / YYYY-MM / YYYY/YYYY).</span>
                    </div>

                    <div class="form-group">
                        <label for="form-default-amount" class="form-label">Default Amount</label>
                        <input
                            type="number"
                            id="form-default-amount"
                            name="default_amount"
                            class="form-input"
                            value="{{ $payment?->default_amount ?? old('default_amount') ?? 0 }}"
                            min="0"
                            step="0.01"
                        >
                        @error('default_amount')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                        <span class="form-label-hint">Dipakai jika komponen biaya (JSON) belum diisi.</span>
                    </div>

                    <div class="form-group" data-fee-editor>
                        <label class="form-label">Komponen Biaya (Opsional)</label>

                        <div class="fee-editor">
                            <div class="fee-editor-rows" data-fee-rows></div>

                            <div class="fee-editor-actions">
                                <button type="button" class="admin-btn admin-btn-cancel" data-fee-add>
                                    + Tambah Komponen
                                </button>

                                <div class="fee-editor-summary">
                                    <span class="form-label-hint">Total komponen:</span>
                                    <strong data-fee-total>Rp 0</strong>
                                </div>
                            </div>

                            <input type="hidden" name="detail_fee_json" data-fee-json value="{{ $initialJsonCompact }}">

                            @error('detail_fee_json')
                                <span class="form-error">{{ $message }}</span>
                            @enderror

                            <details class="fee-editor-advanced">
                                <summary class="fee-editor-advanced-summary">Lihat JSON (opsional)</summary>
                                <textarea class="form-input" rows="6" readonly data-fee-json-view placeholder='[{"label":"SPP","amount":150000,"qty":1}]'>{{ $initialJsonPretty }}</textarea>
                                <span class="form-label-hint">Jika komponen kosong, sistem akan memakai Default Amount.</span>
                            </details>

                            <template data-fee-row-template>
                                <div class="fee-editor-row" data-fee-row>
                                    <div class="fee-editor-col">
                                        <label class="form-label fee-editor-mini">Nama Komponen</label>
                                        <input type="text" class="form-input" data-fee-label placeholder="Contoh: SPP">
                                    </div>
                                    <div class="fee-editor-col fee-editor-col-qty">
                                        <label class="form-label fee-editor-mini">Qty</label>
                                        <input type="number" class="form-input" data-fee-qty min="1" step="1" value="1">
                                    </div>
                                    <div class="fee-editor-col">
                                        <label class="form-label fee-editor-mini">Nominal</label>
                                        <input type="number" class="form-input" data-fee-amount min="0" step="0.01" placeholder="150000">
                                    </div>
                                    <div class="fee-editor-col fee-editor-col-remove">
                                        <button type="button" class="admin-btn admin-btn-cancel fee-editor-remove" data-fee-remove>Hapus</button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <span class="form-label-hint">Isi komponen biaya dengan menambah baris. Total dihitung otomatis dari Σ(nominal×qty).</span>
                    </div>

                    <div class="form-group">
                        <label for="form-start" class="form-label">Start Date (Opsional)</label>
                        <input type="date" id="form-start" name="start_date" class="form-input" value="{{ $payment?->start_date?->format('Y-m-d') ?? old('start_date') }}">
                        @error('start_date')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-end" class="form-label">End Date (Opsional)</label>
                        <input type="date" id="form-end" name="end_date" class="form-input" value="{{ $payment?->end_date?->format('Y-m-d') ?? old('end_date') }}">
                        @error('end_date')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form-is-active" class="form-label">Status</label>
                        @php
                            $isActive = (int)($payment?->is_active ?? old('is_active') ?? 1);
                        @endphp
                        <select id="form-is-active" name="is_active" class="form-input form-select" required>
                            <option value="1" @selected($isActive === 1)>Aktif</option>
                            <option value="0" @selected($isActive === 0)>Nonaktif</option>
                        </select>
                        @error('is_active')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>
                @elseif($type === 'student-payment')
                    @php
                        $sp = $studentPayment;
                        $isPaid = ($sp?->status ?? null) === 'paid';
                    @endphp

                    @if($isEdit)
                        <input type="hidden" name="id_student" value="{{ $sp?->id_student }}">
                        <input type="hidden" name="id_payment" value="{{ $sp?->id_payment }}">
                    @endif

                    <div class="form-group">
                        <label for="form-id-student" class="form-label">Murid</label>
                        @if($isEdit)
                            <input
                                type="text"
                                id="form-id-student"
                                class="form-input"
                                value="{{ ($sp?->student?->name ?? '-') . ' (ID: ' . ($sp?->id_student ?? '-') . ')' }}"
                                readonly
                            >
                        @else
                            <select id="form-id-student" name="id_student" class="form-input form-select" required>
                                <option value="">-- Pilih Murid --</option>
                                @foreach(($students ?? []) as $s)
                                    <option value="{{ $s->id_student }}" @selected((string)($sp?->id_student ?? old('id_student')) === (string)$s->id_student)>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_student')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="form-id-payment" class="form-label">Payment</label>
                        @if($isEdit)
                            <input
                                type="text"
                                id="form-id-payment"
                                class="form-input"
                                value="{{ ($sp?->payment?->name ?? '-') . ' (ID: ' . ($sp?->id_payment ?? '-') . ')' }}"
                                readonly
                            >
                        @else
                            <select id="form-id-payment" name="id_payment" class="form-input form-select" required>
                                <option value="">-- Pilih Payment --</option>
                                @foreach(($payments ?? []) as $p)
                                    <option value="{{ $p->id_payment }}" @selected((string)($sp?->id_payment ?? old('id_payment')) === (string)$p->id_payment)>
                                        {{ $p->name }} ({{ $p->period_mode }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_payment')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                            <span class="form-label-hint">Periode tergantung payment (one_time/monthly/school_year).</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="form-payment-period" class="form-label">Periode</label>
                        <input
                            type="text"
                            id="form-payment-period"
                            name="payment_period"
                            class="form-input"
                            value="{{ $sp?->payment_period ?? old('payment_period') }}"
                            placeholder="ONCE / YYYY-MM / YYYY/YYYY"
                            {{ $isEdit ? 'readonly' : '' }}
                        >
                        @error('payment_period')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                        <span class="form-label-hint">Untuk payment one_time, sistem akan menyimpan periode = ONCE.</span>
                    </div>

                    <div class="form-group">
                        <label for="form-discount" class="form-label">Diskon (Opsional)</label>
                        <input
                            type="number"
                            id="form-discount"
                            name="discount_amount"
                            class="form-input"
                            value="{{ $sp?->discount_amount ?? old('discount_amount') ?? 0 }}"
                            min="0"
                            step="0.01"
                        >
                        @error('discount_amount')
                            <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($isEdit)
                        <div class="form-group">
                            <label for="form-status" class="form-label">Status</label>
                            @php
                                $currentStatus = $sp?->status ?? old('status') ?? 'pending';
                            @endphp
                            <select id="form-status" name="status" class="form-input form-select" required>
                                <option value="pending" @selected($currentStatus === 'pending')>Pending</option>
                                <option value="paid" @selected($currentStatus === 'paid')>Paid</option>
                                <option value="failed" @selected($currentStatus === 'failed')>Failed</option>
                            </select>
                            @error('status')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="form-payment-method" class="form-label">Metode (Opsional)</label>
                            @php
                                $pm = $sp?->payment_method ?? old('payment_method');
                            @endphp
                            <select id="form-payment-method" name="payment_method" class="form-input form-select">
                                <option value="">-- Pilih Metode --</option>
                                <option value="transfer_bank" @selected($pm === 'transfer_bank')>Transfer Bank</option>
                                <option value="e_wallet" @selected($pm === 'e_wallet')>E-Wallet</option>
                                <option value="cash" @selected($pm === 'cash')>Cash</option>
                                <option value="qris" @selected($pm === 'qris')>QRIS</option>
                            </select>
                            @error('payment_method')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="form-proof" class="form-label">Bukti Bayar (Opsional)</label>
                            <input type="file" id="form-proof" name="proof_file" class="form-input" {{ $isPaid ? '' : '' }}>
                            @error('proof_file')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                            <span class="form-label-hint">Upload bukti bayar (maks 4MB).</span>
                        </div>
                    @endif
                @endif
            </div>

            <!-- Modal Footer with Action Buttons -->
            <div class="modal-footer">
                <button type="button" class="admin-btn admin-btn-cancel" data-modal-close="close-modal">
                    Batal
                </button>
                <button type="submit" class="admin-btn admin-btn-submit">
                    {{ $submitText }}
                </button>
            </div>
        </form>
    </div>
</div>
