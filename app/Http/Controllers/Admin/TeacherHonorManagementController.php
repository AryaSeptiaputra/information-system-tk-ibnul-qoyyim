<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherAttendance;
use App\Models\TeacherDetail;
use App\Models\TeacherHonor;
use Illuminate\Http\Request;

class TeacherHonorManagementController extends Controller
{
    public function attendanceSummary(Request $request)
    {
        $validated = $request->validate([
            'id_teacher' => ['required', 'integer', 'exists:teacher_details,id_teacher'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $rows = TeacherAttendance::query()
            ->selectRaw('status, COUNT(*) as total')
            ->where('id_teacher', (int) $validated['id_teacher'])
            ->whereMonth('date', (int) $validated['month'])
            ->whereYear('date', (int) $validated['year'])
            ->groupBy('status')
            ->pluck('total', 'status');

        $data = [
            'hadir' => (int) ($rows['hadir'] ?? 0),
            'izin' => (int) ($rows['izin'] ?? 0),
            'sakit' => (int) ($rows['sakit'] ?? 0),
            'alpa' => (int) ($rows['alpa'] ?? 0),
        ];

        $data['total'] = array_sum($data);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Display a listing of teacher honors with search and filters.
     */
    public function index(Request $request)
    {
        $query = TeacherHonor::query()->with(['teacher.user']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id_honors', 'like', "%{$search}%")
                    ->orWhere('month', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhereHas('teacher', function ($qt) use ($search) {
                        $qt->where('name', 'like', "%{$search}%")
                            ->orWhere('phone_num', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $status = $request->input('status');
            if ($status === 'paid') {
                $query->whereNotNull('payment_date');
            }
            if ($status === 'unpaid') {
                $query->whereNull('payment_date');
            }
        }

        if ($request->filled('month') && $request->input('month') !== 'all') {
            $query->where('month', (int)$request->input('month'));
        }

        if ($request->filled('year') && $request->input('year') !== 'all') {
            $query->where('year', (int)$request->input('year'));
        }

        $query->orderByDesc('year')->orderByDesc('month')->orderByDesc('created_at');

        $perPage = $request->input('per_page', 10);
        $honors = $query->paginate($perPage)->appends($request->query());

        $years = TeacherHonor::query()
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('dashboard.admin.teacher-honors', [
            'honors' => $honors,
            'years' => $years,
            'search' => $request->input('search', ''),
            'status' => $request->input('status', 'all'),
            'month' => $request->input('month', 'all'),
            'year' => $request->input('year', 'all'),
            'per_page' => $perPage,
        ]);
    }

    public function create()
    {
        $teachers = TeacherDetail::query()
            ->with(['user'])
            ->orderBy('name', 'asc')
            ->get(['id_teacher', 'id_user', 'name', 'status', 'phone_num', 'email']);

        return response()->json(['view' => view('components.dashboard.admin.modal-form', [
            'type' => 'teacher-honor',
            'action' => 'create',
            'teacherHonor' => null,
            'teachers' => $teachers,
        ])->render()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_teacher' => ['required', 'integer', 'exists:teacher_details,id_teacher'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'attendance_count' => ['nullable', 'integer', 'min:0'],
            'permission_count' => ['nullable', 'integer', 'min:0'],
            'sickness_count' => ['nullable', 'integer', 'min:0'],
            'absence_count' => ['nullable', 'integer', 'min:0'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'payment_date' => ['nullable', 'date'],
        ]);

        $validated['attendance_count'] = (int)($validated['attendance_count'] ?? 0);
        $validated['permission_count'] = (int)($validated['permission_count'] ?? 0);
        $validated['sickness_count'] = (int)($validated['sickness_count'] ?? 0);
        $validated['absence_count'] = (int)($validated['absence_count'] ?? 0);
        $validated['amount'] = (float)($validated['amount'] ?? 0);

        // Calculator mode: amount = hadir x honor_per_pertemuan (rate_per_meeting)
        $rate = $request->input('rate_per_meeting');
        if ($rate !== null && $rate !== '') {
            $validated['amount'] = max(0, (float) $validated['attendance_count'] * (float) $rate);
        }

        TeacherHonor::create($validated);

        return response()->json(['success' => true, 'message' => 'Honor guru berhasil ditambahkan']);
    }

    public function edit(TeacherHonor $teacherHonor)
    {
        $teacherHonor->loadMissing(['teacher.user']);

        $teachers = TeacherDetail::query()
            ->with(['user'])
            ->orderBy('name', 'asc')
            ->get(['id_teacher', 'id_user', 'name', 'status', 'phone_num', 'email']);

        return response()->json(['view' => view('components.dashboard.admin.modal-form', [
            'type' => 'teacher-honor',
            'action' => 'edit',
            'teacherHonor' => $teacherHonor,
            'teachers' => $teachers,
        ])->render()]);
    }

    public function show(TeacherHonor $teacherHonor)
    {
        $teacherHonor->loadMissing(['teacher.user']);

        return response()->json(['view' => view('components.dashboard.admin.modal-detail', [
            'type' => 'teacher-honor',
            'teacherHonor' => $teacherHonor,
            'teacher' => $teacherHonor->teacher,
        ])->render()]);
    }

    public function update(Request $request, TeacherHonor $teacherHonor)
    {
        $validated = $request->validate([
            'id_teacher' => ['required', 'integer', 'exists:teacher_details,id_teacher'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'attendance_count' => ['nullable', 'integer', 'min:0'],
            'permission_count' => ['nullable', 'integer', 'min:0'],
            'sickness_count' => ['nullable', 'integer', 'min:0'],
            'absence_count' => ['nullable', 'integer', 'min:0'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'payment_date' => ['nullable', 'date'],
        ]);

        $validated['attendance_count'] = (int)($validated['attendance_count'] ?? ($teacherHonor->attendance_count ?? 0));
        $validated['permission_count'] = (int)($validated['permission_count'] ?? ($teacherHonor->permission_count ?? 0));
        $validated['sickness_count'] = (int)($validated['sickness_count'] ?? ($teacherHonor->sickness_count ?? 0));
        $validated['absence_count'] = (int)($validated['absence_count'] ?? ($teacherHonor->absence_count ?? 0));
        $validated['amount'] = (float)($validated['amount'] ?? ($teacherHonor->amount ?? 0));

        $rate = $request->input('rate_per_meeting');
        if ($rate !== null && $rate !== '') {
            $validated['amount'] = max(0, (float) $validated['attendance_count'] * (float) $rate);
        }

        $teacherHonor->update($validated);

        return response()->json(['success' => true, 'message' => 'Honor guru berhasil diperbarui']);
    }

    public function export(Request $request)
    {
        $query = TeacherHonor::query()->with(['teacher.user']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('id_honors', 'like', "%{$search}%")
                    ->orWhere('month', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhereHas('teacher', function ($qt) use ($search) {
                        $qt->where('name', 'like', "%{$search}%")
                            ->orWhere('phone_num', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $status = $request->input('status');
            if ($status === 'paid') {
                $query->whereNotNull('payment_date');
            }
            if ($status === 'unpaid') {
                $query->whereNull('payment_date');
            }
        }

        if ($request->filled('month') && $request->input('month') !== 'all') {
            $query->where('month', (int)$request->input('month'));
        }

        if ($request->filled('year') && $request->input('year') !== 'all') {
            $query->where('year', (int)$request->input('year'));
        }

        $honors = $query->orderByDesc('year')->orderByDesc('month')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="teacher_honors_' . now()->format('Y-m-d_H-i-s') . '.csv"',
        ];

        $callback = function () use ($honors) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Guru',
                'Bulan',
                'Tahun',
                'Hadir',
                'Izin',
                'Sakit',
                'Alpa',
                'Nominal',
                'Payment Date',
                'Dibuat Tanggal',
            ]);

            foreach ($honors as $h) {
                $teacherName = $h->teacher?->name ?? '-';

                fputcsv($file, [
                    $h->id_honors,
                    $teacherName,
                    (int)($h->month ?? 0),
                    (int)($h->year ?? 0),
                    (int)($h->attendance_count ?? 0),
                    (int)($h->permission_count ?? 0),
                    (int)($h->sickness_count ?? 0),
                    (int)($h->absence_count ?? 0),
                    (string)($h->amount ?? 0),
                    $h->payment_date?->format('Y-m-d') ?? '-',
                    $h->created_at?->format('Y-m-d H:i:s') ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
