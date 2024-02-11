<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function convertTypes(array $coll): array
{
    //Функция проходится по массиву данных, чтобы отобразить булевы и NULL значения при парсинге JSON
    $callback = function ($value) {
        if (gettype($value) === 'boolean' || gettype($value) === 'NULL') {
            return json_encode($value);
        }
        return $value;
    };

    $file = array_map(fn($value) => is_array($value) ? convertTypes($value) : $callback($value), $coll);
    return $file;
}

function getData(string $path)
{
    $absolute = $path; //Предполагаем, что изначально в $path был передан абсолютный путь
    $relative = __DIR__ . "/..{$path}"; //Относительный путь, если в $path передан не абсолютный

    if (file_exists($absolute) || file_exists($relative)) {
        $correctPath = file_exists($absolute) ? $absolute : $relative;
    } else {
        throw new \Exception("\nThis file does not exist at this path:\n{$path}\n");
    }

    $extension = pathinfo($correctPath, PATHINFO_EXTENSION);

    if ($extension === 'json') {
        $data = json_decode(file_get_contents($correctPath), true);
    } elseif ($extension === 'yml' || $extension === 'yaml') {
        $data = Yaml::parseFile($correctPath);
    } else {
        throw new \Exception("\nThis file has invalid extension:\n.{$extension}\n");
    }

    if (empty($data)) {
        $basename = pathinfo($correctPath, PATHINFO_BASENAME);
        throw new \Exception("\nThis file is empty:\n{$basename}\n");
    }

    return convertTypes($data);
}
