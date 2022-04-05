<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="center-block">
    <div class="registration-form regular-form">
        <?php $form = ActiveForm::begin(['action' => '/registration', 'method' => 'post']) ?>
            <h3 class="head-main head-task">Регистрация нового пользователя</h3>
            <?= $form->field($userModel, 'name')->textInput() ?>
            <div class="half-wrapper">
                <?= $form->field($userModel, 'email')->textInput() ?>
                <?= $form->field($userModel, 'city_id')->dropDownList($cities) ?>
            </div>
            <?= $form->field($userModel, 'password')->passwordInput() ?>
            <?= $form->field($userModel, 'password_repeat')->passwordInput() ?>

            <?= $form->field($userModel, 'role_id')->checkbox([
                'uncheck' => 1,
                'value' => 2,
                'label' => 'я собираюсь откликаться на заказы',
                'labelOptions' => ['class' => 'control-label'],
                'checked' => '',
            ]) ?>

            <?= HTML::submitButton('Создать аккаунт', ['class' => 'button button--blue']); ?>
        <?php ActiveForm::end() ?>
    </div>
</div>
