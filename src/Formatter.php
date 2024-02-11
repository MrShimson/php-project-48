<?php

namespace DifferenceCalculator\Formatter;

use function DifferenceCalculator\Formatters\Stylish\stylish;
use function DifferenceCalculator\Formatters\Plain\plain;
use function DifferenceCalculator\Formatters\Json\json;

function formatDiff(array $diff, string $format): string
{
    switch ($format) {
        case 'stylish':
            $formattedDiff = stylish($diff);
            break;
        case 'plain':
            $formattedDiff = plain($diff);
            break;
        case 'json':
            $formattedDiff = json($diff);
            break;
        default:
            print_r("Wrong format '{$format}', deafault format 'stylish' applied:\n");
            $formattedDiff = stylish($diff);
    }

    return $formattedDiff;
}
