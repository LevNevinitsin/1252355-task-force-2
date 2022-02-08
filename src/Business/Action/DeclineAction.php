<?php
namespace LevNevinitsin\Business\Action;

class DeclineAction extends Action
{
    public function getName(): string
    {
        return 'Decline';
    }

    public function getInternalTitle(): string
    {
        return 'Отказаться';
    }

    public function isUserAuthorized(int $userId, int $customerId, ?int $contractorId = null): bool
    {
        if ($userId === $contractorId) {
            return true;
        }

        return false;
    }
}
