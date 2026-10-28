<?php

namespace App\Controller;

use App\Dto\CreateTaskDto;
use App\Entity\Task;
use App\Service\ExchangeRateProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'task_')]
class TaskController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ExchangeRateProviderInterface $exchangeRateProvider,
    ) {}

    #[Route('/tasks', name: 'create_task', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateTaskDto $dto,
    ): JsonResponse
    {
        $task = new Task();

        $task->setTitle($dto->title);
        $task->setBudget($dto->budget);
        $task->setCurrency($dto->currency);

        $currentRate = $this->exchangeRateProvider->getRateAgainstUsd($dto->currency);

        $task->setRateSnapshot([
            'synced_at' => new \DateTimeImmutable()->format(\DateTimeInterface::ATOM),
            'rate' => $currentRate,
            'budget_in_usd' => round((float) $dto->budget * $currentRate, 2),
        ]);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Task created successfully',
            'data' => [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'budget' => $task->getBudget(),
                'currency' => $task->getCurrency()->value,
                'postgres_rate_snapshot' => $task->getRateSnapshot(),
            ]
        ], Response::HTTP_CREATED);
    }
}
