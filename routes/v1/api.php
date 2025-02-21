<?php

use App\Http\Controllers\Api\V1;
use Illuminate\Support\Facades\Route;

Route::group([], function () {
    Route::get('/search', [V1\SearchController::class, 'index']);
    Route::post('/alerts', [V1\AlertController::class, 'store']);
});
