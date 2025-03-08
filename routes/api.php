<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/subscription', function (Request $request) {
    return 'New Subscription';
});

Route::patch('/subscription', function (Request $request) {
    return 'Edit Existing Subscription';
});

Route::delete('/subscription', function (Request $request) {
    return 'Delete Existing Subscription';
});
