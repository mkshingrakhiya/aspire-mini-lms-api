<?php

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
        Schema::create('repayments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('loan_id')->constrained();
            $table->unsignedDouble('due', 15, 8);
            $table->unsignedDouble('interest', 15, 8);
            $table->unsignedDouble('outstanding', 15, 8);
            $table->timestamp('due_on')->nullable();
            $table->timestamp('paid_on')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('repayments');
    }
};
