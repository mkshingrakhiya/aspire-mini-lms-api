<?php

use App\Enums\LoanStatus;
use App\Enums\PaymentFrequency;
use App\Enums\UserRole;
use App\Events\LoanApproved;
use App\Events\LoanCreated;
use App\Events\LoanRejected;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;

test('clients can apply for a loan', function () {
    $reviewer = User::factory()->create(['role' => UserRole::REVIEWER]);
    $client = User::factory()->create()->refresh();
    
    $response = $this->actingAs($client, 'sanctum')->postJson(route('loans.store'), [
        'term' => 12,
        'amount' => 10000
    ]);

    $response->assertCreated()
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('client_id', $client->id)
                 ->where('reviewer_id', $reviewer->id)
                 ->where('annual_interest_rate', 10)
                 ->where('repayment_frequency', PaymentFrequency::WEEKLY->value)
                 ->where('status', LoanStatus::PROCESSING->value)
                 ->where('disbursed_at', null)
                 ->etc()
        );

    $this->assertDatabaseCount('repayments', 0);
});

test('reviewers should get forbidden error', function () {
    $reviewer = User::factory()->create(['role' => UserRole::REVIEWER])->refresh();
    
    $response = $this->actingAs($reviewer, 'sanctum')->postJson(route('loans.store'), [
        'term' => 12,
        'amount' => 10000
    ]);

    $response->assertForbidden();
});

it('should dispatch LoanCreated event after application', function () {
    Event::fake([LoanCreated::class]);
    
    $loan = Loan::factory()->create();

    Event::assertDispatched(LoanCreated::class);
    Event::assertDispatched(fn (LoanCreated $event) => $loan->id === $event->loan->id);
});

it('should not dispatch LoanApproved/LoanRejected events after application', function () {
    Event::fake([LoanCreated::class]);
    
    Loan::factory()->create();

    Event::assertNotDispatched(LoanApproved::class);
    Event::assertNotDispatched(LoanRejected::class);
});

it('is invalid', function (array $data, array $errors) {
    $user = User::factory()->create(['email' => 'borrower@aspire.localhost']);

    $response = $this->actingAs($user, 'sanctum')->postJson(route('loans.store'), $data);

    $response->assertInvalid($errors);
})->with([
    "term = null" => [['term' => null], ['term' => validationRequired('term')]],
    'term = 12 weeks' => [['term' => '12 Weeks'], ['term' => validationInteger('term')]],
    'term < 3 weeks' => [['term' => 2], ['term' => validationBetweenNumeric('term', 3, 52 * 10)]],
    'term > 10 years' => [['term' => 5201], ['term' => validationBetweenNumeric('term', 3, 52 * 10)]],

    'amount = null' => [['amount' => null], ['amount' => validationRequired('amount')]],
    'amount = foo' => [['amount' => 'foo'], ['amount' => validationNumeric('amount')]],
    'amount = 0' => [['amount' => 0], ['amount' => validationGreaterNumeric('amount', 0)]],
    'amount < 0' => [['amount' => -10], ['amount' => validationGreaterNumeric('amount', 0)]],
]);
