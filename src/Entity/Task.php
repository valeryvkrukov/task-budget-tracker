<?php

namespace App\Entity;

use App\Enum\CurrencyType;
use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table(name: 'tasks')]
#[ORM\Index(columns: ['budget', 'currency'], name: 'idx_tasks_budget_currency')]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private ?string $budget = null;

    #[ORM\Column(length: 3, type: Types::STRING, enumType: CurrencyType::class)]
    private ?CurrencyType $currency = null;

    #[ORM\Column(type: Types::JSON, options: ['jsonb' => true], nullable: true)]
    private ?array $rateSnapshot = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getBudget(): ?string
    {
        return $this->budget;
    }

    public function setBudget(?string $budget): void
    {
        $this->budget = $budget;
    }

    public function getCurrency(): ?CurrencyType
    {
        return $this->currency;
    }

    public function setCurrency(?CurrencyType $currency): void
    {
        $this->currency = $currency;
    }

    public function getRateSnapshot(): ?array
    {
        return $this->rateSnapshot;
    }

    public function setRateSnapshot(?array $rateSnapshot): void
    {
        $this->rateSnapshot = $rateSnapshot;
    }
}
