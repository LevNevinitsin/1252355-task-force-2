<?php
namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\Task;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use LevNevinitsin\Business\Service\TaskService;

class TasksController extends Controller
{
    public function actionIndex()
    {
        $task = new Task();
        $category = new Category();

        $request = Yii::$app->request;
        $selectedCategories = $request->get('Category')['id'] ?? [];
        $shouldShowWithoutResponses = $request->get('showWithoutResponses') === '1' ? true : false;
        $shouldShowRemoteOnly = $request->get('showRemoteOnly') === '1' ? true : false;
        $selectedPeriod = $request->get('Task')['date_created'] ?? '';

        $tasksQuery = Task::find()->joinWith('category')->joinWith('city')->where(['task_status_id' => 1]);

        $tasksQuery = TaskService::filter(
            $tasksQuery,
            $selectedCategories,
            $shouldShowWithoutResponses,
            $shouldShowRemoteOnly,
            $selectedPeriod
        );

        $newTasks = $tasksQuery->all();
        $categories = Category::find()->select(['name'])->orderBy(['id' => SORT_ASC])->indexBy('id')->column();

        return $this->render('tasks', [
            'taskModel'                  => $task,
            'categoryModel'              => $category,
            'newTasks'                   => $newTasks,
            'categories'                 => $categories,
            'selectedCategories'         => $selectedCategories,
            'shouldShowRemoteOnly'       => $shouldShowRemoteOnly,
            'shouldShowWithoutResponses' => $shouldShowWithoutResponses,
            'selectedPeriod'             => $selectedPeriod,
        ]);
    }

    public function actionView($id)
    {
        $tasksIds = Task::find()->select(['id'])->column();

        if (!$id || !in_array($id, $tasksIds)) {
            throw new NotFoundHttpException();
        }

        $task = Task::findOne($id);

        return $this->render('view-task', [
            'task' => $task,
        ]);
    }
}
