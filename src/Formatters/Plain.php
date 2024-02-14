<?php

namespace Differ\Formatters\Plain;

use function Differ\Formatters\Stylish\isLeaf;

function formatValue(mixed $value): string
{
    if (is_array($value)) {
        return '[complex value]';
    }

    $types = ['null', 'true', 'false'];
    if (!in_array($value, $types, true)) {
        $value = (is_int($value) || is_float($value)) ? $value : "'{$value}'";
    }

    return $value;
}

function buildFormats(string $property): array
{
    return [
        'added' => "Property '{$property}' was added with value: %s\n",
        'removed' => "Property '{$property}' was removed\n",
        'updated' => "Property '{$property}' was updated. From %s to %s\n"
    ];
}

function plain(array $diffTree, string $previousKey = '', bool $trim = true): string
{
    $callback = function ($node) use ($previousKey) {
        $name = $node['name'];
        $formats = buildFormats("{$previousKey}{$name}");
        $innerProperty = "{$previousKey}{$name}.";

        if (!isLeaf($node)) {
            $children = $node['children'];
            $diffLine =  plain($children, $innerProperty, false);
        } else {
            $action = $node['action'];
            $value = $node['value'];

            switch ($action) {
                case 'updated':
                    [$prevValue, $currValue] = $value;
                    $format = $formats['updated'];
                    $diffLine = sprintf(
                        $format,
                        formatValue($prevValue),
                        formatValue($currValue)
                    );

                    break;
                case 'removed':
                    $format = $formats['removed'];
                    $diffLine = sprintf($format, formatValue($value));

                    break;
                case 'added':
                    $format = $formats['added'];
                    $diffLine = sprintf($format, formatValue($value));

                    break;
                default:
                    return;
            }
        }

        return $diffLine;
    };

    if ($trim) {
        return trim(implode('', array_map($callback, $diffTree)));
    }

    return implode('', array_map($callback, $diffTree));
}
