<?php
namespace LevNevinitsin\Business\Service;

use app\models\Response;

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
}
