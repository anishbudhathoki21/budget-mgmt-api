<?php

namespace App\MessageHandler;

use App\Message\ExpenseDecisionMessage;
use App\Entity\Expense;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ExpenseDecisionMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    public function __invoke(ExpenseDecisionMessage $message): void
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
