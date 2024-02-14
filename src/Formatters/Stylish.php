<?php

namespace Differ\Formatters\Stylish;

use function Differ\Differ\isAssociativeArray;

/*function isNode(array $array): bool
{
    return isset($array['children']);
}*/

function isLeaf(array $array): bool
{
    return isset($array['value']);
}

function buildIndents(int $count): array
{
    $none = str_repeat(' ', 4 * $count);
    $end = str_repeat(' ', 4 * ($count - 1));
    $truncated = substr($none, 0, strlen($none) - 2);
    $removed = "{$truncated}- ";
    $added = "{$truncated}+ ";

    return [
        'none' => $none,
        'removed' => $removed,
        'added' => $added,
        'end' => $end
    ];
}

function formatValues(mixed $values, int $deep): string
{
    if (!is_array($values)) {
        return $values;
    }

    $indents = buildIndents($deep);
    $indent = $indents['none'];
    $end = $indents['end'];

    if (isAssociativeArray($values)) {
        $keys = array_keys($values);
        $outputFormat = "{\n%s{$end}}";

        $callback = function ($key) use ($values, $deep, $indent) {
            $lineFormat = "{$indent}{$key}: %s\n";
            $formattedValue = is_array($values[$key]) ?
            formatValues($values[$key], $deep + 1) :
            $values[$key];

            return sprintf($lineFormat, $formattedValue);
        };

        $formattedArray = implode('', array_map($callback, $keys));
    } else {
        $outputFormat = "[\n%s{$end}]";

        $callback = function ($value) use ($deep, $indent) {
            $lineFormat = "{$indent}%s\n";
            $formattedValue = is_array($value) ?
            formatValues($value, $deep + 1) :
            $value;

            return sprintf($lineFormat, $formattedValue);
        };

        $formattedArray = implode('', array_map($callback, $values));
    }

    return sprintf($outputFormat, $formattedArray);
}

function stylish(array $diffTree, int $deep = 1): string
{
    $indents = buildIndents($deep);
    $end = $indents['end'];
    $outputFormat = "{\n%s{$end}}";

    $callback = function ($node) use ($deep, $indents) {
        $none = $indents['none'];
        $removed = $indents['removed'];
        $added = $indents['added'];
        $name = $node['name'];
        $lineFormat = "%s{$name}: %s\n";

        if (!isLeaf($node)) {
            $children = $node['children'];

            return sprintf($lineFormat, $none, stylish($children, $deep + 1));
        } else {
            $action = $node['action'];
            $value = $node['value'];

            switch ($action) {
                case 'unchanged':
                    $formattedValue = formatValues($value, $deep + 1);

                    return sprintf($lineFormat, $none, $formattedValue);
                case 'removed':
                    $formattedValue = formatValues($value, $deep + 1);

                    return sprintf($lineFormat, $removed, $formattedValue);
                case 'added':
                    $formattedValue = formatValues($value, $deep + 1);

                    return sprintf($lineFormat, $added, $formattedValue);
                case 'updated':
                    [$prevValue, $currValue] = $value;
                    $formattedPrevValue = formatValues($prevValue, $deep + 1);
                    $formattedCurrValue = formatValues($currValue, $deep + 1);

                    $firstLine = sprintf($lineFormat, $removed, $formattedPrevValue);
                    $secondLine = sprintf($lineFormat, $added, $formattedCurrValue);

                    return "{$firstLine}{$secondLine}";
            }
        }
    };

    $formattedDiffTree = implode('', array_map($callback, $diffTree));

    return sprintf($outputFormat, $formattedDiffTree);
}
