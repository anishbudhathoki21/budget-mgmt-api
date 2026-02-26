<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Budget;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\RequestStack;

class BudgetCollectionProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $qb = $this->entityManager->getRepository(Budget::class)->createQueryBuilder('b')
            ->leftJoin('b.department', 'd')
            ->leftJoin('b.createdBy', 'u')
            ->addSelect('d')
            ->addSelect('u')
            ->orderBy('b.createdAt', 'DESC');

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
