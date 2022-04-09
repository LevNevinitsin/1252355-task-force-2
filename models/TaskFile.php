<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task_file".
 *
 * @property int $id
 * @property int $task_id
 * @property string $path
 * @property string $original_name
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
            [['task_id', 'path', 'original_name'], 'required'],
            [['task_id'], 'integer'],
            [['date_created'], 'safe'],
            [['path', 'original_name'], 'string', 'max' => 255],
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
            'original_name' => 'Original Name',
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
