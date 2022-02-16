<?php

namespace App\Observers;

use App\Enums\LoanStatus;
use App\Events\LoanCreated;
use App\Models\Loan;

class LoanObserver
{
    /**
     * Handle the Loan "created" event.
     *
     * @param  Loan  $loan
     * @return void
     */
    public function created(Loan $loan): void
    {
        LoanCreated::dispatch($loan);
    }
    
    /**
     * Handle the Loan "updated" event.
     *
     * @param  Loan  $loan
     * @return void
     */
    public function updated(Loan $loan): void
    {
        if ($loan->isDirty('status') && $loan->status === LoanStatus::APPROVED && $loan->isDirty('disbursed_at')) {
            $loan->saveRepaymentEntries();
        }
    }
}
