<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Expense;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class DeleteExpenseProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $expense = $data;
        if (!$expense instanceof Expense) {
            throw new \RuntimeException('Expense not found');
        }

        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException('Authentication required');
        }

        // only owner or manager can delete
        if (!$this->security->isGranted('EXPENSE_CANCEL', $expense) && !$user->isManager()) {
            throw new AccessDeniedHttpException('You do not have permission to delete this expense');
        }

        if ($expense->isDeleted()) {
            return new \App\Dto\DeleteExpenseOutput('Expense already deleted', $expense->getId());
        }

        $expense->delete();
        $this->entityManager->persist($expense);
        $this->entityManager->flush();

        return new \App\Dto\DeleteExpenseOutput('Expense deleted successfully', $expense->getId());
    }
}
