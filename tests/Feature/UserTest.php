<?php

use App\Enums\UserRole;
use App\Models\User;

test('is client', function () {
    $user = User::factory()->create()->refresh();

    expect($user->is_client)->tobeTrue();
});

test('is reviewer', function () {
    $user = User::factory(['role' => UserRole::REVIEWER])->create()->refresh();

    expect($user->is_reviewer)->toBeTrue();
});
