<?php

namespace DifferenceCalculator\Cli;

use Docopt;

use function DifferenceCalculator\Gendiff\genDiff;

/*function parseArgs(object $params): array
{
    $params = json_decode(json_encode($params), true);
    $args = $params['args'];
    return $args;
}*/

function runUtility()
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

    if ($params['--format'] === 'stylish') {
        $pathToFile1 = $params['<firstFile>'];
        $pathToFile2 = $params['<secondFile>'];

        print_r(genDiff($pathToFile1, $pathToFile2));
    }
}
