<?php
namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\Task;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;
use LevNevinitsin\Business\Service\TaskService;

class TasksController extends Controller
{
    private const INVALID_FILES_DATA = ['Invalid files'];

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['add', 'upload-files'],
                        'matchCallback' => function($rule, $action) {
                            return Yii::$app->user->identity->role_id === 1;
                        }
                    ]
                ],
            ],
        ];
    }

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

    public function actionUploadFiles()
    {
        $task = new Task();
        $task->files = UploadedFile::getInstances($task, 'files');

        $filesData = $task->validate('files')
            ? TaskService::handleUploadedFiles($task->files)
            : self::INVALID_FILES_DATA;

        Yii::$app->session->set('filesData', $filesData);
    }

    public function actionAdd()
    {
        $task = new Task();
        $categories = Category::find()->select(['name'])->orderBy(['id' => SORT_ASC])->indexBy('id')->column();
        $filesData = Yii::$app->session->get('filesData');
        $areFilesValid = $filesData !== self::INVALID_FILES_DATA;

        if (Yii::$app->request->getIsPost()) {
            $task->load(Yii::$app->request->post());
            $task->task_status_id = 1;
            $task->customer_id = Yii::$app->user->identity->id;

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($task);
            }

            if ($task->validate() && ($filesData === null || $areFilesValid)) {
                $task->save(false);
                $task->refresh();
                $taskId = $task->id;

                if ($filesData) {
                    TaskService::storeUploadedFiles($filesData, $taskId);
                }

                $this->redirect("/tasks/view/$taskId");
            }
        }

        Yii::$app->session->remove('filesData');

        return $this->render('add-task', [
            'model' => $task,
            'categories' => $categories,
            'areFilesValid' => $areFilesValid,
        ]);
    }
}
