<?php

namespace DifferenceCalculator\Formatters\Stylish;

function stylish(array $diff, string $spaceSymbol = ' ', int|array $spacesCount = 4, bool $inner = false): string|null
{
    if (!is_array($spacesCount)) {
        $spacesCount = [$spacesCount, $spacesCount];
    }
    $indent = str_repeat($spaceSymbol, $spacesCount[1]);
    if ($inner) {
        $endIndent = str_repeat($spaceSymbol, $spacesCount[1] - $spacesCount[0]);
    }
    $spacesCount[1] = $spacesCount[1] + $spacesCount[0];


    $indentWithMinus = substr($indent, 0, strlen($indent) - 2) . '-' . $spaceSymbol;
    $indentWithPlus = substr($indent, 0, strlen($indent) - 2) . '+' . $spaceSymbol;

    $formatted = "{\n";

    foreach ($diff as $key => $value) {
        if (!is_array($value)) {
            $formatted .= "{$indent}{$key}: {$value}\n";
            continue;
        }

        $action = $value[array_key_last($value)];

        switch ($action) {
            case '_update_':
                $previousValue = $value[0];
                if (is_array($previousValue)) {
                    $previousValue = stylish($previousValue, $spaceSymbol, $spacesCount, true);
                }

                $currentValue = $value[1];
                if (is_array($currentValue)) {
                    $currentValue = stylish($currentValue, $spaceSymbol, $spacesCount, true);
                }

                $formatted .= "{$indentWithMinus}{$key}: {$previousValue}\n";
                $formatted .= "{$indentWithPlus}{$key}: {$currentValue}\n";

                break;
            case '_remove_':
                $currentValue = $value[0];
                if (is_array($currentValue)) {
                    $currentValue = stylish($currentValue, $spaceSymbol, $spacesCount, true);
                }

                $formatted .= "{$indentWithMinus}{$key}: {$currentValue}\n";

                break;
            case '_add_':
                $currentValue = $value[0];
                if (is_array($currentValue)) {
                    $currentValue = stylish($currentValue, $spaceSymbol, $spacesCount, true);
                }
                $formatted .= "{$indentWithPlus}{$key}: {$currentValue}\n";

                break;
            default:
                $currentValue = stylish($value, ' ', $spacesCount, true);
                $formatted .= "{$indent}{$key}: {$currentValue}\n";
        }
    }

    $endBracket = $inner ? "{$endIndent}}" : "}\n";
    $formatted .= $endBracket;

    return $formatted;
}
