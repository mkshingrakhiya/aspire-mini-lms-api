<?php

namespace App\Http\Controllers;

use App\Enums\PaymentFrequency;
use App\Enums\UserRole;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Resources\LoanCollection;
use App\Http\Resources\LoanResource;
use App\Models\Loan;
use App\Models\User;

class LoanController extends Controller
{
    /**
     * List loans.
     *
     * @return LoanCollection
     */
    public function index(): LoanCollection
    {
        return new LoanCollection(auth()->user()->loans()->with('client', 'reviewer', 'repayments')->paginate());
    }

    /**
     * Loan application.
     *
     * @param  StoreLoanRequest  $request
     * @return LoanResource
     */
    public function store(StoreLoanRequest $request): LoanResource
    {
        $this->authorize('create', Loan::class);

        // NOTE: Find reviewer based on branch/availability or other business criteria.
        $reviewerId = User::withRole(UserRole::REVIEWER)->pluck('id')->random();

        $loan = Loan::create([
            ...$request->validated(),
            'client_id' => auth()->id(),
            'annual_interest_rate' => 10, // NOTE: This should be predefined based on certain business criteria
            'repayment_frequency' => PaymentFrequency::WEEKLY,
            'reviewer_id' => $reviewerId,
        ]);

        return new LoanResource($loan->refresh());
    }
}
