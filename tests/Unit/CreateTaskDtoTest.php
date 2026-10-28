<?php

namespace App\Tests\Unit;

use App\Dto\CreateTaskDto;
use App\Enum\CurrencyType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateTaskDtoTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->validator = self::getContainer()->get(ValidatorInterface::class);
    }

    public function testValidDtoPassesValidation(): void
    {
        $dto = new CreateTaskDto(
            title: 'Write Symfony API',
            budget: '1500.50',
            currency: CurrencyType::USD
        );

        $errors = $this->validator->validate($dto);

        $this->assertCount(0, $errors);
    }

    public function testInvalidDtoFailsValidation(): void
    {
        $dto = new CreateTaskDto(
            title: 'FIX', // Too short title (< 5 characters)
            budget: '-500', // Negative budget
            currency: CurrencyType::EUR
        );

        $errors = $this->validator->validate($dto);

        $this->assertGreaterThan(0, $errors->count());

        // Check for invalidation of fields that exactly waited for invalidation
        $invalidProperties = [];

        foreach ($errors as $error) {
            $invalidProperties[] = $error->getPropertyPath();
        }

        $this->assertContains('title', $invalidProperties);
        $this->assertContains('budget', $invalidProperties);
    }
}
