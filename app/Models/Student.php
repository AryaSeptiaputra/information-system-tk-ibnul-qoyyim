<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_parents', 'name', 'birth_place', 'birth_date', 'gender', 'group', 'status'])]
class Student extends Model
{
    use HasFactory;

    protected $table = 'students';
    protected $primaryKey = 'id_student';
    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentGuardian::class, 'id_parents', 'id_parents');
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_student', 'id_student', 'id_class');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(StudentAttendance::class, 'id_student', 'id_student');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'id_student', 'id_student');
    }
}
