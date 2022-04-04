<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $password_repeat
 * @property int $city_id
 * @property string|null $birthdate
 * @property string|null $photo
 * @property string|null $phone
 * @property string|null $telegram
 * @property string|null $self_description
 * @property int $role_id
 * @property int $fails_count
 * @property string $date_registered
 *
 * @property Category[] $categories
 * @property ChosenCategory[] $chosenCategories
 * @property City $city
 * @property Response[] $responses
 * @property Role $role
 * @property Task[] $tasks
 */
class User extends \yii\db\ActiveRecord
{
    public $password_repeat;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email', 'password', 'city_id', 'role_id'], 'required'],
            [['city_id', 'role_id', 'fails_count'], 'integer'],
            [['birthdate', 'date_registered'], 'safe'],
            [['self_description'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['password', 'password_repeat'], 'string', 'min' => 8],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
            [['photo'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 11],
            [['telegram'], 'string', 'max' => 64],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::class, 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Ваше имя',
            'email' => 'Email',
            'password' => 'Пароль',
            'password_repeat' => 'Повтор пароля',
            'city_id' => 'Город',
            'birthdate' => 'Birthdate',
            'photo' => 'Photo',
            'phone' => 'Phone',
            'telegram' => 'Telegram',
            'self_description' => 'Self Description',
            'role_id' => 'Role ID',
            'fails_count' => 'Fails Count',
            'date_registered' => 'Date Registered',
        ];
    }

    /**
     * Gets query for [[Categories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])->viaTable('chosen_category', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[ChosenCategories]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChosenCategories()
    {
        return $this->hasMany(ChosenCategory::class, ['user_id' => 'id']);
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
     * Gets query for [[Responses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponses()
    {
        return $this->hasMany(Response::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::class, ['id' => 'role_id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        if ($this->role_id === 1) {
            return $this->hasMany(Task::class, ['customer_id' => 'id']);
        }

        return $this->hasMany(Task::class, ['contractor_id' => 'id']);
    }

    /**
     * Gets query for [[FailedTasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFailedTasks()
    {
        return $this->getTasks()->where(['task_status_id' => 4]);
    }

    /**
     * Gets query for [[FinishedTasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFinishedTasks()
    {
        return $this->getTasks()->where(['task_status_id' => 5]);
    }
}
