<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Expense;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ExpenseCollectionProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $qb = $this->entityManager->getRepository(Expense::class)->createQueryBuilder('e')
            ->leftJoin('e.submittedBy', 'u')
            ->leftJoin('e.budget', 'b')
            ->addSelect('u')
            ->addSelect('b')
            ->orderBy('e.submittedAt', 'DESC');

        if ($user && $user->isManager()) {
            return $qb->getQuery()->getResult();
        }

        if ($user) {
            $qb->andWhere('e.submittedBy = :user')->setParameter('user', $user->getId());
            return $qb->getQuery()->getResult();
        }

        return [];
    }
}
