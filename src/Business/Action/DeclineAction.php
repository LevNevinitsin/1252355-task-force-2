<?php
namespace LevNevinitsin\Business\Action;

use app\models\Task;

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

    public function isUserAuthorized(int $userId, string $userRole, Task $task): bool
    {
        if ($userId === $task->contractor_id) {
            return true;
        }

        return false;
    }
}
