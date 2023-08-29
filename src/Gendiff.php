<?php

namespace DifferenceCalculator\Gendiff;

function getJsonContent(string $pathToFile)
{
    $absolutePath = $pathToFile;
    $relativePath = __DIR__ . "/..{$pathToFile}";
    if (file_exists($absolutePath)) {
        return json_decode(file_get_contents($absolutePath), true);
    } elseif (file_exists($relativePath)) {
        return json_decode(file_get_contents($relativePath), true);
    } else {
        throw new \Exception("\nThe file does not exist at this path:\n{$pathToFile}\n");
    }
}

function getKeys(array $coll1, array $coll2): array
{
    $keys = array_merge(array_keys($coll1), array_keys($coll2));
    $keys = array_values(array_unique($keys));
    sort($keys);
    return $keys;
}

function convertTypes(array $coll): array
{
    $callback = function ($value) {
        if (gettype($value) === 'boolean' || gettype($value) === 'NULL') {
            return json_encode($value);
        }
        return $value;
    };

    $file = array_map(fn($value) => $callback($value), $coll);
    return $file;
}

function genDiff(string $pathToFile1, string $pathToFile2): string
{
    try {
        $file1 = getJsonContent($pathToFile1);
        $file2 = getJsonContent($pathToFile2);
    } catch (\Exception $error) {
        return $error->getMessage();
    }

    $file1 = convertTypes($file1);
    $file2 = convertTypes($file2);

    $keys = getKeys($file1, $file2);

    $diff = array_reduce($keys, function ($acc, $key) use ($file1, $file2) {
        if (array_key_exists($key, $file1) && array_key_exists($key, $file2)) {
            $value1 = $file1[$key];
            $value2 = $file2[$key];
            if ($value1 === $value2) {
                $acc[] = "    {$key}: {$value1}";
            } else {
                $acc[] = "  - {$key}: {$value1}";
                $acc[] = "  + {$key}: {$value2}";
            }
        } elseif (array_key_exists($key, $file1)) {
            $value = $file1[$key];
            $acc[] = "  - {$key}: {$value}";
        } else {
            $value = $file2[$key];
            $acc[] = "  + {$key}: {$value}";
        }
        return $acc;
    }, ['{']);
    $diff[] = "}\n";

    return implode("\n", $diff);
}
