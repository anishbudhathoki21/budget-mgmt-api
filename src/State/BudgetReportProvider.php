<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\BudgetReportOutput;
use App\Entity\Budget;
use App\Entity\Expense;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class BudgetReportProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private \Symfony\Component\HttpFoundation\RequestStack $requestStack
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if (!$user) {
            return [];
        }

        $budgetQb = $this->entityManager->getRepository(Budget::class)->createQueryBuilder('b')
            ->leftJoin('b.department', 'd')
            ->leftJoin('b.expenses', 'e', 'WITH', 'e.deletedAt IS NULL')
            ->addSelect('d')
            ->addSelect('e');

        if (!$user->isManager()) {
            // employee: show only budgets where they have submitted at least one expense
            $budgetQb->andWhere('EXISTS(SELECT 1 FROM App\\Entity\\Expense e2 WHERE e2.budget = b AND e2.submittedBy = :user AND e2.deletedAt IS NULL)')
                ->setParameter('user', $user->getId());
        }

        $budgets = $budgetQb->getQuery()->getResult();

        $reports = [];
        foreach ($budgets as $budget) {
            $report = new BudgetReportOutput(
                $budget->getId(),
                $budget->getName(),
                $budget->getCurrency(),
                $budget->getTotalAmount(),
                $budget->getApprovedExpensesTotal(),
                $budget->getRemainingBalance()
            );

            // Calculate category breakdown and status counts
            $categoryBreakdown = [];
            $statusCounts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];

            // apply status filter if provided
            $statusFilter = null;
            $request = $this->requestStack->getCurrentRequest();
            if ($request) {
                $statusFilter = $request->query->get('status');
                if (!in_array($statusFilter, ['pending', 'approved', 'rejected'], true)) {
                    $statusFilter = null;
                }
            }

            foreach ($budget->getExpenses() as $expense) {
                if ($expense->isDeleted()) {
                    continue;
                }

                // Only include user's own expenses if not manager
                if (!$user->isManager() && $expense->getSubmittedBy()?->getId() !== $user->getId()) {
                    continue;
                }

                // filter by status if requested
                if ($statusFilter && $expense->getStatus() !== $statusFilter) {
                    continue;
                }

                // Category breakdown
                $category = $expense->getCategory();
                if (!isset($categoryBreakdown[$category])) {
                    $categoryBreakdown[$category] = ['count' => 0, 'total' => '0.00'];
                }
                $categoryBreakdown[$category]['count']++;
                $categoryBreakdown[$category]['total'] = (string) ((float) $categoryBreakdown[$category]['total'] + (float) $expense->getAmount());

                // Status counts (for filtered set)
                if (isset($statusCounts[$expense->getStatus()])) {
                    $statusCounts[$expense->getStatus()]++;
                }
            }

            $report->categoryBreakdown = $categoryBreakdown;
            $report->totalPending = $statusCounts['pending'];
            $report->totalApproved = $statusCounts['approved'];
            $report->totalRejected = $statusCounts['rejected'];

            $reports[] = $report;
        }

        return $reports;
    }
}
