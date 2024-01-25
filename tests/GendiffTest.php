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

        $path3 = "tests/fixtures/flat/file1.yaml";
        $path4 = "tests/fixtures/flat/file2.yaml";

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
        $this->assertEquals($expected1, genDiff($path3, $path4));

        $path5 = "tests/fixtures/nested/file1.json";
        $path6 = "tests/fixtures/nested/file2.json";

        $path7 = "tests/fixtures/nested/file1.yaml";
        $path8 = "tests/fixtures/nested/file2.yaml";
        
        $expected2 = <<<EOT
        {
            common: {
              + follow: false
                setting1: Value 1
              - setting2: 200
              - setting3: true
              + setting3: null
              + setting4: blah blah
              + setting5: {
                    key5: value5
                }
                setting6: {
                    doge: {
                      - wow: 
                      + wow: so much
                    }
                    key: value
                  + ops: vops
                }
            }
            group1: {
              - baz: bas
              + baz: bars
                foo: bar
              - nest: {
                    key: value
                }
              + nest: str
            }
          - group2: {
                abc: 12345
                deep: {
                    id: 45
                }
            }
          + group3: {
                deep: {
                    id: {
                        number: 45
                    }
                }
                fee: 100500
            }
        }

        EOT;

        $this->assertEquals($expected2, genDiff($path5, $path6));
        $this->assertEquals($expected2, genDiff($path7, $path8));
    }
}
