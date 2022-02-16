<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoanResource;
use App\Models\Loan;

class UpdateLoanStatusController extends Controller
{
    /**
     * Handle approval/rejection of loan.
     *
     * @param Loan $loan
     * @param string $status
     * @return LoanResource
     */
    public function __invoke(Loan $loan, string $status): LoanResource
    {
        $this->authorize('updateStatus', $loan);

        $loan->updateStatus($status);

        return new LoanResource($loan->refresh());
    }
}
