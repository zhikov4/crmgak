<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/pipeline', function () {
        return view('dashboard'); // sementara
    });

    Route::get('/projects', function () {
        return view('dashboard'); // sementara
    });

    Route::resource('leads', LeadController::class);
});

require __DIR__.'/auth.php';
