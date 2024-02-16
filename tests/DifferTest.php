<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Functional\flatten;
use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public static function getFixtureFullPath($dir, $name, $ext)
    {
        $urlParts = [__DIR__, 'fixtures', $dir, $name . '.' . $ext];
        return realpath(implode('/', $urlParts));
    }

    public static function fillWithPaths(array $paths, array $data)
    {
        return array_map(fn($dataRow) => flatten(array_pad($dataRow, -3, $paths)), $data);
    } 
    
    public static function genDiffDataProvider()
    {
        $jsonPaths = [
            self::getFixtureFullPath('nested', 'file1', 'json'),
            self::getFixtureFullPath('nested', 'file2', 'json')
        ];

        $yamlPaths = [
            self::getFixtureFullPath('nested', 'file1', 'yaml'),
            self::getFixtureFullPath('nested', 'file2', 'yaml')
        ];

        $resultPathStylish = self::getFixtureFullPath('results', 'stylish', 'txt');
        $resultPathPlain = self::getFixtureFullPath('results', 'plain', 'txt');
        $resultPathJson = self::getFixtureFullPath('results', 'json', 'json');

        $formatAndResultPaths = [
            ['stylish', $resultPathStylish],
            ['plain',$resultPathPlain],
            ['json', $resultPathJson]
        ];

        $data = array_merge(
            self::fillWithPaths($jsonPaths, $formatAndResultPaths),
            self::fillWithPaths($yamlPaths, $formatAndResultPaths)
        );

        return $data;
    }

    /**
     * @dataProvider genDiffDataProvider
     */
    public function testGenDiff($path1, $path2, $format, $resultPath): void
    {
        $this->assertStringEqualsFile($resultPath, genDiff($path1, $path2, $format));
    }
}
