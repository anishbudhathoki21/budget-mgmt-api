<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use App\Dto\CreateBudgetInput;
use App\Dto\UpdateBudgetInput;
use App\Repository\BudgetRepository;
use App\State\BudgetCollectionProvider;
use App\State\BudgetItemProvider;
use App\State\CreateBudgetProcessor;
use App\State\UpdateBudgetProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BudgetRepository::class)]
#[ApiResource(
    shortName: 'Budget',
    operations: [
        new GetCollection(
            uriTemplate: '/budgets',
            provider: BudgetCollectionProvider::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            normalizationContext: ['groups' => ['budget:read']]
        ),
        new Get(
            uriTemplate: '/budgets/{id}',
            provider: BudgetItemProvider::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            normalizationContext: ['groups' => ['budget:read']]
        ),
        new Post(
            uriTemplate: '/budgets',
            input: CreateBudgetInput::class,
            processor: CreateBudgetProcessor::class,
            security: "is_granted('BUDGET_CREATE')",
            status: 201,
            normalizationContext: ['groups' => ['budget:read']]
        ),
        new Patch(
            uriTemplate: '/budgets/{id}',
            input: UpdateBudgetInput::class,
            provider: BudgetItemProvider::class,
            processor: UpdateBudgetProcessor::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            normalizationContext: ['groups' => ['budget:read']]
        )
    ]
)]
class Budget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['budget:read', 'expense:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['budget:read', 'expense:read'])]
    #[Assert\NotBlank(message: 'Budget name is required')]
    #[Assert\Length(min: 2, max: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    #[Groups(['budget:read'])]
    #[Assert\NotBlank(message: 'Total amount is required')]
    #[Assert\Positive(message: 'Total amount must be positive')]
    private ?string $totalAmount = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['budget:read'])]
    #[Assert\NotBlank(message: 'Start date is required')]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['budget:read'])]
    #[Assert\NotBlank(message: 'End date is required')]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(length: 3)]
    #[Groups(['budget:read', 'expense:read'])]
    #[Assert\NotBlank(message: 'Currency is required')]
    #[Assert\Currency]
    private ?string $currency = null;

    #[ORM\Column(length: 20)]
    #[Groups(['budget:read'])]
    private string $status = 'active'; // active, closed

    #[ORM\ManyToOne(inversedBy: 'budgets')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['budget:read'])]
    private ?Department $department = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['budget:read'])]
    private ?User $createdBy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['budget:read'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['budget:read'])]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var Collection<int, Expense>
     */
    #[ORM\OneToMany(targetEntity: Expense::class, mappedBy: 'budget')]
    private Collection $expenses;

    public function __construct()
    {
        $this->expenses = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->status = 'active';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): static
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): static
    {
        $this->department = $department;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense): static
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses->add($expense);
            $expense->setBudget($this);
        }
        return $this;
    }

    public function removeExpense(Expense $expense): static
    {
        if ($this->expenses->removeElement($expense)) {
            if ($expense->getBudget() === $this) {
                $expense->setBudget(null);
            }
        }
        return $this;
    }

    #[Groups(['budget:read'])]
    public function getApprovedExpensesTotal(): string
    {
        $total = 0.0;
        foreach ($this->expenses as $expense) {
            if ($expense->getStatus() === 'approved') {
                $total += (float) $expense->getAmount();
            }
        }
        return number_format($total, 2, '.', '');
    }

    #[Groups(['budget:read'])]
    public function getRemainingBalance(): string
    {
        if ($this->totalAmount === null) {
            return '0.00';
        }
        $remaining = (float) $this->totalAmount - (float) $this->getApprovedExpensesTotal();
        return number_format($remaining, 2, '.', '');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isExpired(): bool
    {
        $now = new \DateTime();
        return $this->endDate < $now;
    }

    public function isValid(): bool
    {
        return $this->isActive() && !$this->isExpired();
    }
}
