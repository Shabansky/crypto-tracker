<?php

use App\Domain\Subscription\Application\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::controller(SubscriptionController::class)->group(function () {
    Route::get('/subscription', 'get');
    Route::post('/subscription', 'post');
    Route::patch('/subscription', 'patch');
    Route::delete('/subscription', 'delete');
});
