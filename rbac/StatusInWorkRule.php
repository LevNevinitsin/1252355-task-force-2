<?php
namespace app\rbac;

use yii\rbac\Rule;

/**
 * Checks if task is in "in work" status
 */
class StatusInWorkRule extends Rule
{
    public $name = 'isStatusInWork';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        return isset($params['task']) ? $params['task']->task_status_id === 3 : false;
    }
}
