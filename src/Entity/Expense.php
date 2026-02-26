<?php

namespace App\Entity;

use App\Repository\ExpenseRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use App\Dto\CreateExpenseInput;
use App\Dto\UpdateExpenseInput;
use App\State\ExpenseCollectionProvider;
use App\State\CreateExpenseProcessor;
use App\State\UpdateExpenseProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
#[ApiResource(
    shortName: 'Expense',
    operations: [
        new GetCollection(
            uriTemplate: '/expenses',
            provider: ExpenseCollectionProvider::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            normalizationContext: ['groups' => ['expense:read']]
        ),
        new Get(
            uriTemplate: '/expenses/{id}',
            security: "is_granted('EXPENSE_VIEW', object)",
            normalizationContext: ['groups' => ['expense:read']]
        ),
        new Post(
            uriTemplate: '/expenses',
            input: CreateExpenseInput::class,
            processor: CreateExpenseProcessor::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            status: 201,
            normalizationContext: ['groups' => ['expense:read']]
        ),
        new Patch(
            uriTemplate: '/expenses/{id}',
            input: UpdateExpenseInput::class,
            processor: UpdateExpenseProcessor::class,
            security: "is_granted('EXPENSE_CANCEL', object)",
            normalizationContext: ['groups' => ['expense:read']]
        )
    ]
)]
class Expense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['expense:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    #[Groups(['expense:read'])]
    #[Assert\NotBlank(message: 'Amount is required')]
    #[Assert\Positive(message: 'Amount must be positive')]
    private ?string $amount = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['expense:read'])]
    #[Assert\NotBlank(message: 'Description is required')]
    #[Assert\Length(min: 10, max: 1000, minMessage: 'Description must be at least {{ limit }} characters')]
    private ?string $description = null;

    #[ORM\Column(length: 50)]
    #[Groups(['expense:read'])]
    #[Assert\NotBlank(message: 'Category is required')]
    #[Assert\Choice(choices: ['travel', 'equipment', 'training', 'other'], message: 'Invalid category')]
    private ?string $category = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['expense:read'])]
    private ?string $receiptNote = null;

    #[ORM\Column(length: 20)]
    #[Groups(['expense:read'])]
    private string $status = 'pending'; // pending, approved, rejected, cancelled

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['expense:read'])]
    private ?string $reviewComment = null;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['expense:read'])]
    private ?Budget $budget = null;

    #[ORM\ManyToOne(inversedBy: 'submittedExpenses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['expense:read'])]
    private ?User $submittedBy = null;

    #[ORM\ManyToOne]
    private ?User $reviewedBy = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['expense:read'])]
    private ?\DateTimeInterface $submittedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['expense:read'])]
    private ?\DateTimeInterface $reviewedAt = null;

    public function __construct()
    {
        $this->submittedAt = new \DateTime();
        $this->status = 'pending';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getReceiptNote(): ?string
    {
        return $this->receiptNote;
    }

    public function setReceiptNote(?string $receiptNote): static
    {
        $this->receiptNote = $receiptNote;
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

    public function getReviewComment(): ?string
    {
        return $this->reviewComment;
    }

    public function setReviewComment(?string $reviewComment): static
    {
        $this->reviewComment = $reviewComment;
        return $this;
    }

    public function getBudget(): ?Budget
    {
        return $this->budget;
    }

    public function setBudget(?Budget $budget): static
    {
        $this->budget = $budget;
        return $this;
    }

    public function getSubmittedBy(): ?User
    {
        return $this->submittedBy;
    }

    public function setSubmittedBy(?User $submittedBy): static
    {
        $this->submittedBy = $submittedBy;
        return $this;
    }

    public function getReviewedBy(): ?User
    {
        return $this->reviewedBy;
    }

    public function setReviewedBy(?User $reviewedBy): static
    {
        $this->reviewedBy = $reviewedBy;
        return $this;
    }

    public function getSubmittedAt(): ?\DateTimeInterface
    {
        return $this->submittedAt;
    }

    public function setSubmittedAt(\DateTimeInterface $submittedAt): static
    {
        $this->submittedAt = $submittedAt;
        return $this;
    }

    public function getReviewedAt(): ?\DateTimeInterface
    {
        return $this->reviewedAt;
    }

    public function setReviewedAt(?\DateTimeInterface $reviewedAt): static
    {
        $this->reviewedAt = $reviewedAt;
        return $this;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeCancelled(): bool
    {
        return $this->isPending();
    }
}
