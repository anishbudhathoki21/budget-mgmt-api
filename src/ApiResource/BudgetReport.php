<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Dto\BudgetReportOutput;
use App\State\BudgetReportProvider;

#[ApiResource(
    shortName: 'BudgetReport',
    operations: [
        new GetCollection(
            uriTemplate: '/reports/budgets',
            provider: BudgetReportProvider::class,
            security: "is_granted('IS_AUTHENTICATED_FULLY')",
            normalizationContext: ['groups' => ['report:read']]
        )
    ]
)]
class BudgetReport
{
    // This is a virtual resource; no database entity
}
