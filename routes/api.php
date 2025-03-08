<?php

use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post(
    '/subscription',
    [
        SubscriptionController::class,
        'post',
        ['request' => new Request()]
    ]
);

Route::patch(
    '/subscription',
    [
        SubscriptionController::class,
        'patch',
        ['request' => new Request(), 'id' => 1]
    ]
);

Route::delete(
    '/subscription',
    [
        SubscriptionController::class,
        'delete',
        ['id' => 1]
    ]
);
