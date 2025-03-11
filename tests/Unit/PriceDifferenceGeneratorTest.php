<?php

namespace Tests\Unit;

use App\Domain\Shared\Domain\TimeframeHoursEnum;
use App\Domain\Ticker\Infrastructure\PriceDifferenceGenerator;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class HourlyTicker
{
    public $price;
    public $time;

    public function __construct(int $price, \DateTime $time)
    {
        $this->price = $price;
        $this->time = $time;
    }
}

class PriceDifferenceGeneratorTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function testGeneratorReturnsAllTimeframes()
    {
        $collection = self::generateHourlyTickersCollection(TimeframeHoursEnum::greatest());
        $dtoCollection = new Collection([]);
        foreach (PriceDifferenceGenerator::generate($collection) as $priceDifferenceDto) {
            $dtoCollection->add($priceDifferenceDto);
        }
        $this->assertCount(TimeframeHoursEnum::count(), $dtoCollection);
    }

    public function testGeneratorReturnsOneTimeframe()
    {
        $collection = self::generateHourlyTickersCollection(TimeframeHoursEnum::least());
        $dtoCollection = new Collection([]);
        foreach (PriceDifferenceGenerator::generate($collection) as $priceDifferenceDto) {
            $dtoCollection->add($priceDifferenceDto);
        }
        $this->assertCount(1, $dtoCollection);
    }

    protected static function generateHourlyTickersCollection($max)
    {
        $collection = new Collection([]);

        $items = 0;
        while ($items < $max) {
            $collection
                ->add(new HourlyTicker(
                    $items + 1 * 10000,
                    new \DateTime()->add(new \DateInterval('PT' . $items . 'H'))
                ));
            $items++;
        }

        return $collection;
    }
}
