<?php

namespace App\Http\Controllers;

use App\Http\Resources\RepaymentResource;
use App\Models\Repayment;

class SubmitRepaymentController extends Controller
{
    /**
     * Process the repayment of the loan.
     *
     * @param Repayment $repayment
     * @return RepaymentResource
     */
    public function __invoke(Repayment $repayment): RepaymentResource
    {
        $this->authorize('submit', $repayment);

        // TODO: Call the payment gateway and save transaction id in the database.

        $repayment->submit();

        return new RepaymentResource($repayment->refresh());
    }
}
