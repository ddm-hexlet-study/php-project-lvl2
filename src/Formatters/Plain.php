<?php

namespace Differ\Formatters\Plain;

use function Functional\flatten;

/**
 * Turns boolean/string/int/array value into a string according to the plain output.
 *
 * @param Mixed $value Data to stringify
 * @return String
 */

function stringify(mixed $value): string
{
    $result = match (true) {
        is_array($value) => '[complex value]',
        !isset($value) => 'null',
        is_bool($value) => $value === true ? 'true' : 'false',
        is_string($value) => "'{$value}'",
        default => $value
    };
    return $result;
}

/**
 * Builds a tree of difference according to the stylish output.
 *
 * @param Array $data Data to stringify
 * @param String $property Full name of an item
 * @return Array
 */
function performTree(array $data, string $property = ''): array
{
    $accum = array_map(function ($item) use ($property) {
        $name = $item['name'];
        $type = $item['type'];
        $prop = $property === '' ? $name : "{$property}.{$name}";
        if ($type === 'nested') {
            $children = $item['children'];
            return performTree($children, $prop);
            //  return "{$indent}    {$name}: {$value}";
        } elseif ($type === 'added') {
            $value = stringify($item['value']);
            return "Property '{$prop}' was added with value: {$value}";
        } elseif ($type === 'deleted') {
            return "Property '{$prop}' was removed";
        } elseif ($type === 'unchanged') {
            return "";
        } elseif ($type === 'changed') {
            ['old' => $oldValue, 'new' => $newValue] = $item['value'];
            $stringifiedOld = stringify($oldValue);
            $stringifiedNew = stringify($newValue);
            return "Property '{$prop}' was updated. From {$stringifiedOld} to {$stringifiedNew}";
        } else {
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
    $tree = performTree($difference);
    return implode("\n", $tree);
}
