<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanRepaymentController;
use App\Http\Controllers\SubmitRepaymentController;
use App\Http\Controllers\UpdateLoanStatusController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', RegisterController::class)->name('auth.register');
Route::post('login', LoginController::class)->name('auth.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('loans', [LoanController::class, 'index'])->name('loans.index');
    Route::post('loans', [LoanController::class, 'store'])->name('loans.store');
    
    Route::put('loans/{loan}/{status}', UpdateLoanStatusController::class)
        ->where('status', 'approve|reject')
        ->name('loans.update-status');

    Route::get('loans/{loan}/repayments', [LoanRepaymentController::class, 'index'])->name('loans.repayments');

    Route::put('loans/repayments/{repayment}/submit', SubmitRepaymentController::class)
        ->name('loans.repayments.submit');
});
