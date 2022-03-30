<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property string $overview
 * @property string $description
 * @property int $category_id
 * @property int|null $city_id
 * @property string|null $coordinates
 * @property int|null $budget
 * @property string|null $deadline
 * @property int $task_status_id
 * @property int $customer_id
 * @property int|null $contractor_id
 * @property int|null $score
 * @property string|null $feedback
 * @property string $date_created
 * @property int $responsesCount
 *
 * @property Category $category
 * @property City $city
 * @property User $contractor
 * @property User $customer
 * @property Response[] $responses
 * @property TaskFile[] $taskFiles
 * @property TaskStatus $taskStatus
 */
class Task extends \yii\db\ActiveRecord
{
    public $responsesCount;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['overview', 'description', 'category_id', 'task_status_id', 'customer_id'], 'required'],
            [['description', 'coordinates', 'feedback'], 'string'],
            [['category_id', 'city_id', 'budget', 'task_status_id', 'customer_id', 'contractor_id', 'score'], 'integer'],
            [['deadline', 'date_created'], 'safe'],
            [['overview'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
            [['contractor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['contractor_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['task_status_id'], 'exist', 'skipOnError' => true, 'targetClass' => TaskStatus::class, 'targetAttribute' => ['task_status_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'overview' => 'Overview',
            'description' => 'Description',
            'category_id' => 'Category ID',
            'city_id' => 'City ID',
            'coordinates' => 'Coordinates',
            'budget' => 'Budget',
            'deadline' => 'Deadline',
            'task_status_id' => 'Task Status ID',
            'customer_id' => 'Customer ID',
            'contractor_id' => 'Contractor ID',
            'score' => 'Score',
            'feedback' => 'Feedback',
            'date_created' => 'Date Created',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Contractor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContractor()
    {
        return $this->hasOne(User::class, ['id' => 'contractor_id']);
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(User::class, ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponses()
    {
        return $this->hasMany(Response::class, ['task_id' => 'id'])->inverseOf('task');
    }

    /**
     * Gets query for [[TaskFiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskFiles()
    {
        return $this->hasMany(TaskFile::class, ['task_id' => 'id']);
    }

    /**
     * Gets query for [[TaskStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskStatus()
    {
        return $this->hasOne(TaskStatus::class, ['id' => 'task_status_id']);
    }
}
