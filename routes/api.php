<?php

use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::controller(SubscriptionController::class)->group(function () {
    Route::post('/subscription', 'post');
    Route::patch('/subscription', 'patch');
    Route::delete('/subscription', 'delete');
});
