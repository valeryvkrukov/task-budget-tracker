<?php

namespace App\Service;

use App\Enum\CurrencyType;

interface ExchangeRateProviderInterface
{
    /**
     * Returns the exchange rate of the given currency against USD.
     *
     * @param CurrencyType $currency
     * @return float
     */
    public function getRateAgainstUsd(CurrencyType $currency): float;
}
