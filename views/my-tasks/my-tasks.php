<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;

$currentPage = Yii::$app->request->getPathInfo();
?>
<div class="left-menu">
    <h3 class="head-main head-task">Мои задания</h3>
    <ul class="side-menu-list">
        <?php foreach ($navigationLinks as $linkUrl => $linkTitle): ?>
        <?php $isCurrentPage = "/$currentPage" === $linkUrl ?>
        <li class="side-menu-item <?= $isCurrentPage ? 'side-menu-item--active' : '' ?>">
            <a <?= $isCurrentPage ? '' : "href=\"$linkUrl\"" ?> class="link link--nav"><?= $linkTitle ?></a>
        </li>
        <?php endforeach ?>
    </ul>
</div>
<div class="left-column left-column--task">
    <h3 class="head-main head-regular"><?= $title ?></h3>
    <?php foreach ($tasks as $task): ?>
    <?php $taskUrl = Url::to(['/tasks/view', 'id' => $task->id]) ?>
    <div class="task-card">
        <div class="header-task">
            <a  href="<?= $taskUrl ?>" class="link link--block link--big"><?= Html::encode($task->overview) ?></a>
            <p class="price price--task">
                <?= Yii::$app->formatter->asCurrency($task->budget, 'RUB', [NumberFormatter::MAX_FRACTION_DIGITS => 0]) ?>
            </p>
        </div>
        <p class="info-text">
            <?= StringHelper::mb_ucfirst(Yii::$app->formatter->asRelativeTime($task->date_created)) ?>
        </p>
        <p class="task-text"><?= Html::encode($task->description) ?></p>
        <div class="footer-task">
            <p class="info-text town-text"><?= $task->city->name ?? 'Удалённая работа' ?></p>
            <p class="info-text category-text"><?= $task->category->name ?></p>
            <a href="<?= $taskUrl ?>" class="button button--black">Смотреть&nbsp;Задание</a>
        </div>
    </div>
    <?php endforeach ?>
</div>
