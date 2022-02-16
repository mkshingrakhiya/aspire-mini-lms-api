<?php

use App\Models\Loan;
use Illuminate\Testing\Fluent\AssertableJson;

test('clients can submit repayment', function () {
    $loan = Loan::factory()->create(['term' => 3])->refresh();
    $loan->updateStatus('approve');

    $repayment = $loan->repayments()->first();
    
    $this->actingAs($loan->client, 'sanctum')
        ->putJson(route('loans.repayments.submit', [$repayment]))
        ->assertOk()
        ->assertJson(fn (AssertableJson $json) =>
            $json->hasAll(['due', 'interest', 'principal', 'outstanding', 'loan'])
                ->where('is_paid', true)
                ->etc()
        );
});

test('reviewers can not submit repayment', function () {
    $loan = Loan::factory()->create(['term' => 3])->refresh();
    $loan->updateStatus('approve');

    $repayment = $loan->repayments()->first();
    
    $this->actingAs($loan->reviewer, 'sanctum')
        ->putJson(route('loans.repayments.submit', [$repayment]))
        ->assertForbidden();
});

test('clients can not submit same repayment again', function () {
    $loan = Loan::factory()->create(['term' => 3])->refresh();
    $loan->updateStatus('approve');

    $repayment = $loan->repayments()->first();
    $repayment->submit();
    
    $this->actingAs($loan->client, 'sanctum')
        ->putJson(route('loans.repayments.submit', $repayment))
        ->assertStatus(500)
        ->assertJson([
            'message' => 'The repayment has been already submitted.'
        ]);
});
