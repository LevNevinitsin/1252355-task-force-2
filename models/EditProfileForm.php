<?php
namespace app\models;

use Yii;
use yii\base\Model;

class EditProfileForm extends Model
{
    public $avatar;
    public $name;
    public $email;
    public $birthdate;
    public $phone;
    public $telegram;
    public $self_description;
    public $chosenCategoriesIds;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['avatar', 'image', 'extensions' => ['gif', 'jpg', 'jpeg', 'png']],
            [['name', 'email'], 'required'],
            ['birthdate', 'date', 'format' => 'php:Y-m-d'],
            ['phone', 'match', 'pattern' => '/^7\d{10}$/', 'message' => 'Введите номер в формате 79991234567'],
            ['telegram', 'string', 'max' => 64],
            ['self_description', 'string'],

            ['chosenCategoriesIds', function ($attribute, $params, $validator, $current) {
                if (Yii::$app->user->identity->role_id === 1) {
                    $this->addError($attribute, 'Заказчик не может выбирать специализации.');
                }
            }],

            ['chosenCategoriesIds', 'each', 'rule' => ['integer'], 'skipOnError' => true],

            ['chosenCategoriesIds', 'filter', 'filter' => function ($chosenCategoriesIds) {
                return gettype($chosenCategoriesIds) === 'array'
                    ? array_unique($chosenCategoriesIds)
                    : $chosenCategoriesIds;
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            ['chosenCategoriesIds', 'each', 'rule' => [
                'exist', 'targetClass' => Category::class, 'targetAttribute' => 'id'
            ], 'skipOnError' => true],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'avatar' => 'Аватар',
            'name' => 'Ваше имя',
            'email' => 'Email',
            'birthdate' => 'День рождения',
            'phone' => 'Номер телефона',
            'telegram' => 'Telegram',
            'self_description' => 'Информация о себе',
            'chosenCategoriesIds' => 'Выбор специализаций',
        ];
    }
}
