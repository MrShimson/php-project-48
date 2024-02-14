<?php

namespace Differ\Formatters\Json;

use function Differ\Formatters\Stylish\isLeaf;

function formatType(mixed $value): mixed
{
    switch ($value) {
        case 'null':
            return null;
        case 'true':
            return true;
        case 'false':
            return false;
        default:
            return $value;
    }
}

function searchInTree(string $action, array $diffTree): array
{
    $filteredTree = array_filter(
        $diffTree,
        fn($node) => !isLeaf($node) || $node['action'] === $action
    );

    $names = array_map(fn($node) => $node['name'], $filteredTree);

    $callback = function ($node) use ($action) {
        $name = $node['name'];

        if (!isLeaf($node)) {
            $children = $node['children'];
            $diffByAction = searchInTree($action, $children);
        } else {
            $value = $node['value'];

            if ($action === 'updated') {
                [$prevValue, $currValue] = $value;
                $diffByAction = [
                    'removed' => formatType($prevValue),
                    'added' => formatType($currValue)
                ];
            } else {
                $diffByAction = formatType($value);
            }
        }

        return $diffByAction;
    };

    return array_filter(
        array_combine($names, array_map($callback, $filteredTree)),
        fn($value) => $value !== []
    );
}

function json(array $diffTree): string
{
    $actions = [
        'updated',
        'removed',
        'added',
        'unchanged'
    ];

    return json_encode(
        array_combine(
            $actions,
            array_map(
                fn($action) => searchInTree($action, $diffTree),
                $actions
            )
        ),
        JSON_PRETTY_PRINT
    );
}
