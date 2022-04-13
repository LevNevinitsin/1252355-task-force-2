<?php
namespace LevNevinitsin\Business\Action;

use app\models\Task;

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

    public function isUserAuthorized(int $userId, string $userRole, Task $task): bool
    {
        if ($userId === $task->customer_id) {
            return true;
        }

        return false;
    }
}
