<?php

namespace App\Models;

class HourlyTickerRepository
{
    /**
     * Get the latest tickers with a non-null time, limited to a specified number.
     *
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getLatestTickersPrices(int $limit = 25)
    {
        return HourlyTicker::select('price')
            ->whereNotNull('time')
            ->orderByDesc('time')
            ->limit($limit)
            ->get();
    }
}
