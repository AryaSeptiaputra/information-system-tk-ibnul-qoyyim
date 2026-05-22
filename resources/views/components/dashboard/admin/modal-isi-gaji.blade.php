@php
    $componentKey = $componentKey ?? null;
    $componentLabel = $componentLabel ?? 'Komponen';
    $currentAmount = $currentAmount ?? 0;
    $honorId = $honorId ?? null;

    if (!$componentKey || !$honorId) return;
@endphp

<dialog id="isi-gaji-modal-{{ $componentKey }}" class="admin-modal" style="border:1px solid #ccc;border-radius:8px;padding:1.5rem;max-width:380px;width:90%;">
    <form method="POST" action="{{ route('admin.bendahara.honors.update-component', $honorId) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="component" value="{{ $componentKey }}">

        <h3 style="margin-top:0;">Isi Gaji — {{ $componentLabel }}</h3>

        <div class="form-group">
            <label>Nominal (Rp)</label>
            <input
                type="number"
                name="amount"
                min="0"
                step="0.01"
                value="{{ (float) $currentAmount }}"
                required
                class="form-input"
                autofocus
            >
        </div>

        <div style="display:flex;gap:.5rem;justify-content:flex-end;margin-top:1rem;">
            <button type="button" onclick="document.getElementById('isi-gaji-modal-{{ $componentKey }}').close()" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn btn-primary">SUBMIT</button>
        </div>
    </form>
</dialog>
