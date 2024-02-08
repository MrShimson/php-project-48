<?php

namespace DifferenceCalculator\Formatter;

use function DifferenceCalculator\Formatters\Stylish\stylish;
use function DifferenceCalculator\Formatters\Plain\plain;

function formatDiff(array $diff, string $format): string
{
    if ($format === 'stylish') {
        $formattedDiff = stylish($diff);
    } elseif ($format === 'plain') {
        $formattedDiff = plain($diff);
    }

    return $formattedDiff;
}
