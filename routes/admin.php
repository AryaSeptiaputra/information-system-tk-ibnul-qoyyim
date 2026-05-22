<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BendaharaDashboardController;
use App\Http\Controllers\Admin\BendaharaFundController;
use App\Http\Controllers\Admin\BendaharaHonorController;
use App\Http\Controllers\Admin\BendaharaTransactionController;
use App\Http\Controllers\Admin\ParentManagementController;
use App\Http\Controllers\Admin\RegistrationManagementController;
use App\Http\Controllers\Admin\SchoolClassManagementController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Admin\StudentAttendanceManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\TeacherManagementController;
use App\Http\Controllers\Admin\TeacherAttendanceManagementController;
use App\Http\Controllers\Admin\TeacherAttendanceSelfController;
use App\Http\Controllers\Admin\TeacherHonorManagementController;
use App\Http\Controllers\Admin\TeacherHonorSelfController;
use App\Http\Controllers\Admin\PositionManagementController;
use App\Http\Controllers\Admin\AllowanceTypeManagementController;
use App\Http\Controllers\Admin\PositionAllowanceManagementController;
use App\Http\Controllers\Admin\TeacherPositionManagementController;
use App\Http\Controllers\Admin\TeacherAttendanceRateManagementController;
use App\Http\Controllers\Admin\FacilityManagementController;
use App\Http\Controllers\Admin\PaymentManagementController;
use App\Http\Controllers\Admin\PaymentSettingManagementController;
use App\Http\Controllers\Admin\StudentPaymentManagementController;
use Illuminate\Support\Facades\Route;

// Admin routes - staff only (non-guest). Fine-grained per-module access below.
Route::middleware(['auth', 'ensure.role:superadmin,administration,teacher,headmaster,bendahara'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard - Statistics & Overview (staff)
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User Management Routes (SUPERADMIN ONLY)
    Route::middleware(['ensure.super.admin'])->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
        Route::get('/export', [UserManagementController::class, 'export'])->name('export');
        Route::get('/{user}', [UserManagementController::class, 'show'])->whereNumber('user')->name('show');
    });
    
    // Teacher Management Routes
    Route::prefix('teachers')->name('teachers.')->group(function () {
        // View (superadmin, administration, headmaster)
        Route::middleware(['ensure.role:superadmin,administration,headmaster'])->group(function () {
            Route::get('/', [TeacherManagementController::class, 'index'])->name('index');
            Route::get('/export', [TeacherManagementController::class, 'export'])->name('export');
            Route::get('/{teacher}', [TeacherManagementController::class, 'show'])->whereNumber('teacher')->name('show');
        });

        // Manage (superadmin, administration)
        Route::middleware(['ensure.role:superadmin,administration'])->group(function () {
            Route::get('/create', [TeacherManagementController::class, 'create'])->name('create');
            Route::post('/', [TeacherManagementController::class, 'store'])->name('store');
            Route::get('/{teacher}/edit', [TeacherManagementController::class, 'edit'])->name('edit');
            Route::put('/{teacher}', [TeacherManagementController::class, 'update'])->name('update');
            Route::delete('/{teacher}', [TeacherManagementController::class, 'destroy'])->name('destroy');
        });
    });

    // Registration Management Routes
    Route::prefix('registrations')->name('registrations.')->group(function () {
        // View (superadmin, administration, headmaster)
        Route::middleware(['ensure.role:superadmin,administration,headmaster'])->group(function () {
            Route::get('/', [RegistrationManagementController::class, 'index'])->name('index');
            Route::get('/export', [RegistrationManagementController::class, 'export'])->name('export');
            Route::get('/{registration}', [RegistrationManagementController::class, 'show'])->whereNumber('registration')->name('show');
        });

        // Manage (superadmin, administration)
        Route::middleware(['ensure.role:superadmin,administration'])->group(function () {
            Route::get('/create', [RegistrationManagementController::class, 'create'])->name('create');
            Route::post('/', [RegistrationManagementController::class, 'store'])->name('store');
            Route::get('/{registration}/edit', [RegistrationManagementController::class, 'edit'])->name('edit');
            Route::put('/{registration}', [RegistrationManagementController::class, 'update'])->name('update');
        });
    });

    // Parent/Guardian Management Routes
    Route::prefix('parents')->name('parents.')->group(function () {
        // View (superadmin, administration, headmaster)
        Route::middleware(['ensure.role:superadmin,administration,headmaster'])->group(function () {
            Route::get('/', [ParentManagementController::class, 'index'])->name('index');
            Route::get('/export', [ParentManagementController::class, 'export'])->name('export');
            Route::get('/{parent}', [ParentManagementController::class, 'show'])->whereNumber('parent')->name('show');
        });

        // Manage (superadmin, administration)
        Route::middleware(['ensure.role:superadmin,administration'])->group(function () {
            Route::get('/create', [ParentManagementController::class, 'create'])->name('create');
            Route::post('/', [ParentManagementController::class, 'store'])->name('store');
            Route::get('/{parent}/edit', [ParentManagementController::class, 'edit'])->name('edit');
            Route::put('/{parent}', [ParentManagementController::class, 'update'])->name('update');
        });
    });

    // Student Management Routes
    Route::prefix('students')->name('students.')->group(function () {
        // View (superadmin, administration, headmaster, teacher)
        Route::middleware(['ensure.role:superadmin,administration,headmaster,teacher'])->group(function () {
            Route::get('/', [StudentManagementController::class, 'index'])->name('index');
            Route::get('/export', [StudentManagementController::class, 'export'])->name('export');
            Route::get('/{student}', [StudentManagementController::class, 'show'])->whereNumber('student')->name('show');
        });

        // Manage (superadmin, administration)
        Route::middleware(['ensure.role:superadmin,administration'])->group(function () {
            Route::get('/create', [StudentManagementController::class, 'create'])->name('create');
            Route::post('/', [StudentManagementController::class, 'store'])->name('store');
            Route::get('/{student}/edit', [StudentManagementController::class, 'edit'])->name('edit');
            Route::put('/{student}', [StudentManagementController::class, 'update'])->name('update');
        });
    });

    // School Class Management Routes
    Route::prefix('classes')->name('classes.')->group(function () {
        // View (superadmin, administration, headmaster, teacher)
        Route::middleware(['ensure.role:superadmin,administration,headmaster,teacher'])->group(function () {
            Route::get('/', [SchoolClassManagementController::class, 'index'])->name('index');
            Route::get('/export', [SchoolClassManagementController::class, 'export'])->name('export');
            Route::get('/{schoolClass}', [SchoolClassManagementController::class, 'show'])->whereNumber('schoolClass')->name('show');
        });

        // Manage (superadmin, administration)
        Route::middleware(['ensure.role:superadmin,administration'])->group(function () {
            Route::get('/create', [SchoolClassManagementController::class, 'create'])->name('create');
            Route::post('/', [SchoolClassManagementController::class, 'store'])->name('store');
            Route::get('/{schoolClass}/edit', [SchoolClassManagementController::class, 'edit'])->name('edit');
            Route::put('/{schoolClass}', [SchoolClassManagementController::class, 'update'])->name('update');

            // Pivot management: class_student
            Route::post('/{schoolClass}/students', [SchoolClassManagementController::class, 'attachStudent'])->name('students.attach');
            Route::delete('/{schoolClass}/students/{student}', [SchoolClassManagementController::class, 'detachStudent'])->name('students.detach');

            // Pivot management: class_teacher
            Route::post('/{schoolClass}/teachers', [SchoolClassManagementController::class, 'attachTeacher'])->name('teachers.attach');
            Route::delete('/{schoolClass}/teachers/{teacher}', [SchoolClassManagementController::class, 'detachTeacher'])->name('teachers.detach');
        });
    });

    // Student Attendance Management Routes
    Route::prefix('student-attendance')->name('student-attendance.')->group(function () {
        // View (superadmin, administration, teacher, headmaster)
        Route::middleware(['ensure.role:superadmin,administration,teacher,headmaster'])->group(function () {
            Route::get('/', [StudentAttendanceManagementController::class, 'index'])->name('index');
            Route::get('/export', [StudentAttendanceManagementController::class, 'export'])->name('export');
            Route::get('/{studentAttendance}', [StudentAttendanceManagementController::class, 'show'])->whereNumber('studentAttendance')->name('show');
        });

        // Manage (superadmin, administration, teacher)
        Route::middleware(['ensure.role:superadmin,administration,teacher'])->group(function () {
            Route::get('/create', [StudentAttendanceManagementController::class, 'create'])->name('create');
            Route::post('/', [StudentAttendanceManagementController::class, 'store'])->name('store');
            Route::get('/{studentAttendance}/edit', [StudentAttendanceManagementController::class, 'edit'])->name('edit');
            Route::put('/{studentAttendance}', [StudentAttendanceManagementController::class, 'update'])->name('update');
            Route::delete('/{studentAttendance}', [StudentAttendanceManagementController::class, 'destroy'])->name('destroy');
        });
    });

    // Teacher Attendance Management Routes
    Route::prefix('teacher-attendance')->name('teacher-attendance.')->group(function () {
        // View (superadmin, administration, teacher, headmaster)
        Route::middleware(['ensure.role:superadmin,administration,teacher,headmaster'])->group(function () {
            Route::get('/', [TeacherAttendanceManagementController::class, 'index'])->name('index');
            Route::get('/export', [TeacherAttendanceManagementController::class, 'export'])->name('export');
            Route::get('/{teacherAttendance}', [TeacherAttendanceManagementController::class, 'show'])->whereNumber('teacherAttendance')->name('show');
        });

        // Manage (superadmin, administration, headmaster) — guru tidak diizinkan edit/hapus
        // Kebijakan: edit absen hanya kepala sekolah / admin.
        Route::middleware(['ensure.role:superadmin,administration,headmaster'])->group(function () {
            Route::get('/create', [TeacherAttendanceManagementController::class, 'create'])->name('create');
            Route::post('/', [TeacherAttendanceManagementController::class, 'store'])->name('store');
            Route::get('/{teacherAttendance}/edit', [TeacherAttendanceManagementController::class, 'edit'])->name('edit');
            Route::put('/{teacherAttendance}', [TeacherAttendanceManagementController::class, 'update'])->name('update');
            Route::delete('/{teacherAttendance}', [TeacherAttendanceManagementController::class, 'destroy'])->name('destroy');
        });
    });

    // Teacher self-service: absen mandiri (TEACHER ONLY) — guru hanya bisa absen sekarang,
    // ajukan izin, atau lapor sakit. Tidak ada edit/hapus.
    Route::middleware(['ensure.role:teacher'])->prefix('my-attendance')->name('my-attendance.')->group(function () {
        Route::get('/', [TeacherAttendanceSelfController::class, 'index'])->name('index');
        Route::post('/check-in', [TeacherAttendanceSelfController::class, 'checkIn'])->name('check-in');
        Route::post('/permission', [TeacherAttendanceSelfController::class, 'permission'])->name('permission');
        Route::post('/sick', [TeacherAttendanceSelfController::class, 'sick'])->name('sick');
    });

    // Headmaster portal: dashboard & laporan (HEADMASTER + SUPERADMIN).
    Route::middleware(['ensure.role:superadmin,headmaster'])->prefix('headmaster')->name('headmaster.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\HeadmasterDashboardController::class, 'index'])->name('dashboard');
        Route::get('/reports', [\App\Http\Controllers\Admin\HeadmasterReportController::class, 'index'])->name('reports');
        Route::get('/reports/export', [\App\Http\Controllers\Admin\HeadmasterReportController::class, 'export'])->name('reports.export');
    });

    // Teacher portal: dashboard, murid kelas, profil (TEACHER ONLY).
    Route::middleware(['ensure.role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::get('/students', [\App\Http\Controllers\Admin\TeacherStudentController::class, 'index'])->name('students');
        Route::get('/profile', [\App\Http\Controllers\Admin\TeacherProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\Admin\TeacherProfileController::class, 'update'])->name('profile.update');
    });

    // Teacher Honor Management Routes
    Route::prefix('teacher-honors')->name('teacher-honors.')->group(function () {
        // View (superadmin, administration, headmaster, bendahara)
        Route::middleware(['ensure.role:superadmin,administration,headmaster,bendahara'])->group(function () {
            Route::get('/', [TeacherHonorManagementController::class, 'index'])->name('index');
            Route::get('/attendance-summary', [TeacherHonorManagementController::class, 'attendanceSummary'])->name('attendance-summary');
            Route::get('/export', [TeacherHonorManagementController::class, 'export'])->name('export');
            Route::get('/{teacherHonor}', [TeacherHonorManagementController::class, 'show'])->whereNumber('teacherHonor')->name('show');
        });

        // Manage (superadmin, administration, bendahara)
        Route::middleware(['ensure.role:superadmin,administration,bendahara'])->group(function () {
            Route::get('/create', [TeacherHonorManagementController::class, 'create'])->name('create');
            Route::post('/', [TeacherHonorManagementController::class, 'store'])->name('store');
            Route::get('/{teacherHonor}/edit', [TeacherHonorManagementController::class, 'edit'])->name('edit');
            Route::put('/{teacherHonor}', [TeacherHonorManagementController::class, 'update'])->name('update');
        });
    });

    // Positions (Honor) Management Routes
    Route::prefix('positions')->name('positions.')->group(function () {
        Route::middleware(['ensure.role:superadmin,administration,headmaster,bendahara'])->group(function () {
            Route::get('/', [PositionManagementController::class, 'index'])->name('index');
            Route::get('/export', [PositionManagementController::class, 'export'])->name('export');
            Route::get('/{position}', [PositionManagementController::class, 'show'])->whereNumber('position')->name('show');
        });

        Route::middleware(['ensure.role:superadmin,administration,bendahara'])->group(function () {
            Route::get('/create', [PositionManagementController::class, 'create'])->name('create');
            Route::post('/', [PositionManagementController::class, 'store'])->name('store');
            Route::get('/{position}/edit', [PositionManagementController::class, 'edit'])->whereNumber('position')->name('edit');
            Route::put('/{position}', [PositionManagementController::class, 'update'])->whereNumber('position')->name('update');
            Route::delete('/{position}', [PositionManagementController::class, 'destroy'])->whereNumber('position')->name('destroy');
        });
    });

    // Allowance Types (Honor) Management Routes
    Route::prefix('allowance-types')->name('allowance-types.')->group(function () {
        Route::middleware(['ensure.role:superadmin,administration,headmaster,bendahara'])->group(function () {
            Route::get('/', [AllowanceTypeManagementController::class, 'index'])->name('index');
            Route::get('/export', [AllowanceTypeManagementController::class, 'export'])->name('export');
            Route::get('/{allowanceType}', [AllowanceTypeManagementController::class, 'show'])->whereNumber('allowanceType')->name('show');
        });

        Route::middleware(['ensure.role:superadmin,administration,bendahara'])->group(function () {
            Route::get('/create', [AllowanceTypeManagementController::class, 'create'])->name('create');
            Route::post('/', [AllowanceTypeManagementController::class, 'store'])->name('store');
            Route::get('/{allowanceType}/edit', [AllowanceTypeManagementController::class, 'edit'])->whereNumber('allowanceType')->name('edit');
            Route::put('/{allowanceType}', [AllowanceTypeManagementController::class, 'update'])->whereNumber('allowanceType')->name('update');
            Route::delete('/{allowanceType}', [AllowanceTypeManagementController::class, 'destroy'])->whereNumber('allowanceType')->name('destroy');
        });
    });

    // Position Allowances (Honor) Management Routes
    Route::prefix('position-allowances')->name('position-allowances.')->group(function () {
        Route::middleware(['ensure.role:superadmin,administration,headmaster,bendahara'])->group(function () {
            Route::get('/', [PositionAllowanceManagementController::class, 'index'])->name('index');
            Route::get('/export', [PositionAllowanceManagementController::class, 'export'])->name('export');
            Route::get('/{positionAllowance}', [PositionAllowanceManagementController::class, 'show'])->whereNumber('positionAllowance')->name('show');
        });

        Route::middleware(['ensure.role:superadmin,administration,bendahara'])->group(function () {
            Route::get('/create', [PositionAllowanceManagementController::class, 'create'])->name('create');
            Route::post('/', [PositionAllowanceManagementController::class, 'store'])->name('store');
            Route::get('/{positionAllowance}/edit', [PositionAllowanceManagementController::class, 'edit'])->whereNumber('positionAllowance')->name('edit');
            Route::put('/{positionAllowance}', [PositionAllowanceManagementController::class, 'update'])->whereNumber('positionAllowance')->name('update');
            Route::delete('/{positionAllowance}', [PositionAllowanceManagementController::class, 'destroy'])->whereNumber('positionAllowance')->name('destroy');
        });
    });

    // Teacher Positions (Honor) Management Routes
    Route::prefix('teacher-positions')->name('teacher-positions.')->group(function () {
        Route::middleware(['ensure.role:superadmin,administration,headmaster,bendahara'])->group(function () {
            Route::get('/', [TeacherPositionManagementController::class, 'index'])->name('index');
            Route::get('/export', [TeacherPositionManagementController::class, 'export'])->name('export');
            Route::get('/{teacherPosition}', [TeacherPositionManagementController::class, 'show'])->whereNumber('teacherPosition')->name('show');
        });

        Route::middleware(['ensure.role:superadmin,administration,bendahara'])->group(function () {
            Route::get('/create', [TeacherPositionManagementController::class, 'create'])->name('create');
            Route::post('/', [TeacherPositionManagementController::class, 'store'])->name('store');
            Route::get('/{teacherPosition}/edit', [TeacherPositionManagementController::class, 'edit'])->whereNumber('teacherPosition')->name('edit');
            Route::put('/{teacherPosition}', [TeacherPositionManagementController::class, 'update'])->whereNumber('teacherPosition')->name('update');
            Route::delete('/{teacherPosition}', [TeacherPositionManagementController::class, 'destroy'])->whereNumber('teacherPosition')->name('destroy');
        });
    });

    // Teacher Attendance Rates (Honor) Management Routes
    Route::prefix('teacher-attendance-rates')->name('teacher-attendance-rates.')->group(function () {
        Route::middleware(['ensure.role:superadmin,administration,headmaster,bendahara'])->group(function () {
            Route::get('/', [TeacherAttendanceRateManagementController::class, 'index'])->name('index');
            Route::get('/export', [TeacherAttendanceRateManagementController::class, 'export'])->name('export');
            Route::get('/{teacherAttendanceRate}', [TeacherAttendanceRateManagementController::class, 'show'])->whereNumber('teacherAttendanceRate')->name('show');
        });

        Route::middleware(['ensure.role:superadmin,administration,bendahara'])->group(function () {
            Route::get('/create', [TeacherAttendanceRateManagementController::class, 'create'])->name('create');
            Route::post('/', [TeacherAttendanceRateManagementController::class, 'store'])->name('store');
            Route::get('/{teacherAttendanceRate}/edit', [TeacherAttendanceRateManagementController::class, 'edit'])->whereNumber('teacherAttendanceRate')->name('edit');
            Route::put('/{teacherAttendanceRate}', [TeacherAttendanceRateManagementController::class, 'update'])->whereNumber('teacherAttendanceRate')->name('update');
            Route::delete('/{teacherAttendanceRate}', [TeacherAttendanceRateManagementController::class, 'destroy'])->whereNumber('teacherAttendanceRate')->name('destroy');
        });
    });

    // Teacher self-service: my honor (TEACHER ONLY)
    Route::middleware(['ensure.role:teacher'])->get('/my-honor', [TeacherHonorSelfController::class, 'index'])->name('my-honor.index');

    // Facility (Sarana & Prasarana) Management Routes
    Route::prefix('facilities')->name('facilities.')->group(function () {
        // View (superadmin, administration, headmaster)
        Route::middleware(['ensure.role:superadmin,administration,headmaster'])->group(function () {
            Route::get('/', [FacilityManagementController::class, 'index'])->name('index');
            Route::get('/export', [FacilityManagementController::class, 'export'])->name('export');
            Route::get('/{facility}', [FacilityManagementController::class, 'show'])->whereNumber('facility')->name('show');
        });

        // Manage (superadmin, administration)
        Route::middleware(['ensure.role:superadmin,administration'])->group(function () {
            Route::get('/create', [FacilityManagementController::class, 'create'])->name('create');
            Route::post('/', [FacilityManagementController::class, 'store'])->name('store');
            Route::get('/{facility}/edit', [FacilityManagementController::class, 'edit'])->whereNumber('facility')->name('edit');
            Route::put('/{facility}', [FacilityManagementController::class, 'update'])->whereNumber('facility')->name('update');
            Route::delete('/{facility}', [FacilityManagementController::class, 'destroy'])->whereNumber('facility')->name('destroy');
        });
    });

    // Payments (Master) Management Routes
    Route::prefix('payments')->name('payments.')->group(function () {
        // View (superadmin, administration, headmaster)
        Route::middleware(['ensure.role:superadmin,administration,headmaster'])->group(function () {
            Route::get('/', [PaymentManagementController::class, 'index'])->name('index');
            Route::get('/export', [PaymentManagementController::class, 'export'])->name('export');
            Route::get('/{payment}', [PaymentManagementController::class, 'show'])->whereNumber('payment')->name('show');
        });

        // Manage (superadmin, administration)
        Route::middleware(['ensure.role:superadmin,administration'])->group(function () {
            Route::get('/create', [PaymentManagementController::class, 'create'])->name('create');
            Route::post('/', [PaymentManagementController::class, 'store'])->name('store');
            Route::get('/{payment}/edit', [PaymentManagementController::class, 'edit'])->whereNumber('payment')->name('edit');
            Route::put('/{payment}', [PaymentManagementController::class, 'update'])->whereNumber('payment')->name('update');
            Route::delete('/{payment}', [PaymentManagementController::class, 'destroy'])->whereNumber('payment')->name('destroy');
        });
    });

    // Student Payments (Transactions) Management Routes
    Route::prefix('student-payments')->name('student-payments.')->group(function () {
        // View (superadmin, administration, headmaster)
        Route::middleware(['ensure.role:superadmin,administration,headmaster'])->group(function () {
            Route::get('/', [StudentPaymentManagementController::class, 'index'])->name('index');
            Route::get('/export', [StudentPaymentManagementController::class, 'export'])->name('export');
            Route::get('/{studentPayment}', [StudentPaymentManagementController::class, 'show'])->whereNumber('studentPayment')->name('show');
        });

        // Manage (superadmin, administration)
        Route::middleware(['ensure.role:superadmin,administration'])->group(function () {
            Route::get('/create', [StudentPaymentManagementController::class, 'create'])->name('create');
            Route::post('/', [StudentPaymentManagementController::class, 'store'])->name('store');

            // Proof verification (approve/reject uploaded proofs)
            Route::put('/proofs/{paymentProof}/approve', [StudentPaymentManagementController::class, 'proofApprove'])
                ->name('proofs.approve');
            Route::put('/proofs/{paymentProof}/reject', [StudentPaymentManagementController::class, 'proofReject'])
                ->name('proofs.reject');

            // Installments
            Route::get('/{studentPayment}/installments/create', [StudentPaymentManagementController::class, 'installmentsCreate'])
                ->whereNumber('studentPayment')
                ->name('installments.create');
            Route::post('/{studentPayment}/installments', [StudentPaymentManagementController::class, 'installmentsStore'])
                ->whereNumber('studentPayment')
                ->name('installments.store');

            Route::get('/{studentPayment}/installments/reset', [StudentPaymentManagementController::class, 'installmentsResetConfirm'])
                ->whereNumber('studentPayment')
                ->name('installments.reset.confirm');
            Route::delete('/{studentPayment}/installments', [StudentPaymentManagementController::class, 'installmentsReset'])
                ->whereNumber('studentPayment')
                ->name('installments.reset');

            Route::get('/{studentPayment}/installments/{installment}/pay', [StudentPaymentManagementController::class, 'installmentsPay'])
                ->whereNumber('studentPayment')
                ->whereNumber('installment')
                ->name('installments.pay');
            Route::put('/{studentPayment}/installments/{installment}/pay', [StudentPaymentManagementController::class, 'installmentsPayUpdate'])
                ->whereNumber('studentPayment')
                ->whereNumber('installment')
                ->name('installments.pay.update');

            Route::get('/{studentPayment}/edit', [StudentPaymentManagementController::class, 'edit'])->whereNumber('studentPayment')->name('edit');
            Route::put('/{studentPayment}', [StudentPaymentManagementController::class, 'update'])->whereNumber('studentPayment')->name('update');
            Route::delete('/{studentPayment}', [StudentPaymentManagementController::class, 'destroy'])->whereNumber('studentPayment')->name('destroy');
        });
    });

    // Holiday Management (superadmin + administration)
    Route::prefix('holidays')->name('holidays.')->group(function () {
        Route::middleware(['ensure.role:superadmin,administration,headmaster'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\HolidayManagementController::class, 'index'])->name('index');
        });
        Route::middleware(['ensure.role:superadmin,administration'])->group(function () {
            Route::get('/create', [\App\Http\Controllers\Admin\HolidayManagementController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\HolidayManagementController::class, 'store'])->name('store');
            Route::get('/{holiday}/edit', [\App\Http\Controllers\Admin\HolidayManagementController::class, 'edit'])->whereNumber('holiday')->name('edit');
            Route::put('/{holiday}', [\App\Http\Controllers\Admin\HolidayManagementController::class, 'update'])->whereNumber('holiday')->name('update');
            Route::delete('/{holiday}', [\App\Http\Controllers\Admin\HolidayManagementController::class, 'destroy'])->whereNumber('holiday')->name('destroy');
        });
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        // View (superadmin, administration, headmaster)
        Route::middleware(['ensure.role:superadmin,administration,headmaster'])->group(function () {
            Route::get('/payment-info', [PaymentSettingManagementController::class, 'edit'])->name('payment-info.edit');
        });

        // Manage (superadmin, administration)
        Route::middleware(['ensure.role:superadmin,administration'])->group(function () {
            Route::put('/payment-info', [PaymentSettingManagementController::class, 'update'])->name('payment-info.update');

            Route::post('/payment-info/methods', [PaymentSettingManagementController::class, 'storeMethod'])->name('payment-info.methods.store');
            Route::put('/payment-info/methods/{paymentMethod}', [PaymentSettingManagementController::class, 'updateMethod'])
                ->whereNumber('paymentMethod')
                ->name('payment-info.methods.update');
            Route::delete('/payment-info/methods/{paymentMethod}', [PaymentSettingManagementController::class, 'destroyMethod'])
                ->whereNumber('paymentMethod')
                ->name('payment-info.methods.destroy');

            Route::put('/payment-info/qris', [PaymentSettingManagementController::class, 'updateQris'])->name('payment-info.qris.update');
        });
    });

    // Bendahara: dashboard saldo + input dana + bayar honor.
    // Akses: superadmin + administration (peran administrasi dipakai untuk fungsi bendahara di sekolah ini)
    //        + bendahara (untuk fleksibilitas role terpisah jika dipakai di masa depan).
    Route::middleware(['ensure.role:superadmin,administration,bendahara'])->prefix('bendahara')->name('bendahara.')->group(function () {
        Route::get('/', [BendaharaDashboardController::class, 'index'])->name('dashboard');
        Route::post('/fund', [BendaharaFundController::class, 'store'])->name('fund.store');

        Route::prefix('honors')->name('honors.')->group(function () {
            Route::get('/', [BendaharaHonorController::class, 'index'])->name('index');
            Route::get('/{teacherHonor}', [BendaharaHonorController::class, 'show'])->whereNumber('teacherHonor')->name('show');
            Route::put('/{teacherHonor}/component', [BendaharaHonorController::class, 'updateComponent'])->whereNumber('teacherHonor')->name('update-component');
            Route::post('/{teacherHonor}/pay', [BendaharaHonorController::class, 'pay'])->whereNumber('teacherHonor')->name('pay');
        });

        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', [BendaharaTransactionController::class, 'index'])->name('index');
            Route::get('/export', [BendaharaTransactionController::class, 'export'])->name('export');
            Route::get('/by-source/{fundSource}', [BendaharaTransactionController::class, 'bySource'])
                ->whereNumber('fundSource')
                ->name('by-source');
        });
    });
});
