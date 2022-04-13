<?php
namespace LevNevinitsin\Business\Service;

use Yii;
use yii\helpers\Url;
use app\models\Response;
use app\models\TaskFile;
use app\models\User;
use app\models\Task;
use LevNevinitsin\Business\Action\CancelAction;
use LevNevinitsin\Business\Action\RespondAction;
use LevNevinitsin\Business\Action\AcceptAction;
use LevNevinitsin\Business\Action\DeclineAction;
use LevNevinitsin\Business\Action\CompleteAction;

class TaskService
{
    /**
     * Adds conditions to tasks query by parameters
     *
     * @param \Yii\db\ActiveQuery $tasksQuery
     * @param array $selectedCategories
     * @param boolean $shouldShowWithoutResponses
     * @param boolean $shouldShowRemoteOnly
     * @param string $selectedPeriod
     * @return \Yii\db\ActiveQuery
     */
    public static function filter(
        \Yii\db\ActiveQuery $tasksQuery,
        array $selectedCategories,
        bool $shouldShowWithoutResponses,
        bool $shouldShowRemoteOnly,
        string $selectedPeriod
    ): \Yii\db\ActiveQuery
    {
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

        return $tasksQuery;
    }

    /**
     * Saves uploaded files on server and returns array with files' URLs and original names
     *
     * @param array Array of yii\web\UploadedFile objects
     * @return array Array with files' URLs and original names
     */
    public static function handleUploadedFiles(array $uploadedFiles): array
    {
        foreach ($uploadedFiles as $uploadedFile) {
            $fileExtension = $uploadedFile->getExtension();
            $filename = uniqid('upload_') . '.' . $fileExtension;
            $fileWebPath = '/upload/' . $filename;
            $fileFullPath = '@webroot' . $fileWebPath;
            $uploadedFile->saveAs($fileFullPath);

            $filesData []= [
                'webPath' => $fileWebPath,
                'originalName' => $uploadedFile->getBaseName() . '.' . $fileExtension,
            ];
        }

        return $filesData ?? [];
    }

    /**
     * Inserts the file paths and original names into the database according to the task ID
     *
     * @param array $uploadedFiles
     * @param integer $taskId
     * @return void
     */
    public static function storeUploadedFiles(array $uploadedFiles, int $taskId)
    {
        foreach ($uploadedFiles as $uploadedFile) {
            $file = new TaskFile();
            $file->task_id = $taskId;
            $file->path = $uploadedFile['webPath'];
            $file->original_name = $uploadedFile['originalName'];
            $file->save(false);
        }
    }

    /**
     * Gets available actions for specific task and current user
     *
     * @param Task $task The task
     * @return array Available actions
     */
    public static function getAvailableActions(Task $task): array
    {
        $actionCancel   = new CancelAction();
        $actionRespond  = new RespondAction();
        $actionAccept   = new AcceptAction();
        $actionDecline  = new DeclineAction();
        $actionComplete = new CompleteAction();

        $statusNewId = 1;
        $statusInWorkId = 3;

        $actionsMap = [
            $statusNewId => [
                $actionCancel,
                $actionRespond,
                $actionAccept,
            ],
            $statusInWorkId => [
                $actionDecline,
                $actionComplete,
            ],
        ];

        $availableActions = $actionsMap[$task->task_status_id] ?? [];
        $userId = Yii::$app->user->getId();
        $userRole = User::findOne($userId)->role->name;

        $availableActions = array_filter($availableActions, function ($action) use ($userId, $userRole, $task) {
            return $action->isUserAuthorized($userId, $userRole, $task);
        });

        return $availableActions;
    }

    /**
     * Gets markup for available actions for specific task and current user
     *
     * @param Task $task Specific The task
     * @return string Resulting markup
     */
    public static function getAvailableActionsMarkup(Task $task): string
    {
        $cancelUrl = Url::to(['tasks/cancel', 'id' => $task->id]);

        $actionsMarkupMap = [
            'Cancel' => "<a class='button button--blue' href='$cancelUrl'>Отменить задание</a>",
            'Respond' => '<button class="button button--blue action-btn" data-action="act_response" type="button">Откликнуться на задание</button>',
            'Decline' => '<button class="button button--blue action-btn" data-action="refusal" type="button">Отказаться от задания</button>',
            'Complete' => '<button class="button button--blue action-btn" data-action="completion" type="button">Завершить задание</button>',
        ];

        $markup = '';
        $actionsNames = array_map(function ($action) { return $action->getName(); }, self::getAvailableActions($task));

        foreach ($actionsNames as $actionName) {
            $markup .= $actionsMarkupMap[$actionName] ?? '';
        }

        return $markup;
    }
}
