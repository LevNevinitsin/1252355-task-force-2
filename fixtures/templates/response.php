<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use app\models\User;
use app\models\Task;

$task = $faker->randomElement(Task::find()->asArray()->all());
$taskId = $task['id'];
$taskCreationDate = $task['date_created'];

$contractorsIds = User::find()->select(['id'])->where(['role_id' => 2])->column();

return [
    'task_id'      => $taskId,
    'user_id'      => $faker->randomElement($contractorsIds),
    'price'        => $faker->optional($weight = 0.7)->numberBetween(50, 20000),
    'comment'      => $faker->optional($weight = 0.7)->sentence(),
    'date_created' => $faker->dateTimeBetween($taskCreationDate)->format('Y-m-d H:i:s'),
];
