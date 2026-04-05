<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Registration extends Model
{
    use HasFactory;

    protected $table = 'registrations';
    protected $primaryKey = 'id_registration';
    public $timestamps = true;

    #[Fillable(['id_user', 'candidate_data', 'parents_data', 'group', 'status', 'payment_deadline', 'grace_period_until', 'paid_late', 'reject_reason'])]
    protected $fillable = [
        'id_user',
        'candidate_data',
        'parents_data',
        'group',
        'status',
        'payment_deadline',
        'grace_period_until',
        'paid_late',
        'reject_reason',
    ];

    protected $casts = [
        'candidate_data' => 'array',
        'parents_data' => 'array',
        'payment_deadline' => 'date',
        'grace_period_until' => 'date',
        'paid_late' => 'boolean',
    ];

    /**
     * Get the user who made the registration.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    /**
     * Get the student record for this registration.
     */
    public function student(): HasMany
    {
        return $this->hasMany(Student::class, 'id_registration', 'id_registration');
    }

    /**
     * Get all payments through student records.
     */
    public function payments()
    {
        return StudentPayment::whereIn('id_student', $this->student()->pluck('id_student'))->get();
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApprovedAwaitingPayment($query)
    {
        return $query->where('status', 'approved_awaiting_payment');
    }

    public function scopePendingDue($query)
    {
        return $query->where('status', 'pending_due');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Helper Methods
    public function isApproved(): bool
    {
        return $this->status === 'approved_awaiting_payment';
    }

    public function isPendingDue(): bool
    {
        return $this->status === 'pending_due';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function getDeadlineStatus(): array
    {
        if (!$this->payment_deadline) {
            return ['status' => 'no_deadline', 'days_remaining' => null];
        }

        $today = Carbon::now()->startOfDay();
        $deadline = Carbon::parse($this->payment_deadline)->startOfDay();
        $graceUntil = Carbon::parse($this->grace_period_until)->startOfDay();

        if ($today > $deadline) {
            if ($today <= $graceUntil) {
                return ['status' => 'grace_period', 'days_remaining' => $today->diffInDays($graceUntil)];
            }
            return ['status' => 'expired', 'days_remaining' => 0];
        }

        $daysRemaining = $today->diffInDays($deadline);
        return ['status' => 'pending', 'days_remaining' => $daysRemaining];
    }

    public function markAsApprovedAwaitingPayment(int $deadlineDays = 7, int $gracePeriodDays = 3): void
    {
        $this->status = 'approved_awaiting_payment';
        $this->payment_deadline = Carbon::now()->addDays($deadlineDays)->startOfDay();
        $this->grace_period_until = Carbon::now()->addDays($deadlineDays + $gracePeriodDays)->startOfDay();
        $this->save();
    }

    public function markAsActive(): void
    {
        $this->status = 'active';
        $this->save();
    }

    public function markAsRejected(string $reason = null): void
    {
        $this->status = 'rejected';
        $this->reject_reason = $reason;
        $this->save();
    }
}
