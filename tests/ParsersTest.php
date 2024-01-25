<?php

namespace DifferenceCalculator\Tests;

use PHPUnit\Framework\TestCase;

use function DifferenceCalculator\Parsers\getData;

class ParsersTest extends TestCase
{
    public function testGetData(): void
    {
        //Тест на существования файла
        $path = 'empty.xml';
        $expected = "\nThis file does not exist at this path:\nempty.xml\n";

        try {
            getData($path);
        } catch (\Exception $error) {
            $error = $error->getMessage();
        }

        $this->assertEquals($expected, $error);
        //

        //Тест на корректность расширения
        $path = 'tests/fixtures/empty/empty.xml';
        $expected = "\nThis file has invalid extension:\n.xml\n";

        try {
            getData($path);
        } catch (\Exception $error) {
            $error = $error->getMessage();
        }

        $this->assertEquals($expected, $error);
        //

        //Тесты на наличие в файлах каких-либо данных
        $path1 = 'tests/fixtures/empty/empty.json';
        $path2 = 'tests/fixtures/empty/empty.yaml';

        try {
            getData($path1);
        } catch (\Exception $error) {
            $error1 = $error->getMessage();
        }

        try {
            getData($path2);
        } catch (\Exception $error) {
            $error2 = $error->getMessage();
        }

        $expected1 = ("\nThis file is empty:\nempty.json\n");
        $expected2 = ("\nThis file is empty:\nempty.yaml\n");

        $this->assertEquals($expected1, $error1);
        $this->assertEquals($expected2, $error2);
        //

        //Тесты на корректность парсинга данных из файлов
        $path3 = 'tests/fixtures/flat/file1.json';
        $path4 = 'tests/fixtures/flat/file1.yaml';

        $file1 = [
            'host' => 'hexlet.io',
            'timeout' => '50',
            'proxy' => '123.234.53.22',
            'follow' => 'false'
        ];

        $this->assertEquals($file1, getData($path3));
        $this->assertEquals($file1, getData($path4));

        $path5 = 'tests/fixtures/nested/file1.json';
        $path6 = 'tests/fixtures/nested/file1.yaml';

        $file2 = [
            'common' => [
                'setting1' => 'Value 1',
                'setting2' => '200',
                'setting3' => 'true',
                'setting6' => [
                    'key' => 'value',
                    'doge' => [
                        'wow' => ''
                    ]
                ]
            ],
            'group1' => [
                'baz' => 'bas',
                'foo' => 'bar',
                'nest' => [
                    'key' => 'value'
                ]
            ],
            'group2' => [
                'abc' => '12345',
                'deep' => [
                    'id' => '45'
                ]
            ]
        ];

        $this->assertEquals($file2, getData($path5));
        $this->assertEquals($file2, getData($path6));
        //
    }
}
