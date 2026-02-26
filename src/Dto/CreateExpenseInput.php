<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateExpenseInput
{
    #[Assert\NotBlank(message: 'Amount is required')]
    #[Assert\Positive(message: 'Amount must be positive')]
    public float $amount = 0.0;

    #[Assert\NotBlank(message: 'Description is required')]
    #[Assert\Length(min: 10, max: 1000)]
    public string $description = '';

    #[Assert\NotBlank(message: 'Category is required')]
    #[Assert\Choice(choices: ['travel', 'equipment', 'training', 'other'], message: 'Invalid category')]
    public string $category = '';

    public ?string $receiptNote = null;

    #[Assert\NotBlank(message: 'Budget ID is required')]
    #[Assert\Positive]
    public int $budgetId = 0;
}
