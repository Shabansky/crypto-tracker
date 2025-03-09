<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{

    protected $fillable = [
        'email',
        'timeframe',
        'percentageThreshold'
    ];

    protected string $email;

    protected int $timeframe;

    protected float $percentageThreshold;
}
