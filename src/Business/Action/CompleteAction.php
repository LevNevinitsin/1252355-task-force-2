<?php
namespace LevNevinitsin\Business\Action;

use app\models\Task;

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

    public function isUserAuthorized(int $userId, string $userRole, Task $task): bool
    {
        if ($userId === $task->customer_id) {
            return true;
        }

        return false;
    }
}
