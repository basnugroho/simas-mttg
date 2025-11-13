<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserApprovalController;


// Dashboard: hanya butuh autentikasi (auth). Verified biasanya memeriksa email/akun terverifikasi.
// Provide explicit GET view route for login so front 'Login' link always loads the login form.
// Fortify handles the POST /login authentication; this GET route maps to the Blade view.
Route::view('/login', 'auth.login')->middleware('guest')->name('login');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

// Admin (webmaster) routes to approve/promote users
Route::middleware(['auth'])->prefix('admin')->group(function () {
	Route::get('/pending-users', [UserApprovalController::class, 'index'])->name('admin.pending');
	Route::post('/approve-user/{id}', [UserApprovalController::class, 'approve'])->name('admin.approve');
});

Route::view('/', 'home');
Route::view('/mosque', 'mosque');



