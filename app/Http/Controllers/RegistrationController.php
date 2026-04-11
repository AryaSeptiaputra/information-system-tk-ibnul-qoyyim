<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RegistrationController extends Controller
{
    /**
     * Ensure user is authenticated
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the registration form
     */
    public function create(): RedirectResponse
    {
        return redirect()->to(route('dashboard') . '#registration-form');
    }

    /**
     * Store registration data with multi-step navigation
     */
    public function store(Request $request): RedirectResponse
    {
        $currentStep = (int)$request->input('current_step', 1);
        if ($currentStep < 1) {
            $currentStep = 1;
        }

        // next_step can be present but empty (""), especially on the final submit button.
        // Casting "" to int yields 0, which breaks the step flow and prevents submission.
        $nextStepRaw = $request->input('next_step');
        $nextStep = null;
        if (is_numeric($nextStepRaw)) {
            $nextStep = (int)$nextStepRaw;
        }
        if (!$nextStep || $nextStep < 1) {
            $nextStep = $currentStep + 1;
        }

        // Validate current step data
        $this->validateStep($request, $currentStep);

        // Store data in session based on current step
        if ($currentStep == 1) {
            session([
                'registration.candidate_data' => $request->input('candidate_data'),
                'registration.group' => $request->input('group'),
            ]);
        } elseif ($currentStep == 2) {
            session([
                'registration.parents_data' => $request->input('parents_data'),
            ]);
        } elseif ($currentStep == 3 && $nextStep > $currentStep) {
            // Submit registration
            return $this->submitRegistration($request);
        }

        // Update session step (keep in range 1..3 to avoid blank form rendering)
        $nextStepClamped = $nextStep;
        if ($nextStepClamped < 1) {
            $nextStepClamped = 1;
        }
        if ($nextStepClamped > 3) {
            $nextStepClamped = 3;
        }
        session(['current_step' => $nextStepClamped]);

        return redirect()->to(route('dashboard') . '#registration-form');
    }

    /**
     * Validate form data based on step
     */
    private function validateStep(Request $request, int $step): void
    {
        if ($step == 1) {
            $request->validate([
                'candidate_data.name' => 'required|string|max:255',
                'candidate_data.birth_place' => 'required|string|max:255',
                'candidate_data.birth_date' => 'required|date',
                'candidate_data.gender' => 'required|in:pria,perempuan',
                'group' => 'required|in:A,B',
            ]);
        } elseif ($step == 2) {
            $request->validate([
                'parents_data.father_name' => 'required|string|max:255',
                'parents_data.father_phone' => 'required|string|max:20',
                'parents_data.mother_name' => 'required|string|max:255',
                'parents_data.mother_phone' => 'required|string|max:20',
            ]);
        }
    }

    /**
     * Submit the complete registration
     */
    private function submitRegistration(Request $request): RedirectResponse
    {
        try {
            $candidateData = session('registration.candidate_data');
            $parentsData = session('registration.parents_data');
            $group = session('registration.group');

            if (!$candidateData || !$parentsData || !$group) {
                return redirect()->to(route('dashboard') . '#registration-form')
                    ->with('error', 'Data pendaftaran belum lengkap. Silakan lengkapi semua langkah.');
            }

            // Check if user already has a pending/active registration
            $existingRegistration = Registration::where('id_user', auth()->id())
                ->whereIn('status', ['pending', 'approved_awaiting_payment', 'pending_due', 'active'])
                ->first();
            
            if ($existingRegistration) {
                session()->forget(['registration', 'current_step']);
                return redirect()->route('dashboard')
                    ->with('error', 'Anda sudah memiliki pendaftaran yang aktif.');
            }

            DB::transaction(function () use ($candidateData, $parentsData, $group) {
                $registration = Registration::create([
                    'id_user' => auth()->id(),
                    'candidate_data' => $candidateData,
                    'parents_data' => $parentsData,
                    'group' => $group,
                    'status' => 'pending',
                ]);

                Student::create([
                    'id_registration' => $registration->id_registration,
                    'name' => $candidateData['name'],
                    'birth_place' => $candidateData['birth_place'],
                    'birth_date' => $candidateData['birth_date'],
                    'gender' => $candidateData['gender'],
                    'group' => $group,
                    'status' => 'pending_payment',
                ]);
            });

            // Clear session
            session()->forget(['registration', 'current_step']);

            return redirect()->route('dashboard')
                ->with('success', 'Pendaftaran berhasil! Admin akan segera memproses data Anda.');
        } catch (\Exception $e) {
            session()->forget(['registration', 'current_step']);
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * View registration detail
     */
    public function show(Registration $registration): View
    {
        if ($registration->id_user !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('registration.show', [
            'registration' => $registration,
        ]);
    }
}
