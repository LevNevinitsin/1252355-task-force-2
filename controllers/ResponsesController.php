<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Response;
use app\models\User;
use app\models\Task;
use LevNevinitsin\Business\Action\AcceptAction;
use LevNevinitsin\Business\Task as BusinessTask;

class ResponsesController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['add'],
                        'roles' => ['respondToTask'],
                        'roleParams' => function() {
                            return ['task' => Task::findOne(['id' => Yii::$app->request->post('Response')['task_id']])];
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['accept', 'decline'],
                        'roles' => ['manageResponse'],
                        'roleParams' => function() {
                            return ['task' => Response::findOne(['id' => Yii::$app->request->get('id')])->task];
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionAdd() {
        if (Yii::$app->request->getIsPost()) {
            $response = new Response();
            $response->load(Yii::$app->request->post());
            $user = User::findOne(Yii::$app->user->getId());
            $response->user_id = $user->id;
            $response->save();
            return $this->redirect(['/tasks/view', 'id' => $response->task_id]);
        }
    }

    public function actionAccept($id)
    {
        $response = Response::findOne($id);
        $taskRecord = $response->task;
        $taskRecord->contractor_id = $response->user_id;
        $task = new BusinessTask($taskRecord);
        $task->takeAction(new AcceptAction());
        return $this->redirect(['/tasks/view', 'id' => $taskRecord->id]);
    }

    public function actionDecline($id)
    {
        $response = Response::findOne($id);
        $task = $response->task;
        $response->is_declined = 1;
        $response->save();
        return $this->redirect(['/tasks/view', 'id' => $task->id]);
    }
}
