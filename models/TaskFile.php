<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task_file".
 *
 * @property int $id
 * @property int $task_id
 * @property string|null $path
 * @property string $date_created
 *
 * @property Task $task
 */
class TaskFile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task_file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id'], 'required'],
            [['task_id'], 'integer'],
            [['date_created'], 'safe'],
            [['path'], 'string', 'max' => 255],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'path' => 'Path',
            'date_created' => 'Date Created',
        ];
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }
}
