<?php
use yii\helpers\StringHelper;
use yii\helpers\Url;
use LevNevinitsin\Business\Service\UserService;
?>

<div class="left-column">
    <div class="head-wrapper">
        <h3 class="head-main"><?= $task->overview ?></h3>
        <p class="price price--big">
            <?= Yii::$app->formatter->asCurrency($task->budget, 'RUB', [NumberFormatter::MAX_FRACTION_DIGITS => 0]) ?>
        </p>
    </div>
    <p class="task-description"><?= $task->description ?></p>
    <a href="#" class="button button--blue">Откликнуться на задание</a>
    <div class="task-map">
        <img class="map" src="/img/map.png"  width="725" height="346" alt="Новый арбат, 23, к. 1">
        <p class="map-address town">Москва</p>
        <p class="map-address">Новый арбат, 23, к. 1</p>
    </div>
    <h4 class="head-regular">Отклики на задание</h4>
    <?php foreach ($task->responses as $response): ?>
    <div class="response-card response-card--task">
        <?php $responseUser = $response->user ?>
        <?php if ($responseUserPhoto = $responseUser->photo): ?>
        <img class="customer-photo" src="<?= $responseUserPhoto ?>" width="146" height="156" alt="Фото заказчика">
        <?php else: ?>
        <div class="customer-photo customer-photo--absent">Фото отсутствует</div>
        <?php endif ?>
        <div class="feedback-wrapper">
            <a href="<?= Url::to(['/users/view', 'id' => $responseUser->id]) ?>" class="link link--block link--big"><?= $responseUser->name ?></a>
            <div class="response-wrapper">
                <div class="stars-rating small">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <?php if ($i <= round(UserService::getRating($responseUser))): ?>
                        <span class="fill-star">&nbsp;</span>
                        <?php else: ?>
                        <span class="nofill-star">&nbsp;</span>
                        <?php endif ?>
                    <?php endfor ?>
                </div>
                <p class="reviews">
                    <?= Yii::$app->i18n->messageFormatter->format(
                        '{n, plural, =0{Отзывов нет} one{# отзыв} few{# отзыва} many{# отзывов} other{# отзывов}}',
                        ['n' => count($responseUser->responses)],
                        \Yii::$app->language
                    )?>
                </p>
            </div>
            <p class="response-message"><?= $response->comment ?></p>
        </div>
        <div class="feedback-wrapper">
            <p class="info-text"><span class="current-time"><?= StringHelper::mb_ucfirst(Yii::$app->formatter->asRelativeTime($response->date_created)) ?></span></p>
            <p class="price price--small">
                <?= Yii::$app->formatter->asCurrency($response->price, 'RUB', [NumberFormatter::MAX_FRACTION_DIGITS => 0]) ?>
            </p>
        </div>
        <div class="button-popup">
            <a href="#" class="button button--blue button--small">Принять</a>
            <a href="#" class="button button--orange button--small">Отказать</a>
        </div>
    </div>
    <?php endforeach ?>
</div>
<div class="right-column">
    <div class="right-card black info-card">
        <h4 class="head-card">Информация о задании</h4>
        <dl class="black-list">
            <dt>Категория</dt>
            <dd><a class="black-list-link" href="<?= Url::to(['/tasks', 'Category[id][]' => $task->category->id]) ?>"><?= $task->category->name ?></a></dd>
            <dt>Дата публикации</dt>
            <dd><?= StringHelper::mb_ucfirst(Yii::$app->formatter->asRelativeTime($task->date_created)) ?></dd>
            <?php if ($taskDeadline = $task->deadline): ?>
            <dt>Срок выполнения</dt>
            <dd><?= Yii::$app->formatter->asDate($taskDeadline, 'php: j F, H:i') ?></dd>
            <?php endif ?>
            <dt>Статус</dt>
            <dd><?= $task->taskStatus->name ?></dd>
        </dl>
    </div>
    <div class="right-card white file-card">
        <h4 class="head-card">Файлы задания</h4>
        <ul class="enumeration-list">
            <li class="enumeration-item">
                <a href="#" class="link link--block link--clip">my_picture.jpg</a>
                <p class="file-size">356 Кб</p>
            </li>
            <li class="enumeration-item">
                <a href="#" class="link link--block link--clip">information.docx</a>
                <p class="file-size">12 Кб</p>
            </li>
        </ul>
    </div>
</div>
