<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;

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
    public function create()
    {
        return view('registration.create');
    }

    /**
     * Store a new registration in database
     */
    public function store(Request $request)
    {
        // Validate all required fields
        $validated = $request->validate([
            // Candidate data
            'candidate_data.name' => 'required|string|max:255',
            'candidate_data.birth_place' => 'required|string|max:255',
            'candidate_data.birth_date' => 'required|date',
            'candidate_data.gender' => 'required|in:pria,perempuan',

            // Parents data
            'parents_data.father_name' => 'required|string|max:255',
            'parents_data.mother_name' => 'required|string|max:255',
            'parents_data.father_phone_num' => 'required|string|max:20',
            'parents_data.mother_phone_num' => 'required|string|max:20',
            'parents_data.father_occupation' => 'nullable|string|max:255',
            'parents_data.mother_occupation' => 'nullable|string|max:255',
            'parents_data.father_address' => 'required|string',
            'parents_data.mother_address' => 'required|string',

            // Group selection
            'group' => 'required|in:A,B,C',
        ]);

        // Check if user already has a registration
        $existingRegistration = Registration::where('id_user', auth()->id())->first();
        if ($existingRegistration) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda sudah memiliki pendaftaran. Silakan hubungi sekolah jika ingin mendaftar anak lain.');
        }

        try {
            // Create the registration
            Registration::create([
                'id_user' => auth()->id(),
                'candidate_data' => $validated['candidate_data'],
                'parents_data' => $validated['parents_data'],
                'group' => $validated['group'],
                'status' => 'pending',
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Pendaftaran berhasil dikirim! Silakan tunggu konfirmasi dari sekolah.');

        } catch (\Exception $e) {
            \Log::error('Registration creation failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengirim pendaftaran. Silakan coba lagi.');
        }
    }

    /**
     * Show the edit form for pending registration
     */
    public function edit($id_registration)
    {
        $registration = Registration::findOrFail($id_registration);

        // Ensure user can only edit their own registrations
        if ($registration->id_user !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Only allow editing if status is pending or rejected
        if (!in_array($registration->status, ['pending', 'rejected'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Pendaftaran ini tidak dapat diubah.');
        }

        return view('registration.edit', ['registration' => $registration]);
    }

    /**
     * Update a registration
     */
    public function update(Request $request, $id_registration)
    {
        $registration = Registration::findOrFail($id_registration);

        // Ensure user can only update their own registrations
        if ($registration->id_user !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Only allow updating if status is pending or rejected
        if (!in_array($registration->status, ['pending', 'rejected'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Pendaftaran ini tidak dapat diubah.');
        }

        // Validate all fields
        $validated = $request->validate([
            // Candidate data
            'candidate_data.name' => 'required|string|max:255',
            'candidate_data.birth_place' => 'required|string|max:255',
            'candidate_data.birth_date' => 'required|date',
            'candidate_data.gender' => 'required|in:pria,perempuan',

            // Parents data
            'parents_data.father_name' => 'required|string|max:255',
            'parents_data.mother_name' => 'required|string|max:255',
            'parents_data.father_phone_num' => 'required|string|max:20',
            'parents_data.mother_phone_num' => 'required|string|max:20',
            'parents_data.father_occupation' => 'nullable|string|max:255',
            'parents_data.mother_occupation' => 'nullable|string|max:255',
            'parents_data.father_address' => 'required|string',
            'parents_data.mother_address' => 'required|string',

            // Group selection
            'group' => 'required|in:A,B,C',
        ]);

        try {
            // Update the registration
            $registration->update([
                'candidate_data' => $validated['candidate_data'],
                'parents_data' => $validated['parents_data'],
                'group' => $validated['group'],
                'status' => 'pending', // Reset status to pending after edit
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Pendaftaran berhasil diperbarui!');

        } catch (\Exception $e) {
            \Log::error('Registration update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui pendaftaran. Silakan coba lagi.');
        }
    }
}
