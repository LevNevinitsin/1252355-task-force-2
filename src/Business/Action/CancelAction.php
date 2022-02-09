<?php
namespace LevNevinitsin\Business\Action;

class CancelAction extends Action
{
    public function getName(): string
    {
        return 'Cancel';
    }

    public function getInternalTitle(): string
    {
        return 'Отменить';
    }

    public function isUserAuthorized(int $userId, string $userRole, int $customerId, ?int $contractorId = null): bool
    {
        if ($userId === $customerId) {
            return true;
        }

        return false;
    }
}
