<?php

namespace Differ\Formatter;

use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Json\json;

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
