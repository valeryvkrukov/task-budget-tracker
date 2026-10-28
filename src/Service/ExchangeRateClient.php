<?php

namespace App\Service;

use App\Enum\CurrencyType;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExchangeRateClient implements ExchangeRateProviderInterface
{
    // Inject HttpClient via constructor directly (Dependency Injection)
    public function __construct(
        private HttpClientInterface $httpClient
    ) {}

    public function getRateAgainstUsd(CurrencyType $currency): float
    {
        // If currency is USD, return 1.00
        if ($currency === CurrencyType::USD) {
            return 1.0;
        }

        try {
            // ---
            $response = $this->httpClient->request('GET', 'https://coingecko.com/api/v3/simple/price', [
                'query' => [
                    'ids' => $this->getApiId($currency),
                    'vs_currencies' => 'usd'
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new HttpException($response->getStatusCode(), 'Failed to retrieve exchange rate');
            }

            $data = $response->toArray();
            $apiId = $this->getApiId($currency);

            return (float) ($data[$apiId]['usd'] ?? $this->getFallbackRate($currency));
        } catch (\Throwable $e) {
            // If external API fails, return fallback rate
            return $this->getFallbackRate($currency);
        }
    }

    private function getApiId(CurrencyType $currency): string
    {
        return match ($currency) {
            CurrencyType::BTC => 'bitcoin',
            CurrencyType::ETH => 'ethereum',
            CurrencyType::USD => 'usd',
            CurrencyType::EUR => 'euro',
            default => throw new \InvalidArgumentException('Unsupported currency type'),
        };
    }

    private function getFallbackRate(CurrencyType $currency): float
    {
        return match ($currency) {
            CurrencyType::BTC => 68500.00,
            CurrencyType::ETH => 2500.00,
            CurrencyType::USD => 1.0,
            CurrencyType::EUR => 0.92,
        };
    }
}
