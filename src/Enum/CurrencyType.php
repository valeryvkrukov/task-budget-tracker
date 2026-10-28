<?php

namespace App\Enum;

enum CurrencyType: string
{
    case EUR = 'EUR';
    case USD = 'USD';
    case BTC = 'BTC';
    case ETH = 'ETH';
}
