<?php

use App\Actions\Contracts\GeneratesToken;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
    $this->route = route('auth.login');
});

it('should login', function (array $data, array $credentials) {
    User::factory()->create($data);

    $this->postJson($this->route, $credentials)
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            $json->hasAll('name', 'email', '_token')
                ->where('name', $data['name'])
                ->where('email', $data['email'])
                ->where('role', $data['role'] ?? UserRole::CLIENT->value)
                ->missing('password')
                ->etc()
        );
})->with([
    'for client' => [
        ['name' => 'John Doe', 'email' => 'borrower@aspire.localhost', 'password' => '12345678'],
        ['email' => 'borrower@aspire.localhost', 'password' => '12345678']
    ],
    'for reviewer' => [
        ['name' => 'Jane Doe', 'email' => 'reviewer@aspire.localhost', 'role' => 'REVIEWER'],
        ['email' => 'reviewer@aspire.localhost', 'password' => 'password']
    ]
])->shouldHaveCalledAction(GeneratesToken::class);

it('is invalid', function (array $data, array $errors) {
    User::factory()->create(['email' => 'borrower@aspire.localhost']);

    $this->postJson($this->route, $data)->assertInvalid($errors);
})->with([
    'email = null' => [['email' => null], ['email' => validationRequired('email')]],
    'email = foo' => [['email' => 'foo'], ['email' => validationEmail('email')]],
    'email > 255 characters' => [['email' => str_repeat('a', 256)], ['email' => validationMaxString('email', 255)]],
    
    "password = null" => [['password' => null], ['password' => validationRequired('password')]],
    'password < 8 characters' => [['password' => '1234567'], ['password' => validationMinString('password', 8)]],
]);
