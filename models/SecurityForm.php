<?php
namespace app\models;

use Yii;
use yii\base\Model;

class SecurityForm extends Model
{
    const NEW_PASSWORD_CLIENT_VALIDATION_CONDITION = "function (attribute, value) {
        return Boolean($('#securityform-oldpassword').val())
            && !$('.field-securityform-oldpassword').hasClass('has-error');
    }";

    public $oldPassword;
    public $newPassword;
    public $newPasswordRepeat;
    public $hideContacts;

    private $_isOldPasswordValidated;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['oldPassword', 'required', 'when' => function ($model) {
                return !empty($model->newPassword);
            }, 'whenClient' => "function (attribute, value) {
                return Boolean($('#securityform-newpassword').val());
            }", 'message' => 'Для установки нового пароля нужно ввести старый пароль.'],

            ['oldPassword', 'validatePassword'],

            [['newPassword', 'newPasswordRepeat'], 'required', 'when' => function ($model) {
                return $this->isOldPasswordConfirmed($model);
            }, 'whenClient' => self::NEW_PASSWORD_CLIENT_VALIDATION_CONDITION],

            [['newPassword', 'newPasswordRepeat'], 'string', 'min' => 8, 'when' => function ($model) {
                return $this->isOldPasswordConfirmed($model);
            }, 'whenClient' => self::NEW_PASSWORD_CLIENT_VALIDATION_CONDITION],

            ['newPasswordRepeat', 'compare', 'compareAttribute' => 'newPassword', 'when' => function ($model) {
                return $this->isOldPasswordConfirmed($model);
            }, 'whenClient' => self::NEW_PASSWORD_CLIENT_VALIDATION_CONDITION],

            ['hideContacts', 'boolean'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'oldPassword' => 'Старый пароль',
            'newPassword' => 'Новый пароль',
            'newPasswordRepeat' => 'Повторите новый пароль',
            'hideContacts' => 'Показывать контактные данные только заказчикам',
        ];
    }

    /**
     * Validates the password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array|null $params the additional name-value pairs given in the rule
     * @return void
     */
    public function validatePassword(string $attribute, ?array $params)
    {
        $user = User::findOne(Yii::$app->user->id);

        if (!$user->validatePassword($this->oldPassword)) {
            $this->addError($attribute, 'Неверно указан старый пароль');
            return;
        }

        $this->_isOldPasswordValidated = true;
    }

    /**
     * Checks if old password value is not empty and is validated
     *
     * @param Model $model the model being validated
     * @return boolean "true" if old password value is not empty and is validated, "false" otherwise
     */
    private function isOldPasswordConfirmed(Model $model)
    {
        return !empty($model->oldPassword) && $this->_isOldPasswordValidated;
    }
}
