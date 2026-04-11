<!-- Section Header with Search & Filter -->
@php
    // Determine section type for proper labels
    $sectionName = match($type) {
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

    $currentRole = auth()->user()?->role ?? 'guest';

    // Centralized control: which roles can add/create for each section type.
    $canAdd = match ($type) {
        'user' => $currentRole === 'superadmin',
        'student-attendance', 'teacher-attendance' => in_array($currentRole, ['superadmin', 'administration', 'teacher'], true),
        default => in_array($currentRole, ['superadmin', 'administration'], true),
    };
@endphp

<div class="admin-section-control">
    <!-- Top Row: Export + Add (below divider line) -->
    <div class="admin-section-actions-top">
        <a href="{{ $exportUrl ?? '#' }}" class="admin-btn admin-btn-export" title="Download Excel">
            <span>📥 Download Excel</span>
        </a>
        @if($canAdd)
            <button type="button" class="admin-btn admin-btn-add" data-modal-open="add-{{ $type }}-modal" title="Tambah {{ $sectionName }}">
                <span>➕ Tambah {{ $sectionName }}</span>
            </button>
        @endif
    </div>

    <!-- Bottom Row: Search + Filter + Search/Reset (right above table) -->
    <form method="GET" class="admin-search-filter-form">
        <div class="admin-control-left">
            <!-- Search Input -->
            <div class="admin-search-wrapper">
                <input 
                    type="text" 
                    name="search" 
                    class="admin-search-input" 
                    placeholder="Cari {{ strtolower($sectionName) }}..."
                    value="{{ $search ?? '' }}"
                >
                <span class="admin-search-icon">🔍</span>
            </div>

            <!-- Filter Dropdowns -->
            <div class="admin-filter-group">
            @if($type === 'user')
                <!-- Role Filter -->
                <select name="role" class="admin-filter-select">
                    <option value="all">Semua Peran</option>
                    <option value="superadmin" @selected(($role ?? 'all') === 'superadmin')>Super Admin</option>
                    <option value="headmaster" @selected(($role ?? 'all') === 'headmaster')>Kepala Sekolah</option>
                    <option value="administration" @selected(($role ?? 'all') === 'administration')>Administrasi</option>
                    <option value="teacher" @selected(($role ?? 'all') === 'teacher')>Guru</option>
                    <option value="guest" @selected(($role ?? 'all') === 'guest')>Orang Tua</option>
                </select>

                <!-- Status Filter -->
                <select name="status" class="admin-filter-select">
                    <option value="all">Semua Status</option>
                    <option value="active" @selected(($status ?? 'all') === 'active')>Aktif</option>
                    <option value="inactive" @selected(($status ?? 'all') === 'inactive')>Nonaktif</option>
                </select>
            @endif

            @if($type === 'teacher')
                <select name="status" class="admin-filter-select">
                    <option value="all">Semua Status</option>
                    <option value="active" @selected(($status ?? 'all') === 'active')>Aktif</option>
                    <option value="inactive" @selected(($status ?? 'all') === 'inactive')>Nonaktif</option>
                </select>
            @endif

            @if($type === 'teacher-honor')
                <select name="status" class="admin-filter-select">
                    <option value="all">Semua</option>
                    <option value="paid" @selected(($status ?? 'all') === 'paid')>Sudah Dibayar</option>
                    <option value="unpaid" @selected(($status ?? 'all') === 'unpaid')>Belum Dibayar</option>
                </select>

                <select name="month" class="admin-filter-select">
                    <option value="all">Semua Bulan</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" @selected((string)($month ?? 'all') === (string)$m)>
                            {{ $m }}
                        </option>
                    @endfor
                </select>

                <select name="year" class="admin-filter-select">
                    <option value="all">Semua Tahun</option>
                    @foreach(($years ?? []) as $yr)
                        <option value="{{ $yr }}" @selected((string)($year ?? 'all') === (string)$yr)>
                            {{ $yr }}
                        </option>
                    @endforeach
                </select>
            @endif

            @if($type === 'facility')
                <select name="status" class="admin-filter-select">
                    <option value="all">Semua Status</option>
                    <option value="active" @selected(($status ?? 'all') === 'active')>Aktif</option>
                    <option value="inactive" @selected(($status ?? 'all') === 'inactive')>Nonaktif</option>
                </select>
            @endif

            @if($type === 'payment')
                <select name="period_mode" class="admin-filter-select">
                    <option value="all">Semua Mode Periode</option>
                    <option value="one_time" @selected(($period_mode ?? 'all') === 'one_time')>Sekali Bayar</option>
                    <option value="monthly" @selected(($period_mode ?? 'all') === 'monthly')>Bulanan</option>
                    <option value="school_year" @selected(($period_mode ?? 'all') === 'school_year')>Tahun Ajaran</option>
                </select>

                <select name="status" class="admin-filter-select">
                    <option value="all">Semua Status</option>
                    <option value="active" @selected(($status ?? 'all') === 'active')>Aktif</option>
                    <option value="inactive" @selected(($status ?? 'all') === 'inactive')>Nonaktif</option>
                </select>
            @endif

            @if($type === 'student-payment')
                <select name="status" class="admin-filter-select">
                    <option value="all">Semua Status</option>
                    <option value="pending" @selected(($status ?? 'all') === 'pending')>Pending</option>
                    <option value="paid" @selected(($status ?? 'all') === 'paid')>Paid</option>
                    <option value="failed" @selected(($status ?? 'all') === 'failed')>Failed</option>
                </select>

                <select name="id_payment" class="admin-filter-select">
                    <option value="all">Semua Payment</option>
                    @foreach(($payments ?? []) as $p)
                        <option value="{{ $p->id_payment }}" @selected((string)($id_payment ?? 'all') === (string)$p->id_payment)>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            @if($type === 'registration')
                <select name="status" class="admin-filter-select">
                    <option value="all">Semua Status</option>
                    <option value="pending" @selected(($status ?? 'all') === 'pending')>Pending</option>
                    <option value="active" @selected(($status ?? 'all') === 'active')>Active</option>
                    <option value="rejected" @selected(($status ?? 'all') === 'rejected')>Rejected</option>
                </select>

                <select name="group" class="admin-filter-select">
                    <option value="all">Semua Grup</option>
                    <option value="A" @selected(($group ?? 'all') === 'A')>Grup A</option>
                    <option value="B" @selected(($group ?? 'all') === 'B')>Grup B</option>
                </select>
            @endif

            @if($type === 'student')
                <select name="status" class="admin-filter-select">
                    <option value="all">Semua Status</option>
                    <option value="pending_payment" @selected(($status ?? 'all') === 'pending_payment')>Belum Aktif</option>
                    <option value="aktif" @selected(($status ?? 'all') === 'aktif')>Aktif</option>
                    <option value="non-aktif" @selected(($status ?? 'all') === 'non-aktif')>Nonaktif</option>
                    <option value="lulus" @selected(($status ?? 'all') === 'lulus')>Lulus</option>
                    <option value="pindah" @selected(($status ?? 'all') === 'pindah')>Pindah</option>
                    <option value="rejected" @selected(($status ?? 'all') === 'rejected')>Rejected</option>
                </select>

                <select name="group" class="admin-filter-select">
                    <option value="all">Semua Grup</option>
                    <option value="A" @selected(($group ?? 'all') === 'A')>Grup A</option>
                    <option value="B" @selected(($group ?? 'all') === 'B')>Grup B</option>
                </select>

                <select name="gender" class="admin-filter-select">
                    <option value="all">Semua Gender</option>
                    <option value="pria" @selected(($gender ?? 'all') === 'pria')>Pria</option>
                    <option value="perempuan" @selected(($gender ?? 'all') === 'perempuan')>Perempuan</option>
                </select>
            @endif

            @if($type === 'parent')
                <select name="contact" class="admin-filter-select">
                    <option value="all">Semua Kontak</option>
                    <option value="has_contact" @selected(($contact ?? 'all') === 'has_contact')>Ada Kontak</option>
                    <option value="no_contact" @selected(($contact ?? 'all') === 'no_contact')>Tidak Ada Kontak</option>
                </select>
            @endif

            @if($type === 'class')
                <select name="school_year" class="admin-filter-select">
                    <option value="all">Semua Tahun Ajaran</option>
                    @foreach(($schoolYears ?? []) as $yr)
                        <option value="{{ $yr }}" @selected((string)($school_year ?? 'all') === (string)$yr)>
                            {{ $yr }}
                        </option>
                    @endforeach
                </select>
            @endif

            @if(in_array($type, ['student-attendance', 'teacher-attendance'], true))
                <select name="status" class="admin-filter-select">
                    <option value="all">Semua Status</option>
                    <option value="hadir" @selected(($status ?? 'all') === 'hadir')>Hadir</option>
                    <option value="izin" @selected(($status ?? 'all') === 'izin')>Izin</option>
                    <option value="sakit" @selected(($status ?? 'all') === 'sakit')>Sakit</option>
                    <option value="alpa" @selected(($status ?? 'all') === 'alpa')>Alpa</option>
                </select>

                @if($type === 'student-attendance')
                    <select name="id_class" class="admin-filter-select">
                        <option value="all">Semua Kelas</option>
                        @foreach(($classes ?? []) as $c)
                            @php
                                $classLabel = ($c->class_name ?? '-') . (($c->school_year ?? null) ? ' (' . $c->school_year . ')' : '');
                            @endphp
                            <option value="{{ $c->id_class }}" @selected((string)($id_class ?? 'all') === (string)$c->id_class)>
                                {{ $classLabel }}
                            </option>
                        @endforeach
                    </select>
                @endif

                <input type="date" name="date_from" class="admin-filter-select" value="{{ $date_from ?? '' }}" title="Dari Tanggal">
                <input type="date" name="date_to" class="admin-filter-select" value="{{ $date_to ?? '' }}" title="Sampai Tanggal">
            @endif
            </div>
        </div>

        <div class="admin-action-buttons">
            <button type="submit" class="admin-btn admin-btn-search">
                <span>🔍 Cari</span>
            </button>
            <a href="{{ $resetUrl ?? '#' }}" class="admin-btn admin-btn-reset">Reset</a>
        </div>
    </form>
</div>
