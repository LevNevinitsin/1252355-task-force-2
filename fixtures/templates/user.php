<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'name'             => $faker->name(),
    'email'            => $faker->unique()->email,
    'password'         => Yii::$app->getSecurity()->generatePasswordHash('password_' . $index),
    'city_id'          => random_int(1, 1087),
    'birthdate'        => $faker->dateTimeBetween('-60 years', '-10 years')->format('Y-m-d'),
    'photo'            => '/web/img/avatars/' . random_int(1, 5) . '.png',
    'phone'            => $faker->numerify('7##########'),
    'telegram'         => $faker->unique()->word(),
    'self_description' => $faker->paragraph(),
    'role_id'          => random_int(1, 2),
    'fails_count'      => $faker->randomDigit(),
    'date_registered'  => $faker->dateTimeBetween('-2 years')->format('Y-m-d H:i:s'),
];
