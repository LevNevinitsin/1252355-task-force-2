<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
$taskStatusId = random_int(1, 5);

$customersIds   = [1, 2, 3, 4, 5, 6, 7];
$contractorsIds = [8, 9, 10];

return [
    'overview'       => $faker->sentence(),
    'description'    => $faker->paragraph(),
    'category_id'    => random_int(1, 8),
    'city_id'        => random_int(1, 1087),
    'budget'         => random_int(50, 10000),
    'deadline'       => $faker->dateTimeBetween('+1 day', '+10 days')->format('Y-m-d'),
    'task_status_id' => $taskStatusId,
    'customer_id'    => $customersIds[array_rand($customersIds)],
    'contractor_id'  => $taskStatusId !== 1 && $taskStatusId !== 2 ? $contractorsIds[array_rand($contractorsIds)] : NULL,
    'score'          => $taskStatusId === 5 ? random_int(1, 5) : NULL,
    'feedback'       => $taskStatusId === 5 ? $faker->paragraph() : NULL,
    'date_created'   => $faker->dateTimeBetween('-2 years')->format('Y-m-d H:i:s'),
];
