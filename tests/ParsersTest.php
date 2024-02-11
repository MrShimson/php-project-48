<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Parsers\getData;

class ParsersTest extends TestCase
{
    private $pathEmptyXml;
    private $pathEmptyJson;
    private $pathEmptyYaml;

    private $existanceMessage;
    private $extensionMessage;
    private $emptinessMessageJson;
    private $emptinessMessageYaml;

    public function getFixtureFullPath($dir, $name, $ext)
    {
        $urlParts = [__DIR__, 'fixtures', $dir, $name . '.' . $ext];
        return realpath(implode('/', $urlParts));
    }

    public function setUp(): void
    {
        $this->pathEmptyXml = $this->getFixtureFullPath('empty', 'empty', 'xml');
        $this->pathEmptyJson = $this->getFixtureFullPath('empty', 'empty', 'json');
        $this->pathEmptyYaml = $this->getFixtureFullPath('empty', 'empty', 'yaml');

        $this->existanceMessage = "\nThis file does not exist at this path:\nempty.xml\n";
        $this->extensionMessage = "\nThis file has invalid extension:\n.xml\n";
        $this->emptinessMessageJson = "\nThis file is empty:\nempty.json\n";
        $this->emptinessMessageYaml = "\nThis file is empty:\nempty.yaml\n";
    }

    public function testException(): void
    {
        //Тест на существования файла
        $this->expectExceptionMessage($this->existanceMessage);

        $path = 'empty.xml';
        getData($path);
        //

        //Тест на корректность расширения
        $this->expectExceptionMessage($this->extensionMessage);

        $path = $this->pathEmptyXml;
        getData($path);
        //

        //Тесты на наличие в файлах каких-либо данных
        $this->expectExceptionMessage($this->emptinessMessageJson);

        $path = $this->pathEmptyJson;
        getData($path);

        $this->expectExceptionMessage($this->emptinessMessageYaml);

        $path = $this->pathEmptyYaml;
        getData($path);
        //
    }

    public function testGetData(): void
    {
        //Тесты на корректность парсинга данных из файлов
        $path1 = $this->getFixtureFullPath('nested', 'file1', 'json');
        $path2 = $this->getFixtureFullPath('nested', 'file1', 'yaml');

        $file = [
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

        $this->assertEquals($file, getData($path1));
        $this->assertEquals($file, getData($path2));
        //
    }
}
