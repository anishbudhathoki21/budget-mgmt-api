<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Expense;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class ExpenseCollectionProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private RequestStack $requestStack
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if (!$user) {
            return [];
        }

        $qb = $this->entityManager->getRepository(Expense::class)->createQueryBuilder('e')
            ->leftJoin('e.submittedBy', 'u')
            ->leftJoin('e.budget', 'b')
            ->addSelect('u')
            ->addSelect('b')
            ->andWhere('e.deletedAt IS NULL')
            ->orderBy('e.submittedAt', 'DESC');

        if (!$user->isManager()) {
            $qb->andWhere('e.submittedBy = :user')->setParameter('user', $user->getId());
        }

        // Get pagination parameters
        $itemsPerPage = $operation->getPaginationItemsPerPage() ?? 10;
        $request = $this->requestStack->getCurrentRequest();
        $page = max(1, (int)($request?->query->get('page') ?? 1));

        // Calculate offset
        $offset = ($page - 1) * $itemsPerPage;
        $qb->setFirstResult($offset)->setMaxResults($itemsPerPage);

        // Use Doctrine Paginator for proper pagination support
        return new Paginator($qb->getQuery(), false);
    }
}
