<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\UpdateExpenseInput;
use App\Entity\Expense;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UpdateExpenseProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Expense
    {
        $expense = $context['previous_data'] ?? null;
        if (!$expense instanceof Expense) {
            throw new BadRequestHttpException('Expense not found');
        }

        /** @var UpdateExpenseInput $update */
        $update = $data;

        if ($update->status === 'cancelled') {
            $user = $this->security->getUser();
            if (!$user) {
                throw new AccessDeniedHttpException('Authentication required');
            }

            if ($expense->getSubmittedBy()?->getId() !== $user->getId()) {
                throw new AccessDeniedHttpException('You can only cancel your own expenses');
            }

            if (!$expense->canBeCancelled()) {
                throw new BadRequestHttpException(json_encode(['errors' => [[ 'message' => 'Only pending expenses can be cancelled']]]));
            }

            $expense->setStatus('cancelled');
            $this->entityManager->persist($expense);
            $this->entityManager->flush();
        }

        return $expense;
    }
}
