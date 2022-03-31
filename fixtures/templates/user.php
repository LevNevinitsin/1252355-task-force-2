<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

return [
    'name'             => $faker->name(),
    'email'            => $faker->unique()->email,
    'password'         => Yii::$app->getSecurity()->generatePasswordHash('password_' . $index),
    'city_id'          => $faker->numberBetween(1, 1087),

    'birthdate'        => $faker
        ->optional($weight = 0.8)
        ->passthrough($faker->dateTimeBetween('-60 years', '-10 years')->format('Y-m-d')),

    'photo'            => $faker->optional($weight = 0.8)->passthrough('/img/avatars/' . random_int(1, 5) . '.png'),
    'phone'            => $faker->optional($weight = 0.8)->numerify('7##########'),
    'telegram'         => $faker->optional($weight = 0.8)->word(),
    'self_description' => $faker->optional($weight = 0.8)->paragraph(),
    'role_id'          => $faker->numberBetween(1, 2),
    'fails_count'      => $faker->randomDigit(),
    'date_registered'  => $faker->dateTimeBetween('-2 years')->format('Y-m-d H:i:s'),
];
