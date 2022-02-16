<?php

namespace App\Policies;

use App\Models\Repayment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class RepaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can submit the repayment.
     *
     * @param  User  $user
     * @param  Repayment  $repayment
     * @return Response|bool
     */
    public function submit(User $user, Repayment $repayment): Response|bool
    {
        return $user->is_client && $repayment->loan->client_id === $user->id;
    }
}
