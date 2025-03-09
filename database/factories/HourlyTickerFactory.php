<?php

namespace Database\Factories;

use App\Models\HourlyTicker;
use App\Services\TickerProviders\TickerResponseDto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HourlyTicker>
 */
class HourlyTickerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    public static function fromProviderDto(TickerResponseDto $dto)
    {
        return new HourlyTicker($dto->price, $dto->time);
    }
}
