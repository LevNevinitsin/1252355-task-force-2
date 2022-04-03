<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use app\models\User;
use app\models\Category;

$contractorsIds = User::find()->select(['id'])->where(['role_id' => 2])->column();
$categoriesIds = Category::find()->select(['id'])->column();

// Тут будут все варианты комбинаций "исполнитель - категория"
$combinations = [];

foreach ($contractorsIds as $contractorId) {
    foreach ($categoriesIds as $categoryId) {
        $combinations []= [
            'user_id' => $contractorId,
            'category_id' => $categoryId,
        ];
    }
}

$combination = $faker->unique()->randomElement($combinations);

return $combination;
