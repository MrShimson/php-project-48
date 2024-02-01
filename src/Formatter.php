<?php

namespace DifferenceCalculator\Formatter;

function stylish(array $array, string $spaceSymbol = ' ', int|array $spacesCount = 4, bool $nested = false): string|null
{
    if (!is_array($spacesCount)) {
        $spacesCount = [$spacesCount, $spacesCount];
    }
    $indent = str_repeat($spaceSymbol, $spacesCount[1]);
    if ($nested) {
        $endIndent = str_repeat($spaceSymbol, $spacesCount[1] - $spacesCount[0]);
    }
    $spacesCount[1] = $spacesCount[1] + $spacesCount[0];


    $indentWithMinus = substr($indent, 0, strlen($indent) - 2) . '-' . $spaceSymbol;
    $indentWithPlus = substr($indent, 0, strlen($indent) - 2) . '+' . $spaceSymbol;

    $formatted = "{\n";

    foreach ($array as $key => $value) {
        if (!is_array($value)) {
            $formatted .= "{$indent}{$key}: {$value}\n";
            continue;
        }

        $sign = $value[array_key_last($value)];

        switch ($sign) {
            case 'update':
                $firstValue = $value[0];
                if (is_array($firstValue)) {
                    $firstValue = stylish($firstValue, $spaceSymbol, $spacesCount, true);
                }

                $secondValue = $value[1];
                if (is_array($secondValue)) {
                    $secondValue = stylish($secondValue, $spaceSymbol, $spacesCount, true);
                }

                $formatted .= "{$indentWithMinus}{$key}: {$firstValue}\n";
                $formatted .= "{$indentWithPlus}{$key}: {$secondValue}\n";

                break;
            case 'remove':
                $firstValue = $value[0];
                if (is_array($firstValue)) {
                    $firstValue = stylish($firstValue, $spaceSymbol, $spacesCount, true);
                }

                $formatted .= "{$indentWithMinus}{$key}: {$firstValue}\n";

                break;
            case 'add':
                $secondValue = $value[0];
                if (is_array($secondValue)) {
                    $secondValue = stylish($secondValue, $spaceSymbol, $spacesCount, true);
                }
                $formatted .= "{$indentWithPlus}{$key}: {$secondValue}\n";

                break;
            default:
                $value = stylish($value, ' ', $spacesCount, true);
                $formatted .= "{$indent}{$key}: {$value}\n";
        }
    }

    $endBracket = $nested ? "{$endIndent}}" : "}\n";
    $formatted .= $endBracket;

    return $formatted;
}
