<?php

namespace App\Message;

class ExpenseDecisionMessage
{
    public function __construct(
        private int $expenseId,
        private string $decision,
        private int $decidedByUserId,
        private ?string $comment = null
    ) {}

    public function getExpenseId(): int
    {
        return $this->expenseId;
    }

    public function getDecision(): string
    {
        return $this->decision;
    }

    public function getDecidedByUserId(): int
    {
        return $this->decidedByUserId;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }
}
