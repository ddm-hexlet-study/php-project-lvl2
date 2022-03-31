<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

/**
 * Parses data according to its type.
 *
 * @param Array $fileContent Data to be parsed. Contains keys 'type' and 'data'
 * @param String $type Type of data
 * @return Array
 */
function parse(array $fileContent): array
{
    ['type' => $type, 'data' => $data] = $fileContent;
    switch ($type) {
        case 'json':
            return json_decode($data, true);
        case 'yaml':
        case 'yml':
            return Yaml::parse($data);
        default:
            throw new \Exception("Data is not readable");
    }
}
