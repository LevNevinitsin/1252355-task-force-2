<?php
use LevNevinitsin\Business\Task;
require_once __DIR__ . '/vendor/autoload.php';

use LevNevinitsin\Business\Action\CancelAction;
use LevNevinitsin\Business\Action\RespondAction;
use LevNevinitsin\Business\Action\StartAction;
use LevNevinitsin\Business\Action\DeclineAction;
use LevNevinitsin\Business\Action\CompleteAction;

$actionCancel   = new CancelAction();
$actionRespond  = new RespondAction();
$actionStart    = new StartAction();
$actionDecline  = new DeclineAction();
$actionComplete = new CompleteAction();

$task = new Task(1, 2);

echo "<b>Задание в статусе \"Новое\".</b><br><br>";
echo "Действия для заказчика:<br><br>";
var_dump($task->getAvailableActions(1));
echo "<br><br>Действия для других пользователей:<br><br>";
var_dump($task->getAvailableActions(3));

assert($task->getNextStatus($actionCancel) === Task::STATUS_CANCELLED, 'cancel task');
assert($task->getNextStatus($actionRespond) === Task::STATUS_NEW, 'respond to task');
assert($task->getNextStatus($actionStart) === Task::STATUS_AT_WORK, 'start task');

$task->takeAction($actionCancel);

echo "<br><br><br>";
echo "<b>Задание в статусе \"Отменено\".</b><br><br>";
echo "Действия для заказчика:<br><br>";
var_dump($task->getAvailableActions(1));
echo "<br><br>Действия для исполнителя:<br><br>";
var_dump($task->getAvailableActions(2));

$task->takeAction($actionStart);

echo "<br><br><br>";
echo "<b>Задание в статусе \"В работе\".</b><br><br>";
echo "Действия для заказчика:<br><br>";
var_dump($task->getAvailableActions(1));
echo "<br><br>Действия для исполнителя:<br><br>";
var_dump($task->getAvailableActions(2));
echo "<br><br>Действия для других пользователей:<br><br>";
var_dump($task->getAvailableActions(3));

assert($task->getNextStatus($actionDecline) === Task::STATUS_FAILED, 'decline task');
assert($task->getNextStatus($actionComplete) === Task::STATUS_DONE, 'confirm task');

