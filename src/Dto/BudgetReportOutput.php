<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class BudgetReportOutput
{
    #[Groups(['report:read'])]
    public int $budgetId;

    #[Groups(['report:read'])]
    public string $budgetName;

    #[Groups(['report:read'])]
    public string $currency;

    #[Groups(['report:read'])]
    public string $totalAllocated;

    #[Groups(['report:read'])]
    public string $totalSpent;

    #[Groups(['report:read'])]
    public string $remainingBalance;

    #[Groups(['report:read'])]
    public array $categoryBreakdown = [];

    #[Groups(['report:read'])]
    public int $totalPending = 0;

    #[Groups(['report:read'])]
    public int $totalApproved = 0;

    #[Groups(['report:read'])]
    public int $totalRejected = 0;

    public function __construct(
        int $budgetId = 0,
        string $budgetName = '',
        string $currency = 'USD',
        string $totalAllocated = '0.00',
        string $totalSpent = '0.00',
        string $remainingBalance = '0.00'
    ) {
        $this->budgetId = $budgetId;
        $this->budgetName = $budgetName;
        $this->currency = $currency;
        $this->totalAllocated = $totalAllocated;
        $this->totalSpent = $totalSpent;
        $this->remainingBalance = $remainingBalance;
    }
}
