@php
    $action = $action ?? 'create';
    $holiday = $holiday ?? null;
    $isEdit = $action === 'edit' && $holiday;
    $formAction = $isEdit
        ? route('admin.holidays.update', $holiday)
        : route('admin.holidays.store');
@endphp

{{-- Form ini di-load via window.loadFormIntoModal — submit handler auto-attached.
     Pakai class admin-modal-form supaya kompatibel dengan utility extract form. --}}
<form id="admin-form-holiday-{{ $isEdit ? 'edit' : 'create' }}" class="admin-modal-form" method="POST" action="{{ $formAction }}">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="ui-form-group">
        <label class="ui-form-label">Tanggal *</label>
        <input
            type="date"
            name="date"
            value="{{ $holiday?->date?->format('Y-m-d') ?? old('date') }}"
            required
            class="ui-input"
        >
    </div>

    <div class="ui-form-group">
        <label class="ui-form-label">Nama Hari Libur *</label>
        <input
            type="text"
            name="name"
            value="{{ $holiday?->name ?? old('name') }}"
            required
            maxlength="150"
            class="ui-input"
            placeholder="Contoh: Hari Raya Idul Fitri"
        >
    </div>

    <div class="ui-form-group">
        <label class="ui-form-label">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked($holiday?->is_active ?? true)>
            Aktif (hitung sebagai kredit libur untuk honor guru)
        </label>
    </div>

    <div style="display: flex; gap: var(--ui-space-sm); justify-content: flex-end; margin-top: var(--ui-space-md);">
        <button type="button" class="ui-btn ui-btn--secondary" @click="open = false">Batal</button>
        <button type="submit" class="ui-btn ui-btn--primary">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah' }}</button>
    </div>
</form>
