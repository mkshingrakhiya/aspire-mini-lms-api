<?php

namespace App\Models;

use App\Enums\LoanStatus;
use App\Enums\PaymentFrequency;
use App\Events\LoanApproved;
use App\Events\LoanRejected;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'repayment_frequency' => PaymentFrequency::class,
        'status' => LoanStatus::class,
        'disbursed_at' => 'datetime'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_id',
        'amount',
        'term',
        'annual_interest_rate',
        'repayment_frequency',
        'reviewer_id',
        'status',
        'disbursed_at'
    ];

    /**
     * Get the carbon interval based on repayment frequency.
     *
     * @return Attribute
     */
    public function carbonInterval(): Attribute
    {
        return new Attribute(
            get: fn () => match ($this->repayment_frequency) {
                PaymentFrequency::WEEKLY => CarbonInterval::weeks(1),
                PaymentFrequency::MONTHLY => CarbonInterval::months(1),
                PaymentFrequency::QUARTERLY => CarbonInterval::months(3),
                PaymentFrequency::YEARLY => CarbonInterval::year(),
                default => CarbonInterval::weeks(1)
            }
        );
    }

    /**
     * Get the actual interest rate based on repayment frequency.
     *
     * @return Attribute
     */
    public function interestRate(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => $attributes['annual_interest_rate'] / 100 / match ($this->repayment_frequency) {
                PaymentFrequency::WEEKLY => 52,
                PaymentFrequency::MONTHLY => 12,
                PaymentFrequency::QUARTERLY => 4,
                PaymentFrequency::YEARLY => 1,
                default => 52
            },
        );
    }

    /**
     * Determine whether the loan status is approved.
     *
     * @return Attribute
     */
    public function isApproved(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => ($attributes['status'] === LoanStatus::APPROVED->value)
        );
    }
    
    /**
     * Determine whether the loan is disbursed.
     *
     * @return Attribute
     */
    public function isDisbursed(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => !is_null($attributes['disbursed_at'])
        );
    }
    
    /**
     * Determine whether the loan status is processing.
     *
     * @return Attribute
     */
    public function isProcessing(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => ($attributes['status'] === LoanStatus::PROCESSING->value)
        );
    }
    
    /**
     * Determine whether the loan status is rejected.
     *
     * @return Attribute
     */
    public function isRejected(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => ($attributes['status'] === LoanStatus::REJECTED->value)
        );
    }

    /**
     * Get the repayment frequency as a text.
     *
     * @return Attribute
     */
    public function repaymentFrequencyText(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => PaymentFrequency::from($attributes['repayment_frequency'])->name
        );
    }
    
    /**
     * Get the status as a text.
     *
     * @return Attribute
     */
    public function statusText(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => LoanStatus::from($attributes['status'])->name
        );
    }

    /**
     * Save repayment entries in database and return total number of entries.
     *
     * @return array
     */
    public function saveRepaymentEntries(): array
    {
        $amount = $this->amount;
        $rate = $this->interest_rate;
        $due = ($amount * ($rate * pow(1 + $rate, $this->term))) / (pow(1 + $rate, $this->term) - 1);
        $dueDate = ($this->disbursed_at ?? now())->copy();
        $entries = [];

        do {
            $interest = round($amount * $rate, 8);
            $principal = $due - $interest;
            $outstanding = $amount - $principal;
            if ($amount < $due) {
                $outstanding = 0;
                $due = $amount;
            }
            
            $amount -= $principal;
            
            $dueDate = $dueDate->copy()->add($this->carbon_interval);

            $entries[] = new Repayment([
                'interest' => round($interest, 8),
                'outstanding' => round($outstanding, 8),
                'due' => round($due, 8),
                'due_on' => $dueDate,
            ]);
        } while ($outstanding > 0);

        return $this->repayments()->saveMany($entries);
    }

    /**
     * Update the loan status to approved/rejected.
     *
     * @param string $status
     * @return bool
     */
    public function updateStatus(string $status): bool
    {
        $this->fill(['status' => match ($status) {
            'approve' => LoanStatus::APPROVED,
            'reject' => LoanStatus::REJECTED
        }]);

        if ($this->is_approved) {
            // NOTE: Loan disbursal could be done later depending on business flow.
            $this->fill(['disbursed_at' => now()]);

            $event = new LoanApproved($this);
        } else {
            $event = new LoanRejected($this);
        }

        return $this->save() && event($event);
    }

    /**
     * Get the client that owns the loan
     *
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the reviewer that owns the Loan
     *
     * @return BelongsTo
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    /**
     * Get all of the repayments for the loan
     *
     * @return HasMany
     */
    public function repayments(): HasMany
    {
        return $this->hasMany(Repayment::class)->orderBy('due_on');
    }
}
