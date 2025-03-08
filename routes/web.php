<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;

Route::get('/{any}', function () {
    return new Response(status: 404);
});
