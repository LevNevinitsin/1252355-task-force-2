<?php
namespace app\rbac;

use yii\rbac\Rule;
use app\models\Response;

/**
 * Checks if there is a response from current user for particular task
 */
class UniqueResponseRule extends Rule
{
    public $name = 'isUniqueResponse';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        return isset($params['task'])
            ? !(Response::find()->where(['task_id' => $params['task']->id, 'user_id' => $user])->exists())
            : false;
    }
}
