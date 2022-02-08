<?php
namespace LevNevinitsin\Business\Action;

class CompleteAction extends Action
{
    public function getName(): string
    {
        return 'Complete';
    }

    public function getInternalTitle(): string
    {
        return 'Завершить';
    }

    public function isUserAuthorized(int $userId, int $customerId, ?int $contractorId = null): bool
    {
        if ($userId === $customerId) {
            return true;
        }

        return false;
    }
}
