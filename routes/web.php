<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\FundController;
use App\Http\Controllers\ContributionController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PenaltyController;
use App\Http\Controllers\InterestDistributionController;

// Penalty payment route


// Home Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Members Routes
Route::resource('members', MemberController::class);
// Route::get('/members/{member}', [MemberController::class, 'show'])->name('members.show');

Route::post('/penalties/{penalty}/pay', [PenaltyController::class, 'pay'])->name('penalties.pay');
Route::get('/penalties/search', [PenaltyController::class, 'search'])->name('penalties.search');
// Funds Routes
Route::resource('funds', FundController::class);

// Contributions Routes
Route::get('/contributions/search', [ContributionController::class, 'search'])->name('contributions.search');
Route::resource('contributions', ContributionController::class);

// Loans Routes
Route::get('/loans/search', [LoanController::class, 'search'])->name('loans.search');
Route::resource('loans', LoanController::class);
// Loan repayment route
Route::post('/loans/{loan}/repay', [LoanController::class, 'repay'])->name('loans.repay');

use App\Http\Controllers\TransferController;

// Penalties Routes
Route::resource('penalties', PenaltyController::class);

Route::resource('transfers', TransferController::class);

// Interest Distribution Routes
Route::resource('interest-distributions', InterestDistributionController::class);

Route::post('/interest-distributions/calculate/{member}', [InterestDistributionController::class, 'calculateMemberShare'])
    ->name('interest-distributions.calculate');





