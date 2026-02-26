<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $managerRole = $manager->getRepository(Role::class)->findOneBy(['name' => 'MANAGER']);
        $employeeRole = $manager->getRepository(Role::class)->findOneBy(['name' => 'EMPLOYEE']);

        // Create Manager Users
        $manager1 = new User();
        $manager1->setName('John Manager');
        $manager1->setEmail('manager@example.com');
        $manager1->setPassword($this->passwordHasher->hashPassword($manager1, 'password123'));
        $manager1->setRole($managerRole);
        $manager->persist($manager1);

        $manager2 = new User();
        $manager2->setName('Sarah Director');
        $manager2->setEmail('sarah@example.com');
        $manager2->setPassword($this->passwordHasher->hashPassword($manager2, 'password123'));
        $manager2->setRole($managerRole);
        $manager->persist($manager2);

        // Create Employee Users
        $employee1 = new User();
        $employee1->setName('Jane Employee');
        $employee1->setEmail('employee@example.com');
        $employee1->setPassword($this->passwordHasher->hashPassword($employee1, 'password123'));
        $employee1->setRole($employeeRole);
        $manager->persist($employee1);

        $employee2 = new User();
        $employee2->setName('Bob Developer');
        $employee2->setEmail('bob@example.com');
        $employee2->setPassword($this->passwordHasher->hashPassword($employee2, 'password123'));
        $employee2->setRole($employeeRole);
        $manager->persist($employee2);

        $employee3 = new User();
        $employee3->setName('Alice Marketer');
        $employee3->setEmail('alice@example.com');
        $employee3->setPassword($this->passwordHasher->hashPassword($employee3, 'password123'));
        $employee3->setRole($employeeRole);
        $manager->persist($employee3);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RoleFixture::class,
        ];
    }
}
