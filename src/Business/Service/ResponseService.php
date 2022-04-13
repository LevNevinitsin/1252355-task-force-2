<?php
namespace LevNevinitsin\Business\Service;

use app\models\Task;
use app\models\User;
use app\models\Response;

class ResponseService
{
    /**
     * Gets responses that are relevant to particular task and current user.
     * If current user is author of the task, function returns all of the task responses.
     *
     * Else it queries responses according to task and current user. In this case if resulting array is empty,
     * it returns null, so the Responses block won't be shown in markup.
     *
     * @param integer $currentUserId
     * @param Task $task
     * @return array|null
     */
    public static function getRelevant(int $currentUserId, Task $task): ?array
    {
        if ($currentUserId === $task->customer_id) {
            return $task->responses;
        }

        return Response::find()->where(['task_id' => $task->id, 'user_id' => $currentUserId])->all() ?: null;
    }
}
