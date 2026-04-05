<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\PaymentType;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->middleware('auth');
        $this->paymentService = $paymentService;
    }

    /**
     * Show payment form
     */
    public function create(Student $student): View
    {
        // Verify student belongs to authenticated user
        if ($student->registration?->id_user !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Get registration payment type
        $paymentType = PaymentType::where('code', 'REGISTRATION')->first();

        // Generate fee breakdown
        $breakdown = $this->paymentService->generateFeeBreakdown($paymentType);

        // Check if payment already exists
        $payment = StudentPayment::where('id_student', $student->id_student)
            ->where('id_payment_type', $paymentType->id_payment_type)
            ->first();

        if ($payment && $payment->isPaid()) {
            return redirect()->route('payment.success', $payment->id_student_payment);
        }

        return view('payment.create', [
            'student' => $student,
            'paymentType' => $paymentType,
            'breakdown' => $breakdown,
            'payment' => $payment,
            'paymentMethods' => $this->paymentService->getPaymentMethods(),
        ]);
    }

    /**
     * Process payment submission
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_student' => 'required|exists:students,id_student',
            'payment_method' => 'required|in:transfer_bank,e_wallet,cash,qris',
            'proof_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        try {
            $student = Student::findOrFail($validated['id_student']);

            // Verify student belongs to authenticated user
            if ($student->registration?->id_user !== auth()->id()) {
                abort(403, 'Unauthorized');
            }

            // Get or create payment
            $paymentType = PaymentType::where('code', 'REGISTRATION')->first();
            $payment = StudentPayment::firstOrCreate(
                [
                    'id_student' => $student->id_student,
                    'id_payment_type' => $paymentType->id_payment_type,
                ],
                [
                    'payment_method' => $validated['payment_method'],
                    'status' => 'pending',
                ]
            );

            // Generate unique code for bank transfer
            if ($validated['payment_method'] === 'transfer_bank') {
                $uniqueCode = $this->paymentService->generateUniqueCode();
                $payment->unique_code = $uniqueCode;
                $payment->unique_code_valid_until = now()->addHour();
            }

            // Handle proof file upload
            if ($request->hasFile('proof_file')) {
                $path = $request->file('proof_file')->store('payment-proofs', 'public');
                $payment->proof_file = $path;
            }

            $payment->payment_method = $validated['payment_method'];
            $payment->save();

            // Create payment items
            $this->paymentService->createPaymentWithItems($payment);

            // Check if payment is late (after deadline)
            $registration = $student->registration;
            if ($registration && $registration->payment_deadline && now() > $registration->payment_deadline) {
                $payment->is_late = true;
                $student->paid_late = true;
                $student->save();
            }

            // Mark as paid if proof uploaded or QRIS
            if ($request->hasFile('proof_file') || $validated['payment_method'] === 'qris') {
                $payment->markAsPaid($validated['payment_method']);
                $student->registration->markAsActive();
                return redirect()->route('payment.success', $payment->id_student_payment);
            }

            return redirect()->route('payment.success', $payment->id_student_payment)
                ->with('info', 'Pembayaran Anda sedang diproses. Terima kasih!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Payment success page
     */
    public function success(StudentPayment $payment): View
    {
        // Verify ownership
        if ($payment->student->registration?->id_user !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('payment.success', [
            'payment' => $payment,
            'student' => $payment->student,
        ]);
    }

    /**
     * Payment failed page
     */
    public function failed(StudentPayment $payment): View
    {
        // Verify ownership
        if ($payment->student->registration?->id_user !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('payment.failed', [
            'payment' => $payment,
            'student' => $payment->student,
        ]);
    }

    /**
     * Download/view receipt/invoice
     */
    public function invoice(StudentPayment $payment): View
    {
        // Verify ownership
        if ($payment->student->registration?->id_user !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('payment.invoice', [
            'payment' => $payment,
            'student' => $payment->student,
            'items' => $payment->items,
        ]);
    }
}
