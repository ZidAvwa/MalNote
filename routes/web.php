<?php

use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ExportController;

Route::get('/imports/{session}/export', ExportController::class)->name('imports.export');
Route::post('/imports/{session}/submit', [ReviewController::class, 'submit'])->name('imports.submit');
Route::post('/imports/{session}/skip', [ReviewController::class, 'skip'])->name('imports.skip');
Route::post('/imports/{session}/back', [ReviewController::class, 'back'])->name('imports.back');
Route::get('/search', SearchController::class)
    ->middleware('throttle:60,1')
    ->name('search');
Route::get('/', [ImportController::class, 'create'])->name('imports.create');
Route::post('/imports', [ImportController::class, 'store'])->name('imports.store');
Route::get('/imports/{session}', [ImportController::class, 'show'])->name('imports.show');
Route::get('/imports', [ImportController::class, 'index'])->name('imports.index');