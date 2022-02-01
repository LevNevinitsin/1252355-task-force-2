<?php
use LevNevinitsin\Business\Task;
require_once __DIR__ . '/vendor/autoload.php';

$task = new Task(1, 1);

echo($task->getAvailableActions());

assert($task->getNextStatus('cancel') === Task::STATUS_CANCELLED, 'cancel task');
assert($task->getNextStatus('respond') === Task::STATUS_AT_WORK, 'respond to task');

$task->takeAction('respond');
echo("<br><br>" . $task->getAvailableActions());

assert($task->getNextStatus('confirm') === Task::STATUS_DONE, 'confirm task');
assert($task->getNextStatus('decline') === Task::STATUS_FAILED, 'decline task');
