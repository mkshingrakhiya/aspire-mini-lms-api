<?php

use App\Enums\LoanStatus;
use App\Enums\PaymentFrequency;
use App\Models\Loan;
use Carbon\CarbonInterval;

it('can return a carbon interval', function (array $data, $expected) {
    $loan = Loan::factory()->create($data);

    expect($loan->carbon_interval)->toEqual($expected);
})->with([
    [[], CarbonInterval::weeks(1)],
    [['repayment_frequency' => PaymentFrequency::WEEKLY], CarbonInterval::weeks(1)],
    [['repayment_frequency' => PaymentFrequency::MONTHLY], CarbonInterval::months(1)],
    [['repayment_frequency' => PaymentFrequency::QUARTERLY], CarbonInterval::months(3)],
    [['repayment_frequency' => PaymentFrequency::YEARLY], CarbonInterval::year()]
]);

it('can return a real interest rate', function (float $annualInterestRate, $rate) {
    $loan = Loan::factory()->create(['annual_interest_rate' => $annualInterestRate]);

    expect($loan->interest_rate)->toEqual($rate);
})->with([
    [8.25, 8.25 / 100 / 52],
    [9.5, 9.5 / 100 / 52],
    [4, 4 / 100 / 52],
    [6.8, 6.8 / 100 / 52],
    [4.99, 4.99 / 100 / 52]
]);

test('is_processing', function () {
    $loan = Loan::factory()->create(['status' => LoanStatus::PROCESSING]);

    expect($loan->is_processing)->toBeTrue();
});

test('is_approved', function () {
    $loan = Loan::factory()->create(['status' => LoanStatus::APPROVED]);

    expect($loan->is_approved)->toBeTrue();
});

test('is_rejected', function () {
    $loan = Loan::factory()->create(['status' => LoanStatus::REJECTED]);

    expect($loan->is_rejected)->toBeTrue();
});

test('is_disbursed', function ($disbursedAt, $expected) {
    $loan = Loan::factory()->create(['disbursed_at' => $disbursedAt]);

    expect($loan->is_disbursed)->toBe($expected);
})->with([[now(), true], [null, false]]);

it('can return a repayment frequency text', function (array $data, $expected) {
    $loan = Loan::factory()->create($data);

    expect($loan->repayment_frequency_text)->toEqual(PaymentFrequency::from($expected)->name);
})->with([
    [[], 'W'],
    [['repayment_frequency' => PaymentFrequency::WEEKLY],'W'],
    [['repayment_frequency' => PaymentFrequency::MONTHLY],'M'],
    [['repayment_frequency' => PaymentFrequency::QUARTERLY],'Q'],
    [['repayment_frequency' => PaymentFrequency::YEARLY],'Y']
]);

it('can return a status text', function (array $data, $expected) {
    $loan = Loan::factory()->create($data)->refresh();

    expect($loan->status_text)->toEqual(LoanStatus::from($expected)->name);
})->with([
    [[], 1],
    [['status' => LoanStatus::PROCESSING], 1],
    [['status' => LoanStatus::APPROVED], 2],
    [['status' => LoanStatus::REJECTED], 3]
]);
