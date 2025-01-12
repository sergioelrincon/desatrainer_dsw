<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ScenarioController;
use App\Http\Controllers\Admin\DesatrainerController;

/**
 * Redirigimos la página principal a la de login
 */
Route::get('/', function () {
	return redirect()->route('login');
});

/**
 * Rutas de autenticación
 */
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Definición de rutas de usuarios mediante Route::resource (https://ies-el-rincon.gitbook.io/dsw/laravel/routing/route-resource)
    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('scenarios', ScenarioController::class);
    Route::resource('desa-trainers', DesatrainerController::class);
 });

 /**
  * Dashboard de usuario
  */
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    
});
