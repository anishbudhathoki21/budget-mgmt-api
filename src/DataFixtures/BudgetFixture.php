<?php

namespace App\DataFixtures;

use App\Entity\Budget;
use App\Entity\Department;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BudgetFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $departments = $manager->getRepository(Department::class)->findAll();
        $managerUser = $manager->getRepository(User::class)->findOneBy(['email' => 'manager@example.com']);

        $budgets = [
            [
                'name' => 'Engineering Q1 2026',
                'totalAmount' => '250000.00',
                'currency' => 'USD',
                'startDate' => '2026-01-01',
                'endDate' => '2026-03-31',
                'departmentIndex' => 0, // Engineering
            ],
            [
                'name' => 'Marketing Q1 2026',
                'totalAmount' => '150000.00',
                'currency' => 'USD',
                'startDate' => '2026-01-01',
                'endDate' => '2026-03-31',
                'departmentIndex' => 1, // Marketing
            ],
            [
                'name' => 'Sales Q1 2026',
                'totalAmount' => '200000.00',
                'currency' => 'USD',
                'startDate' => '2026-01-01',
                'endDate' => '2026-03-31',
                'departmentIndex' => 2, // Sales
            ],
            [
                'name' => 'HR Annual 2026',
                'totalAmount' => '100000.00',
                'currency' => 'USD',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
                'departmentIndex' => 3, // Human Resources
            ],
            [
                'name' => 'Finance Operations 2026',
                'totalAmount' => '75000.00',
                'currency' => 'USD',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
                'departmentIndex' => 4, // Finance
            ],
            [
                'name' => 'Engineering Infrastructure',
                'totalAmount' => '500000.00',
                'currency' => 'USD',
                'startDate' => '2026-02-01',
                'endDate' => '2026-12-31',
                'departmentIndex' => 0, // Engineering
            ],
            [
                'name' => 'Marketing Digital Tools',
                'totalAmount' => '80000.00',
                'currency' => 'USD',
                'startDate' => '2026-03-01',
                'endDate' => '2026-08-31',
                'departmentIndex' => 1, // Marketing
            ],
            [
                'name' => 'Sales Travel Budget',
                'totalAmount' => '120000.00',
                'currency' => 'USD',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
                'departmentIndex' => 2, // Sales
            ],
            [
                'name' => 'Engineering Cloud Services',
                'totalAmount' => '300000.00',
                'currency' => 'USD',
                'startDate' => '2026-01-01',
                'endDate' => '2026-12-31',
                'departmentIndex' => 0, // Engineering
            ],
            [
                'name' => 'Marketing Campaigns Q2',
                'totalAmount' => '95000.00',
                'currency' => 'USD',
                'startDate' => '2026-04-01',
                'endDate' => '2026-06-30',
                'departmentIndex' => 1, // Marketing
            ],
            [
                'name' => 'Sales Equipment',
                'totalAmount' => '50000.00',
                'currency' => 'USD',
                'startDate' => '2026-02-01',
                'endDate' => '2026-06-30',
                'departmentIndex' => 2, // Sales
            ],
            [
                'name' => 'HR Training Programs',
                'totalAmount' => '60000.00',
                'currency' => 'USD',
                'startDate' => '2026-02-01',
                'endDate' => '2026-12-31',
                'departmentIndex' => 3, // Human Resources
            ],
        ];

        foreach ($budgets as $data) {
            $budget = new Budget();
            $budget->setName($data['name']);
            $budget->setTotalAmount($data['totalAmount']);
            $budget->setCurrency($data['currency']);
            $budget->setStartDate(\DateTime::createFromFormat('Y-m-d', $data['startDate']));
            $budget->setEndDate(\DateTime::createFromFormat('Y-m-d', $data['endDate']));
            $budget->setDepartment($departments[$data['departmentIndex']]);
            $budget->setCreatedBy($managerUser);

            $manager->persist($budget);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            DepartmentFixture::class,
            UserFixture::class,
        ];
    }
}
