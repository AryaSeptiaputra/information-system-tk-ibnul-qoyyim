<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Holiday::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('year') && $request->input('year') !== 'all') {
            $year = (int) $request->input('year');
            $query->whereYear('date', $year);
        }

        $perPage = (int) $request->input('per_page', 15);
        if (!in_array($perPage, [10, 15, 25, 50], true)) {
            $perPage = 15;
        }

        $holidays = $query
            ->orderBy('date')
            ->paginate($perPage)
            ->appends($request->query());

        // Tahun yang tersedia untuk filter
        $years = Holiday::query()
            ->selectRaw('DISTINCT YEAR(date) AS year')
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->all();

        return view('dashboard.admin.holidays', [
            'holidays' => $holidays,
            'search' => $request->input('search', ''),
            'year' => $request->input('year', 'all'),
            'years' => $years,
            'per_page' => $perPage,
        ]);
    }

    public function create()
    {
        return response()->json(['view' => view('components.dashboard.admin.holiday-form', [
            'action' => 'create',
            'holiday' => null,
        ])->render()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'unique:holidays,date'],
            'name' => ['required', 'string', 'max:150'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);

        Holiday::create($validated);

        return response()->json(['success' => true, 'message' => 'Hari libur berhasil ditambahkan']);
    }

    public function edit(Holiday $holiday)
    {
        return response()->json(['view' => view('components.dashboard.admin.holiday-form', [
            'action' => 'edit',
            'holiday' => $holiday,
        ])->render()]);
    }

    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'unique:holidays,date,' . $holiday->id],
            'name' => ['required', 'string', 'max:150'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);

        $holiday->update($validated);

        return response()->json(['success' => true, 'message' => 'Hari libur berhasil diperbarui']);
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return response()->json(['success' => true, 'message' => 'Hari libur berhasil dihapus']);
    }
}
