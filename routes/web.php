<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');


Route::get('dashboard', [UserController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::resource('users', UserController::class)->only(['show', 'store', 'update', 'destroy'])->middleware(['auth', 'verified']);

require __DIR__.'/settings.php';