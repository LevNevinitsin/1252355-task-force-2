<?php
namespace LevNevinitsin\Business\Action;

use app\models\Task;

class AcceptAction extends Action
{
    public function getName(): string
    {
        return 'Accept';
    }

    public function getInternalTitle(): string
    {
        return 'Принять';
    }

    public function isUserAuthorized(int $userId, string $userRole, Task $task): bool
    {
        if ($userId === $task->customer_id) {
            return true;
        }

        return false;
    }
}
