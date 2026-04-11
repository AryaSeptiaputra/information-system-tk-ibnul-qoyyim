<!-- Dashboard Statistics Section - DEFAULT VIEW -->
<div id="admin-dashboard-stats" class="admin-section admin-section-active">
    <div class="admin-section-header">
        <h2>Dashboard Statistik</h2>
        <p class="admin-section-subtitle">Ringkasan data keseluruhan sistem</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid-admin">
        <!-- Total Users -->
        <div class="stat-card">
            <div class="stat-card-header stat-card-header-blue">
                <span class="stat-icon">👥</span>
            </div>
            <div class="stat-card-body">
                <h3 class="stat-label">Total Pengguna</h3>
                <p class="stat-value">{{ $stats['total_users'] ?? 0 }}</p>
                <p class="stat-subtext">{{ $stats['active_users'] ?? 0 }} aktif</p>
            </div>
        </div>

        <!-- Total Teachers -->
        <div class="stat-card">
            <div class="stat-card-header stat-card-header-green">
                <span class="stat-icon">👨‍🏫</span>
            </div>
            <div class="stat-card-body">
                <h3 class="stat-label">Total Guru</h3>
                <p class="stat-value">{{ $stats['total_teachers'] ?? 0 }}</p>
                <p class="stat-subtext">Pendidik aktif</p>
            </div>
        </div>

        <!-- Total Students -->
        <div class="stat-card">
            <div class="stat-card-header stat-card-header-yellow">
                <span class="stat-icon">👶</span>
            </div>
            <div class="stat-card-body">
                <h3 class="stat-label">Total Siswa</h3>
                <p class="stat-value">{{ $stats['total_students'] ?? 0 }}</p>
                <p class="stat-subtext">Terdaftar aktif</p>
            </div>
        </div>

        <!-- Total Classes -->
        <div class="stat-card">
            <div class="stat-card-header stat-card-header-purple">
                <span class="stat-icon">📚</span>
            </div>
            <div class="stat-card-body">
                <h3 class="stat-label">Total Kelas</h3>
                <p class="stat-value">{{ $stats['total_classes'] ?? 0 }}</p>
                <p class="stat-subtext">Kelas tersedia</p>
            </div>
        </div>

        <!-- Pending Registrations -->
        <div class="stat-card">
            <div class="stat-card-header stat-card-header-orange">
                <span class="stat-icon">⏳</span>
            </div>
            <div class="stat-card-body">
                <h3 class="stat-label">Pendaftaran Menunggu</h3>
                <p class="stat-value">{{ $stats['pending_registrations'] ?? 0 }}</p>
                <p class="stat-subtext">Perlu review</p>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="stat-card">
            <div class="stat-card-header stat-card-header-green-dark">
                <span class="stat-icon">💰</span>
            </div>
            <div class="stat-card-body">
                <h3 class="stat-label">Total Pendapatan</h3>
                <p class="stat-value">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</p>
                <p class="stat-subtext">Pembayaran sukses</p>
            </div>
        </div>
    </div>

    <!-- Recent Activities Section -->
    <div class="admin-section-split">
        <div class="admin-section-card">
            <div class="admin-section-card-header">
                <h3>Pendaftaran Terbaru</h3>
                <a href="#" class="text-link">Lihat Semua →</a>
            </div>
            <div class="admin-activities-list">
                @forelse($stats['recent_registrations'] ?? [] as $registration)
                    <div class="activity-item">
                        <div class="activity-icon">📝</div>
                        <div class="activity-content">
                            <p class="activity-title">Pendaftaran dari {{ $registration->candidate_data['name'] ?? '-' }}</p>
                            <p class="activity-time">{{ $registration->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="activity-badge">{{ strtoupper(str_replace('_', ' ', $registration->status)) }}</div>
                    </div>
                @empty
                    <p class="text-empty">Tidak ada pendaftaran terbaru</p>
                @endforelse
            </div>
        </div>

        <!-- Financial Overview -->
        <div class="admin-section-card">
            <div class="admin-section-card-header">
                <h3>Ringkasan Keuangan</h3>
            </div>
            <div class="admin-finance-summary">
                <div class="finance-item">
                    <span class="finance-label">Pembayaran Sukses</span>
                    <span class="finance-value finance-value-green">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="finance-item">
                    <span class="finance-label">Pembayaran Tertunggak</span>
                    <span class="finance-value finance-value-orange">Rp {{ number_format($stats['outstanding_payments'] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="finance-item">
                    <span class="finance-label">Registrasi Disetujui</span>
                    <span class="finance-value finance-value-blue">{{ $stats['approved_registrations'] ?? 0 }} siswa</span>
                </div>
            </div>
        </div>
    </div>
</div>
