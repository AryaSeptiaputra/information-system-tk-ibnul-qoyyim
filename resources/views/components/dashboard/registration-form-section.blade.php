<div id="registration-form" style="scroll-margin-top: 24px;">
    <div class="registration-container">
        <div class="registration-wrapper">
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 12px;">
                <h1 class="registration-title" style="margin: 0;">Formulir Pendaftaran Siswa Baru</h1>
                <button
                    type="button"
                    onclick="closeRegistrationForm()"
                    class="ui-btn ui-btn--ghost"
                    aria-label="Tutup formulir pendaftaran"
                    title="Tutup formulir"
                >
                    ✕ Tutup Formulir
                </button>
            </div>

            @include('components.registration.progress-bar', [
                'currentStep' => $currentStep ?? 1,
                'totalSteps' => 3
            ])

            <form action="{{ route('registration.store') }}" method="POST" id="registrationForm" enctype="multipart/form-data">
                @csrf

                @if(($currentStep ?? 1) == 1 || session()->has('new_registration'))
                    @include('components.registration.step-1-candidate', [
                        'candidateData' => session('registration.candidate_data'),
                        'errors' => $errors
                    ])
                @endif

                @if(($currentStep ?? 1) == 2)
                    @include('components.registration.step-2-parents', [
                        'parentsData' => session('registration.parents_data'),
                        'errors' => $errors
                    ])
                @endif

                @if(($currentStep ?? 1) == 3)
                    @include('components.registration.step-3-review', [
                        'candidateData' => session('registration.candidate_data'),
                        'parentsData' => session('registration.parents_data'),
                        'group' => session('registration.group')
                    ])
                @endif

                @include('components.registration.form-navigation', [
                    'currentStep' => $currentStep ?? 1,
                    'totalSteps' => 3
                ])
            </form>
        </div>
    </div>
</div>
