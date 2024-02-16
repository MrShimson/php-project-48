<?php

namespace Differ\Differ;

use function Functional\sort;
use function Differ\Parsers\getData;
use function Differ\Formatter\formatDiffTree;

function isAssociativeArray(mixed $value): bool
{
    if (is_array($value)) {
        return !array_is_list($value) ? true : false;
    }

    return false;
}

function mergeKeys(array $firstArray, array $secondArray): array
{
    //Функция сливает все ключи из двух массивов, убирает повторяющиеся
    //и сортирует их в алфавитном порядке
    $firstArrayKeys = array_keys($firstArray);
    $secondArrayKeys = array_keys($secondArray);
    $mergedKeys = array_values(
        array_unique(
            array_merge($firstArrayKeys, $secondArrayKeys)
        )
    );

    return sort($mergedKeys, fn($left, $right) => strcmp($left, $right));
}

function buildDiffTree(array $firstArray, array $secondArray): array
{
    $keys = mergeKeys($firstArray, $secondArray);

    $callback = function ($key) use ($firstArray, $secondArray) {

        if (isset($firstArray[$key]) && isset($secondArray[$key])) {
            $prevValue = $firstArray[$key];
            $currValue = $secondArray[$key];

            if (isAssociativeArray($prevValue) && isAssociativeArray($currValue)) {
                $diffNode = ['name' => $key, 'children' => buildDiffTree($prevValue, $currValue)];
            } else {
                $diffNode = $prevValue === $currValue ?
                ['name' => $key, 'action' => 'unchanged', 'value' => $currValue] :
                ['name' => $key, 'action' => 'updated', 'value' => [$prevValue, $currValue]];
            }
        } elseif (isset($firstArray[$key])) {
            $value = $firstArray[$key];

            $diffNode = ['name' => $key, 'action' => 'removed', 'value' => $value];
        } else {
            $value = $secondArray[$key];

            $diffNode = ['name' => $key, 'action' => 'added', 'value' => $value];
        }

        return $diffNode;
    };

    return array_map($callback, $keys);
}

function genDiff(string $firstFilePath, string $secondFilePath, string $format = 'stylish')//: string
{
    try {
        $firstFileData = getData($firstFilePath);
        $secondFileData = getData($secondFilePath);
    } catch (\Exception $error) {
        return $error->getMessage();
    }

    $diffTree = buildDiffTree($firstFileData, $secondFileData);

    return formatDiffTree($diffTree, $format);
    //return $diffTree;
}
