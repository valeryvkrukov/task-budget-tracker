<?php

namespace App\Command;

use App\Enum\CurrencyType;
use App\Service\ExchangeRateProviderInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:rates:sync',
    description: 'Sync actual currency and crypto rates from external provider',
)]
class SyncCurrencyRatesCommand extends Command
{
    public function __construct(
        private ExchangeRateProviderInterface $exchangeRateClient,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Syncing currency rates');

        $io->progressStart(count(CurrencyType::cases()));

        $results = [];

        foreach (CurrencyType::cases() as $currencyType) {
            // Progressbar imitation
            usleep(250000);

            $rate = $this->exchangeRateClient->getRateAgainstUsd($currencyType);
            $results[] = [$currencyType->value, sprintf('%.2f USD', $rate)];

            $io->progressAdvance();
        }

        $io->progressFinish();

        // Results output as a structured table
        $io->section('Actual rates against USD:');
        $io->table(['Currency', 'Rate'], $results);

        $io->success('Currency rates synced successfully');

        return Command::SUCCESS;
    }
}
