<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

use LevNevinitsin\Files\CsvIntoSqlHandler;
use LevNevinitsin\Files\Exception\SourceFileException;
use LevNevinitsin\Files\Exception\FileFormatException;

$categoriesCsvHandler = new CsvIntoSqlHandler('../data', 'categories.csv', ['name', 'icon']);

try {
    $categoriesCsvHandler->import();
    $categoriesCsvHandler->generateSqlfromCsvCategories('category', ['name', 'icon']);
} catch (SourceFileException $e) {
    echo "Не удалось обработать csv файл: " . $e->getMessage();
} catch (FileFormatException $e) {
    echo "Неверная форма файла импорта: " . $e->getMessage();
}

$citiesCsvHandler = new CsvIntoSqlHandler('../data', 'cities.csv', ['name', 'lat', 'long']);

try {
    $citiesCsvHandler->import();
    $citiesCsvHandler->generateSqlfromCsvCities('city', ['name', 'latitude', 'longitude']);
} catch (SourceFileException $e) {
    echo "Не удалось обработать csv файл: " . $e->getMessage();
} catch (FileFormatException $e) {
    echo "Неверная форма файла импорта: " . $e->getMessage();
}
