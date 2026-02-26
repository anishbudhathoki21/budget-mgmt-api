<?php

namespace App\DataFixtures;

use App\Entity\Budget;
use App\Entity\Expense;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ExpenseFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $budgets = $manager->getRepository(Budget::class)->findAll();
        $employee1 = $manager->getRepository(User::class)->findOneBy(['email' => 'employee@example.com']);
        $employee2 = $manager->getRepository(User::class)->findOneBy(['email' => 'bob@example.com']);
        $employee3 = $manager->getRepository(User::class)->findOneBy(['email' => 'alice@example.com']);
        $managerUser = $manager->getRepository(User::class)->findOneBy(['email' => 'manager@example.com']);

        $employees = [$employee1, $employee2, $employee3];

        $categories = ['travel', 'equipment', 'training', 'other'];
        $statuses = ['pending', 'approved', 'rejected'];

        // Create multiple expenses per budget
        foreach ($budgets as $budgetIndex => $budget) {
            $expenseCount = rand(3, 8); // 3-8 expenses per budget

            for ($i = 0; $i < $expenseCount; $i++) {
                $expense = new Expense();

                // Set basic info
                $amount = (string) rand(500, 50000) / 100; // Random amount between $5.00 and $500.00
                $expense->setAmount($amount);
                $expense->setDescription(sprintf(
                    '%s for project %d - Item %d',
                    ucfirst($categories[$i % count($categories)]),
                    $budgetIndex + 1,
                    $i + 1
                ));
                $expense->setCategory($categories[$i % count($categories)]);

                // Assign to random employee
                $submittedBy = $employees[$i % count($employees)];
                $expense->setSubmittedBy($submittedBy);
                $expense->setBudget($budget);

                // Distribute statuses: 40% pending, 50% approved, 10% rejected
                $rand = rand(1, 100);
                if ($rand <= 10) {
                    $status = 'rejected';
                } elseif ($rand <= 60) {
                    $status = 'approved';
                } else {
                    $status = 'pending';
                }

                $expense->setStatus($status);

                // Set submitted date (within last 30 days)
                $submitDate = new \DateTime('-' . rand(0, 29) . ' days');
                $expense->setSubmittedAt($submitDate);

                // Set review info for non-pending expenses
                if ($status !== 'pending') {
                    $expense->setReviewedBy($managerUser);
                    $reviewDate = clone $submitDate;
                    $reviewDate->modify('+' . rand(1, 7) . ' days');
                    $expense->setReviewedAt($reviewDate);
                }

                $manager->persist($expense);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BudgetFixture::class,
            UserFixture::class,
        ];
    }
}
