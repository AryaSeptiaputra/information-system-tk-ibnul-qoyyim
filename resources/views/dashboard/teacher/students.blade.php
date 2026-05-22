@extends('layouts.dashboard')

@section('title', 'Murid Kelas Saya - TK Ibnul Qoyyim')
@section('page_title', 'Murid Kelas Saya')

@php
    $tabs = $classes->map(fn ($c) => [
        'key' => (string) $c->id_class,
        'label' => $c->class_name . ' (' . $c->students_count . ')',
        'url' => route('admin.teacher.students', ['class' => $c->id_class]),
    ])->all();
@endphp

@section('content')

<x-ui.page-header title="Murid Kelas Saya" />

@if(session('success'))
    <x-ui.toast variant="success">{{ session('success') }}</x-ui.toast>
@endif

@if(!$teacher)
    <x-ui.empty-state message="Akun Anda belum tertaut ke data guru." />
@elseif($classes->isEmpty())
    <x-ui.empty-state icon="📭" message="Anda belum ditugaskan mengajar kelas manapun." />
@else
    @if(count($tabs) > 1)
        <x-ui.tab-bar :tabs="$tabs" :active="(string) $activeClassId" />
    @endif

    <div class="ui-toolbar">
        <form method="GET" action="{{ route('admin.teacher.students') }}" class="ui-toolbar__search">
            <input type="hidden" name="class" value="{{ $activeClassId }}">
            <input type="search" name="search" value="{{ $search }}" class="ui-input" placeholder="Cari murid...">
        </form>
    </div>

    @if($students->isEmpty())
        <x-ui.empty-state icon="📭" :message="$search !== '' ? 'Tidak ada murid yang cocok pencarian.' : 'Belum ada murid di kelas ini.'" />
    @else
        <div class="ui-table-wrapper">
            <table class="ui-table">
                <thead>
                    <tr>
                        <th>Nama Murid</th>
                        <th>Gender</th>
                        <th>Orangtua</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $s)
                        @php
                            $parentName = $s->parent
                                ? trim(($s->parent->father_name ?? '') . (($s->parent->father_name && $s->parent->mother_name) ? ' / ' : '') . ($s->parent->mother_name ?? ''))
                                : '-';
                            $statusBadge = match ($s->status ?? '') {
                                'aktif' => 'success',
                                'rejected' => 'danger',
                                default => 'neutral',
                            };
                        @endphp
                        <tr>
                            <td><strong>{{ $s->name ?? '-' }}</strong></td>
                            <td>{{ $s->gender ?? '-' }}</td>
                            <td style="color: var(--color-muted);">{{ $parentName ?: '-' }}</td>
                            <td><x-ui.badge :variant="$statusBadge">{{ ucfirst($s->status ?? '-') }}</x-ui.badge></td>
                            <td>
                                <button
                                    type="button"
                                    class="ui-btn ui-btn--ghost ui-btn--sm"
                                    onclick="openAbsenMurid({{ $s->id_student }}, '{{ addslashes($s->name ?? '-') }}')"
                                >
                                    Absen
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endif

<x-ui.modal name="absen-murid-modal" title="Absen Murid" maxWidth="560px">
    <div id="absen-murid-content">
        <p class="ui-stat-card__hint">Memuat...</p>
    </div>
</x-ui.modal>

<script>
    function openAbsenMurid(studentId, studentName) {
        window.loadFormIntoModal(
            '{{ route('admin.student-attendance.create') }}',
            'absen-murid-content',
            'absen-murid-modal',
            {
                onSuccess: () => window.location.reload(),
            }
        );

        // Setelah form di-inject: auto-check student, hide checkbox lain, isi tanggal hari ini.
        // Polling sebentar karena loadFormIntoModal async.
        const tryPreselect = (attempts = 0) => {
            const container = document.getElementById('absen-murid-content');
            const cb = container.querySelector(`input[name="id_student[]"][value="${studentId}"]`);
            if (!cb) {
                if (attempts < 20) setTimeout(() => tryPreselect(attempts + 1), 100);
                return;
            }

            // Check student yg dipilih
            cb.checked = true;

            // Sembunyikan checkbox lain (hanya tampil yang dipilih) + ganti hint
            const list = container.querySelector('.admin-checkbox-list');
            if (list) {
                list.querySelectorAll('.admin-checkbox-item').forEach(item => {
                    const input = item.querySelector('input[name="id_student[]"]');
                    if (input && input.value !== String(studentId)) {
                        item.style.display = 'none';
                    }
                });
            }
            const hint = container.querySelector('.form-label-hint');
            if (hint) {
                hint.textContent = `Absensi untuk: ${studentName}`;
            }

            // Auto-fill tanggal hari ini (YYYY-MM-DD)
            const dateInput = container.querySelector('input[name="date"]');
            if (dateInput && !dateInput.value) {
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                dateInput.value = `${yyyy}-${mm}-${dd}`;
            }
        };
        tryPreselect();
    }
</script>

@endsection
