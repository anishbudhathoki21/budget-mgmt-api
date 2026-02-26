<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\CreateExpenseInput;
use App\Entity\Budget;
use App\Entity\Expense;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CreateExpenseProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private Security $security
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Expense
    {
        /** @var CreateExpenseInput $data */
        $errors = $this->validator->validate($data);
        if (\count($errors) > 0) {
            $violations = [];
            foreach ($errors as $error) {
                $violations[] = [
                    'field' => $error->getPropertyPath(),
                    'message' => $error->getMessage()
                ];
            }
            throw new BadRequestHttpException(json_encode(['errors' => $violations]));
        }

        $budget = $this->entityManager->getRepository(Budget::class)->find($data->budgetId);
        if (!$budget) {
            throw new BadRequestHttpException(json_encode(['errors' => [[ 'field' => 'budgetId', 'message' => 'Budget not found']]]));
        }

        if (!$budget->isValid()) {
            throw new BadRequestHttpException(json_encode(['errors' => [[ 'message' => 'Cannot submit expense to a closed or expired budget']]]));
        }

        // Calculate used amount = approved + pending
        $approved = (float) $budget->getApprovedExpensesTotal();
        $pending = 0.0;
        foreach ($budget->getExpenses() as $existing) {
            if ($existing->getStatus() === 'pending') {
                $pending += (float) $existing->getAmount();
            }
        }

        $totalUsed = $approved + $pending;
        $remaining = (float) $budget->getTotalAmount() - $totalUsed;

        if ((float) $data->amount > $remaining) {
            throw new BadRequestHttpException(json_encode(['errors' => [[ 'field' => 'amount', 'message' => 'Expense exceeds remaining budget balance']]]));
        }

        /** @var User $user */
        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException('Authentication required');
        }

        $expense = new Expense();
        $expense->setAmount((string) $data->amount);
        $expense->setDescription($data->description);
        $expense->setCategory($data->category);
        $expense->setReceiptNote($data->receiptNote ?? null);
        $expense->setBudget($budget);
        $expense->setSubmittedBy($user);

        $this->entityManager->persist($expense);
        $this->entityManager->flush();

        return $expense;
    }
}
