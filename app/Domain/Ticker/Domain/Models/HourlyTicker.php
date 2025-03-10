<?php

namespace App\Domain\Ticker\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HourlyTicker extends Model
{
    /** @use HasFactory<\Database\Factories\HourlyTickerFactory> */
    use HasFactory;

    protected $fillable = [
        'price',
        'time'
    ];

    protected $casts = [
        'price' => 'float',
        'time' => 'datetime',
    ];

    protected float $price;

    protected \DateTime $time;
}
