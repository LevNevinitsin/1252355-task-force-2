<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\assets\MainAsset;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

AppAsset::register($this);
MainAsset::register($this);

$currentPage = Yii::$app->request->getPathInfo();
$userIdentity = Yii::$app->user->identity;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
<?php $this->beginBody() ?>

<header class="page-header">
    <nav class="main-nav">
        <a <?= $currentPage === 'tasks' ? '' : 'href="/tasks"' ?> class="header-logo">
            <img class="logo-image" src="/img/logotype.png" width=227 height=60 alt="taskforce">
        </a>
        <?php $isRegistrationPage = $currentPage === 'registration' ?>
        <?php if (!$isRegistrationPage): ?>
        <div class="nav-wrapper">
            <ul class="nav-list">
                <li class="list-item list-item--active">
                    <a class="link link--nav" >Новое</a>
                </li>
                <li class="list-item">
                    <a href="#" class="link link--nav" >Мои задания</a>
                </li>
                <?php if ($userIdentity->role_id === 1): ?>
                <li class="list-item">
                    <a href="<?= Url::to('/tasks/add') ?>" class="link link--nav" >Создать задание</a>
                </li>
                <?php endif ?>
                <li class="list-item">
                    <a href="#" class="link link--nav" >Настройки</a>
                </li>
            </ul>
        </div>
        <?php endif ?>
    </nav>
    <?php if (!$isRegistrationPage): ?>
    <div class="user-block">
        <a href="#">
            <?= Yii::$app->formatter->asImage(
                $userIdentity->photo,
                ['class' => 'user-photo', 'width' => 55, 'height' => 55, 'alt' => 'Аватар']
            ) ?>
        </a>
        <div class="user-menu">
            <p class="user-name"><?= $userIdentity->name ?></p>
            <div class="popup-head">
                <ul class="popup-menu">
                    <li class="menu-item">
                        <a href="#" class="link">Настройки</a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="link">Связаться с нами</a>
                    </li>
                    <li class="menu-item">
                        <a href="<?= Url::to('/users/logout') ?>" class="link">Выход из системы</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <?php endif ?>
</header>

<main class="main-content container">
    <?= $content ?>
</main>

<?php
$controller = Yii::$app->controller;
$isTaskView = ($controller->id === 'tasks' && $controller->action->id === 'view');

if ($isTaskView) {
    $taskId = Yii::$app->request->get('id');
}
?>
<?php if ($isTaskView): ?>
<section class="pop-up pop-up--act_response">
    <?= Html::button('', ['class' => 'button--close']) ?>
    <h4 class="pop-up__heading">Добавить отклик</h2>
    <?php $form = ActiveForm::begin([
        'id' => 'add-response-form',
        'action' => '/responses/add',
        'method' => 'POST',
        'options' => ['class' => 'pop-up--form'],
        'fieldConfig' => [
            'labelOptions' => ['class' => 'control-label'],
        ],
    ]) ?>
        <?= $form->field($this->params['responseModel'], 'task_id')->hiddenInput(['value'=> $taskId])->label(false)->error(false) ?>
        <?= $form->field($this->params['responseModel'], 'price')->textInput(['type' => 'number']) ?>
        <?= $form->field($this->params['responseModel'], 'comment')->textarea() ?>

        <?= HTML::submitButton('Опубликовать', ['class' => 'button button--blue']); ?>
    <?php ActiveForm::end() ?>
</section>

<section class="pop-up pop-up--refusal">
    <?= Html::button('', ['class' => 'button--close']) ?>
    <h4 class="pop-up__heading">Отказаться от задания</h2>
    <p class="pop-up-text">Вы уверены, что хотите отказаться от задания?</p>
    <a class="button button--blue" href="<?= Url::to(['/tasks/decline', 'id' => $taskId]) ?>">Да</a>
</section>

<section class="pop-up pop-up--completion">
    <?= Html::button('', ['class' => 'button--close']) ?>
    <h4 class="pop-up__heading">Завершить задание</h2>
    <?php $form = ActiveForm::begin([
        'id' => 'complete-task-form',
        'action' => '/tasks/complete',
        'method' => 'POST',
        'options' => ['class' => 'pop-up--form'],
        'fieldConfig' => [
            'labelOptions' => ['class' => 'control-label'],
        ],
    ]) ?>
        <?= $form->field($this->params['taskModel'], 'task_id')->hiddenInput(['value'=> $taskId])->label(false)->error(false) ?>
        <?= $form->field($this->params['taskModel'], 'task_status_id', ['inputOptions' => ['class' => 'task-status-id']])
            ->hiddenInput(['value'=> 5])
            ->label(false)
            ->error(false)
        ?>

        <div class="rating-container">
            <p class="rating-label">Оценка:</p>
            <div class="stars-rating big stars-rating--interactive" data-total-value="">
                <span class="nofill-star rating-item" data-item-value="5">&nbsp;</span>
                <span class="nofill-star rating-item" data-item-value="4">&nbsp;</span>
                <span class="nofill-star rating-item" data-item-value="3">&nbsp;</span>
                <span class="nofill-star rating-item" data-item-value="2">&nbsp;</span>
                <span class="nofill-star rating-item" data-item-value="1">&nbsp;</span>
            </div>
        </div>
        <?= $form->field($this->params['taskModel'], 'score', ['inputOptions' => ['class' => 'rating-input']])
            ->hiddenInput()
            ->label(false)
        ?>

        <?= $form->field($this->params['taskModel'], 'feedback')->textarea() ?>

        <?= HTML::submitButton('Завершить', ['class' => 'button button--blue']); ?>
    <?php ActiveForm::end() ?>
</section>
<?php endif ?>

<div class="overlay"></div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
