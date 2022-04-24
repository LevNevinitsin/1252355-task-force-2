<?php
use yii\helpers\StringHelper;
use yii\helpers\Url;
use LevNevinitsin\Business\Service\UserService;
use yii\helpers\Html;

?>

<div class="left-column">
    <h3 class="head-main"><?= Html::encode($user->name) ?></h3>
    <div class="user-card">
        <div class="photo-rate">
            <?php if ($userPhoto = Html::encode($user->photo)): ?>
            <img class="card-photo" src="<?= $userPhoto ?>" width="191" height="190" alt="Фото пользователя">
            <?php else: ?>
            <div class="card-photo card-photo--absent">Фото отсутствует</div>
            <?php endif ?>
            <div class="card-rate">
                <?php
                    $userRating = UserService::getRating($user);
                    $roundedUserRating = round($userRating);
                 ?>
                <div class="stars-rating big">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <?php if ($i <= $roundedUserRating): ?>
                        <span class="fill-star">&nbsp;</span>
                        <?php else: ?>
                        <span>&nbsp;</span>
                        <?php endif ?>
                    <?php endfor ?>
                </div>
                <span class="current-rate"><?= Yii::$app->formatter->asDecimal($userRating, 2) ?></span>
            </div>
        </div>
        <?php if ($userSelfDescription = Html::encode($user->self_description)): ?>
        <p class="user-description"><?= $userSelfDescription ?></p>
        <?php endif ?>
    </div>
    <div class="specialization-bio">
        <div class="specialization">
            <p class="head-info">Специализации</p>
            <ul class="special-list">
                <?php foreach($user->categories as $category): ?>
                <li class="special-item">
                    <a href="<?= Url::to(['/tasks', 'Category[id][]' => $category->id]) ?>" class="link link--regular"><?= $category->name ?></a>
                </li>
                <?php endforeach ?>
            </ul>
        </div>
        <div class="bio">
            <p class="head-info">Био</p>
            <p class="bio-info">
                <span class="country-info">Россия</span>, <span class="town-info"><?= $user->city->name ?></span>
                <?php if ($userBirthdate = $user->birthdate): ?>
                <?= ', ' . Yii::$app->i18n->messageFormatter->format(
                    '{n, plural, one{# год} few{# года} many{# лет} other{# лет}}',
                    ['n' => UserService::getAge($userBirthdate)],
                    \Yii::$app->language
                )?>
                <?php endif ?>
            </p>
        </div>
    </div>
    <h4 class="head-regular">Отзывы заказчиков</h4>
    <?php $finishedTasks = $user->finishedTasks ?>
    <?php if ($finishedTasks): ?>
        <?php foreach ($finishedTasks as $finishedTask): ?>
        <div class="response-card response-card--user-profile">
            <?php if ($customerPhoto = $finishedTask->customer->photo): ?>
            <img class="customer-photo" src="<?= $customerPhoto ?>" width="120" height="127" alt="Фото заказчика">
            <?php else: ?>
            <div class="customer-photo customer-photo--absent customer-photo--user-profile">Фото отсутствует</div>
            <?php endif ?>
            <div class="feedback-wrapper">
                <p class="feedback">«<?= Html::encode($finishedTask->feedback) ?>»</p>
                <p class="task">Задание «<a href="<?= Url::to(['/tasks/view', 'id' => $finishedTask->id]) ?>" class="link link--small"><?= Html::encode($finishedTask->overview) ?></a>» выполнено</p>
            </div>
            <div class="feedback-wrapper">
                <div class="stars-rating small">
                    <?php $taskScore = $finishedTask->score ?>
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <?php if ($i <= $taskScore): ?>
                        <span class="fill-star">&nbsp;</span>
                        <?php else: ?>
                        <span class="nofill-star">&nbsp;</span>
                        <?php endif ?>
                    <?php endfor ?>
                </div>
                <p class="info-text">
                    <?= StringHelper::mb_ucfirst(Yii::$app->formatter->asRelativeTime($finishedTask->date_updated)) ?>
                </p>
            </div>
        </div>
        <?php endforeach ?>
    <?php else: ?>
        <div>Отзывов пока нет.</div>
    <?php endif ?>
</div>
<div class="right-column">
    <div class="right-card black">
        <h4 class="head-card">Статистика исполнителя</h4>
        <dl class="black-list">
            <dt>Всего заказов</dt>
            <dd><?= count($finishedTasks) ?> выполнено, <?= count($user->failedTasks) ?> провалено</dd>
            <dt>Место в рейтинге</dt>
            <dd><?= UserService::getRank($user) ?> место</dd>
            <dt>Дата регистрации</dt>
            <dd><?= Yii::$app->formatter->asDate($user->date_registered, 'php: j F, H:i') ?></dd>
            <dt>Статус</dt>
            <dd><?= UserService::getStatus($user) ?></dd>
        </dl>
    </div>
    <?php if ($currentUser->role_id === 1 || $currentUser->id === $user->id || !$user->hide_contacts): ?>
    <div class="right-card white">
        <h4 class="head-card">Контакты</h4>
        <ul class="enumeration-list">
            <?php if ($userPhone = Html::encode($user->phone)): ?>
            <li class="enumeration-item">
                <a href="<?= Url::to("tel:+$userPhone") ?>" class="link link--block link--phone">
                    <?= Yii::$app->formatter->asPhone($userPhone) ?>
                </a>
            </li>
            <?php endif ?>
            <li class="enumeration-item">
                <?= Yii::$app->formatter->asEmail($user->email, ['class' => 'link link--block link--email']) ?>
            </li>
            <?php if ($userTelegram = Html::encode($user->telegram)): ?>
            <li class="enumeration-item">
                <a href="<?= Url::to("https://t.me/$userTelegram") ?>" class="link link--block link--tg">@<?= $userTelegram ?></a>
            </li>
            <?php endif ?>
        </ul>
    </div>
    <?php endif ?>
</div>
