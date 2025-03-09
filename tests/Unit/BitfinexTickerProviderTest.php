<?php

namespace Tests\Unit;

use App\Services\TickerProviders\BitfinexTickerProvider;
use App\Services\TickerProviders\TickerResponseDto;
use PHPUnit\Framework\TestCase;
use App\Services\TickerProviders\TickerProviderApiException;

class BitfinexTickerProviderTest extends TestCase
{
    private $provider;

    public function testValidResponseReturnsDto()
    {
        $this->provider = new class extends BitfinexTickerProvider {
            protected function getTickerData(): string
            {
                return '[
                    86283,
                    3.24699078,
                    86285,
                    5.38491747,
                    -259,
                    -0.00299252,
                    86290,
                    844.85316369,
                    86890,
                    85936
                ]';
            }
        };

        $result = $this->provider->get();

        $this->assertInstanceOf(TickerResponseDto::class, $result);
    }

    public function testInvalidJsonThrowsException()
    {
        $this->provider = new class extends BitfinexTickerProvider {
            protected function getTickerData(): string
            {
                return 'Not a JSON';
            }
        };

        $this->expectException(TickerProviderApiException::class);
        $this->expectExceptionMessage('Response is not a valid JSON');

        $this->provider->get();
    }

    public function testNoPriceValueThrowsException()
    {
        $this->provider = new class extends BitfinexTickerProvider {
            protected function getTickerData(): string
            {
                return '[]';
            }
        };

        $this->expectException(TickerProviderApiException::class);
        $this->expectExceptionMessage('Response missing price value');

        $this->provider->get();
    }

    public function testNonNumericalPriceThrowsException()
    {
        $this->provider = new class extends BitfinexTickerProvider {
            protected function getTickerData(): string
            {
                return '[
                    "not_a_price",
                    3.24699078,
                    86285,
                    5.38491747,
                    -259,
                    -0.00299252,
                    86290,
                    844.85316369,
                    86890,
                    85936
                ]';
            }
        };

        $this->expectException(TickerProviderApiException::class);
        $this->expectExceptionMessage('Price is not a valid float');

        $this->provider->get();
    }
}
