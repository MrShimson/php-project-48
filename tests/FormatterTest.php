<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Formatter\formatDiffTree;

class FormatterTest extends TestCase
{
    public function testException(): void
    {
        $this->expectExceptionMessage("Wrong format 'AwEsOmE'");

        formatDiffTree([], 'AwEsOmE');
    }
}
