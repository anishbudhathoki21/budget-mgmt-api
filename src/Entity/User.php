<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Email already exists')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'budget:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['user:read', 'budget:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['user:read', 'budget:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user:read'])]
    private ?Role $role = null;

    /**
     * @var Collection<int, Expense>
     */
    #[ORM\OneToMany(targetEntity: Expense::class, mappedBy: 'submittedBy')]
    private Collection $submittedExpenses;

    public function __construct()
    {
        $this->submittedExpenses = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        return $this->role ? [$this->role->getName()] : [];
    }

    public function eraseCredentials(): void
    {

    }

    /**
     * @return Collection<int, Expense>
     */
    public function getSubmittedExpenses(): Collection
    {
        return $this->submittedExpenses;
    }

    public function addSubmittedExpense(Expense $expense): static
    {
        if (!$this->submittedExpenses->contains($expense)) {
            $this->submittedExpenses->add($expense);
            $expense->setSubmittedBy($this);
        }
        return $this;
    }

    public function removeSubmittedExpense(Expense $expense): static
    {
        if ($this->submittedExpenses->removeElement($expense)) {
            if ($expense->getSubmittedBy() === $this) {
                $expense->setSubmittedBy(null);
            }
        }
        return $this;
    }

    public function isManager(): bool
    {
        return $this->role && $this->role->getName() === 'MANAGER';
    }

    public function isEmployee(): bool
    {
        return $this->role && $this->role->getName() === 'EMPLOYEE';
    }
}
