<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['id_student', 'invoice_name', 'due_date', 'payment_date', 'total_bill', 'status', 'file_joc'])]
class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'id_payment';
    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'payment_date' => 'date',
            'total_bill' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_student', 'id_student');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class, 'id_payment', 'id_payment');
    }
}
