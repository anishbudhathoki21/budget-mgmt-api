<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist((new Role())->setName('EMPLOYEE'));
        $manager->persist((new Role())->setName('MANAGER'));
        $manager->flush();
    }
}
