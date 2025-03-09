<?php

namespace App\Services\TickerProviders;

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
            $tempResponse = $this->getTickerData();

            return $this->deserialize($tempResponse);
        } catch (TickerProviderApiException $e) {
            throw $e;
        }
    }

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
