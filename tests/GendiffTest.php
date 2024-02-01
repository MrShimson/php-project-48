<?php

namespace DifferenceCalculator\Tests;

use PHPUnit\Framework\TestCase;

use function DifferenceCalculator\Gendiff\genDiff;

class GendiffTest extends TestCase
{
    public function getFixtureFullPath($dir, $name, $ext)
    {
        $urlParts = [__DIR__, 'fixtures', $dir, $name . '.' . $ext];
        return realpath(implode('/', $urlParts));
    }

    public function testGenDiff(): void
    {
        $pathJson1 = $this->getFixtureFullPath('nested', 'file1', 'json');
        $pathJson2 = $this->getFixtureFullPath('nested', 'file2', 'json');

        $pathYaml1 = $this->getFixtureFullPath('nested', 'file1', 'yaml');
        $pathYaml2 = $this->getFixtureFullPath('nested', 'file2', 'yaml');

        $pathResult = $this->getFixtureFullPath('results', 'nested', 'txt');

        $this->assertStringEqualsFile($pathResult, genDiff($pathJson1, $pathJson2));
        $this->assertStringEqualsFile($pathResult, genDiff($pathYaml1, $pathYaml2));
    }
}
