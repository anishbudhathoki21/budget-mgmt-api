<?php

namespace App\DataFixtures;

use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DepartmentFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $departments = [
            ['name' => 'Engineering', 'description' => 'Software development and technical operations'],
            ['name' => 'Marketing', 'description' => 'Marketing and brand management'],
            ['name' => 'Sales', 'description' => 'Sales and business development'],
            ['name' => 'Human Resources', 'description' => 'HR and employee relations'],
            ['name' => 'Finance', 'description' => 'Financial planning and accounting'],
        ];

        foreach ($departments as $data) {
            $department = new Department();
            $department->setName($data['name']);
            $department->setDescription($data['description']);
            $manager->persist($department);
        }

        $manager->flush();
    }
}
