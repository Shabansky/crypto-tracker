<?php

use App\Services\TickerProviders\BitfinexTickerProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //

    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->call(function () {
            $provider = new BitfinexTickerProvider();
            $response = $provider->get();

            $logMessage = sprintf(
                "Price is %s at %s",
                $response?->price,
                $response?->time->format('Y-m-d H:i:s')
            );

            Log::notice($logMessage);
        })->hourly();
    })->create();
