<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Contracts\GeneratesToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle the user login.
     *
     * @param  LoginUserRequest  $request
     * @param  GeneratesToken  $generateToken
     * @return UserResource
     *
     * @throws ValidationException
     */
    public function __invoke(LoginUserRequest $request, GeneratesToken $generateToken): UserResource
    {
        $validated = $request->validated();

        if (! Auth::attempt(Arr::only($validated, ['email', 'password']))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed')
            ]);
        }

        $user = User::firstWhere('email', $validated['email']);
        $user->token = $generateToken($user);

        return new UserResource($user);
    }
}
