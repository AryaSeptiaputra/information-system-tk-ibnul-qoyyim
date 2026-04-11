<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_user', 'name', 'education', 'phone_num', 'email', 'status'])]
class TeacherDetail extends Model
{
    use HasFactory;

    protected $table = 'teacher_details';
    protected $primaryKey = 'id_teacher';
    public $timestamps = true;

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

    public function honors(): HasMany
    {
        return $this->hasMany(TeacherHonor::class, 'id_teacher', 'id_teacher');
    }
}
