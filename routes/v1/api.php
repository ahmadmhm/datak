<?php

use Illuminate\Support\Facades\Route;

Route::group([], function () {
    Route::prefix('s')->group(function () {
        Route::get('', [\App\Http\Controllers\Api\V1\SearchController::class, 'index']);
    });
});
