<?php
namespace app\rbac;

use yii\rbac\Rule;

/**
 * Checks if task is in "new" status
 */
class StatusNewRule extends Rule
{
    public $name = 'isStatusNew';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        return isset($params['task']) ? $params['task']->task_status_id === 1 : false;
    }
}
