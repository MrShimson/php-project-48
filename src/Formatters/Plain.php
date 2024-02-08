<?php

namespace DifferenceCalculator\Formatters\Plain;

function formatType(string $value): string
{
    $types = ['null', 'true', 'false'];
    if (!in_array($value, $types)) {
        $value = (is_int($value) || is_float($value)) ? $value : "'{$value}'";
    }

    return $value;
}

function plain(array $diff, string $previousKey = ''): string|null
{
    $formatted = '';

    foreach ($diff as $key => $value) {
        $property = "{$previousKey}{$key}";
        $addFormat = "Property '{$property}' was added with value: %s\n";
        $removeFormat = "Property '{$property}' was removed\n";
        $updateFormat = "Property '{$property}' was updated. From %s to %s\n";
        $property .= '.';

        if (!is_array($value)) {
            continue;
        }

        $action = $value[array_key_last($value)];

        switch ($action) {
            case '_update_':
                $previousValue = $value[0];
                $previousValue = is_array($previousValue) ?
                    '[complex value]' : formatType($previousValue);

                $currentValue = $value[1];
                $currentValue = is_array($currentValue) ?
                    '[complex value]' : formatType($currentValue);

                $formatted .= sprintf($updateFormat, $previousValue, $currentValue);

                break;
            case '_remove_':
                $currentValue = $value[0];
                $currentValue = is_array($currentValue) ?
                    '[complex value]' : formatType($currentValue);

                $formatted .= sprintf($removeFormat, $currentValue);

                break;
            case '_add_':
                $currentValue = $value[0];
                $currentValue = is_array($currentValue) ?
                    '[complex value]' : formatType($currentValue);

                $formatted .= sprintf($addFormat, $currentValue);

                break;
            default:
                $currentValue = plain($value, $property);
                $formatted .= $currentValue;
        }
    }

    return $formatted;
}
