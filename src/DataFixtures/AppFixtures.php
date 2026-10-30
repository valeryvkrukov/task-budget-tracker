<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Enum\CurrencyType;
use App\Service\ExchangeRateProviderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function __construct(
        private ExchangeRateProviderInterface $exchangeRateService
    ) {}

    public function load(ObjectManager $manager): void
    {
        $sampleTasks = [
            [
                'title' => 'Implementation of REST API auth module (Symfony 8)',
                'budget' => '1200.00',
                'currency' => CurrencyType::USD,
            ],
            [
                'title' => 'Indexes optimisation and build complex queries to optimize performance',
                'budget' => '850.00',
                'currency' => CurrencyType::EUR,
            ],
            [
                'title' => 'Building smart-contract for rearranging tokens',
                'budget' => '0.075',
                'currency' => CurrencyType::BTC,
            ],
            [
                'title' => 'Payment gateway integration (Stripe API)',
                'budget' => '1.50',
                'currency' => CurrencyType::ETH,
            ],
            [
                'title' => 'Legacy architecture refactoring and business logic migration',
                'budget' => '3200.00',
                'currency' => CurrencyType::USD,
            ],
        ];

        foreach ($sampleTasks as $taskData) {
            $task = new Task();
            $task->setTitle($taskData['title']);
            $task->setBudget($taskData['budget']);
            $task->setCurrency($taskData['currency']);

            // Get the rate for the snapshot
            $rate = $this->exchangeRateService->getRateAgainstUsd($taskData['currency']);

            $task->setRateSnapshot([
                'synced_at' => new \DateTimeImmutable()->format(\DateTimeImmutable::ATOM),
                'rate_against_usd' => $rate,
                'budget_in_usd' => round((float) $taskData['budget'] * $rate, 2),
            ]);

            $manager->persist($task);
        }

        $manager->flush();
    }
}
