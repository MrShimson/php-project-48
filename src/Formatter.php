<?php

namespace Differ\Formatter;

use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Json\json;

function formatDiffTree(array $diffTree, string $format): string
{
    switch ($format) {
        case 'stylish':
            return stylish($diffTree);
        case 'plain':
            return plain($diffTree);
        case 'json':
            return json($diffTree);
        default:
            throw new \Exception("Wrong format '{$format}'");
    }
}
