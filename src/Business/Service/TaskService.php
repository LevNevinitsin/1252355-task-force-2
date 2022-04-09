<?php
namespace LevNevinitsin\Business\Service;

use app\models\Response;
use app\models\TaskFile;
use Yii;

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
}
