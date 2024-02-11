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

        $pathResultStylish = $this->getFixtureFullPath('results', 'stylish', 'txt');

        $this->assertStringEqualsFile($pathResultStylish, genDiff($pathJson1, $pathJson2, 'stylish'));
        $this->assertStringEqualsFile($pathResultStylish, genDiff($pathYaml1, $pathYaml2, 'stylish'));

        $pathResultPlain = $this->getFixtureFullPath('results', 'plain', 'txt');

        $this->assertStringEqualsFile($pathResultPlain, genDiff($pathJson1, $pathJson2, 'plain'));
        $this->assertStringEqualsFile($pathResultPlain, genDiff($pathYaml1, $pathYaml2, 'plain'));

        $pathResultJson = $this->getFixtureFullPath('results', 'json', 'json');

        $this->assertStringEqualsFile($pathResultJson, genDiff($pathJson1, $pathJson2, 'json'));
        $this->assertStringEqualsFile($pathResultJson, genDiff($pathYaml1, $pathYaml2, 'json'));
    }
}
