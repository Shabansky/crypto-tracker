<?php

namespace Database\Factories;

use App\Domain\Ticker\Domain\Models\HourlyTicker;
use App\Domain\TickerProviders\Infrastructure\TickerResponseDto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HourlyTicker>
 */
class HourlyTickerFactory extends Factory
{
    protected $model = HourlyTicker::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price' => $this->faker->randomFloat(2, 10000, 50000),
            'time' => new \DateTime(),
        ];
    }

    public static function makeFromAttributes(float $price, \DateTime $time)
    {
        $factory = new static();

        return $factory->create([
            'price' => $price,
            'time' => $time
        ]);
    }

    public static function fromProviderDto(TickerResponseDto $dto)
    {
        return self::makeFromAttributes($dto->price, $dto->time);
    }
}
