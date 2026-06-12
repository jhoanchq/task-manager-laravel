<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('/dashboard', '/tasks')->name('dashboard');
    Route::resource('tasks', TaskController::class);
});

require __DIR__.'/auth.php';
