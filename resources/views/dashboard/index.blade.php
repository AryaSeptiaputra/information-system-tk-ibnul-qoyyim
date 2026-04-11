@extends('layouts.dashboard')

@section('title', 'Dashboard - TK Ibnul Qoyyim')
@section('page_title', ($role === 'guest') ? 'Halaman Orang Tua' : 'Dashboard')

@section('content')

<!-- Welcome Section -->
@include('components.dashboard.welcome', [
    'greeting' => $greeting,
    'user' => $user,
    'role' => $role
])

<!-- Role-Based Content -->
@if($role === 'guest')
    @php
        $canRegister = !($pendingRegistration ?? null) || (($pendingRegistration->status ?? null) === 'rejected');
        $hasApproved = (bool)($approvedRegistration ?? null);
        $currentStepResolved = $currentStep ?? session('current_step', 1);
        $hasDraft = session()->has('registration.candidate_data') || session()->has('registration.parents_data');
        $showFormByDefault = ($errors->any() || $hasDraft || ((int)$currentStepResolved > 1));

        $detailRegistration = $pendingRegistration ?? $approvedRegistration ?? null;
    @endphp

    <!-- Guest Dashboard (Status Section) -->
    <div id="guest-registration-section" @if($showFormByDefault) hidden aria-hidden="true" @endif>
        @include('components.dashboard.guest-registration', [
            'pendingRegistration' => $pendingRegistration ?? null,
            'approvedRegistration' => $approvedRegistration ?? null,
            'hasChild' => $hasChild ?? false,
            'studentInfo' => $studentInfo ?? null,
        ])
    </div>

    <!-- Guest Registration Form (Embedded Section) -->
    @if($canRegister && !$hasApproved)
        <div id="registration-form-section" @if(!$showFormByDefault) hidden aria-hidden="true" @endif>
            @include('components.dashboard.registration-form-section', [
                'currentStep' => $currentStepResolved,
            ])
        </div>
    @endif

    @if(($hasStudent ?? false) || ($approvedRegistration ?? null) || ($pendingRegistration ?? null))
        <div class="card" style="margin-bottom: 32px;">
            <h2 style="margin-bottom: 12px;">⚡ Quick Access</h2>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('dashboard.info') }}" class="btn-secondary">👨‍👩‍👧‍👦 Info Murid & Orang Tua</a>
                <a href="{{ route('dashboard.bills') }}" class="btn-primary">🧾 Lihat Tagihan</a>
            </div>
        </div>
    @endif

    @if($detailRegistration)
        @include('components.dashboard.registration-detail-modal', [
            'registration' => $detailRegistration,
        ])
    @endif

@else
    <!-- Staff/Admin Dashboard -->
    @include('components.dashboard.stats', [
        'stats' => $stats
    ])

    @include('components.dashboard.placeholder')
@endif

<script>
    (function () {
        const guestSection = document.getElementById('guest-registration-section');
        const formSection = document.getElementById('registration-form-section');

        // If the server rendered the form visible (errors/draft/step>1), keep it visible
        // even when the URL hash is empty (fragments are not preserved on validation redirects).
        const serverWantsFormVisible = !!(formSection && !formSection.hidden);

        function setVisibility(showForm) {
            if (!guestSection || !formSection) return;

            if (showForm) {
                guestSection.hidden = true;
                guestSection.setAttribute('aria-hidden', 'true');
                formSection.hidden = false;
                formSection.setAttribute('aria-hidden', 'false');
            } else {
                formSection.hidden = true;
                formSection.setAttribute('aria-hidden', 'true');
                guestSection.hidden = false;
                guestSection.setAttribute('aria-hidden', 'false');
            }
        }

        function syncFromHash() {
            const hash = window.location.hash;

            if (hash === '#registration-form') {
                setVisibility(true);
                return;
            }

            if (!hash && serverWantsFormVisible) {
                setVisibility(true);
                return;
            }

            setVisibility(false);
        }

        window.addEventListener('hashchange', syncFromHash);
        syncFromHash();

        // Registration detail modal (popup)
        const modal = document.getElementById('registration-detail-modal');
        const openButtons = document.querySelectorAll('[data-modal-open="registration-detail"]');
        const closeButtons = document.querySelectorAll('[data-modal-close="registration-detail"]');

        function openModal() {
            if (!modal) return;
            modal.hidden = false;
            modal.setAttribute('aria-hidden', 'false');
        }

        function closeModal() {
            if (!modal) return;
            modal.hidden = true;
            modal.setAttribute('aria-hidden', 'true');
        }

        openButtons.forEach((btn) => btn.addEventListener('click', openModal));
        closeButtons.forEach((btn) => btn.addEventListener('click', closeModal));

        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });
        }

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    })();
</script>

@endsection
