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
// Public AJAX endpoint to fetch region children (used by Create form dependent selects).
// Keep this route outside the 'auth' middleware so SPA/ajax requests from the Create
// page (which may be unauthenticated in some flows) can receive a JSON response
// instead of an auth redirect/401 that would break client-side logic.
Route::get('admin/regions/children', [\App\Http\Controllers\Admin\RegionController::class, 'children'])
	->name('admin.regions.children');

Route::middleware(['auth'])->prefix('admin')->group(function () {
	Route::get('/pending-users', [UserApprovalController::class, 'index'])->name('admin.pending');
	Route::post('/approve-user/{id}', [UserApprovalController::class, 'approve'])->name('admin.approve');
	// user management
	Route::get('/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('admin.users');
	Route::get('/users/create', [\App\Http\Controllers\Admin\UserManagementController::class, 'create'])->name('admin.users.create');
	Route::post('/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'store'])->name('admin.users.store');
	Route::post('/users/{id}', [\App\Http\Controllers\Admin\UserManagementController::class, 'update'])->name('admin.users.update');
	// Toggle approved/unapproved via AJAX (returns JSON)
	Route::post('/users/{id}/toggle-approved', [\App\Http\Controllers\Admin\UserManagementController::class, 'toggleApproved'])->name('admin.users.toggle_approved');
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
	// AJAX endpoint: return direct children for a given parent_id (optionally filter by level)
	// NOTE: the actual route is registered above outside the auth middleware so
	// client-side AJAX calls won't be intercepted by auth middleware. Keep this
	// comment here to explain why there is no in-group route definition.
	// other masters
	Route::resource('facilities', \App\Http\Controllers\Admin\FacilityController::class)->names('admin.facilities');
	Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->names('admin.categories');
	Route::resource('activities', \App\Http\Controllers\Admin\ActivityController::class)->names('admin.activities');
	Route::resource('subsidiaries', \App\Http\Controllers\Admin\SubsidiaryController::class)->names('admin.subsidiaries');
	Route::resource('mosques', \App\Http\Controllers\Admin\MosqueController::class)->names('admin.mosques');
		// photo management for mosques
		Route::delete('mosque-photos/{photo}', [\App\Http\Controllers\Admin\MosquePhotoController::class, 'destroy'])->name('admin.mosque_photos.destroy');

		// Facilities management endpoints (used by admin UI JS)
		Route::get('mosques/{mosque}/facilities', [\App\Http\Controllers\Admin\MosqueFacilityController::class, 'show']);
		Route::post('mosques/{mosque}/facilities', [\App\Http\Controllers\Admin\MosqueFacilityController::class, 'update']);

		// Mosque-facility photos endpoints
		Route::get('mosques/{mosque}/facilities/{facility}/photos', [\App\Http\Controllers\Admin\MosqueFacilityPhotoController::class, 'index']);
		Route::post('mosques/{mosque}/facilities/{facility}/photos', [\App\Http\Controllers\Admin\MosqueFacilityPhotoController::class, 'store']);
		Route::patch('mosque-facility-photos/{photo}', [\App\Http\Controllers\Admin\MosqueFacilityPhotoController::class, 'update']);
		Route::delete('mosque-facility-photos/{photo}', [\App\Http\Controllers\Admin\MosqueFacilityPhotoController::class, 'destroy']);
});

Route::get('/', [App\Http\Controllers\Home\Beranda\BerandaController::class, 'index']);

// Autocomplete suggestions (used by frontend JS)
Route::get('/search/suggestions', [\App\Http\Controllers\Home\Beranda\SearchController::class, 'suggestions'])->name('search.suggestions');

// Search results page
Route::get('/search', [\App\Http\Controllers\Home\Beranda\SearchController::class, 'results'])->name('search.results');
// Backward-compatibility alias: some templates or code may reference route('search')
Route::get('/search', [\App\Http\Controllers\Home\Beranda\SearchController::class, 'results'])->name('search');

// Frontend mosque detail (use {mosque} for route-model binding)
Route::get('/masjid/{mosque}', [\App\Http\Controllers\Home\Mosque\MosqueController::class, 'show'])->name('mosque.show');

Route::get('/masjid', [App\Http\Controllers\Home\Mosque\MosqueController::class, 'index'])->name('masjid');
Route::get('/musholla', [\App\Http\Controllers\Home\Mosque\MosqueController::class, 'mushallaIndex'])->name('mushalla');

// Contact form submit
Route::get('/contact', function () {return view('home.contact.index');})->name('contact');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');

// Article form submit
Route::get('/article', function () {return view('home.article.index');})->name('article');
// Article detail
Route::get('/article/{id}', [\App\Http\Controllers\Home\ArticleController::class, 'show'])->name('article.show');
