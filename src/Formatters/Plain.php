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
            $stringValue = 'null';
            break;
        case 'boolean':
            $stringValue = $data === true ? 'true' : 'false';
            break;
        case 'array':
            $stringValue = '[complex value]';
            break;
        case 'string':
            $stringValue = "'{$data}'";
            break;
        default:
            $stringValue = $data;
    }
    return $stringValue;
}

/**
 * Builds a tree of difference according to the stylish output.
 *
 * @param Array $data Data to stringify
 * @param String $property Full name of an item
 * @return Array
 */
function performPlain(array $data, string $property = ''): array
{
    $accum = array_map(function ($item) use ($property) {
        $name = $item['name'];
        $type = $item['type'];
        $updatedProperty = $property === '' ? $name : "{$property}.{$name}";
        switch ($type) {
            case 'nested':
                $children = $item['children'];
                return performPlain($children, $updatedProperty);
            case 'added':
                $value = stringify($item['value']);
                return "Property '{$updatedProperty}' was added with value: {$value}";
            case 'deleted':
                return "Property '{$updatedProperty}' was removed";
            case 'unchanged':
                return "";
            case 'changed':
                ['old' => $oldValue, 'new' => $newValue] = $item['value'];
                $stringifiedOld = stringify($oldValue);
                $stringifiedNew = stringify($newValue);
                return "Property '{$updatedProperty}' was updated. From {$stringifiedOld} to {$stringifiedNew}";
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
