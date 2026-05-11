<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'id_user',
    'name',
    'position',
    'nip',
    'nuptk',
    'birth_place',
    'birth_date',
    'start_work_date',
    'education',
    'phone_num',
    'email',
    'status',
])]
class TeacherDetail extends Model
{
    use HasFactory;

    protected $table = 'teacher_details';
    protected $primaryKey = 'id_teacher';
    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'start_work_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_teacher', 'id_teacher', 'id_class');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(TeacherAttendance::class, 'id_teacher', 'id_teacher');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(TeacherPosition::class, 'id_teacher', 'id_teacher');
    }

    public function attendanceRates(): HasMany
    {
        return $this->hasMany(TeacherAttendanceRate::class, 'id_teacher', 'id_teacher');
    }

    public function honors(): HasMany
    {
        return $this->hasMany(TeacherHonor::class, 'id_teacher', 'id_teacher');
    }
}
