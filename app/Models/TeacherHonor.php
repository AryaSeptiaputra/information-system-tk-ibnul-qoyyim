<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_teacher', 'month', 'year', 'attendance_count', 'permission_count', 'sickness_count', 'absence_count', 'amount', 'payment_date'])]
class TeacherHonor extends Model
{
    use HasFactory;

    protected $table = 'teacher_honors';
    protected $primaryKey = 'id_honors';
    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(TeacherDetail::class, 'id_teacher', 'id_teacher');
    }
}
