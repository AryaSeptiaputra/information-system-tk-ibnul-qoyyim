<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing.index');
})->name('landing.index');

require __DIR__.'/auth.php';
