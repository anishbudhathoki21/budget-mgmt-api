<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateBudgetInput
{
    #[Assert\Length(min: 2, max: 100)]
    public ?string $name = null;

    #[Assert\Positive(message: 'Total amount must be positive')]
    public ?float $totalAmount = null;

    #[Assert\Date]
    public ?string $startDate = null;

    #[Assert\Date]
    public ?string $endDate = null;

    #[Assert\Currency]
    #[Assert\Length(exactly: 3)]
    public ?string $currency = null;

    #[Assert\Choice(choices: ['active', 'closed'])]
    public ?string $status = null;
}
