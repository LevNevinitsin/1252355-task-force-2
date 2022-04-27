<?php
namespace app\controllers;

use Yii;
use app\models\Category;
use app\models\Task;
use app\models\User;
use app\models\City;
use app\models\Response as ResponseModel;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use LevNevinitsin\Business\Action\CancelAction;
use LevNevinitsin\Business\Action\CompleteAction;
use LevNevinitsin\Business\Action\DeclineAction;
use LevNevinitsin\Business\Service\TaskService;
use LevNevinitsin\Business\Service\LocationService;
use LevNevinitsin\Business\Task as BusinessTask;
use yii\data\Pagination;

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
                        'roles' => ['customer'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['decline'],
                        'roles' => ['declineTask'],
                        'roleParams' => function() {
                            return ['task' => Task::findOne(['id' => Yii::$app->request->get('id')])];
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['cancel'],
                        'roles' => ['cancelOwnTask'],
                        'roleParams' => function() {
                            return ['task' => Task::findOne(['id' => Yii::$app->request->get('id')])];
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['complete'],
                        'roles' => ['completeOwnTask'],
                        'roleParams' => function() {
                            return ['task' => Task::findOne(['id' => Yii::$app->request->post('Task')['task_id']])];
                        },
                    ],
                ],
            ],
            [
                'class' => 'yii\filters\HttpCache',
                'only' => ['view'],
                'cacheControlHeader' => 'Cache-Control: no-cache',
                'etagSeed' => function ($action, $params) {
                    $task = Task::findOne(Yii::$app->request->get('id'));
                    $taskUpdateTimstamp = Yii::$app->formatter->asTimestamp($task->date_updated);

                    $lastResponseTimestamp = Yii::$app->formatter
                        ->asTimestamp($task->getResponses()->max('date_created'));

                    return serialize([$taskUpdateTimstamp, $lastResponseTimestamp, Yii::$app->user->id]);
                },
            ],
        ];
    }

    public function actionIndex()
    {
        $task = new Task();
        $category = new Category();
        $pageSize = 5;

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

        $tasksCount = $tasksQuery->count();
        $pagination = new Pagination(['totalCount' => $tasksCount, 'pageSize' => $pageSize]);

        $newTasks = $tasksQuery->orderBy(['date_created' => SORT_DESC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

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
            'pagination'                 => $pagination,
        ]);
    }

    public function actionView($id)
    {
        if (!$id || !$task = Task::findOne($id)) {
            throw new NotFoundHttpException();
        }

        $geocoderApiKey = 'e666f398-c983-4bde-8f14-e3fec900592a';
        $geocoderApiUri = 'https://geocode-maps.yandex.ru/';

        $client = new Client([
            'base_uri' => $geocoderApiUri,
        ]);

        if ($task->latitude && $task->longitude) {
            try {
                $cacheKey = "task-{$task->id}-location";
                $cacheDuration = 86400;

                $responseData = Yii::$app->cache->getOrSet($cacheKey, function () use ($client, $task, $geocoderApiKey) {
                    $response = $client->request('GET', '1.x', [
                        'query' => [
                            'geocode' => "$task->longitude, $task->latitude",
                            'apikey' => $geocoderApiKey,
                            'format' => 'json',
                         ],
                    ]);

                    $content = $response->getBody()->getContents();
                    return json_decode($content, true);
                }, $cacheDuration);

                $geoObject = ArrayHelper::getValue($responseData, 'response.GeoObjectCollection.featureMember.0.GeoObject');
                $cityName = LocationService::getCity($geoObject);
                $address = ArrayHelper::getValue($geoObject, 'name');
            } catch (\Exception $e) {
                $cityName = $task->city->name;
                $address = $task->location;
            }
        } else {
            $cityName = $task->city->name ?? '';
            $address = $task->location ?? '';
        }

        $response = new ResponseModel();
        $this->view->params['taskModel'] = $task;
        $this->view->params['responseModel'] = $response;

        return $this->render('view-task', [
            'task' => $task,
            'cityName' => $cityName,
            'address' => $address,
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
        $userCity = User::findOne(Yii::$app->user->getId())->city;

        if (Yii::$app->request->getIsPost()) {
            $task->load(Yii::$app->request->post());
            $task->task_status_id = 1;
            $task->customer_id = Yii::$app->user->identity->id;

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($task);
            }

            if ($task->validate() && ($filesData === null || $areFilesValid)) {
                if ($taskCityName = $task->cityName) {
                    $task->city_id = City::findOne(['name' => $taskCityName])->id;
                }

                $task->save(false);
                $task->refresh();
                $taskId = $task->id;

                if ($filesData) {
                    TaskService::storeUploadedFiles($filesData, $taskId);
                }

                return $this->redirect(['/tasks/view', 'id' => $taskId]);
            }
        }

        Yii::$app->session->remove('filesData');

        return $this->render('add-task', [
            'model' => $task,
            'categories' => $categories,
            'areFilesValid' => $areFilesValid,
            'userCity' => $userCity,
        ]);
    }

    public function actionCancel($id)
    {
        $taskRecord = Task::findOne($id);
        $task = new BusinessTask($taskRecord);
        $task->takeAction(new CancelAction());
        return $this->redirect(['/tasks/view', 'id' => $taskRecord->id]);
    }

    public function actionDecline($id)
    {
        $taskRecord = Task::findOne($id);
        $task = new BusinessTask($taskRecord);
        $task->takeAction(new DeclineAction());
        return $this->redirect(['/tasks/view', 'id' => $taskRecord->id]);
    }

    public function actionComplete()
    {
        if (Yii::$app->request->getIsPost()) {
            $requestData = Yii::$app->request->post('Task');
            $taskRecord = Task::findOne($requestData['task_id']);
            $taskRecord->score = $requestData['score'];
            $taskRecord->feedback = $requestData['feedback'];
            $task = new BusinessTask($taskRecord);
            $task->takeAction(new CompleteAction());
            return $this->redirect(['/tasks/view', 'id' => $taskRecord->id]);
        }
    }
}
