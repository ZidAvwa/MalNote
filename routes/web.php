<?php

use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;

Route::get('/search', SearchController::class)
    ->middleware('throttle:60,1')
    ->name('search');
Route::get('/', [ImportController::class, 'create'])->name('imports.create');
Route::post('/imports', [ImportController::class, 'store'])->name('imports.store');
Route::get('/imports/{session}', [ImportController::class, 'show'])->name('imports.show');
