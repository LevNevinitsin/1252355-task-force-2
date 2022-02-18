<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';
(new yii\web\Application($config))->run();

use app\models\City;
use app\models\Category;

$city = City::findOne(1);
echo '<pre>';
print_r($city);
echo '</pre>';

echo '<br><br>';

$category = Category::findOne(1);
echo '<pre>';
print_r($category);
echo '</pre>';
