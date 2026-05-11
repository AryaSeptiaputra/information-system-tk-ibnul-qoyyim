@php
    $user = $user ?? auth()->user();
    $myTeacherDetail = $myTeacherDetail ?? null;
    $myHonor = $myHonor ?? null;
    $myHonorLatest = $myHonorLatest ?? null;
    $myHonorList = $myHonorList ?? collect();
    $showAllLink = $showAllLink ?? true;

    $monthNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    $formatPeriod = static function (?int $month, ?int $year) use ($monthNames): string {
        $m = (int)($month ?? 0);
        $y = (int)($year ?? 0);
        if ($m < 1 || $m > 12 || $y < 1) {
            return '-';
        }
        return ($monthNames[$m] ?? (string)$m) . ' ' . $y;
    };

    $formatPeriodRange = static function ($row) use ($formatPeriod): string {
        if ($row?->period_start && $row?->period_end) {
            return $row->period_start->format('Y-m-d') . ' s/d ' . $row->period_end->format('Y-m-d');
        }
        return $formatPeriod((int)($row?->month ?? 0), (int)($row?->year ?? 0));
    };

    $formatRupiah = static function ($amount): string {
        $val = (float)($amount ?? 0);
        return 'Rp ' . number_format($val, 0, ',', '.');
    };
@endphp

<div class="admin-section-card">
    <div class="admin-section-card-header">
        <h3>Informasi Honor Saya</h3>
        @if($showAllLink)
            <a href="{{ route('admin.my-honor.index') }}" class="text-link">Lihat Semua</a>
        @endif
    </div>

    @if(!$myTeacherDetail)
        <p class="text-empty">Data guru belum terhubung ke akun ini.</p>
    @else
        <div class="admin-finance-summary">
            <div class="finance-item">
                <span class="finance-label">Nama Guru</span>
                <span class="finance-value finance-value-blue">{{ $myTeacherDetail->name ?? ($user->name ?? '-') }}</span>
            </div>

            <div class="finance-item">
                <span class="finance-label">Periode Saat Ini</span>
                <span class="finance-value finance-value-green">
                    {{ $myHonor ? $formatPeriodRange($myHonor) : '-' }}
                </span>
            </div>

            <div class="finance-item">
                <span class="finance-label">Nominal (Periode Saat Ini)</span>
                <span class="finance-value finance-value-green">{{ $myHonor ? $formatRupiah($myHonor->amount ?? 0) : 'Rp 0' }}</span>
            </div>

            <div class="finance-item">
                <span class="finance-label">Status Pembayaran</span>
                @php
                    $paid = (bool)($myHonor && $myHonor->payment_date);
                @endphp
                <span class="finance-value {{ $paid ? 'finance-value-green' : 'finance-value-orange' }}">
                    {{ $paid ? 'Dibayar' : 'Belum dibayar' }}
                </span>
            </div>

            <div class="finance-item">
                <span class="finance-label">Pembayaran Terakhir</span>
                <span class="finance-value finance-value-blue">
                    @if($myHonorLatest)
                        {{ $formatPeriodRange($myHonorLatest) }}
                    @else
                        -
                    @endif
                </span>
            </div>

            <div class="finance-item">
                <span class="finance-label">Tanggal Pembayaran Terakhir</span>
                <span class="finance-value finance-value-blue">
                    {{ $myHonorLatest?->payment_date ? $myHonorLatest->payment_date->format('d/m/Y') : '-' }}
                </span>
            </div>
        </div>

        <div class="registration-detail-divider"></div>

        <div class="admin-section-card-header">
            <h3>Riwayat (6 Terakhir)</h3>
        </div>

        <div class="admin-finance-summary">
            @forelse($myHonorList as $row)
                <div class="finance-item">
                    <span class="finance-label">{{ $formatPeriodRange($row) }}</span>
                    <span class="finance-value {{ $row->payment_date ? 'finance-value-green' : 'finance-value-orange' }}">
                        {{ $formatRupiah($row->amount ?? 0) }}
                    </span>
                </div>
            @empty
                <p class="text-empty">Belum ada data honor.</p>
            @endforelse
        </div>
    @endif
</div>
