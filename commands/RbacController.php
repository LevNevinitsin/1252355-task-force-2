<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\User;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();

        // add rules
        $authorRule             = new \app\rbac\AuthorRule;
        $selectedContractorRule = new \app\rbac\SelectedContractorRule;
        $statusNewRule          = new \app\rbac\StatusNewRule;
        $statusInWorkRule       = new \app\rbac\StatusInWorkRule;
        $uniqueResponse         = new \app\rbac\UniqueResponseRule;
        $auth->add($authorRule);
        $auth->add($selectedContractorRule);
        $auth->add($statusNewRule);
        $auth->add($statusInWorkRule);
        $auth->add($uniqueResponse);

        $manageOwnTask = $auth->createPermission('manageOwnTask');
        $manageOwnTask->description = 'Manage own task';
        $manageOwnTask->ruleName = $authorRule->name;
        $auth->add($manageOwnTask);

        $manageTaskNew = $auth->createPermission('manageTaskNew');
        $manageTaskNew->description = 'Manage task which status is "new"';
        $manageTaskNew->ruleName = $statusNewRule->name;
        $auth->add($manageTaskNew);
        $auth->addChild($manageOwnTask, $manageTaskNew);

        $manageTaskInWork = $auth->createPermission('manageTaskInWork');
        $manageTaskInWork->description = 'Manage task which status is "in work"';
        $manageTaskInWork->ruleName = $statusInWorkRule->name;
        $auth->add($manageTaskInWork);
        $auth->addChild($manageOwnTask, $manageTaskInWork);

        // add "cancelOwnTask" permission - ONLY TASK AUTHOR, task status === new
        $cancelOwnTask = $auth->createPermission('cancelOwnTask');
        $cancelOwnTask->description = 'Cancel own task';
        $auth->add($cancelOwnTask);
        $auth->addChild($manageTaskNew, $cancelOwnTask);

        // add "manageResponse" permission - ONLY TASK AUTHOR, task status === new
        $manageResponse = $auth->createPermission('manageResponse');
        $manageResponse->description = 'Manage response';
        $auth->add($manageResponse);
        $auth->addChild($manageTaskNew, $manageResponse);

        // add "completeOwnTask" permission - ONLY TASK AUTHOR, task status === in work
        $completeOwnTask = $auth->createPermission('completeOwnTask');
        $completeOwnTask->description = 'Complete task';
        $auth->add($completeOwnTask);
        $auth->addChild($manageTaskInWork, $completeOwnTask);


        $actTaskNew = $auth->createPermission('actTaskNew');
        $actTaskNew->description = 'Perform an action with task which status is "new"';
        $actTaskNew->ruleName = $statusNewRule->name;
        $auth->add($actTaskNew);

        $actTaskInWork = $auth->createPermission('actTaskInWork');
        $actTaskInWork->description = 'Perform an action with task which status is "in work"';
        $actTaskInWork->ruleName = $statusInWorkRule->name;
        $auth->add($actTaskInWork);

        // add "respondToTask" permission - ANY CONTRACTOR, task status === new, only one response per task for specific user
        $respondToTask = $auth->createPermission('respondToTask');
        $respondToTask->description = 'Respond to task';
        $respondToTask->ruleName = $uniqueResponse->name;
        $auth->add($respondToTask);
        $auth->addChild($actTaskNew, $respondToTask);

        // add "declineTask" permission - ONLY TASK CONTRACTOR, task status === in work
        $declineTask = $auth->createPermission('declineTask');
        $declineTask->description = 'Decline task';
        $declineTask->ruleName = $selectedContractorRule->name;
        $auth->add($declineTask);
        $auth->addChild($actTaskInWork, $declineTask);


        // add "customer" role and give this role permissions
        $customer = $auth->createRole('customer');
        $auth->add($customer);
        $auth->addChild($customer, $manageOwnTask);

        // add "contractor" role and give this role permissions
        $contractor = $auth->createRole('contractor');
        $auth->add($contractor);
        $auth->addChild($contractor, $actTaskNew);
        $auth->addChild($contractor, $actTaskInWork);

        $allUsers = User::find()->all();

        foreach ($allUsers as $user) {
            if ($user->role_id === 1) {
                $auth->assign($customer, $user->getId());
            } else {
                $auth->assign($contractor, $user->getId());
            }
        }
    }
}
