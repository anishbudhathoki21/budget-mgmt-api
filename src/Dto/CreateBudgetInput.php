<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateBudgetInput
{
    public function __construct(
        #[Assert\NotBlank(message: 'Budget name is required')]
        #[Assert\Length(min: 2, max: 100, minMessage: 'Name must be at least {{ limit }} characters')]
        public string $name = '',

        #[Assert\NotBlank(message: 'Total amount is required')]
        #[Assert\Positive(message: 'Total amount must be positive')]
        public float $totalAmount = 0.0,

        #[Assert\NotBlank(message: 'Start date is required')]
        #[Assert\Date]
        public string $startDate = '',

        #[Assert\NotBlank(message: 'End date is required')]
        #[Assert\Date]
        #[Assert\GreaterThan(propertyPath: 'startDate', message: 'End date must be after start date')]
        public string $endDate = '',

        #[Assert\NotBlank(message: 'Currency is required')]
        #[Assert\Currency]
        #[Assert\Length(exactly: 3)]
        public string $currency = '',

        #[Assert\NotBlank(message: 'Department ID is required')]
        #[Assert\Positive]
        public int $departmentId = 0,
    ) {}
}
