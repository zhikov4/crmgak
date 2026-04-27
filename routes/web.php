<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/leads', function () {
        return view('dashboard'); // sementara
    });

    Route::get('/pipeline', function () {
        return view('dashboard'); // sementara
    });

    Route::get('/projects', function () {
        return view('dashboard'); // sementara
    });
});

require __DIR__.'/auth.php';