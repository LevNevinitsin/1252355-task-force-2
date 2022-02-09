<?php
namespace LevNevinitsin\Business\Action;

class StartAction extends Action
{
    public function getName(): string
    {
        return 'Start';
    }

    public function getInternalTitle(): string
    {
        return 'Принять';
    }

    public function isUserAuthorized(int $userId, string $userRole, int $customerId, ?int $contractorId = null): bool
    {
        if ($userId === $customerId) {
            return true;
        }

        return false;
    }
}
