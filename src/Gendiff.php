<?php

namespace DifferenceCalculator\Gendiff;

use function DifferenceCalculator\Parsers\getData;
use function DifferenceCalculator\Formatter\formatDiff;

function mergeKeys(array $coll1, array $coll2): array
{
    $keys = array_merge(array_keys($coll1), array_keys($coll2));//Функция сливает все ключи из двух массивов
    $keys = array_values(array_unique($keys));                  //убирает повторяющиеся и
    sort($keys);                                                //сортирует в алфавитном порядке
    return $keys;
}

function buildDiff(array $tree1, array $tree2): array
{
    $keys = mergeKeys($tree1, $tree2);

    $callback = function ($acc, $key) use ($tree1, $tree2) {
        if (isset($tree1[$key]) && isset($tree2[$key])) {
            $value1 = $tree1[$key];
            $value2 = $tree2[$key];

            if ($value1 === $value2) {
                $acc[$key] = ['unchanged', $value1];
            } else {
                if (is_array($value1) && is_array($value2)) {
                    $acc[$key] = (!array_is_list($value1) && !array_is_list($value2)) ?
                        buildDiff($value1, $value2) : ['updated', $value1, $value2];
                } else {
                    $acc[$key] = ['updated', $value1, $value2];
                }
            }
        } elseif (isset($tree1[$key])) {
            $value = $tree1[$key];
            $acc[$key] = ['removed', $value];
        } else {
            $value = $tree2[$key];
            $acc[$key] = ['added', $value];
        }

        return $acc;
    };

    return array_reduce($keys, $callback, []);
}

function genDiff(string $pathToFile1, string $pathToFile2, $format = 'stylish'): string|array
{
    try {
        $file1 = getData($pathToFile1);
        $file2 = getData($pathToFile2);
    } catch (\Exception $error) {
        return $error->getMessage();
    }

    $diff = buildDiff($file1, $file2);

    return formatDiff($diff, $format);
}
