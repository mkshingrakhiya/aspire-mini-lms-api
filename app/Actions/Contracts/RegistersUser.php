<?php

namespace App\Actions\Contracts;

use App\Models\User;

interface RegistersUser
{
    // NOTE: This contract allows to have multiple implementation of user registration.
    // NOTE: Contracts can be also easily tested and re-used.
    public function __invoke(array $data): User;
}
