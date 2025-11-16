<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/subscription/check/{domain}', [\App\Http\Controllers\Api\SubscriptionController::class, 'check'])
    ->where('domain', '.*');