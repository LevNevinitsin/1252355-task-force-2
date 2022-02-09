<?php
namespace LevNevinitsin\Business\Action;

class RespondAction extends Action
{
    public function getName(): string
    {
        return 'Respond';
    }

    public function getInternalTitle(): string
    {
        return 'Откликнуться';
    }

    public function isUserAuthorized(int $userId, string $userRole, int $customerId, ?int $contractorId = null): bool
    {
        if ($userRole === 'contractor') {
            return true;
        }

        return false;
    }
}
