<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<div class="left-menu left-menu--edit">
    <h3 class="head-main head-task">Настройки</h3>
    <ul class="side-menu-list">
        <li class="side-menu-item side-menu-item--active">
            <a class="link link--nav">Мой профиль</a>
        </li>
        <li class="side-menu-item">
            <a href="/edit-profile/security" class="link link--nav">Безопасность</a>
        </li>
    </ul>
</div>
<div class="my-profile-form">
    <?php $form = ActiveForm::begin(['id' => 'edit-profile-form', 'action' => '/edit-profile', 'method' => 'post']) ?>
        <h3 class="head-main head-regular">Мой профиль</h3>
        <div class="photo-editing">
            <div class="avatar-container">
                <p class="form-label avatar-caption">Аватар</p>
                <?php $userPhoto = Html::encode($user->photo) ?>
                <?= Yii::$app->formatter->asImage($userPhoto ?? '', [
                    'class' => 'avatar-preview ' . ($userPhoto ? '' : 'avatar-preview--hidden'),
                    'width' => 83,
                    'height' => 83,
                    'alt' => 'Аватар'
                ]) ?>
                <?php if (!$userPhoto): ?>
                <div class="avatar-preview avatar-preview--absent js-avatar-preview-absent">Аватар не выбран</div>
                <?php endif ?>
            </div>
            <?= $form->field($model, 'avatar')
                ->fileInput(['class' => 'js-avatar-input', 'hidden' => ''])
                ->label('Сменить аватар', ['class' => 'button button--black'])
            ?>
        </div>

        <?= $form->field($model, 'name')->textInput() ?>

        <div class="half-wrapper">
            <?= $form->field($model, 'email')->textInput(['type' => 'email']) ?>
            <?= $form->field($model, 'birthdate')->textInput(['type' => 'date']) ?>
        </div>
        <div class="half-wrapper">
            <?= $form->field($model, 'phone')->textInput(['type' => 'tel']) ?>
            <?= $form->field($model, 'telegram')->textInput() ?>
        </div>

        <?= $form->field($model, 'selfDescription')->textarea() ?>

        <?php if ($user->role_id === 2): ?>
        <p class="control-label">Выбор специализаций</p>

        <?= $form->field($model, 'chosenCategoriesIds', [
            'options' => ['class' => 'categories-list'],
        ])->checkBoxList($categories, [
            'tag' => false,
            'item' => function($index, $label, $name, $checked, $value) use ($selectedCategories) {
                $checkedStatus = in_array($value, $selectedCategories) ? ' checked' : '';

                return "<label><input class=\"checkbox\" type=\"checkbox\" name=\"$name\" value=\"$value\"$checkedStatus> "
                . Html::encode($label)
                . "</label>";
            },
            'unselect' => null,
        ])->label(false) ?>

        <?php endif ?>

        <?= HTML::submitButton('Сохранить', ['class' => 'button button--blue button--submit']); ?>
    <?php ActiveForm::end() ?>
</div>
