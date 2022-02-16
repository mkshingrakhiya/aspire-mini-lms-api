<?php

namespace App\Http\Controllers;

use App\Http\Resources\RepaymentCollection;
use App\Models\Loan;

class LoanRepaymentController extends Controller
{
    /**
     * List loan repayments.
     *
     * @param  Loan  $loan
     * @return RepaymentCollection
     */
    public function index(Loan $loan): RepaymentCollection
    {
        return new RepaymentCollection($loan->repayments()->with('loan')->paginate());
    }
}
