<?php

namespace App\Observers;

use App\Actions\Contracts\GeneratesToken;
use App\Enums\UserRole;
use App\Models\User;

class UserObserver
{
    /**
     * Instantiate UserObserver class.
     *
     * @param GeneratesToken $generateToken
     */
    public function __construct(private GeneratesToken $generateToken) {}

    /**
     * Handle the User "creating" event.
     *
     * @param  User  $user
     * @return void
     */
    public function creating(User $user): void
    {
        if ($user->role === null) {
            $user->role = UserRole::CLIENT;
        }
    }
    
    /**
     * Handle the User "created" event.
     *
     * @param  User  $user
     * @return void
     */
    public function created(User $user): void
    {
        $user->token = ($this->generateToken)($user);
    }
}
