<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="add-task-container">
    <div class="add-task-form regular-form">
        <?php $form = ActiveForm::begin(['id' => 'add-task-form', 'action' => '/tasks/add', 'method' => 'post']) ?>
            <h3 class="head-main head-main">Публикация нового задания</h3>
            <?= $form->field($model, 'overview')->textInput() ?>
            <?= $form->field($model, 'description')->textarea() ?>
            <?= $form->field($model, 'category_id')->dropDownList($categories) ?>

            <div class="form-group">
                <label class="control-label" for="location">Локация</label>
                <input id="location" type="text" disabled>
                <div class="help-block"></div>
            </div>

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

