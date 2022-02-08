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

    public function isUserAuthorized(int $userId, int $customerId, ?int $contractorId = null): bool
    {
        if ($userId !== $customerId) {
            return true;
        }

        return false;
    }
}
