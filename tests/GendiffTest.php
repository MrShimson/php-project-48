<?php

namespace DifferenceCalculator\Tests;

use PHPUnit\Framework\TestCase;

use function DifferenceCalculator\Gendiff\genDiff;

class GendiffTest extends TestCase
{
    public function testGenDiffWithJSON(): void
    {
        $path1 = 'file1.json';
        $path2 = 'file2.json';
        $expected1 = ("\nThe file does not exist at this path:\n{$path1}\n");

        $this->assertEquals($expected1, genDiff($path1, $path2));

        $path1 = "tests/fixtures/file1.json";
        $expected2 = ("\nThe file does not exist at this path:\n{$path2}\n");

        $this->assertEquals($expected2, genDiff($path1, $path2));

        $path2 = "tests/fixtures/file2.json";
        $expected3 = <<<EOT
        {
          - follow: false
            host: hexlet.io
          - proxy: 123.234.53.22
          - timeout: 50
          + timeout: 20
          + verbose: true
        }
        
        EOT;
        $this->assertEquals($expected3, genDiff($path1, $path2));
    }

    public function testGenDiffWithYAML(): void
    {
        $path1 = 'file1.yaml';
        $path2 = 'file2.yaml';
        $expected1 = ("\nThe file does not exist at this path:\n{$path1}\n");

        $this->assertEquals($expected1, genDiff($path1, $path2));

        $path1 = "tests/fixtures/file1.yaml";
        $expected2 = ("\nThe file does not exist at this path:\n{$path2}\n");

        $this->assertEquals($expected2, genDiff($path1, $path2));

        $path2 = "tests/fixtures/file2.yaml";
        $expected3 = <<<EOT
        {
          - follow: false
            host: hexlet.io
          - proxy: 123.234.53.22
          - timeout: 50
          + timeout: 20
          + verbose: true
        }
        
        EOT;
        $this->assertEquals($expected3, genDiff($path1, $path2));
    }
}
