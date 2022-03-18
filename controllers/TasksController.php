<?php
namespace app\controllers;
use app\models\Task;
use yii\web\Controller;

class TasksController extends Controller
{
    public function actionIndex()
    {
        $newTasks = Task::find()->joinWith('category')->where(['task_status_id' => 1])->all();
        return $this->render('tasks', ['newTasks' => $newTasks]);
    }
}
