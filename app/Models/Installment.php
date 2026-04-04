<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['id_payment', 'installment_number', 'due_date', 'payment_date', 'installment_amount', 'status', 'file_joc'])]
class Installment extends Model
{
    use HasFactory;

    protected $table = 'installments';
    protected $primaryKey = 'id_installment';
    public $timestamps = true;

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'payment_date' => 'date',
            'installment_amount' => 'decimal:2',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'id_payment', 'id_payment');
    }
}
