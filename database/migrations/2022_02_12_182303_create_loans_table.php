<?php

use App\Enums\LoanStatus;
use App\Enums\PaymentFrequency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('client_id');
            $table->double('amount');
            $table->unsignedInteger('term')->comment('Loan tenure in weeks');
            $table->float('annual_interest_rate', 4, 2);
            $table->char('repayment_frequency')->default(PaymentFrequency::WEEKLY->value);
            $table->unsignedBigInteger('reviewer_id');
            $table->unsignedTinyInteger('status')->default(LoanStatus::PROCESSING->value);
            $table->timestamp('disbursed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('users');
            $table->foreign('reviewer_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
};
