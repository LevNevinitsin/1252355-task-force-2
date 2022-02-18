<?php
namespace LevNevinitsin\Files;

use LevNevinitsin\Files\Exception\SourceFileException;
use LevNevinitsin\Files\Exception\FileFormatException;

class CsvIntoSqlHandler
{
    private $filename;
    private $columns;
    private $fileObject;

    public function __construct(string $dataFolderName, string $filename, array $columns)
    {
        $this->filename = $_SERVER['DOCUMENT_ROOT'] . '/' . $dataFolderName . '/' . $filename;
        $this->columns = $columns;
    }

    public function import(): void
    {
        if (!$this->validateColumns($this->columns)) {
            throw new FileFormatException("Заданы неверные заголовки столбцов");
        }

        if (!file_exists($this->filename)) {
            throw new SourceFileException("Файл \"{$this->filename}\" не существует");
        }

        try {
            $this->fileObject = new \SplFileObject($this->filename);
        }
        catch (\RuntimeException $exception) {
            throw new SourceFileException("Не удалось открыть файл \"{$this->filename}\" на чтение");
        }

        $header_data = $this->getHeaderData();

        if ($header_data !== $this->columns) {
            throw new FileFormatException("Исходный файл \"{$this->filename}\" не содержит необходимых столбцов");
        }

        foreach ($this->getNextLine() as $line) {
            $this->results[] = $line;
        }
    }

    public function generateSqlfromCsvCategories(string $tableName, array $columns): void
    {
        $this->generateSqlfromCsv($tableName, $columns, 'generateCategoryValuesSqlString');
    }

    public function generateSqlfromCsvCities(string $tableName, array $columns): void
    {
        $this->generateSqlfromCsv($tableName, $columns, 'generateCityValuesSqlString');
    }

    private function generateSqlfromCsv(string $tableName, array $columns, string $cb): void
    {
        $sqlFilename = "${_SERVER['DOCUMENT_ROOT']}/$tableName-filler.sql";
        $sqlFileObject = new \SplFileObject($sqlFilename, 'w');
        $columnsString = implode(', ', $columns);
        $sqlFileObject->fwrite("INSERT INTO $tableName ($columnsString) VALUES");

        foreach($this->results as $lineValues) {
            $valuesString = $this->$cb($lineValues);
            $sqlFileObject->fwrite("\n  ($valuesString),");
        }

        $currentFilePosition = $sqlFileObject->ftell();
        $sqlFileObject->ftruncate($currentFilePosition - 1);
        $sqlFileObject->fseek($currentFilePosition - 1);
        $sqlFileObject->fwrite(";\n");
        $sqlFileObject = null;
    }

    private function generateCategoryValuesSqlString(array $categoryAttributes): string
    {
        $categoryName = $categoryAttributes[0];
        $categoryIcon = $categoryAttributes[1];
        return "'$categoryName', '$categoryIcon'";
    }

    private function generateCityValuesSqlString(array $cityAttributes): string
    {
        $cityName = $cityAttributes[0];
        $coordinateLat = $cityAttributes[1];
        $coordinateLong = $cityAttributes[2];
        return "'$cityName', $coordinateLat, $coordinateLong";
    }

    private function getHeaderData(): ?array {
        $this->fileObject->rewind();
        $data = $this->fileObject->fgetcsv();
        $bom = "\xef\xbb\xbf";
        $firstColumnName = $data[0];

        if (str_starts_with($firstColumnName, $bom)) {
            $data[0] = substr($firstColumnName, 3);
        }

        return $data;
    }

    private function getNextLine(): ?iterable {
        while (!$this->fileObject->eof()) {
            $valuesOfLine = $this->fileObject->fgetcsv();

            if ($valuesOfLine[0]) {
                yield $valuesOfLine;
            }
        }
    }

    private function validateColumns(array $columns): bool
    {
        if (!count($columns)) {
            return false;
        }

        foreach($columns as $column) {
            if (!is_string($column)) {
                return false;
            }
        }

        return true;
    }
}
