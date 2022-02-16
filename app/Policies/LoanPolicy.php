<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class LoanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        return $user->is_client;
    }

    /**
     * Determine whether the user can update the status on the model.
     *
     * @param  User  $user
     * @param  Loan  $loan
     * @return Response|bool
     */
    public function updateStatus(User $user, Loan $loan): Response|bool
    {
        return $user->is_reviewer && $loan->is_processing;
    }
}
