<?php
namespace app\rbac;

use yii\rbac\Rule;

/**
 * Checks if contractor ID matches user passed via params
 */
class SelectedContractorRule extends Rule
{
    public $name = 'isSelectedContractor';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        return isset($params['task']) ? $params['task']->contractor_id === $user : false;
    }
}
