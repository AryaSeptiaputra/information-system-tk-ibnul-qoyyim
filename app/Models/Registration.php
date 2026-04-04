<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    use HasFactory;

    protected $table = 'registrations';
    protected $primaryKey = 'id_registration';
    public $timestamps = true;

    #[Fillable(['id_user', 'candidate_data', 'parents_data', 'group', 'status'])]
    protected $fillable = ['id_user', 'candidate_data', 'parents_data', 'group', 'status'];

    protected $casts = [
        'candidate_data' => 'array',
        'parents_data' => 'array',
    ];

    /**
     * Get the user who made the registration.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
