<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\FakerController;
use \App\Http\Middleware\CheckAccessScopes;
use \App\Http\Middleware\Billable;

Route::middleware(['verify.shopify', CheckAccessScopes::class, Billable::class])->group(function () {
    Route::view('/', 'app')->name('home');
    Route::post('/fake-data', [FakerController::class, 'store']);
    Route::delete('/fake-data', [FakerController::class, 'destroy']);
});
