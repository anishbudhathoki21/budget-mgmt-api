<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Budget;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class BudgetCollectionProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $qb = $this->entityManager->getRepository(Budget::class)->createQueryBuilder('b')
            ->leftJoin('b.department', 'd')
            ->leftJoin('b.createdBy', 'u')
            ->orderBy('b.createdAt', 'DESC');

        // For now, all authenticated users can see all budgets
        // In future: filter by department for employees when expense feature is added

        return $qb->getQuery()->getResult();
    }
}
