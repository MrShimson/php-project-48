<?php

namespace DifferenceCalculator\Tests;

use PHPUnit\Framework\TestCase;

use function DifferenceCalculator\Gendiff\genDiff;

class GendiffTest extends TestCase
{
    public function testGenDiff(): void
    {
        $path1 = "tests/fixtures/flat/file1.json";
        $path2 = "tests/fixtures/flat/file2.json";

        $expected1 = <<<EOT
        {
          - follow: false
            host: hexlet.io
          - proxy: 123.234.53.22
          - timeout: 50
          + timeout: 20
          + verbose: true
        }

        EOT;

        $this->assertEquals($expected1, genDiff($path1, $path2));

        $path1 = "tests/fixtures/flat/file1.yaml";
        $path2 = "tests/fixtures/flat/file2.yaml";

        $expected2 = <<<EOT
        {
          - follow: false
            host: hexlet.io
          - proxy: 123.234.53.22
          - timeout: 50
          + timeout: 20
          + verbose: true
        }

        EOT;

        $this->assertEquals($expected2, genDiff($path1, $path2));
    }
}
