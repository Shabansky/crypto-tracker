<?php

namespace App\Domain\TickerProviders\Application\Providers;

use Illuminate\Support\Facades\Http;
use App\Domain\TickerProviders\Infrastructure\TickerProviderInterface;
use App\Domain\TickerProviders\Infrastructure\TickerResponseDto;
use App\Domain\TickerProviders\Infrastructure\TickerProviderApiException;

class BitfinexTickerProvider implements TickerProviderInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct() {}

    /**
     * Tries to return a TickerResponseDto based on Bitfinex
     * response. May fail if a response cannot be retrieved
     * or if said response is malformed.
     *
     * @return TickerResponseDto
     * @throws TickerProviderApiException
     */
    public function get(): ?TickerResponseDto
    {
        try {
            $response = $this->getTickerData();

            return $this->deserialize($response);
        } catch (TickerProviderApiException $e) {
            throw $e;
        }
    }

    protected function getTickerData(): string
    {
        $response = Http::withHeaders([
            'accept' => 'application/json'
        ])->get('https://api-pub.bitfinex.com/v2/ticker/tBTCUSD');

        if ($response->failed()) {
            throw new TickerProviderApiException(
                sprintf("Error from server: %s", $response->body())
            );
        }

        return $response->body();
    }

    /**
     * Deserializes the incoming response into a TickerResponseDto.
     * Provides validation prior to deserialization and throws
     * TickerProviderApiException on failure.
     * 
     * @return TickerResponseDto
     * @throws TickerProviderApiException
     */
    protected function deserialize(string $response): TickerResponseDto
    {
        if (!json_validate($response)) {
            throw new TickerProviderApiException('Response is not a valid JSON');
        }

        $responseToArr = json_decode($response, associative: true);

        if (!isset($responseToArr[0])) {
            throw new TickerProviderApiException('Response missing price value');
        }

        $price = $responseToArr[0];
        if (!is_numeric($price)) {
            throw new TickerProviderApiException('Price is not a valid float');
        }

        return new TickerResponseDto(price: $price, time: new \DateTime());
    }
}
