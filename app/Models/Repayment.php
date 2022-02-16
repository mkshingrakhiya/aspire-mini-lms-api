<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Repayment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'due_on' => 'datetime',
        'paid_on' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loan_id',
        'due',
        'interest',
        'outstanding',
        'due_on',
        'paid_on',
    ];

    /**
     * Get the principal amount paid for this record.
     *
     * @return Attribute
     */
    public function principal(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => round($attributes['due'] - $attributes['interest'], 2),
        );
    }

    /**
     * Determine whether the repayment is submitted or not.
     *
     * @return Attribute
     */
    public function isPaid(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => !is_null($attributes['paid_on']),
        );
    }

    /**
     * Submit the repayment.
     *
     * @return bool
     * 
     * @throws Exception
     */
    public function submit(): bool
    {
        throw_if($this->is_paid, Exception::class, 'The repayment has been already submitted.');

        return $this->update(['paid_on' => now()]);
    }

    /**
     * Get the loan that owns the Repayment
     *
     * @return BelongsTo
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
