<?php

namespace Differ\Formatter;

use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Json\json;

function formatDiff(array $diff, string $format): string
{
    switch ($format) {
        case 'stylish':
            return stylish($diff);
        case 'plain':
            return plain($diff);
        case 'json':
            return json($diff);
    }
}
