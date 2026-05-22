@php
    $source = $source ?? null;
    if (!$source) return;
@endphp

<dialog id="input-dana-modal-{{ $source->id }}" class="admin-modal" style="border:1px solid #ccc;border-radius:8px;padding:1.5rem;max-width:480px;width:90%;">
    <form method="POST" action="{{ route('admin.bendahara.fund.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id_fund_source" value="{{ $source->id }}">

        <h3 style="margin-top:0;">Input Dana — {{ $source->name }}</h3>

        @if($source->description)
            <p class="text-muted" style="font-size:.85rem;">{{ $source->description }}</p>
        @endif

        <div class="form-group">
            <label>Nominal (Rp)</label>
            <input type="number" name="amount" min="1" step="1" required class="form-input" placeholder="Contoh: 150000">
        </div>

        <div class="form-group">
            <label>Tanggal Transaksi</label>
            <input type="date" name="transaction_date" value="{{ now()->toDateString() }}" required class="form-input">
        </div>

        <div class="form-group">
            <label>Keterangan (opsional)</label>
            <textarea name="description" rows="2" class="form-input" placeholder="Mis: Transfer BOSDA Triwulan 2"></textarea>
        </div>

        <div class="form-group">
            <label>Upload Bukti (wajib)</label>
            <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png" required class="form-input">
            <span class="form-label-hint">PDF/JPG/PNG, max 2MB. Kuitansi atau bukti transfer.</span>
        </div>

        <div style="display:flex;gap:.5rem;justify-content:flex-end;margin-top:1rem;">
            <button type="button" onclick="document.getElementById('input-dana-modal-{{ $source->id }}').close()" class="btn btn-secondary">Batal</button>
            <button type="submit" class="btn btn-primary">SUBMIT</button>
        </div>
    </form>
</dialog>
