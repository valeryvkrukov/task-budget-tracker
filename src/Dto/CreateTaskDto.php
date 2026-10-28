<?php

namespace App\Dto;

use App\Enum\CurrencyType;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CreateTaskDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Task name cannot be blank.')]
        #[Assert\Length(
            min: 5,
            max: 255,
            minMessage: 'Task name cannot be shorter than {{ limit }} characters.',
            maxMessage: 'Task name cannot be longer than {{ limit }} characters.')
        ]
        public string $title,

        #[Assert\NotBlank(message: 'Budget is mandatory to follow.')]
        #[Assert\Positive(message: 'Budget must be positive number.')]
        #[Assert\Regex(
            pattern: '/^\d+(\.\d{1,2})?$/',
            message: 'Budget must be a valid number with maximum 2 decimal places (for example: 1050.50).'
        )]
        public string $budget,

        #[Assert\NotBlank(message: 'Currency is mandatory to follow.')]
        #[Assert\Type(type: CurrencyType::class, message: 'Currency must be a valid currency.')]
        public CurrencyType $currency
    )
    {
    }
}
