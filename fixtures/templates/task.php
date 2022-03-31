<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use app\models\User;

$taskStatusId = $faker->numberBetween(1, 5);

$customersIds = User::find()->select(['id'])->where(['role_id' => 1])->column();
$contractorsIds = User::find()->select(['id'])->where(['role_id' => 2])->column();

$dateCreated = $faker->dateTimeBetween('-1 day')->format('Y-m-d H:i:s');

return [
    'overview'       => substr($faker->sentence(3), 0, -1),
    'description'    => $faker->paragraph(),
    'category_id'    => $faker->numberBetween(1, 8),
    'city_id'        => $faker->optional()->numberBetween(1, 1087),
    'budget'         => $faker->optional()->numberBetween(50, 20000),

    'deadline'       => $faker
        ->optional($weight = 0.75)
        ->passthrough($faker->dateTimeBetween('+1 day', '+10 days')->format('Y-m-d H:i:s')),

    'task_status_id' => $taskStatusId,
    'customer_id'    => $faker->randomElement($customersIds),
    'contractor_id'  => $taskStatusId !== 1 && $taskStatusId !== 2 ? $faker->randomElement($contractorsIds) : NULL,
    'score'          => $taskStatusId === 5 ? $faker->numberBetween(1, 5) : NULL,
    'feedback'       => $taskStatusId === 5 ? $faker->paragraph() : NULL,
    'date_updated'   => $taskStatusId !== 1 ? $faker->dateTimeBetween($dateCreated)->format('Y-m-d H:i:s') : $dateCreated,
    'date_created'   => $dateCreated,
];
