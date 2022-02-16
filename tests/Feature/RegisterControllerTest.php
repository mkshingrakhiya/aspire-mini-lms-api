<?php

use App\Actions\Contracts\GeneratesToken;
use App\Actions\Contracts\RegistersUser;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('can register', function (array $data, array $expected) {
    $response = $this->postJson(route('auth.register'), $data);

    $response->assertCreated()
        ->assertJson(fn (AssertableJson $json) =>
            $json->hasAll('name', 'email', '_token')
                ->where('name', $expected['name'] ?? $data['name'])
                ->where('email', $expected['email'] ?? $data['email'])
                ->where('role', $expected['role'] ?? $data['role'] ?? UserRole::CLIENT->value)
                ->missing('password')
                ->etc()
        );

    expect(User::query()->exists())->toBeTrue();
    expect(User::firstWhere('email', $expected['email'])->tokens()->exists())->toBeTrue();
})->with([
    'for client' => [[
        'email' => 'borrower@aspire.localhost',
        'name' => 'John Doe',
        'password' => '12345678',
        'password_confirmation' => '12345678',
    ], [
        'email' => 'borrower@aspire.localhost',
        'role' => UserRole::CLIENT->value
    ]],
    'for reviewer' => [[
        'email' => 'reviewer@aspire.localhost',
        'name' => 'Jane Doe',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'REVIEWER'
    ], [
        'email' => 'reviewer@aspire.localhost',
        'role' => UserRole::REVIEWER->value
    ]]
])->shouldHaveCalledAction(RegistersUser::class, GeneratesToken::class);

it('is invalid', function (array $data, array $errors) {
    User::factory()->create(['email' => 'borrower@aspire.localhost']);

    $this->postJson(route('auth.register'), $data)->assertInvalid($errors);

    expect(User::query()->count())->toBe(1);
    // expect(User::firstWhere('email', $data['email'])->tokens()->exists())->toBeFalse();
})->with([
    "name = null" => [['name' => null], ['name' => validationRequired('name')]],
    'name = 123' => [['name' => 123], ['name' => validationString('name')]],
    'name > 255 characters' => [['name' => str_repeat('a', 256)], ['name' => validationMaxString('name', 255)]],

    'email = null' => [['email' => null], ['email' => validationRequired('email')]],
    'email = foo' => [['email' => 'foo'], ['email' => validationEmail('email')]],
    'email > 255 characters' => [['email' => str_repeat('a', 256)], ['email' => validationMaxString('email', 255)]],
    'email = borrower@aspire.localhost' => [
        ['email' => 'borrower@aspire.localhost'],
        ['email' => validationUnique('email')]
    ],
    
    'role = ADMIN' => [['role' => 'ADMIN'], ['role' => validationEnum('role')]],
    'role = client' => [['role' => 'client'], ['role' => validationEnum('role')]],
    
    "password = null" => [['password' => null], ['password' => validationRequired('password')]],
    'password < 8 characters' => [['password' => '1234567'], ['password' => validationMinString('password', 8)]],
    'password != password_confirmation' => [
        ['password' => '12345670', 'password_confirmation' => '12345678'],
        ['password' => validationConfirmed('password')]
    ],
]);
