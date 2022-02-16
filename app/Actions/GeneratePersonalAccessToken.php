<?php

namespace App\Actions;

use App\Actions\Contracts\GeneratesToken;
use App\Models\User;

class GeneratePersonalAccessToken implements GeneratesToken
{
    /**
     * Generates authorization token.
     *
     * @param User $user
     * @return string
     */
    public function __invoke(User $user): string
    {
        $user->tokens()->delete();

        // TODO: Optionally pass abilities array to provide ability based authorization.
        return $user->createToken('api')->plainTextToken;
    }
}