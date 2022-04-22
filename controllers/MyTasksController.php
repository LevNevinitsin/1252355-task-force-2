<?php
namespace app\controllers;

use app\models\User;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

class MyTasksController extends Controller
{
    private $navigationMap = [
        'customer' => [
            '/my-tasks/new'     => 'Новые',
            '/my-tasks/in-work' => 'В процессе',
            '/my-tasks/closed'  => 'Закрытые',
        ],
        'contractor' => [
            '/my-tasks/in-work' => 'В процессе',
            '/my-tasks/overdue' => 'Просрочено',
            '/my-tasks/closed'  => 'Закрытые',
        ],
    ];

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'in-work', 'closed'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['new'],
                        'roles' => ['customer'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['overdue'],
                        'roles' => ['contractor'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->getIdentity()->role_id === 1) {
            return $this->redirect('/my-tasks/new');
        }

        return $this->redirect('/my-tasks/in-work');
    }

    public function actionNew()
    {
        $title = 'Новые задания';
        $user = User::findOne(Yii::$app->user->id);
        $navigationLinks = $this->navigationMap['customer'];
        $tasks = $user->getTasks()->where(['task_status_id' => 1])->orderBy(['date_created' => SORT_DESC])->all();

        return $this->render('my-tasks', [
            'tasks' => $tasks,
            'title' => $title,
            'navigationLinks' => $navigationLinks,
        ]);
    }

    public function actionInWork()
    {
        $title = 'Задания в процессе выполнения';
        $user = User::findOne(Yii::$app->user->id);

        $navigationLinks = $user->role_id === 1
            ? $this->navigationMap['customer']
            : $this->navigationMap['contractor'];

        $tasks = $user->getTasks()->where(['task_status_id' => 3])->orderBy(['date_created' => SORT_DESC])->all();

        return $this->render('my-tasks', [
            'tasks' => $tasks,
            'title' => $title,
            'navigationLinks' => $navigationLinks,
        ]);
    }

    public function actionOverdue()
    {
        $title = 'Просроченные задания';
        $user = User::findOne(Yii::$app->user->id);
        $navigationLinks = $this->navigationMap['contractor'];
        $today = date('Y-m-d');

        $tasks = $user->getTasks()
            ->where(['task_status_id' => 3])
            ->andWhere(['<', 'deadline', $today])
            ->orderBy(['date_created' => SORT_DESC])->all();

        return $this->render('my-tasks', [
            'tasks' => $tasks,
            'title' => $title,
            'navigationLinks' => $navigationLinks,
        ]);
    }

    public function actionClosed()
    {
        $title = 'Зыкрытые задания';
        $user = User::findOne(Yii::$app->user->id);
        $isCustomer = $user->role_id === 1;

        $navigationLinks = $isCustomer
            ? $this->navigationMap['customer']
            : $this->navigationMap['contractor'];

        $tasksQuery = $isCustomer
            ? $user->getTasks()->where(['task_status_id' => [2, 4, 5]])
            : $user->getTasks()->where(['task_status_id' => [4, 5]]);

        $tasks = $tasksQuery->orderBy(['date_created' => SORT_DESC])->all();

        return $this->render('my-tasks', [
            'tasks' => $tasks,
            'title' => $title,
            'navigationLinks' => $navigationLinks,
        ]);
    }
}
