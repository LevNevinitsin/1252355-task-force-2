<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use yii\helpers\StringHelper;
use yii\helpers\Url;
?>

<div class="left-column">
    <h3 class="head-main head-task">Новые задания</h3>
    <ul class="tasks-list">
        <?php foreach ($newTasks as $newTask): ?>
        <li class="task-card">
            <?php $taskUrl = Url::to(['/tasks/view', 'id' => $newTask->id]) ?>
            <div class="header-task">
                <a  href="<?= $taskUrl ?>" class="link link--block link--big"><?= Html::encode($newTask->overview) ?></a>
                <p class="price price--task">
                    <?= Yii::$app->formatter->asCurrency($newTask->budget, 'RUB', [NumberFormatter::MAX_FRACTION_DIGITS => 0]) ?>
                </p>
            </div>
            <p class="info-text">
                <?= StringHelper::mb_ucfirst(Yii::$app->formatter->asRelativeTime($newTask->date_created)) ?>
            </p>
            <p class="task-text"><?= Html::encode($newTask->description) ?></p>
            <div class="footer-task">
                <p class="info-text town-text"><?= $newTask->city->name ?? 'Удалённая работа' ?></p>
                <p class="info-text category-text"><?= $newTask->category->name ?></p>
                <a href="<?= $taskUrl ?>" class="button button--black">Смотреть&nbsp;Задание</a>
            </div>
        </li>
        <?php endforeach ?>
    </ul>
    <?= LinkPager::widget([
        'pagination' => $pagination,
        'options' => ['class' => 'pagination-list'],
        'prevPageLabel' => '',
        'prevPageCssClass' => 'pagination-item mark',
        'nextPageLabel' => '',
        'nextPageCssClass' => 'pagination-item mark',
        'pageCssClass' => 'pagination-item',
        'activePageCssClass' => 'pagination-item--active',
        'linkOptions' => ['class' => 'link link--page'],
    ]) ?>
</div>
<div class="right-column">
    <div class="right-card black">
        <?php
        $form = ActiveForm::begin([
            'id' => 'tasks-filter-form',
            'action' => '/tasks',
            'method' => 'get',
            'options' => ['class' => 'search-form'],
        ]); ?>

            <div class="fields-container">
                <fieldset class="fieldset">
                    <h4 class="head-card">Категории</h4>
                    <?= $form->field($categoryModel, 'id', [
                        'template' => '{input}',
                        'options' => ['tag' => false],
                    ])->checkBoxList($categories, [
                        'tag' => false,
                        'item' => function($index, $label, $name, $checked, $value) use ($selectedCategories) {
                            $checkedStatus = in_array($value, $selectedCategories) ? ' checked' : '';

                            return "<label><input class=\"checkbox\" type=\"checkbox\" name=\"$name\" value=\"$value\"$checkedStatus> "
                            . Html::encode($label)
                            . "</label>";
                        },
                        'unselect' => null,

                    ]) ?>
                </fieldset>

                <fieldset class="fieldset">
                    <h4 class="head-card">Дополнительно</h4>
                    <?= $form->field($taskModel, 'city_id', [
                        'template' => '{input}{label}',
                        'options' => ['tag' => false],
                    ])->checkbox([
                        'uncheck' => null,
                        'label' => 'Удалённая работа',
                        'name' => 'showRemoteOnly',
                        'value' => 1,
                        'checked' => $shouldShowRemoteOnly ? '' : null,
                        'class' => 'checkbox',
                    ]) ?>
                    <?= $form->field($taskModel, 'id', [
                        'template' => '{input}{label}',
                        'options' => ['tag' => false],
                    ])->checkbox([
                        'uncheck' => null,
                        'label' => 'Без откликов',
                        'name' => 'showWithoutResponses',
                        'value' => 1,
                        'checked' => $shouldShowWithoutResponses ? '' : null,
                        'class' => 'checkbox',
                    ]) ?>
                </fieldset>

                <fieldset class="fieldset">
                    <h4 class="head-card">Период</h4>
                    <?= $form->field($taskModel, 'date_created', ['template' => '{input}'])->dropDownList(
                        [
                            '01:00:00' => '1 час',
                            '12:00:00' => '12 часов',
                            '24:00:00' => '24 часа',
                        ],
                        [
                            'prompt' => 'Выберите период',
                            'options' => [
                                $selectedPeriod => ['selected' => ''],
                            ],
                        ]
                    ) ?>
                </fieldset>
            </div>

            <?= HTML::submitButton('Искать', ['class' => 'button button--blue']); ?>
        <?php ActiveForm::end() ?>
    </div>
</div>
