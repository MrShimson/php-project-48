<?php

namespace Differ\Formatters\Stylish;

function isAssociative(array $arr): bool
{
    if (array_is_list($arr)) {
        return false;
    }
    return true;
}

function stylish(array $diff, int $deep = 1): string|null
{
    $startSpace = str_repeat(' ', 4 * $deep);
    $endSpace = str_repeat(' ', 4 * ($deep - 1));
    $removedSpace = substr($startSpace, 0, strlen($startSpace) - 2) . '- ';
    $addedSpace = substr($startSpace, 0, strlen($startSpace) - 2) . '+ ';

    if (isAssociative($diff)) {
        $keys = array_keys($diff);

        $callback = function ($acc, $key) use ($diff, $deep, $startSpace, $removedSpace, $addedSpace) {
            $value = $diff[$key];
            $format = "%s{$key}: %s";
            $formatLf = "%s{$key}: %s\n";

            if (is_array($value)) {
                if (isAssociative($value)) {
                    $currValue = stylish($value, $deep + 1);
                    $acc[] = sprintf($format, $startSpace, $currValue, "");
                } else {
                    if (sizeof($value) === 2) {
                        [$action, $currValue] = $value;
                    } else {
                        [$action, $prevValue, $currValue] = $value;
                    }

                    switch ($action) {
                        case 'unchanged':
                            if (is_array($currValue)) {
                                $currValue = stylish($currValue, $deep + 1);
                                $acc[] = sprintf($format, $startSpace, $currValue);
                            } else {
                                $acc[] = sprintf($formatLf, $startSpace, $currValue);
                            }
                            break;
                        case 'removed':
                            if (is_array($currValue)) {
                                $currValue = stylish($currValue, $deep + 1);
                                $acc[] = sprintf($format, $removedSpace, $currValue);
                            } else {
                                $acc[] = sprintf($formatLf, $removedSpace, $currValue);
                            }
                            break;
                        case 'added':
                            if (is_array($currValue)) {
                                $currValue = stylish($currValue, $deep + 1);
                                $acc[] = sprintf($format, $addedSpace, $currValue);
                            } else {
                                $acc[] = sprintf($formatLf, $addedSpace, $currValue);
                            }
                            break;
                        case 'updated':
                            if (is_array($prevValue)) {
                                $prevValue = stylish($prevValue, $deep + 1);
                                $acc[] = sprintf($format, $removedSpace, $prevValue);
                            } else {
                                $acc[] = sprintf($formatLf, $removedSpace, $prevValue);
                            }

                            if (is_array($currValue)) {
                                $currValue = stylish($currValue, $deep + 1);
                                $acc[] = sprintf($format, $addedSpace, $currValue);
                            } else {
                                $acc[] = sprintf($formatLf, $addedSpace, $currValue);
                            }

                            break;
                    }
                }
            } else {
                $acc[] = sprintf($formatLf, $startSpace, $value);
            }
            return $acc;
        };

        $formattedDiff = array_reduce($keys, $callback, ["{\n"]);
        $formattedDiff[] = "{$endSpace}}\n";
    } else {
        $formattedDiff = array_reduce($diff, fn($value) => $acc[] = "{$startSpace}{$value}\n", ["[\n"]);
        $formattedDiff[] = "{$endSpace}]\n";
    }

    return implode('', $formattedDiff);
}
