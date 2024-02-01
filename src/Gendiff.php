<?php

namespace DifferenceCalculator\Gendiff;

use function DifferenceCalculator\Parsers\getData;
use function DifferenceCalculator\Formatter\stylish;

function getKeys(array $coll1, array $coll2): array
{
    $keys = array_merge(array_keys($coll1), array_keys($coll2));//Функция выбирает все ключи из двух массивов
    $keys = array_values(array_unique($keys));                  //убирает повторяющиеся и
    sort($keys);                                                //сортирует в алфавитном порядке
    return $keys;
}

function buildDiffTree(array $tree1, array $tree2): array
{
    $keys = getKeys($tree1, $tree2);

    $diff = [];

    foreach ($keys as $key) {
        if (isset($tree1[$key]) && isset($tree2[$key])) {
            $value1 = $tree1[$key];
            $value2 = $tree2[$key];
            if (!is_array($value1) && !is_array($value2)) {
                $diff[$key] = $value1 === $value2 ? $value1 : [$value1, $value2, 'update'];
            } elseif (is_array($value1) && is_array($value2)) {
                if (!array_is_list($value1) && !array_is_list($value2)) {
                    $diff[$key] = buildDiffTree($value1, $value2);
                } else {
                    $diff[$key] = [$value1, $value2, 'update'];
                }
            } else {
                $diff[$key] = [$value1, $value2, 'update'];
            }
        } elseif (isset($tree1[$key])) {
            $value1 = $tree1[$key];
            $diff[$key] = [$value1, 'remove'];
        } else {
            $value2 = $tree2[$key];
            $diff[$key] = [$value2, 'add'];
        }
    }

    return $diff;
}

function genDiff(string $pathToFile1, string $pathToFile2, $format = 'stylish'): string
{
    try {
        $file1 = getData($pathToFile1);
        $file2 = getData($pathToFile2);
    } catch (\Exception $error) {
        return $error->getMessage();
    }

    $diff = buildDiffTree($file1, $file2);

    if ($format === 'stylish') {
        $diff = stylish($diff);
    }

    return $diff;
}
