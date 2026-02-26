<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\ApproveExpenseInput;
use App\Entity\Expense;
use App\Message\ExpenseDecisionMessage;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApproveExpenseProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private Security $security,
        private LoggerInterface $logger
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Expense
    {
        $expense = $context['previous_data'] ?? null;
        if (!$expense instanceof Expense) {
            throw new BadRequestHttpException('Expense not found');
        }

        /** @var ApproveExpenseInput $input */
        $input = $data;

        $errors = $this->validator->validate($input);
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

        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedHttpException('Authentication required');
        }

        if (!$this->security->isGranted('EXPENSE_APPROVE', $expense)) {
            throw new AccessDeniedHttpException('You do not have permission to approve or reject this expense');
        }

        if (!$expense->isPending()) {
            throw new BadRequestHttpException(json_encode(['errors' => [['message' => 'Only pending expenses can be approved or rejected']]]));
        }

        $decision = $input->decision;
        $expense->setStatus($decision);
        $expense->setReviewComment($input->comment);
        $expense->setReviewedBy($user);
        $expense->setReviewedAt(new \DateTime());

        $this->entityManager->persist($expense);
        $this->entityManager->flush();

        // Log decision
        $message = new ExpenseDecisionMessage(
            $expense->getId(),
            $decision,
            $user->getId(),
            $input->comment
        );
        $this->logDecision($message);

        return $expense;
    }

    private function logDecision(ExpenseDecisionMessage $message): void
    {
        $expense = $this->entityManager->getRepository(Expense::class)->find($message->getExpenseId());
        if (!$expense) {
            $this->logger->warning("Expense not found for decision", ['expenseId' => $message->getExpenseId()]);
            return;
        }

        $decision = strtoupper($message->getDecision());
        $submittedBy = $expense->getSubmittedBy()?->getName() ?? 'Unknown';
        $budgetName = $expense->getBudget()?->getName() ?? 'Unknown';
        $amount = $expense->getAmount();

        $logMessage = sprintf(
            "Expense #%d (%s, %s %s) for budget '%s' by %s was %s",
            $expense->getId(),
            $expense->getCategory(),
            $amount,
            $expense->getBudget()?->getCurrency() ?? 'N/A',
            $budgetName,
            $submittedBy,
            $message->getDecision()
        );

        if ($message->getComment()) {
            $logMessage .= ". Comment: " . $message->getComment();
        }

        $this->logger->info($logMessage, [
            'expenseId' => $message->getExpenseId(),
            'decision' => $message->getDecision(),
            'comment' => $message->getComment()
        ]);
    }
}
