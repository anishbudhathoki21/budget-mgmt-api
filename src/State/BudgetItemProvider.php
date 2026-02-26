<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Budget;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BudgetItemProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $budget = $this->entityManager->getRepository(Budget::class)->find($uriVariables['id']);

        if (!$budget) {
            throw new NotFoundHttpException('Budget not found');
        }

        if (!$this->security->isGranted('BUDGET_VIEW', $budget)) {
            throw new AccessDeniedHttpException('You do not have permission to view this budget');
        }

        return $budget;
    }
}
