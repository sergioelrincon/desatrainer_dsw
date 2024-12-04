<?php

use Illuminate\Support\Facades\Route;

/**
 * Redirigimos la página principal a la de login
 */
Route::get('/', function () {
	return redirect()->route('login');
});

/*
Route::get('/', function () {
    return view('welcome');
});
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
