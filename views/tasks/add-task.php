<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->registerCssFile('/css/autocomplete-js/autoComplete.02.css');
$this->registerJsFile('/js/autoComplete.min.js');
$this->registerJsFile('/js/location-autocomplete.js');
?>
<div class="add-task-container">
    <div class="add-task-form regular-form">
        <?php $form = ActiveForm::begin(['id' => 'add-task-form', 'action' => '/tasks/add', 'method' => 'post']) ?>
            <h3 class="head-main head-main">Публикация нового задания</h3>
            <?= $form->field($model, 'overview')->textInput() ?>
            <?= $form->field($model, 'description')->textarea() ?>
            <?= $form->field($model, 'category_id')->dropDownList($categories) ?>

            <?= $form->field($model, 'location', ['inputOptions' => [
                'data-library' => 'autoComplete',
                'data-user-city-name' => $userCity->name,
                'data-user-city-coordinates' => $userCity->longitude . ', ' . $userCity->latitude,
            ]])->textInput() ?>

            <?= $form->field($model, 'latitude', ['inputOptions' => ['class' => 'js-latitude']])
                ->hiddenInput()->label(false)->error(false) ?>
            <?= $form->field($model, 'longitude', ['inputOptions' => ['class' => 'js-longitude']])
                ->hiddenInput()->label(false)->error(false) ?>
            <?= $form->field($model, 'cityName', ['inputOptions' => ['class' => 'js-cityName']])
                ->hiddenInput()->label(false)->error(false) ?>

            <div class="half-wrapper">
                <?= $form->field($model, 'budget')->textInput(['type' => 'number']) ?>
                <?= $form->field($model, 'deadline', ['enableAjaxValidation' => true])->textInput(['type' => 'date']) ?>
            </div>

            <div class="add-files-container">
                <p class="form-label">Файлы</p>
                <div id="dropzone" class="dropzone new-file">
                    <p class="add-file dz-message">Добавить новый файл</p>
                </div>
                <div class="help-block add-files-error">
                    <?php if($areFilesValid === false): ?>
                    Что-то не так с файлами (вероятнее всего, неправильный формат).
                    <?php endif ?>
                </div>
            </div>

            <?= HTML::submitButton('Опубликовать', ['class' => 'button button--blue']); ?>
        <?php $form = ActiveForm::end() ?>
    </div>
</div>


