<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DishController as AdminDishController;
use App\Http\Controllers\Admin\FoodController as AdminFoodController;
use App\Http\Controllers\Admin\RestaurantController as AdminRestaurantController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\TaxonomyController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HealthProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/foods', [HomeController::class, 'foods'])->name('foods.index');
Route::get('/foods/{food}', [HomeController::class, 'food'])->name('foods.show');
Route::get('/dishes', [HomeController::class, 'dishes'])->name('dishes.index');
Route::get('/dishes/{dish}', [HomeController::class, 'dish'])->name('dishes.show');
Route::get('/restaurants', [HomeController::class, 'restaurants'])->name('restaurants.index');
Route::get('/restaurants/{restaurant}', [HomeController::class, 'restaurant'])->name('restaurants.show');
Route::get('/compare', [HomeController::class, 'compare'])->name('compare');
Route::match(['get', 'post'], '/calculator', [HomeController::class, 'calculator'])->name('calculator');
Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::middleware(['auth', 'active'])->group(function (): void {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/suggestions', [HomeController::class, 'suggestions'])->name('suggestions');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/health-profile', [HealthProfileController::class, 'edit'])->name('health-profile.edit');
    Route::put('/health-profile', [HealthProfileController::class, 'update'])->name('health-profile.update');
    Route::patch('/preferences', [HealthProfileController::class, 'preferences'])->name('preferences.update');
    Route::get('/journal', [JournalController::class, 'index'])->name('journal.index');
    Route::post('/journal/entries', [JournalController::class, 'store'])->name('journal.store');
    Route::patch('/journal/entries/{entry}', [JournalController::class, 'update'])->name('journal.update');
    Route::delete('/journal/entries/{entry}', [JournalController::class, 'destroy'])->name('journal.destroy');
    Route::post('/journal/water', [JournalController::class, 'water'])->name('journal.water');
    Route::post('/journal/weight', [JournalController::class, 'weight'])->name('journal.weight');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{type}/{id}', [FavoriteController::class, 'toggle'])->whereIn('type', ['dish', 'food', 'restaurant'])->name('favorites.toggle');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'active', 'admin'])->group(function (): void {
    Route::get('/', AdminDashboardController::class)->name('dashboard');
    Route::resource('foods', AdminFoodController::class)->except('show');
    Route::resource('dishes', AdminDishController::class)->except('show');
    Route::resource('restaurants', AdminRestaurantController::class)->except('show');
    Route::get('/taxonomies', [TaxonomyController::class, 'index'])->name('taxonomies.index');
    Route::post('/taxonomies/{type}/{id?}', [TaxonomyController::class, 'save'])->whereIn('type', ['group', 'unit', 'allergy'])->name('taxonomies.save');
    Route::delete('/taxonomies/{type}/{id}', [TaxonomyController::class, 'destroy'])->whereIn('type', ['group', 'unit', 'allergy'])->name('taxonomies.destroy');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
