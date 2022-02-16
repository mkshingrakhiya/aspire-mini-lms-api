<?php

namespace App\Listeners;

use App\Events\LoanRejected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendLoanRejectedNotification
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
     * @param  LoanRejected  $event
     * @return void
     */
    public function handle(LoanRejected $event)
    {
        // TODO: Send Loan Rejected Notification to client
    }
}
