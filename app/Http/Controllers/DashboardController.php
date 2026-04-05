<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show dashboard based on user role
     */
    public function index()
    {
        $user = Auth::user();
        
        $data = [
            'user' => $user,
            'role' => $user->role,
            'greeting' => $this->getGreeting($user),
            'stats' => $this->getStats($user),
            'activities' => $this->getActivities($user),
        ];

        // Prepare guest-specific data if user is guest
        if ($user->role === 'guest') {
            $data['currentStep'] = session('current_step', 1);

            $approvedRegistration = Registration::where('id_user', $user->id)
                ->whereIn('status', ['approved', 'approved_awaiting_payment', 'pending_due', 'active'])
                ->latest('id_registration')
                ->first();

            $pendingRegistration = Registration::where('id_user', $user->id)
                ->whereIn('status', ['pending', 'rejected'])
                ->latest('id_registration')
                ->first();

            $data['approvedRegistration'] = $approvedRegistration;
            $data['pendingRegistration'] = $pendingRegistration;
            $data['studentInfo'] = null;
            $data['hasChild'] = false;

            // If approved, get student information
            if ($approvedRegistration) {
                $student = Student::where('id_registration', $approvedRegistration->id_registration)->first();

                if ($student) {
                    $data['studentInfo'] = [
                        'id_student' => $student->id_student,
                        'name' => $student->name,
                        'birth_date' => $student->birth_date,
                        'gender' => $student->gender,
                        'group' => $approvedRegistration->group,
                        'status' => $student->status,
                    ];
                    $data['hasChild'] = true;
                }
            }
        }

        return view('dashboard.index', $data);
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
