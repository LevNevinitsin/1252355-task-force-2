<?php
namespace LevNevinitsin\Business\Action;

use app\models\Response;
use app\models\Task;

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

    public function isUserAuthorized(int $userId, string $userRole, Task $task): bool
    {
        $hadResponded = Response::find()->where(['task_id' => $task->id, 'user_id' => $userId])->exists();

        if ($userRole === 'contractor' && !$hadResponded) {
            return true;
        }

        return false;
    }
}
