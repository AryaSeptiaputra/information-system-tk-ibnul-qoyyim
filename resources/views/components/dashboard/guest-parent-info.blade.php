@php
    /** @var \App\Models\Registration|null $registration */
    $registration = $registration ?? null;
    $studentInfo = $studentInfo ?? null;
    $candidate = $registration?->candidate_data ?? [];
    $parents = $registration?->parents_data ?? [];

    $candidateBirthDateRaw = $candidate['birth_date'] ?? null;
    $candidateBirthDateLabel = '-';
    if ($candidateBirthDateRaw) {
        if (is_string($candidateBirthDateRaw)) {
            $candidateBirthDateLabel = trim(explode(' ', $candidateBirthDateRaw)[0]);
        } elseif (is_object($candidateBirthDateRaw) && method_exists($candidateBirthDateRaw, 'format')) {
            $candidateBirthDateLabel = $candidateBirthDateRaw->format('Y-m-d');
        } else {
            $candidateBirthDateLabel = (string)$candidateBirthDateRaw;
        }
    }

    $gender = $candidate['gender'] ?? null;
    $genderLabel = $gender === 'pria' ? 'Laki-laki' : ($gender === 'perempuan' ? 'Perempuan' : '-');

    $studentName = $studentInfo['name'] ?? ($candidate['name'] ?? '-');
    $studentGenderLabel = '-';
    if (($studentInfo['gender'] ?? null) === 'male') {
        $studentGenderLabel = 'Laki-laki';
    } elseif (($studentInfo['gender'] ?? null) === 'female') {
        $studentGenderLabel = 'Perempuan';
    } elseif ($genderLabel !== '-') {
        $studentGenderLabel = $genderLabel;
    }

    $studentBirthLabel = '-';
    if ($studentInfo['birth_date'] ?? null) {
        try {
            $studentBirthLabel = \Carbon\Carbon::parse($studentInfo['birth_date'])->format('d M Y');
        } catch (\Exception $_) {
            $studentBirthLabel = (string)$studentInfo['birth_date'];
        }
    } elseif ($candidateBirthDateLabel !== '-') {
        $studentBirthLabel = $candidateBirthDateLabel;
    }

    $studentGroupLabel = $studentInfo['group'] ?? ($registration?->group ?? '-');
    $studentStatusLabel = '-';
    if (($studentInfo['status'] ?? null) === 'active') {
        $studentStatusLabel = 'Aktif';
    } elseif (($studentInfo['status'] ?? null) === 'inactive') {
        $studentStatusLabel = 'Tidak Aktif';
    } elseif (($studentInfo['status'] ?? null)) {
        $studentStatusLabel = (string)$studentInfo['status'];
    }
@endphp

<style>
    .parent-info {
        background: white;
        border-radius: 16px;
        padding: 24px;
        border: 2px solid var(--green-light);
        margin-bottom: 32px;
    }

    .parent-info h3 {
        font-family: 'Fredoka One', cursive;
        font-size: 18px;
        color: var(--dark);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .parent-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
    }

    .parent-info-grid.two-col {
        grid-template-columns: repeat(2, minmax(240px, 1fr));
    }

    .parent-info-block {
        background: var(--bg);
        border-radius: 12px;
        padding: 14px;
        border-left: 3px solid var(--green);
    }

    .parent-info-block-title {
        font-size: 12px;
        font-weight: 900;
        color: var(--gray);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 10px;
    }

    .parent-info-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        font-size: 12px;
        margin-bottom: 8px;
    }

    .parent-info-row:last-child {
        margin-bottom: 0;
    }

    .parent-info-row span {
        color: var(--gray);
        font-weight: 800;
    }

    .parent-info-row strong {
        color: var(--dark);
        font-weight: 900;
        text-align: right;
    }

    .guest-reg-timeline {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 10px;
        margin-bottom: 14px;
    }

    .guest-reg-step {
        background: white;
        border-radius: 12px;
        padding: 12px;
        border: 2px solid var(--green-light);
        opacity: 0.65;
    }

    .guest-reg-step.active {
        opacity: 1;
        border-color: var(--green);
    }

    .guest-reg-step.done {
        opacity: 1;
        background: var(--bg);
    }

    .guest-reg-step-title {
        font-size: 12px;
        font-weight: 900;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .guest-reg-step-desc {
        font-size: 12px;
        color: var(--gray);
        font-weight: 700;
        margin-top: 6px;
        line-height: 1.35;
    }

    @media (max-width: 600px) {
        .parent-info {
            padding: 16px;
        }

        .parent-info-grid {
            grid-template-columns: 1fr;
        }

        .parent-info-grid.two-col {
            grid-template-columns: 1fr;
        }
    }
</style>

{{-- BARIS 1: INFORMASI SISWA --}}
<div class="parent-info">
    <h3>👦 Informasi Siswa</h3>

    <div class="parent-info-grid">
        <div class="parent-info-block">
            <div class="parent-info-block-title">Data Siswa</div>
            <div class="parent-info-row"><span>Nama</span><strong>{{ $studentName }}</strong></div>
            <div class="parent-info-row"><span>Jenis Kelamin</span><strong>{{ $studentGenderLabel }}</strong></div>
            <div class="parent-info-row"><span>Tanggal Lahir</span><strong>{{ $studentBirthLabel }}</strong></div>
            <div class="parent-info-row"><span>Kelompok</span><strong>{{ $studentGroupLabel }}</strong></div>
            <div class="parent-info-row"><span>Status Siswa</span><strong>{{ $studentStatusLabel }}</strong></div>
        </div>
    </div>
</div>

{{-- BARIS 2: ORANG TUA (AYAH + IBU) --}}
<div class="parent-info">
    <h3>👨‍👩‍👧‍👦 Informasi Orang Tua</h3>

    <div class="parent-info-grid two-col">
        <div class="parent-info-block">
            <div class="parent-info-block-title">Data Ayah/Wali</div>
            <div class="parent-info-row"><span>Nama</span><strong>{{ $parents['father_name'] ?? '-' }}</strong></div>
            <div class="parent-info-row"><span>Telepon</span><strong>{{ $parents['father_phone'] ?? '-' }}</strong></div>
            <div class="parent-info-row"><span>Pekerjaan</span><strong>{{ $parents['father_job'] ?? '-' }}</strong></div>
            <div class="parent-info-row"><span>Alamat</span><strong>{{ $parents['father_address'] ?? '-' }}</strong></div>
        </div>

        <div class="parent-info-block">
            <div class="parent-info-block-title">Data Ibu</div>
            <div class="parent-info-row"><span>Nama</span><strong>{{ $parents['mother_name'] ?? '-' }}</strong></div>
            <div class="parent-info-row"><span>Telepon</span><strong>{{ $parents['mother_phone'] ?? '-' }}</strong></div>
            <div class="parent-info-row"><span>Pekerjaan</span><strong>{{ $parents['mother_job'] ?? '-' }}</strong></div>
            <div class="parent-info-row"><span>Alamat</span><strong>{{ $parents['mother_address'] ?? '-' }}</strong></div>
        </div>
    </div>

</div>

{{-- BARIS 3: INFORMASI PENDAFTARAN --}}
<div class="parent-info">
    <h3>📝 Informasi Pendaftar</h3>

    @php
        $regStatus = (string)($registration?->status ?? '');
        $isRejected = $regStatus === 'rejected';

        $stepSubmit = $registration ? 'done' : '';
        $stepVerify = 'active';
        $stepPay = '';
        $stepActive = '';

        if ($regStatus === 'pending') {
            $stepVerify = 'active';
        } elseif ($regStatus === 'approved_awaiting_payment') {
            $stepVerify = 'done';
            $stepPay = 'active';
        } elseif ($regStatus === 'pending_due') {
            $stepVerify = 'active';
        } elseif ($regStatus === 'active') {
            $stepVerify = 'done';
            $stepPay = 'done';
            $stepActive = 'done';
        } elseif ($regStatus === 'rejected') {
            $stepVerify = 'done';
        }

        if (!$registration) {
            $stepVerify = '';
        }
    @endphp

    @if($registration)
        <div class="guest-reg-timeline">
            <div class="guest-reg-step {{ $stepSubmit }}">
                <div class="guest-reg-step-title">✅ Diajukan</div>
                <div class="guest-reg-step-desc">Pendaftaran sudah dikirim.</div>
            </div>

            <div class="guest-reg-step {{ $stepVerify }}">
                <div class="guest-reg-step-title">🔍 Verifikasi</div>
                <div class="guest-reg-step-desc">
                    @if($isRejected)
                        Data diperiksa dan ditolak.
                    @elseif($regStatus === 'active')
                        Data sudah diverifikasi.
                    @else
                        Menunggu verifikasi admin.
                    @endif
                </div>
            </div>

            <div class="guest-reg-step {{ $stepPay }}">
                <div class="guest-reg-step-title">💳 Pembayaran</div>
                <div class="guest-reg-step-desc">
                    @if($regStatus === 'approved_awaiting_payment')
                        Menunggu pembayaran sesuai instruksi admin.
                    @elseif($regStatus === 'active')
                        Pembayaran selesai.
                    @else
                        Jika diperlukan, tagihan akan muncul di halaman Tagihan.
                    @endif
                </div>
            </div>

            <div class="guest-reg-step {{ $stepActive }}">
                <div class="guest-reg-step-title">🎉 Aktif</div>
                <div class="guest-reg-step-desc">
                    @if($regStatus === 'active')
                        Pendaftaran aktif.
                    @elseif($isRejected)
                        Belum aktif.
                    @else
                        Menunggu proses selesai.
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="parent-info-grid">
        <div class="parent-info-block">
            <div class="parent-info-block-title">Data Pendaftaran</div>
            <div class="parent-info-row"><span>ID Pendaftaran</span><strong>{{ $registration?->id_registration ?? '-' }}</strong></div>
            <div class="parent-info-row"><span>Kelompok</span><strong>{{ $registration?->group ?? '-' }}</strong></div>
            <div class="parent-info-row"><span>Status</span><strong>{{ strtoupper(str_replace('_', ' ', $registration?->status ?? '-')) }}</strong></div>
            <div class="parent-info-row"><span>Tanggal Pengajuan</span><strong>{{ $registration?->created_at ? $registration->created_at->format('Y-m-d') : '-' }}</strong></div>
            @if($registration?->payment_deadline)
                <div class="parent-info-row"><span>Deadline Pembayaran</span><strong>{{ $registration->payment_deadline->format('Y-m-d') }}</strong></div>
            @endif
            @if($registration?->grace_period_until)
                <div class="parent-info-row"><span>Masa Tenggang</span><strong>{{ $registration->grace_period_until->format('Y-m-d') }}</strong></div>
            @endif
            @if(($registration?->reject_reason ?? null))
                <div class="parent-info-row"><span>Alasan Ditolak</span><strong>{{ $registration->reject_reason }}</strong></div>
            @endif
        </div>
    </div>
</div>
