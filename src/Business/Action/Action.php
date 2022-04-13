<?php
namespace LevNevinitsin\Business\Action;

use app\models\Task;

abstract class Action
{
    /**
     * Get action name
     *
     * @return string Action name
     */
    abstract public function getName(): string;

    /**
     * Get action internal title
     *
     * @return string Action internal title
     */
    abstract public function getInternalTitle(): string;

    /**
     * Checks if user is authorized to take an action on particular task
     *
     * @param integer $userId Current user ID
     * @param string $userRole Current user role
     * @param Task $task Particular task instance
     * @return boolean "true" if user is authorized, "false" otherwise
     */
    abstract public function isUserAuthorized(int $userId, string $userRole, Task $task): bool;
}

