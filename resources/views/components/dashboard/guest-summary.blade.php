@php
    $billSummary = $billSummary ?? [];
    $studentSummaries = collect($studentSummaries ?? []);

    $studentCount = (int)($billSummary['student_count'] ?? $studentSummaries->count());
    $pendingBills = (int)($billSummary['pending_bills'] ?? 0);
    $waitingVerification = (int)($billSummary['waiting_verification'] ?? 0);
    $failedBills = (int)($billSummary['failed_bills'] ?? 0);
    $outstandingAmount = (float)($billSummary['outstanding_amount'] ?? 0);
@endphp

<style>
    .guest-summary {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 2px solid var(--green-light);
        margin-bottom: 24px;
    }

    .guest-summary-title {
        font-family: 'Fredoka One', cursive;
        font-size: 18px;
        color: var(--dark);
        margin: 0 0 6px;
    }

    .guest-summary-subtitle {
        color: var(--gray);
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 14px;
    }

    .guest-summary-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .guest-summary-card {
        background: var(--bg);
        border-radius: 12px;
        padding: 12px 14px;
        border: 2px solid var(--green-light);
    }

    .guest-summary-card-title {
        font-size: 12px;
        color: var(--gray);
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 6px;
    }

    .guest-summary-card-value {
        font-size: 16px;
        font-weight: 900;
        color: var(--dark);
    }

    .guest-summary-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 12px;
    }

    .guest-summary-student {
        border-radius: 12px;
        padding: 12px 14px;
        border: 2px solid var(--green-light);
        background: white;
    }

    .guest-summary-student-name {
        font-weight: 900;
        color: var(--dark);
        font-size: 14px;
        margin-bottom: 4px;
    }

    .guest-summary-student-meta {
        font-size: 12px;
        color: var(--gray);
        font-weight: 700;
        margin-bottom: 8px;
    }

    .guest-summary-student-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        font-size: 12px;
    }

    .guest-summary-student-row span {
        color: var(--gray);
        font-weight: 800;
    }

    .guest-summary-student-row strong {
        color: var(--dark);
        font-weight: 900;
        text-align: right;
    }

    @media (max-width: 600px) {
        .guest-summary {
            padding: 16px;
        }
    }
</style>

<div class="guest-summary">
    <div class="guest-summary-title">Ringkasan Orang Tua</div>
    <div class="guest-summary-subtitle">Informasi singkat anak dan tagihan terbaru.</div>

    <div class="guest-summary-stats">
        <div class="guest-summary-card">
            <div class="guest-summary-card-title">Jumlah Anak</div>
            <div class="guest-summary-card-value">{{ $studentCount }}</div>
        </div>
        <div class="guest-summary-card">
            <div class="guest-summary-card-title">Tagihan Menunggu</div>
            <div class="guest-summary-card-value">{{ $pendingBills }}</div>
        </div>
        <div class="guest-summary-card">
            <div class="guest-summary-card-title">Verifikasi Admin</div>
            <div class="guest-summary-card-value">{{ $waitingVerification }}</div>
        </div>
        <div class="guest-summary-card">
            <div class="guest-summary-card-title">Tagihan Gagal</div>
            <div class="guest-summary-card-value">{{ $failedBills }}</div>
        </div>
        <div class="guest-summary-card">
            <div class="guest-summary-card-title">Total Tagihan</div>
            <div class="guest-summary-card-value">Rp {{ number_format($outstandingAmount, 0, ',', '.') }}</div>
        </div>
    </div>

    @if($studentSummaries->count() > 0)
        <div class="guest-summary-list">
            @foreach($studentSummaries as $summary)
                <div class="guest-summary-student">
                    <div class="guest-summary-student-name">{{ $summary['name'] ?? '-' }}</div>
                    <div class="guest-summary-student-meta">
                        {{ $summary['status_label'] ?? '-' }} @if(!empty($summary['group'])) • Kelompok {{ $summary['group'] }} @endif
                    </div>
                    <div class="guest-summary-student-row">
                        <span>Tagihan belum lunas</span>
                        <strong>{{ $summary['pending_bills'] ?? 0 }}</strong>
                    </div>
                    <div class="guest-summary-student-row">
                        <span>Absensi terakhir</span>
                        <strong>{{ $summary['latest_attendance'] ?? '-' }} ({{ $summary['latest_attendance_date'] ?? '-' }})</strong>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="guest-summary-student">
            <div class="guest-summary-student-name">Belum ada data siswa</div>
            <div class="guest-summary-student-meta">Silakan lakukan pendaftaran untuk memulai.</div>
        </div>
    @endif
</div>
