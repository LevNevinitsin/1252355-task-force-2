<?php
declare(strict_types=1);

use LevNevinitsin\Business\Task;
require_once __DIR__ . '/vendor/autoload.php';

use LevNevinitsin\Business\Action\CancelAction;
use LevNevinitsin\Business\Action\RespondAction;
use LevNevinitsin\Business\Action\StartAction;
use LevNevinitsin\Business\Action\DeclineAction;
use LevNevinitsin\Business\Action\CompleteAction;
use LevNevinitsin\Business\Exception\TaskException;

$actionCancel   = new CancelAction();
$actionRespond  = new RespondAction();
$actionStart    = new StartAction();
$actionDecline  = new DeclineAction();
$actionComplete = new CompleteAction();

error_reporting(E_ALL);
ini_set('display_errors', 'true');
ini_set('log_errors', 'false');

try {
    $task = new Task('new', 1, 2);
} catch (TaskException $e) {
    echo "Не удалось создать экземпляр задания: {$e->getMessage()}";
    exit;
}

echo "<b>Задание в статусе \"Новое\".</b><br><br>";
echo "Действия для заказчика задания:<br><br>";
try {
    var_dump($task->getAvailableActions(1, 'customer'));
} catch (TaskException $e) {
    echo "Не удалось получить возможные действия: {$e->getMessage()}";
}

echo "<br><br>Действия для других пользователей-заказчиков:<br><br>";
try {
    var_dump($task->getAvailableActions(3, 'customer'));
} catch (TaskException $e) {
    echo "Не удалось получить возможные действия: {$e->getMessage()}";
}

echo "<br><br>Действия для других пользователей-исполнителей:<br><br>";
try {
    var_dump($task->getAvailableActions(4, 'contractor'));
} catch (TaskException $e) {
    echo "Не удалось получить возможные действия: {$e->getMessage()}";
}

assert($task->getNextStatus($actionCancel) === Task::STATUS_CANCELLED, 'cancel task');
assert($task->getNextStatus($actionRespond) === Task::STATUS_NEW, 'respond to task');
assert($task->getNextStatus($actionStart) === Task::STATUS_AT_WORK, 'start task');

$task->takeAction($actionCancel);

echo "<br><br><br>";
echo "<b>Задание в статусе \"Отменено\".</b><br><br>";
echo "Действия для заказчика:<br><br>";
try {
    var_dump($task->getAvailableActions(1, 'customer'));
} catch (TaskException $e) {
    echo "Не удалось получить возможные действия: {$e->getMessage()}";
}

echo "<br><br>Действия для исполнителя:<br><br>";
try {
    var_dump($task->getAvailableActions(2, 'contractor'));
} catch (TaskException $e) {
    echo "Не удалось получить возможные действия: {$e->getMessage()}";
}

$task->takeAction($actionStart);

echo "<br><br><br>";
echo "<b>Задание в статусе \"В работе\".</b><br><br>";
echo "Действия для заказчика:<br><br>";
try {
    var_dump($task->getAvailableActions(1, 'customer'));
} catch (TaskException $e) {
    echo "Не удалось получить возможные действия: {$e->getMessage()}";
}

echo "<br><br>Действия для исполнителя:<br><br>";
try {
    var_dump($task->getAvailableActions(2, 'contractor'));
} catch (TaskException $e) {
    echo "Не удалось получить возможные действия: {$e->getMessage()}";
}

echo "<br><br>Действия для других пользователей-заказчиков:<br><br>";
try {
    var_dump($task->getAvailableActions(3, 'customer'));
} catch (TaskException $e) {
    echo "Не удалось получить возможные действия: {$e->getMessage()}";
}

echo "<br><br>Действия для других пользователей-исполнителей:<br><br>";
try {
    var_dump($task->getAvailableActions(3, 'contractor'));
} catch (TaskException $e) {
    echo "Не удалось получить возможные действия: {$e->getMessage()}";
}

assert($task->getNextStatus($actionDecline) === Task::STATUS_FAILED, 'decline task');
assert($task->getNextStatus($actionComplete) === Task::STATUS_DONE, 'confirm task');

