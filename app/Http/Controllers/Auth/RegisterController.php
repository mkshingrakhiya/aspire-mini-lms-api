<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Contracts\RegistersUser;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  Request  $request
     * @param  RegistersUser  $registerUser
     * @return UserResource
     */
    public function __invoke(Request $request, RegistersUser $registerUser): UserResource
    {
        return new UserResource($registerUser($request->all()));
    }
}
