<?php

namespace Differ\Cli;

use Docopt;

use function Differ\Differ\genDiff;

function runUtility(): string
{
    $doc = <<<DOC
    Generate diff

    Usage:
      gendiff (-h|--help)
      gendiff (-v|--version)
      gendiff [--format <fmt>] <firstFile> <secondFile>

    Options:
      -h --help                     Show this screen
      -v --version                  Show version
      --format <fmt>                Report format [default: stylish]
    DOC;

    $params = Docopt::handle($doc);
    $format = $params['--format'];
    $pathToFile1 = $params['<firstFile>'];
    $pathToFile2 = $params['<secondFile>'];

    return genDiff($pathToFile1, $pathToFile2, $format);
}
