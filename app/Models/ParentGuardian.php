<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['father_name', 'mother_name', 'father_phone_num', 'mother_phone_num', 'father_occupation', 'mother_occupation', 'father_address', 'mother_address'])]
class ParentGuardian extends Model
{
    use HasFactory;

    protected $table = 'parents';
    protected $primaryKey = 'id_parents';
    public $timestamps = true;

    /**
     * Get the students for the parent.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'id_parents', 'id_parents');
    }
}
