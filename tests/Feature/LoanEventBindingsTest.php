<?php

use App\Events\LoanApproved;
use App\Events\LoanCreated;
use App\Events\LoanRejected;
use App\Listeners\SendLoanApplicationReceivedNotification;
use App\Listeners\SendLoanApprovedNotification;
use App\Listeners\SendLoanRejectedNotification;
use Illuminate\Support\Facades\Event;

test('SendLoanApplicationReceivedNotification is attached to LoanCreated', function () {
    Event::fake();
    Event::assertListening(LoanCreated::class, SendLoanApplicationReceivedNotification::class);
});

test('SendLoanApproved is attached to LoanApproved', function () {
    Event::fake();
    Event::assertListening(LoanApproved::class, SendLoanApprovedNotification::class);
});

test('SendLoanRejected is attached to LoanRejected', function () {
    Event::fake();
    Event::assertListening(LoanRejected::class, SendLoanRejectedNotification::class);
});
