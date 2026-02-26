<?php

namespace App\Security\Voter;

use App\Entity\Budget;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BudgetVoter extends Voter
{
    public const CREATE = 'BUDGET_CREATE';
    public const EDIT = 'BUDGET_EDIT';
    public const VIEW = 'BUDGET_VIEW';
    public const CLOSE = 'BUDGET_CLOSE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::CREATE) {
            return true;
        }

        return in_array($attribute, [self::EDIT, self::VIEW, self::CLOSE])
            && $subject instanceof Budget;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($attribute === self::CREATE) {
            return $user->isManager();
        }

        /** @var Budget $budget */
        $budget = $subject;

        return match($attribute) {
            self::EDIT => $this->canEdit($budget, $user),
            self::VIEW => $this->canView($budget, $user),
            self::CLOSE => $this->canClose($budget, $user),
            default => false,
        };
    }

    private function canEdit(Budget $budget, User $user): bool
    {
        // Only managers can edit budgets
        return $user->isManager();
    }

    private function canView(Budget $budget, User $user): bool
    {
        // Managers can view all budgets
        if ($user->isManager()) {
            return true;
        }

        // Employees can view all budgets (simplified for manager-only feature)
        // In future: restrict to department when expense feature is added
        return true;
    }

    private function canClose(Budget $budget, User $user): bool
    {
        // Only managers can close budgets
        return $user->isManager() && $budget->isActive();
    }
}
