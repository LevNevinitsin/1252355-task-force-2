<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\authclient\widgets\AuthChoice;
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
    <div class="auth-block auth-block--registration">
        <?php $authAuthChoice = AuthChoice::begin([
            'baseAuthUrl' => ['external-auth/customer-auth-registration'],
            'popupMode' => true,
            'options' => ['class' => 'auth-container'],
        ]); ?>
        <ul class="auth-list">
        <?php foreach ($authAuthChoice->getClients() as $client): ?>
            <?php $clientTitle = $client->getTitle() ?>
            <li>
                <?= $authAuthChoice->clientLink(
                    $client,
                    "Вход через $clientTitle (заказчик)",
                    ['class' => "button button--auth button--$clientTitle"]
                ) ?>
            </li>
        <?php endforeach; ?>
        </ul>
        <?php AuthChoice::end(); ?>

        <?php $authAuthChoice = AuthChoice::begin([
            'baseAuthUrl' => ['external-auth/contractor-auth-registration'],
            'popupMode' => true,
            'options' => ['class' => 'auth-container'],
        ]); ?>
        <ul class="auth-list">
        <?php foreach ($authAuthChoice->getClients() as $client): ?>
            <?php $clientTitle = $client->getTitle() ?>
            <li>
                <?= $authAuthChoice->clientLink(
                    $client,
                    "Вход через $clientTitle (исполнитель)",
                    ['class' => "button button--auth button--$clientTitle"]
                ) ?>
            </li>
        <?php endforeach; ?>
        </ul>
        <?php AuthChoice::end(); ?>
    </div>
    <?php if ($authError = Yii::$app->getSession()->getFlash('authError')): ?>
    <div class="auth-error auth-error--registration"><?= Html::encode($authError) ?></div>
    <?php endif ?>
</div>
