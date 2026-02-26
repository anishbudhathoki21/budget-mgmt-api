<?php

namespace App\Repository;

use App\Entity\Expense;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expense>
 */
class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.submittedBy = :user')
            ->setParameter('user', $user)
            ->orderBy('e.submittedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPendingByUser(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.submittedBy = :user')
            ->andWhere('e.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'pending')
            ->orderBy('e.submittedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPendingForReview(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.status = :status')
            ->setParameter('status', 'pending')
            ->orderBy('e.submittedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
