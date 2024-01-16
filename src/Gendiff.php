<?php

namespace DifferenceCalculator\Gendiff;

use function DifferenceCalculator\Parsers\getData;

function getKeys(array $coll1, array $coll2): array
{
    $keys = array_merge(array_keys($coll1), array_keys($coll2));//Функция выбирает все ключи из двух массивов
    $keys = array_values(array_unique($keys));                  //убирает повторяющиеся и
    sort($keys);                                                //сортирует в правильном порядке
    return $keys;
}

function genDiff(string $pathToFile1, string $pathToFile2): string
{
    try {
        $file1 = getData($pathToFile1);
        $file2 = getData($pathToFile2);
    } catch (\Exception $error) {
        return $error->getMessage();
    }

    $keys = getKeys($file1, $file2);

    $diff = array_reduce($keys, function ($acc, $key) use ($file1, $file2) {
        if (array_key_exists($key, $file1) && array_key_exists($key, $file2)) {
            $value1 = $file1[$key];
            $value2 = $file2[$key];
            if ($value1 === $value2) {
                $acc[] = "    {$key}: {$value1}";
            } else {
                $acc[] = "  - {$key}: {$value1}";
                $acc[] = "  + {$key}: {$value2}";
            }
        } elseif (array_key_exists($key, $file1)) {
            $value = $file1[$key];
            $acc[] = "  - {$key}: {$value}";
        } else {
            $value = $file2[$key];
            $acc[] = "  + {$key}: {$value}";
        }
        return $acc;
    }, ['{']);
    $diff[] = "}\n";

    return implode("\n", $diff);
}
