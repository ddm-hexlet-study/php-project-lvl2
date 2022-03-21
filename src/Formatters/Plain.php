<?php

namespace Differ\Formatters\Plain;

use function Differ\Tree\getName;
use function Differ\Tree\getTypeNode;
use function Differ\Tree\getChildrenNode;
use function Differ\Tree\getChildrenNested;
use function Differ\Tree\getStatusLeaf;
use function Differ\Tree\getValueLeaf;
use function Functional\flatten;

/**
 * Turns boolean/string/int/array value into a string according to the plain output.
 *
 * @param Mixed $value Data to stringify
 * @return String
 */

function stringify(mixed $value): string
{
    $result = match(true) {
        is_array($value) => '[complex value]',
        !isset($value) => 'null',
        is_bool($value) => $value === true ? 'true' : 'false',
        is_string($value) => "'{$value}'",
        default => $value
    };
    return $result;
}

/**
 * Performs a nested node of a tree as a string.
 *
 * @param Array $nested Data to stringify
 * @param String $property Full name of an item
 * @return String
 */
function performNested(array $nested, string $property): string
{
    ['deleted' => $deleted, 'added' => $added] = getChildrenNested($nested);
    $valueOld = stringify($deleted);
    $valueNew = stringify($added)   ;
    return "Property '{$property}' was updated. From {$valueOld} to {$valueNew}";
}

/**
 * Performs a leaf of a tree as a string.
 *
 * @param Array $leaf Data to stringify
 * @param String $property Full name of an item
 * @return String
 */
function performLeaf(array $leaf, string $property): string
{
    $status = getStatusLeaf($leaf);
    $value = getValueLeaf($leaf);
    $correctValue = stringify($value);
    $result = match ($status) {
        'added' => "Property '{$property}' was added with value: {$correctValue}",
        'deleted' => "Property '{$property}' was removed",
        default => "",
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
        $name = getName($item);
        $type = getTypeNode($item);
        $prop = $property === '' ? $name : "{$property}.{$name}";
        if ($type === 'node') {
            $children = getChildrenNode($item);
            return performTree($children, $prop);
        } elseif ($type === 'nested') {
            return performNested($item, $prop);
        } else {
            return performLeaf($item, $prop);
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
