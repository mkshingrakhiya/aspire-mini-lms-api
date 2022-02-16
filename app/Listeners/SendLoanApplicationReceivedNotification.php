<?php

namespace App\Listeners;

use App\Events\LoanCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLoanApplicationReceivedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LoanCreated  $event
     * @return void
     */
    public function handle(LoanCreated $event)
    {
        // TODO: Send Loan Application Received Notification to reviewer
    }
}
