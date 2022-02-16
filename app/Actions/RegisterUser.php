<?php

namespace App\Actions;

use App\Actions\Contracts\GeneratesToken;
use App\Actions\Contracts\RegistersUser;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class RegisterUser implements RegistersUser
{
    /**
     * Handle registration of user.
     *
     * @param array $data
     * @return User
     */
    public function __invoke(array $data): User
    {
        $validated = Validator::validate($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'role' => ['nullable', new Enum(UserRole::class)],
            'password' => ['required', Password::defaults(), 'confirmed']
        ]);

        return User::create($validated);
    }
}