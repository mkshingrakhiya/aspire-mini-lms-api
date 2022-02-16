<?php

namespace App\Actions\Contracts;

use App\Models\User;

interface GeneratesToken
{
    public function __invoke(User $user): string;
}
