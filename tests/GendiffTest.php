<?php

namespace DifferenceCalculator\Tests;

use PHPUnit\Framework\TestCase;

use function DifferenceCalculator\Gendiff\genDiff;

class GendiffTest extends TestCase
{
    public function testGenDiff(): void
    {
        $path1 = 'file1.json';
        $path2 = 'file2.json';
        $expected = ("\nThe file does not exist at this path:\n{$path1}\n");

        $this->assertEquals($expected, genDiff($path1, $path2));

        $path1 = "tests/fixtures/file1.json";
        $expected = ("\nThe file does not exist at this path:\n{$path2}\n");

        $this->assertEquals($expected, genDiff($path1, $path2));

        $path1 = "tests/fixtures/file1.json";
        $path2 = "/home/mr_shimson/hexlet-projects/difference-calculator/tests/fixtures/file2.json";

        $difference = <<<EOT
        {
          - follow: false
            host: hexlet.io
          - proxy: 123.234.53.22
          - timeout: 50
          + timeout: 20
          + verbose: true
        }
        
        EOT;
        $this->assertEquals($difference, genDiff($path1, $path2));
    }
}
