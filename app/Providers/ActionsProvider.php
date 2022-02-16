<?php

namespace App\Providers;

use App\Actions\Contracts\GeneratesToken;
use App\Actions\Contracts\RegistersUser;
use App\Actions\GeneratePersonalAccessToken;
use App\Actions\RegisterUser;
use Illuminate\Support\ServiceProvider;

class ActionsProvider extends ServiceProvider
{
    /**
     * Bind classes to contracts in service container.
     *
     * @var array
     */
    public array $bindings = [
        RegistersUser::class => RegisterUser::class,
        GeneratesToken::class => GeneratePersonalAccessToken::class
    ];
}
