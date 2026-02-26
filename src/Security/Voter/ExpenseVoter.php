<?php

namespace App\Security\Voter;

use App\Entity\Expense;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ExpenseVoter extends Voter
{
    public const VIEW = 'EXPENSE_VIEW';
    public const CANCEL = 'EXPENSE_CANCEL';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::CANCEL]) && $subject instanceof Expense;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Expense $expense */
        $expense = $subject;

        return match ($attribute) {
            self::VIEW => $this->canView($expense, $user),
            self::CANCEL => $this->canCancel($expense, $user),
            default => false,
        };
    }

    private function canView(Expense $expense, User $user): bool
    {
        if ($user->isManager()) {
            return true;
        }
        return $expense->getSubmittedBy()?->getId() === $user->getId();
    }

    private function canCancel(Expense $expense, User $user): bool
    {
        return $expense->getSubmittedBy()?->getId() === $user->getId() && $expense->canBeCancelled();
    }
}
