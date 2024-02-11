<?php

namespace DifferenceCalculator\Formatters\Json;

use function DifferenceCalculator\Formatters\Stylish\isAssociative;

function formatType(mixed $value): mixed
{
    if (is_array($value)) {
        return $value;
    }

    switch ($value) {
        case 'null':
            $value = null;
            break;
        case 'true':
            $value = true;
            break;
        case 'false':
            $value = false;
            break;
    }

    return $value;
}

function buildKeyRow(array $keys, $value): array
{
    $keys = array_reverse($keys);
    return array_reduce($keys, function ($acc, $key) use ($value) {
        if (empty($acc)) {
            $acc[$key] = $value;
        } else {
            $stash = $acc;
            $acc = [];
            $acc[$key] = $stash;
        }
        return $acc;
    }, []);
}

function json(array $diff, array $previousKeys = [], bool $encode = true)
{
    $keys = array_keys($diff);

    $callback = function ($acc, $key) use ($diff, $previousKeys) {
        $value = $diff[$key];
        $previousKeys[] = $key;
        if (isAssociative($value)) {
            $acc = array_merge_recursive($acc, json($value, $previousKeys, false));
        } else {
            if (sizeof($value) === 2) {
                [$action, $finalValue] = $value;
                $finalValue = formatType($finalValue);
            } else {
                [$action, $prevValue, $currValue] = $value;
                $prevValue = formatType($prevValue);
                $currValue = formatType($currValue);
                $finalValue = ['removed' => $prevValue, 'added' => $currValue];
            }
            $finalValue = buildKeyRow($previousKeys, $finalValue);
            $acc[$action] = array_merge_recursive(($acc[$action] ?? []), $finalValue);
        }

        return $acc;
    };

    $formattedDiff = array_reduce($keys, $callback, []);

    return $encode ?
        json_encode($formattedDiff, JSON_PRETTY_PRINT) . "\n" : $formattedDiff;
}
