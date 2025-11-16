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
	// user management
	Route::get('/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('admin.users');
	Route::post('/users/{id}', [\App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('admin.users.update');
    // bulk delete users (selected)
    Route::post('/users/delete-multiple', [\App\Http\Controllers\Admin\UserManagementController::class, 'bulkDelete'])->name('admin.users.bulk_delete');
	Route::post('/users/{id}/delete', [\App\Http\Controllers\Admin\UserManagementController::class, 'deleteSingle'])->name('admin.users.delete_single');

	// user region role assignments
	Route::post('/users/{id}/roles', [\App\Http\Controllers\Admin\UserRegionRoleController::class, 'store'])->name('admin.users.roles.store');
	Route::delete('/users/roles/{id}', [\App\Http\Controllers\Admin\UserRegionRoleController::class, 'destroy'])->name('admin.users.roles.destroy');
    // AJAX endpoint to fetch allowed regions for a target role based on current user's scope
    Route::get('/allowed-regions', [\App\Http\Controllers\Admin\UserManagementController::class, 'allowedRegions'])->name('admin.allowed_regions');
    // Master regions CRUD
    Route::resource('regions', \App\Http\Controllers\Admin\RegionController::class)->names('admin.regions');
	// other masters
	Route::resource('facilities', \App\Http\Controllers\Admin\FacilityController::class)->names('admin.facilities');
	Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->names('admin.categories');
	Route::resource('mosques', \App\Http\Controllers\Admin\MosqueController::class)->names('admin.mosques');
		// photo management for mosques
		Route::delete('mosque-photos/{photo}', [\App\Http\Controllers\Admin\MosquePhotoController::class, 'destroy'])->name('admin.mosque_photos.destroy');
});

Route::view('/', 'home');
Route::view('/mosque', 'mosque');



