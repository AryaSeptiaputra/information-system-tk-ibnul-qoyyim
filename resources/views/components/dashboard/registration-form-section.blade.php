<div id="registration-form" style="scroll-margin-top: 24px;">
    <div class="registration-container">
        <div class="registration-wrapper">
            <h1 class="registration-title">Formulir Pendaftaran Siswa Baru</h1>

            @include('components.registration.progress-bar', [
                'currentStep' => $currentStep ?? 1,
                'totalSteps' => 3
            ])

            <form action="{{ route('registration.store') }}" method="POST" id="registrationForm">
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
