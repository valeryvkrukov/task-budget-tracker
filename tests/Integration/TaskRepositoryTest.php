<?php

namespace App\Tests\Integration;

use App\Entity\Task;
use App\Enum\CurrencyType;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()
            ->get('doctrine')
            ->getManager();

        // Clean table before each test for integration cleanliness
        $this->entityManager->getConnection()->executeStatement('TRUNCATE TABLE tasks RESTART IDENTITY CASCADE');
    }

    public function testTaskCanBeSavedWithPostgresFeatures(): void
    {
        // 1. Create an entity using PHP 8.5 Enum & JSON data for Postgres
        $task = new Task();
        $task->setTitle('Building mobile backend using Spring Boot');
        $task->setBudget('4500.00');
        $task->setCurrency(CurrencyType::BTC);

        // Record the Postgres specific JSONB snapshot
        $task->setRateSnapshot([
            'synced_at' => new \DateTimeImmutable()->format(\DateTimeInterface::ATOM),
            'base_currency' => 'USD',
            'rate' => 64250.75
        ]);

        // 2. Save the task to the database
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        // Clean manager to ensure that Doctrine made a clean SELECT query
        $this->entityManager->clear();

        // 3. Fetch it back and check for assertions
        /** @var Task $savedTask */
        $savedTask = $this->entityManager
            ->getRepository(Task::class)
            ->findOneBy(['title' => 'Building mobile backend using Spring Boot']);

        $this->assertNotNull($savedTask);
        $this->assertSame('4500.00', $savedTask->getBudget());

        // Check for strict Enum type
        $this->assertSame(CurrencyType::BTC, $savedTask->getCurrency());

        // Check for JSONB structure
        $this->assertIsArray($savedTask->getRateSnapshot());
        $this->assertSame('USD', $savedTask->getRateSnapshot()['base_currency']);
        $this->assertEquals(64250.75, $savedTask->getRateSnapshot()['rate']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->entityManager) {
            $this->entityManager->close();
            $this->entityManager = null;
        }
    }
}
