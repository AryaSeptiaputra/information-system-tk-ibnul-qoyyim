<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\StudentPaymentInstallment;
use App\Models\PaymentProof;
use App\Models\PaymentMethod;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Show dashboard based on user role
     */
    public function index()
    {
        $user = Auth::user();

        $roleRaw = (string)($user?->role ?? '');
        $role = $roleRaw === 'super_admin' ? 'superadmin' : $roleRaw;

        // Staff roles use the staff portal (/admin). Keep /dashboard as guest portal.
        if (in_array($role, ['superadmin', 'administration', 'teacher', 'headmaster'], true)) {
            return redirect()->route('admin.dashboard');
        }
        
        $data = [
            'user' => $user,
            'role' => $role,
            'greeting' => $this->getGreeting($user),
            'stats' => $this->getStats($user),
            'activities' => $this->getActivities($user),
        ];

        // Prepare guest-specific data if user is guest
        if ($user->role === 'guest') {
            $data = array_merge($data, $this->buildGuestDashboardData($user->id));
        }

        return view('dashboard.index', $data);
    }

    public function guestInfo(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user || ($user->role ?? null) !== 'guest') {
            return redirect()->route('admin.dashboard');
        }

        $data = $this->buildGuestDashboardData($user->id);

        return view('dashboard.guest.info', $data);
    }

    public function guestBills(): View|RedirectResponse
    {
        $user = Auth::user();
        if (!$user || ($user->role ?? null) !== 'guest') {
            return redirect()->route('admin.dashboard');
        }

        $data = $this->buildGuestDashboardData($user->id);

        return view('dashboard.guest.bills', $data);
    }

    public function guestBillsPay(Request $request, StudentPayment $studentPayment): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || ($user->role ?? null) !== 'guest') {
            return redirect()->route('admin.dashboard');
        }

        $studentPayment->load(['student.registration', 'payment']);
        $ownerId = $studentPayment->student?->registration?->id_user;
        if ((int)$ownerId !== (int)$user->id) {
            abort(403, 'Unauthorized');
        }

        $regStatus = (string)($studentPayment->student?->registration?->status ?? '');
        $jenis = (string)($studentPayment->payment?->jenis_payment ?? '');
        if ($jenis === 'uang_pendaftaran') {
            if (!in_array($regStatus, ['approved_awaiting_payment', 'pending_due'], true)) {
                return redirect()->route('dashboard.bills')->with('error', 'Pembayaran uang pendaftaran hanya bisa dilakukan setelah pendaftaran di-approve (menunggu pembayaran).');
            }
        } else {
            if ($regStatus !== 'active') {
                return redirect()->route('dashboard.bills')->with('error', 'Pembayaran tagihan hanya bisa dilakukan setelah pendaftaran aktif.');
            }
        }

        if (($studentPayment->status ?? 'pending') === 'paid') {
            return redirect()->route('dashboard.bills')->with('error', 'Tagihan sudah Lunas.');
        }

        $hasPendingProof = PaymentProof::query()
            ->where('proofable_type', StudentPayment::class)
            ->where('proofable_id', (int)$studentPayment->id_student_payment)
            ->where('status', 'pending')
            ->exists();
        if ($hasPendingProof) {
            return redirect()->route('dashboard.bills')->with('error', 'Masih ada bukti pembayaran yang menunggu verifikasi admin. Silakan tunggu hasil verifikasi sebelum mengirim bukti baru.');
        }

        if ($studentPayment->installments()->exists()) {
            return redirect()->route('dashboard.bills')->with('error', 'Tagihan ini menggunakan cicilan. Silakan upload bukti pada cicilan yang tersedia.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:transfer_bank,e_wallet,cash,qris'],
            'payment_method_item_id' => ['nullable', 'integer'],
            'proof_file' => ['required', 'file', 'max:4096', 'mimes:jpg,jpeg,png,pdf'],
        ]);

        $methodSnapshot = [
            'payment_method_label' => null,
            'payment_method_account_number' => null,
            'payment_method_account_name' => null,
        ];

        if (Schema::hasTable('payment_methods') && in_array($validated['payment_method'], ['transfer_bank', 'e_wallet'], true)) {
            $requiredType = $validated['payment_method'] === 'transfer_bank' ? 'bank' : 'ewallet';
            $hasAny = PaymentMethod::query()->where('type', $requiredType)->where('is_active', true)->exists();

            $selectedId = (int)($validated['payment_method_item_id'] ?? 0);
            if ($hasAny && $selectedId <= 0) {
                return redirect()->route('dashboard.bills')->with('error', 'Silakan pilih ' . ($requiredType === 'bank' ? 'bank' : 'provider e-wallet') . ' tujuan.');
            }

            if ($selectedId > 0) {
                $pm = PaymentMethod::query()->where('id', $selectedId)->where('is_active', true)->first();
                if (!$pm || (string)($pm->type ?? '') !== $requiredType) {
                    return redirect()->route('dashboard.bills')->with('error', 'Pilihan metode pembayaran tidak valid.');
                }

                $methodSnapshot = [
                    'payment_method_label' => $pm->label ?? null,
                    'payment_method_account_number' => $pm->account_number ?? null,
                    'payment_method_account_name' => $pm->account_name ?? null,
                ];
            }
        }

        if ($request->hasFile('proof_file')) {
            $stored = $request->file('proof_file')->store('payment-proofs/guest/student-payments', 'public');

            $publicPath = 'storage/' . $stored;

            // Append proof history (do not delete/overwrite old proofs)
            $proofPayload = [
                'proofable_type' => StudentPayment::class,
                'proofable_id' => (int)$studentPayment->id_student_payment,
                'uploaded_by_user_id' => (int)$user->id,
                'payment_method' => $validated['payment_method'],
                'file_path' => $publicPath,
                'status' => 'pending',
            ];

            if (Schema::hasColumn('payment_proofs', 'payment_method_label')) {
                $proofPayload['payment_method_label'] = $methodSnapshot['payment_method_label'];
            }
            if (Schema::hasColumn('payment_proofs', 'payment_method_account_number')) {
                $proofPayload['payment_method_account_number'] = $methodSnapshot['payment_method_account_number'];
            }
            if (Schema::hasColumn('payment_proofs', 'payment_method_account_name')) {
                $proofPayload['payment_method_account_name'] = $methodSnapshot['payment_method_account_name'];
            }

            PaymentProof::create($proofPayload);

            $studentPayment->update([
                'payment_method' => $validated['payment_method'],
                // keep latest for backward compatibility
                'proof_file' => $publicPath,
                // If previously failed, allow re-verification.
                'status' => ($studentPayment->status ?? 'pending') === 'failed' ? 'pending' : ($studentPayment->status ?? 'pending'),
            ]);
        }

        return redirect()->route('dashboard.bills')->with('success', 'Bukti pembayaran berhasil dikirim. Menunggu verifikasi admin.');
    }

    public function guestBillsInstallmentPay(Request $request, StudentPayment $studentPayment, StudentPaymentInstallment $installment): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || ($user->role ?? null) !== 'guest') {
            return redirect()->route('admin.dashboard');
        }

        if ((int)($installment->id_student_payment ?? 0) !== (int)($studentPayment->id_student_payment ?? 0)) {
            abort(404);
        }

        $studentPayment->load(['student.registration', 'payment']);
        $ownerId = $studentPayment->student?->registration?->id_user;
        if ((int)$ownerId !== (int)$user->id) {
            abort(403, 'Unauthorized');
        }

        $regStatus = (string)($studentPayment->student?->registration?->status ?? '');
        if ($regStatus !== 'active') {
            return redirect()->route('dashboard.bills')->with('error', 'Pembayaran tagihan hanya bisa dilakukan setelah pendaftaran aktif.');
        }

        if (($installment->status ?? 'pending') === 'paid') {
            return redirect()->route('dashboard.bills')->with('error', 'Cicilan ini sudah Lunas.');
        }

        $hasPendingProof = PaymentProof::query()
            ->where('proofable_type', StudentPaymentInstallment::class)
            ->where('proofable_id', (int)$installment->id_student_payment_installment)
            ->where('status', 'pending')
            ->exists();
        if ($hasPendingProof) {
            return redirect()->route('dashboard.bills')->with('error', 'Masih ada bukti cicilan yang menunggu verifikasi admin. Silakan tunggu hasil verifikasi sebelum mengirim bukti baru.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'in:transfer_bank,e_wallet,cash,qris'],
            'payment_method_item_id' => ['nullable', 'integer'],
            'proof_file' => ['required', 'file', 'max:4096', 'mimes:jpg,jpeg,png,pdf'],
        ]);

        $methodSnapshot = [
            'payment_method_label' => null,
            'payment_method_account_number' => null,
            'payment_method_account_name' => null,
        ];

        if (Schema::hasTable('payment_methods') && in_array($validated['payment_method'], ['transfer_bank', 'e_wallet'], true)) {
            $requiredType = $validated['payment_method'] === 'transfer_bank' ? 'bank' : 'ewallet';
            $hasAny = PaymentMethod::query()->where('type', $requiredType)->where('is_active', true)->exists();

            $selectedId = (int)($validated['payment_method_item_id'] ?? 0);
            if ($hasAny && $selectedId <= 0) {
                return redirect()->route('dashboard.bills')->with('error', 'Silakan pilih ' . ($requiredType === 'bank' ? 'bank' : 'provider e-wallet') . ' tujuan.');
            }

            if ($selectedId > 0) {
                $pm = PaymentMethod::query()->where('id', $selectedId)->where('is_active', true)->first();
                if (!$pm || (string)($pm->type ?? '') !== $requiredType) {
                    return redirect()->route('dashboard.bills')->with('error', 'Pilihan metode pembayaran tidak valid.');
                }

                $methodSnapshot = [
                    'payment_method_label' => $pm->label ?? null,
                    'payment_method_account_number' => $pm->account_number ?? null,
                    'payment_method_account_name' => $pm->account_name ?? null,
                ];
            }
        }

        if ($request->hasFile('proof_file')) {
            $stored = $request->file('proof_file')->store('payment-proofs/guest/student-payment-installments', 'public');

            $publicPath = 'storage/' . $stored;

            $proofPayload = [
                'proofable_type' => StudentPaymentInstallment::class,
                'proofable_id' => (int)$installment->id_student_payment_installment,
                'uploaded_by_user_id' => (int)$user->id,
                'payment_method' => $validated['payment_method'],
                'file_path' => $publicPath,
                'status' => 'pending',
            ];

            if (Schema::hasColumn('payment_proofs', 'payment_method_label')) {
                $proofPayload['payment_method_label'] = $methodSnapshot['payment_method_label'];
            }
            if (Schema::hasColumn('payment_proofs', 'payment_method_account_number')) {
                $proofPayload['payment_method_account_number'] = $methodSnapshot['payment_method_account_number'];
            }
            if (Schema::hasColumn('payment_proofs', 'payment_method_account_name')) {
                $proofPayload['payment_method_account_name'] = $methodSnapshot['payment_method_account_name'];
            }

            PaymentProof::create($proofPayload);

            $installment->update([
                'payment_method' => $validated['payment_method'],
                // keep latest for backward compatibility
                'proof_file' => $publicPath,
            ]);
        }

        return redirect()->route('dashboard.bills')->with('success', 'Bukti pembayaran cicilan berhasil dikirim. Menunggu verifikasi admin.');
    }

    private function deleteLocalProofFile(?string $proofPath): void
    {
        $path = trim((string)($proofPath ?? ''));
        if ($path === '') return;

        // We store files as "storage/<path>" for public access.
        if (!str_starts_with($path, 'storage/')) {
            return;
        }

        $relative = substr($path, strlen('storage/'));
        if ($relative === '') return;

        try {
            Storage::disk('public')->delete($relative);
        } catch (\Throwable $_) {
            // ignore
        }
    }

    private function buildGuestDashboardData(int $userId): array
    {
        $data = [];
        $data['currentStep'] = session('current_step', 1);

        $approvedRegistration = Registration::where('id_user', $userId)
            ->where('status', 'active')
            ->latest('id_registration')
            ->first();

        $pendingRegistration = Registration::where('id_user', $userId)
            ->whereIn('status', ['pending', 'rejected', 'approved_awaiting_payment', 'pending_due'])
            ->latest('id_registration')
            ->first();

        $data['approvedRegistration'] = $approvedRegistration;
        $data['pendingRegistration'] = $pendingRegistration;
        $data['studentInfo'] = null;
        $data['hasChild'] = false;
        $data['hasStudent'] = false;
        $data['studentPayments'] = collect();
        $data['paymentMethods'] = Schema::hasTable('payment_methods')
            ? PaymentMethod::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get()
            : collect();

        // Legacy fallback (single row), kept for backward compatibility.
        $data['paymentSettings'] = Schema::hasTable('payment_settings')
            ? PaymentSetting::query()->first()
            : null;

        // If approved, get student information
        if ($approvedRegistration) {
            $student = Student::where('id_registration', $approvedRegistration->id_registration)->first();

            if ($student) {
                $genderValue = $student->gender ?? null;
                $genderNormalized = $genderValue === 'pria'
                    ? 'male'
                    : ($genderValue === 'perempuan' ? 'female' : $genderValue);

                $statusValue = $student->status ?? null;
                $statusNormalized = $statusValue === 'aktif'
                    ? 'active'
                    : ($statusValue === 'non-aktif' ? 'inactive' : $statusValue);

                $data['studentInfo'] = [
                    'id_student' => $student->id_student,
                    'name' => $student->name,
                    'birth_date' => $student->birth_date,
                    'gender' => $genderNormalized,
                    'group' => $approvedRegistration->group,
                    'status' => $statusNormalized,
                ];
                $data['hasChild'] = true;
            }
        }

        // Load student's bills (tagihan) for this user.
        // Prefer the approved registration; otherwise fall back to latest pending/rejected registration.
        $registrationForStudent = $approvedRegistration ?? $pendingRegistration;
        if ($registrationForStudent) {
            $student = Student::where('id_registration', $registrationForStudent->id_registration)->first();
            if ($student) {
                $data['hasStudent'] = true;
                $data['studentPayments'] = StudentPayment::query()
                    ->where('id_student', $student->id_student)
                    ->with([
                        'payment',
                        'proofs' => fn($q) => $q->latest('id_payment_proof'),
                        'installments' => fn($q) => $q->orderBy('installment_number'),
                        'installments.proofs' => fn($q) => $q->latest('id_payment_proof'),
                    ])
                    ->orderByDesc('created_at')
                    ->get();
            }
        }

        return $data;
    }

    /**
     * Get greeting based on time
     */
    private function getGreeting($user)
    {
        $hour = now()->hour;
        
        if ($hour < 12) {
            return 'Selamat Pagi';
        } elseif ($hour < 17) {
            return 'Selamat Siang';
        } else {
            return 'Selamat Malam';
        }
    }

    /**
     * Get statistics based on user role
     */
    private function getStats($user)
    {
        $stats = [];

        switch ($user->role) {
            case 'superadmin':
                $stats = [
                    ['icon' => '👥', 'value' => '5', 'label' => 'Total Users'],
                    ['icon' => '👨‍🏫', 'value' => '10', 'label' => 'Total Teachers'],
                    ['icon' => '📚', 'value' => '15', 'label' => 'Total Classes'],
                    ['icon' => '📊', 'value' => '95%', 'label' => 'System Health'],
                ];
                break;

            case 'administration':
                $stats = [
                    ['icon' => '📝', 'value' => '12', 'label' => 'Pending Registrations'],
                    ['icon' => '✅', 'value' => '8', 'label' => 'Approved Today'],
                    ['icon' => '👨‍🎓', 'value' => '25', 'label' => 'New Students'],
                    ['icon' => '⚠️', 'value' => '3', 'label' => 'Incomplete Applications'],
                ];
                break;

            case 'teacher':
                $stats = [
                    ['icon' => '👥', 'value' => '28', 'label' => 'Jumlah Siswa'],
                    ['icon' => '📚', 'value' => '5', 'label' => 'Kelas Diajar'],
                    ['icon' => '📝', 'value' => '12', 'label' => 'Tugas Diberikan'],
                    ['icon' => '⭐', 'value' => '4.8', 'label' => 'Rating'],
                ];
                break;

            case 'headmaster':
                $stats = [
                    ['icon' => '👥', 'value' => '285', 'label' => 'Total Siswa'],
                    ['icon' => '👨‍🏫', 'value' => '22', 'label' => 'Total Guru'],
                    ['icon' => '📚', 'value' => '12', 'label' => 'Total Kelas'],
                    ['icon' => '📊', 'value' => '98%', 'label' => 'Tingkat Kehadiran'],
                ];
                break;

            default: // guest
                $stats = [
                    ['icon' => '📚', 'value' => 'TK', 'label' => 'Jenis Sekolah'],
                    ['icon' => '🏆', 'value' => '12+', 'label' => 'Tahun Berpengalaman'],
                    ['icon' => '👥', 'value' => '300+', 'label' => 'Alumni Sukses'],
                    ['icon' => '⭐', 'value' => '5/5', 'label' => 'Rating'],
                ];
                break;
        }

        return $stats;
    }

    /**
     * Get activities/todos based on role
     */
    private function getActivities($user)
    {
        $activities = [];

        switch ($user->role) {
            case 'superadmin':
                $activities = [
                    ['icon' => '🔧', 'text' => 'Check system logs', 'time' => '1 jam lalu'],
                    ['icon' => '👤', 'text' => 'Add new administrator', 'time' => '3 jam lalu'],
                    ['icon' => '🔐', 'text' => 'System backup completed', 'time' => '6 jam lalu'],
                ];
                break;

            case 'administration':
                $activities = [
                    ['icon' => '✍️', 'text' => 'Review 3 new registrations', 'time' => 'pending'],
                    ['icon' => '✅', 'text' => 'Approve student documents', 'time' => '1 jam lalu'],
                    ['icon' => '📧', 'text' => 'Send notification to parents', 'time' => '2 jam lalu'],
                ];
                break;

            case 'teacher':
                $activities = [
                    ['icon' => '📝', 'text' => 'Grade assignments - Class A', 'time' => 'pending'],
                    ['icon' => '📢', 'text' => 'Post class announcement', 'time' => '1 jam lalu'],
                    ['icon' => '👥', 'text' => 'Update attendance report', 'time' => '2 jam lalu'],
                ];
                break;

            case 'headmaster':
                $activities = [
                    ['icon' => '📊', 'text' => 'Review monthly report', 'time' => 'pending'],
                    ['icon' => '👥', 'text' => 'Schedule staff meeting', 'time' => '1 jam lalu'],
                    ['icon' => '📋', 'text' => 'Approve budget allocation', 'time' => '3 jam lalu'],
                ];
                break;

            default: // guest
                $activities = [
                    ['icon' => '📖', 'text' => 'Pelajari program sekolah', 'time' => 'tersedia'],
                    ['icon' => '📞', 'text' => 'Hubungi pihak sekolah', 'time' => 'tersedia'],
                    ['icon' => '📝', 'text' => 'Daftar siswa baru', 'time' => 'buka hingga 30 April'],
                ];
                break;
        }

        return $activities;
    }
}
