<?php

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SyncCurrencyRatesCommandTest extends KernelTestCase
{
    public function testExecuteLogsSuccessAndTable(): void
    {
        // 1. Symfony kernel and console app init
        self::bootKernel();

        $application = new Application(self::$kernel);

        // 2. Find the command
        $command = $application->find('app:rates:sync');
        $commandTester = new CommandTester($command);

        // 3. Run command within CommandTester
        $commandTester->execute([]);

        // 4. Check for return code (0 is a success)
        $this->assertSame(0, $commandTester->getStatusCode());

        // 5. Check for console output
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Syncing currency rates', $output);
        $this->assertStringContainsString('Actual rates against USD:', $output);
        $this->assertStringContainsString('Currency rates synced successfully', $output);

        // Check for currency rates table
        $this->assertStringContainsString('BTC', $output);
        $this->assertStringContainsString('ETH', $output);
        $this->assertStringContainsString('USD', $output);
        $this->assertStringContainsString('EUR', $output);
    }
}
