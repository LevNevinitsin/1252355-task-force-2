<?php
namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\Task;
use app\models\Response;
use yii\web\Controller;

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
        $tasksQuery->andFilterWhere(['category_id' => $selectedCategories]);

        if ($shouldShowRemoteOnly) {
            $tasksQuery->andWhere(['city_id' => NULL]);
        }

        if ($shouldShowWithoutResponses) {
            $responsesCountSubquery = Response::find()
                ->select(['task_id', 'COUNT(*) as responsesCount'])
                ->groupBy('task_id');

            $tasksQuery
                ->leftJoin(['r' => $responsesCountSubquery], 'r.task_id = task.id')
                ->andWhere(['r.responsesCount' => NULL]);
        }

        $tasksQuery->andFilterWhere(['<', 'TIMEDIFF(NOW(), task.date_created)', $selectedPeriod]);

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
}
