<?php

namespace Differ\Formatters\Plain;

use function Functional\flatten;

/**
 * Turns boolean/string/int/array value into a string according to the plain output.
 *
 * @param Mixed $data Data to stringify
 * @return String
 */

function stringify(mixed $data): string
{
    $type = gettype($data);
    switch ($type) {
        case 'NULL':
            return 'null';
        case 'boolean':
            return $data === true ? 'true' : 'false';
        case 'array':
            return '[complex value]';
        case 'string':
            return "'{$data}'";
        default:
            return $data;
    }
}

/**
 * Builds a tree of difference according to the stylish output.
 *
 * @param Array $data Data to stringify
 * @param Array $property Full name of an item
 * @return Array
 */
function performPlain(array $data, array $property = []): array
{
    $accum = array_map(function ($item) use ($property) {
        $name = $item['name'];
        $type = $item['type'];
        $nextLevelProperty = [...$property, $name];
        $stringifiedProperty = implode('.', $nextLevelProperty);
        switch ($type) {
            case 'nested':
                $children = $item['children'];
                return performPlain($children, $nextLevelProperty);
            case 'added':
                $value = stringify($item['value']);
                return "Property '{$stringifiedProperty}' was added with value: {$value}";
            case 'deleted':
                return "Property '{$stringifiedProperty}' was removed";
            case 'unchanged':
                return "";
            case 'changed':
                ['old' => $oldValue, 'new' => $newValue] = $item['value'];
                $stringifiedOld = stringify($oldValue);
                $stringifiedNew = stringify($newValue);
                return "Property '{$stringifiedProperty}' was updated. From {$stringifiedOld} to {$stringifiedNew}";
            default:
                throw new \Exception("Unknown node format");
        }
    }, $data);
    $flatResult = flatten($accum);
    $result = array_diff($flatResult, array(''));
    return $result;
}

/**
 * Returns final result.
 *
 * @param Array $difference Difference between two sets of data
 * @return String
 */
function outputPlain(array $difference): string
{
    $result = performPlain($difference);
    return implode("\n", $result);
}
