<?php

namespace App\Tests\Unit;

use App\Enum\CurrencyType;
use App\Service\ExchangeRateClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class ExchangeRateClientTest extends TestCase
{
    public function testGetRateReturnsCorrectValueFromApi(): void
    {
        // 1. Create a fake JSON response from API CoinGecko
        $mockResponseBody = json_encode([
            'bitcoin' => ['usd' => 68500.25],
        ]);

        $mockResponse = new MockResponse($mockResponseBody, [
            'http_code' => 200,
            'response_headers' => ['Content-Type' => 'application/json'],
        ]);

        // 2. Inject MockHttpClient instead of real HttpClient
        $mockHttpClient = new MockHttpClient($mockResponse);
        $client = new ExchangeRateClient($mockHttpClient);

        // 3. Call a method and check the result
        $rate = $client->getRateAgainstUsd(CurrencyType::BTC);

        $this->assertEquals(68500.25, $rate);
    }

    public function testGetRateReturnsFallbackOnApiFailure(): void
    {
        // Simulate unaccessible external server (err. 500)
        $mockResponse = new MockResponse('Internal Server Error', ['http_code' => 500]);
        $mockHttpClient = new MockHttpClient($mockResponse);
        $client = new ExchangeRateClient($mockHttpClient);

        // Service must catch error and return fallback value (68500.00 for BTC)
        $rate = $client->getRateAgainstUsd(CurrencyType::BTC);

        $this->assertSame(68500.00, $rate);
    }
}
