<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [RecipeController::class, 'index'])->name('home');
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/recipes/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');
Route::get('/users/{user}', [UserProfileController::class, 'show'])->name('users.show');

// Static pages
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/about', 'pages.about')->name('about');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Ingredient autocomplete
    Route::get('/api/ingredients', [IngredientController::class, 'search'])->name('api.ingredients.search');

    // Recipe CRUD
    Route::get('/recipes/new/create', [RecipeController::class, 'create'])->name('recipes.create');
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('/recipes/{recipe}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/recipes/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

    // Voting
    Route::post('/recipes/{recipe}/vote', [VoteController::class, 'toggle'])
        ->middleware('throttle:30,1')
        ->name('recipes.vote');

    // Reporting
    Route::post('/recipes/{recipe}/report', [ReportController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('recipes.report');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::post('/reports/{report}/dismiss', [AdminController::class, 'dismissReport'])->name('reports.dismiss');
    Route::post('/recipes/{recipe}/archive', [AdminController::class, 'archiveRecipe'])->name('recipes.archive');
    Route::post('/recipes/{recipe}/restore', [AdminController::class, 'restoreRecipe'])->name('recipes.restore');
});

require __DIR__.'/auth.php';
