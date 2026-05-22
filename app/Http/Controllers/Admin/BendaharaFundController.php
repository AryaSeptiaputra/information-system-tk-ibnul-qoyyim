<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundSource;
use App\Models\FundTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BendaharaFundController extends Controller
{
    /**
     * Catat dana masuk manual ke salah satu fund_source.
     * Upload bukti WAJIB (kuitansi/transfer).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_fund_source' => ['required', 'integer', 'exists:fund_sources,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:500'],
            'attachment' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $source = FundSource::query()->find($validated['id_fund_source']);
        if (!$source || !$source->is_active) {
            throw ValidationException::withMessages([
                'id_fund_source' => ['Sumber dana tidak ditemukan atau nonaktif.'],
            ]);
        }

        $stored = $request->file('attachment')->store('fund-receipts', 'public');
        $attachmentPath = 'storage/' . $stored;

        FundTransaction::create([
            'id_fund_source' => (int) $source->id,
            'direction' => 'in',
            'amount' => (float) $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'description' => $validated['description'] ?? "Setoran manual ke {$source->name}",
            'attachment_path' => $attachmentPath,
            'reference_type' => 'manual',
            'reference_id' => null,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.bendahara.dashboard')
            ->with('success', "Dana sebesar Rp " . number_format((float) $validated['amount'], 0, ',', '.') . " masuk ke {$source->name}.");
    }
}
