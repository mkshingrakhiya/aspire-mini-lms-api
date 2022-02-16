<?php

use App\Enums\LoanStatus;
use App\Events\LoanApproved;
use App\Events\LoanRejected;
use App\Models\Loan;
use Illuminate\Testing\Fluent\AssertableJson;

test('reviewer can approve loan', function () {
    $loan = Loan::factory()->create(['term' => 3])->refresh();
    
    $this->actingAs($loan->reviewer, 'sanctum')
        ->putJson(route('loans.update-status', [$loan, 'approve']))
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            $json->has('disbursed_at')
                ->where('reviewer_id', $loan->reviewer->id)
                ->where('status', LoanStatus::APPROVED->value)
                ->where('is_disbursed', true)
                ->etc()
        );
});

test('reviewer can reject loan', function () {
    $loan = Loan::factory()->create(['term' => 6])->refresh();
    
    $response = $this->actingAs($loan->reviewer, 'sanctum')->putJson(route('loans.update-status', [$loan, 'reject']));

    $response->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            $json->where('reviewer_id', $loan->reviewer->id)
                 ->where('status', LoanStatus::REJECTED->value)
                 ->where('disbursed_at', null)
                 ->etc()
        );

    $this->assertDatabaseCount('repayments', 0);
});

test('client can not update loan status', function (string $action) {
    $loan = Loan::factory()->create(['term' => 3])->refresh();
    
    $this->actingAs($loan->client, 'sanctum')
        ->putJson(route('loans.update-status', [$loan, $action]))
        ->assertForbidden();
})->with([
    'for approval' => ['approve'],
    'for rejection' => ['reject'],
]);

it('should dispatch LoanApproved event on approval', function () {
    Event::fake([LoanApproved::class]);

    $loan = Loan::factory()->create(['term' => 3])->refresh();
    
    $this->actingAs($loan->reviewer, 'sanctum')->putJson(route('loans.update-status', [$loan, 'approve']));

    Event::assertDispatched(LoanApproved::class);
    Event::assertDispatched(fn (LoanApproved $event) => $loan->id === $event->loan->id);
});

it('should dispatch LoanRejected event on approval', function () {
    Event::fake([LoanRejected::class]);

    $loan = Loan::factory()->create(['term' => 3])->refresh();
    
    $this->actingAs($loan->reviewer, 'sanctum')->putJson(route('loans.update-status', [$loan, 'reject']));

    Event::assertDispatched(LoanRejected::class);
    Event::assertDispatched(fn (LoanRejected $event) => $loan->id === $event->loan->id);
});

it('should not dispatch LoanRejected event on approval', function () {
    Event::fake([LoanApproved::class]);

    $loan = Loan::factory()->create(['term' => 3])->refresh();
    
    $this->actingAs($loan->reviewer, 'sanctum')->putJson(route('loans.update-status', [$loan, 'approve']));

    Event::assertNotDispatched(LoanRejected::class);
});

it('should not dispatch LoanAccepted event on rejection', function () {
    Event::fake([LoanRejected::class]);

    $loan = Loan::factory()->create(['term' => 3])->refresh();
    
    $this->actingAs($loan->reviewer, 'sanctum')->putJson(route('loans.update-status', [$loan, 'reject']));

    Event::assertNotDispatched(LoanApproved::class);
});

test('term should have x repayment entries', function (int $term) {
    $loan = Loan::factory()->create(['term' => $term, 'repayment_frequency' => 'W'])->refresh();
    
    $this->actingAs($loan->reviewer, 'sanctum')->putJson(route('loans.update-status', [$loan, 'approve']));

    $this->assertDatabaseCount('repayments', $loan->term);
})->with([
    '3 weeks' => [3],
    '6 weeks' => [6],
    '9 weeks' => [9],
    '12 weeks' => [12],
    '24 weeks' => [24],
    '1 years' => [1 * 52],
    '3 years' => [3 * 52],
    '5 years' => [5 * 52],
    '8 years' => [8 * 52],
    '10 years' => [10 * 52],
]);