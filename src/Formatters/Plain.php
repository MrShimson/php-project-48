<?php

namespace Differ\Formatters\Plain;

use function Differ\Formatters\Stylish\isAssociative;

function formatType(string|int|float $value): string
{
    $types = ['null', 'true', 'false'];
    if (!in_array($value, $types)) {
        $value = (is_int($value) || is_float($value)) ? $value : "'{$value}'";
    }

    return $value;
}

function plain(array $diff, string $previousKey = '', int $deep = 1): string
{
    $keys = array_keys($diff);

    $callback = function ($acc, $key) use ($diff, $previousKey) {
        $property = "{$previousKey}{$key}";
        $addFormat = "Property '{$property}' was added with value: %s\n";
        $removeFormat = "Property '{$property}' was removed\n";
        $updateFormat = "Property '{$property}' was updated. From %s to %s\n";
        $property = "{$property}.";

        $value = $diff[$key];

        if (isAssociative($value)) {
            $acc[] = plain($value, $property, 2);
        } else {
            if (sizeof($value) === 2) {
                [$action, $currValue] = $value;
            } else {
                [$action, $prevValue, $currValue] = $value;
            }

            switch ($action) {
                case 'unchanged':
                    break;
                case 'removed':
                    if (is_array($currValue)) {
                        $currValue = "[complex value]";
                    } else {
                        $currValue = formatType($currValue);
                    }
                    $acc[] = sprintf($removeFormat, $currValue);
                    break;
                case 'added':
                    if (is_array($currValue)) {
                        $currValue = "[complex value]";
                    } else {
                        $currValue = formatType($currValue);
                    }
                    $acc[] = sprintf($addFormat, $currValue);
                    break;
                case 'updated':
                    if (is_array($prevValue)) {
                        $prevValue = "[complex value]";
                    } else {
                        $prevValue = formatType($prevValue);
                    }

                    if (is_array($currValue)) {
                        $currValue = "[complex value]";
                    } else {
                        $currValue = formatType($currValue);
                    }

                    $acc[] = sprintf($updateFormat, $prevValue, $currValue);
                    break;
            }
        }

        return $acc;
    };

    $formattedDiff = array_reduce($keys, $callback, []);
    $formattedDiff = implode('', $formattedDiff);

    return $deep === 1 ? trim($formattedDiff) : $formattedDiff;
}
