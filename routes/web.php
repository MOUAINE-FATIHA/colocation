<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\BannedMiddleware;
use App\Http\Controllers\ColocationController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\PaymentController;


use Illuminate\Support\Facades\Mail;
// Route::get('/test-mail', function () {
//     Mail::raw('Hadchi test mail men Laravel', function ($message) {
//         $message->to('test@example.com')
//                 ->subject('Test Mailtrap');
//     });
//     return 'Email sent!';
// });


Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth', 'verified', BannedMiddleware::class])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/colocations/create', [ColocationController::class, 'create'])->name('colocations.create');
    Route::post('/colocations', [ColocationController::class, 'store'])->name('colocations.store');
    Route::get('/colocations/{colocation}', [ColocationController::class, 'show'])->name('colocations.show');
    Route::post('/colocations/{colocation}/cancel', [ColocationController::class, 'cancel'])->name('colocations.cancel');

    Route::post('/colocations/{colocation}/leave', [ColocationController::class, 'leave'])->name('colocations.leave');

    Route::get('/colocations/{colocation}/invite', [InvitationController::class, 'create'])->name('invitations.create');

    Route::post('/colocations/{colocation}/invite', [InvitationController::class, 'store'])->name('invitations.store');
    Route::get('/invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');

    Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
    Route::post('/invitations/{token}/refuse', [InvitationController::class, 'refuse'])->name('invitations.refuse');

    Route::delete('/colocations/{colocation}/members/{user}', [ColocationController::class, 'removeMember'])
    ->name('colocations.removeMember');
    Route::get('/colocations/{colocation}/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/colocations/{colocation}/categories', [CategoryController::class, 'store'])->name('categories.store');

    Route::delete('/colocations/{colocation}/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');



    Route::get('/colocations/{colocation}/expenses', [ExpenseController::class, 'index'])->name('expenses.index');

    Route::get('/colocations/{colocation}/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');

    Route::post('/colocations/{colocation}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

    Route::delete('/colocations/{colocation}/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    Route::get('/colocations/{colocation}/balances', [BalanceController::class, 'index'])
        ->name('balances.index');
    Route::post('/colocations/{colocation}/payments', [PaymentController::class, 'store'])
        ->name('payments.store');
    Route::get('/colocations/{colocation}/payments', [PaymentController::class, 'index'])
        ->name('payments.index');

    Route::middleware([RoleMiddleware::class . ':admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::post('/users/{user}/ban', [AdminController::class, 'ban'])->name('users.ban');
        Route::post('/users/{user}/unban', [AdminController::class, 'unban'])->name('users.unban');
    });
});

require __DIR__ . '/auth.php';