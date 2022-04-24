<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="left-menu left-menu--edit">
    <h3 class="head-main head-task">Настройки</h3>
    <ul class="side-menu-list">
        <li class="side-menu-item">
            <a href="<?= Url::to('/edit-profile') ?>" class="link link--nav">Мой профиль</a>
        </li>
        <li class="side-menu-item side-menu-item--active">
            <a class="link link--nav">Безопасность</a>
        </li>
    </ul>
</div>
<div class="my-profile-form">
    <?php $form = ActiveForm::begin([
        'id' => 'security-form',
        'action' => '/edit-profile/security',
        'method' => 'post',
        'enableAjaxValidation' => true,
    ]) ?>

        <?= $form->field($model, 'oldPassword')->passwordInput() ?>
        <?= $form->field($model, 'newPassword')->passwordInput() ?>
        <?= $form->field($model, 'newPasswordRepeat')->passwordInput() ?>

        <?php if ($user->role_id === 2): ?>
        <?= $form->field($model, 'hideContacts')->checkbox([
                'uncheck' => 0,
                'value' => 1,
                'labelOptions' => ['class' => 'control-label'],
                'checked' => $user->hide_contacts === 1,
        ]) ?>
        <?php endif ?>

        <?= HTML::submitButton('Сохранить', ['class' => 'button button--blue button--submit']); ?>
    <?php ActiveForm::end() ?>
</div>
