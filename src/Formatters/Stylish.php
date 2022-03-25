<?php

namespace Differ\Formatters\Stylish;

use function Differ\Tree\getName;
use function Differ\Tree\getTypeNode;
use function Differ\Tree\getChildrenNode;
use function Differ\Tree\getChildrenNested;
use function Differ\Tree\getStatusLeaf;
use function Differ\Tree\getValueLeaf;

const INDENT_LENGTH = 4;
const DELETED_PREFIX = '  - ';
const ADDED_PREFIX = '  + ';
const UNCHANGED_PREFIX = '    ';

/**
 * Returns prefix due to the status of a leaf.
 *
 * @param Array $leaf Variable that contains a leaf
 * @return String
 */
function getPrefix(array $leaf): string
{
    $status = getStatusLeaf($leaf);
    $prefix = match ($status) {
        'added' => ADDED_PREFIX,
        'deleted' => DELETED_PREFIX,
        default => UNCHANGED_PREFIX,
    };
    return $prefix;
}

/**
 * Returns indent due to the depth of iteration.
 *
 * @param Int $level Depth of iteration
 * @return String
 */
function getIndent(int $level): string
{
    return str_repeat(' ', $level * INDENT_LENGTH);
}

/**
 * Turns boolean value into string.
 *
 * @param Mixed $value Value
 * @return String
 */
function stringifyBool(mixed $value): string
{
    if (!isset($value)) {
        $stringValue = "null";
    } elseif (is_bool($value)) {
        $stringValue = $value === true ? 'true' : 'false';
    } else {
        $stringValue = $value;
    }
    return $stringValue;
}


/**
 * Turns array value into string.
 *
 * @param Array $data Data to stringify
 * @param Int $level Depth of iteration
 * @return String
 */
function stringifyNonScalar(array $data, int $level): string
{
    $keys = array_keys($data);
    $outerIndent = getIndent($level);
    $accum = array_map(function ($item) use ($data, $level) {
        $innerIndent = getIndent($level + 1);
        if (is_array($data[$item])) {
            $value = stringifyNonScalar($data[$item], $level + 1);
            return "{$innerIndent}{$item}: {$value}";
        } else {
            $value = stringifyBool($data[$item]);
            return "{$innerIndent}{$item}: {$value}";
        }
    }, $keys);
    $result = implode("\n", ["{", ...$accum, "{$outerIndent}}"]);
    return $result;
}

/**
 * Performs a leaf of a tree as a string.
 *
 * @param Array $leaf Data to stringify
 * @param Int $level Depth of iteration
 * @return String
 */
function performLeaf(array $leaf, int $level): string
{
    $name = getName($leaf);
    $prefix = getPrefix($leaf);
    $value = getValueLeaf($leaf);
    $res = is_array($value) ? stringifyNonScalar($value, $level + 1) : stringifyBool($value);
    $indent = getIndent($level);
    $performance = "{$indent}{$prefix}{$name}: {$res}";
    return $performance;
}

/**
 * Performs a nested node of a tree as a string.
 *
 * @param Array $nested Data to stringify
 * @param Int $level Depth of iteration
 * @return String
 */
function performNested(array $nested, int $level): string
{
    $indent = getIndent($level);
    $name = getName($nested);
    ['deleted' => $deleted, 'added' => $added] = getChildrenNested($nested);
    $stringifiedDel = is_array($deleted) ? stringifyNonScalar($deleted, $level + 1) : stringifyBool($deleted);
    $performanceDel = $indent . DELETED_PREFIX . $name . ": " . $stringifiedDel;
    $stringifiedAdd = is_array($added) ? stringifyNonScalar($added, $level + 1) : stringifyBool($added);
    $performanceAdd = $indent . ADDED_PREFIX . $name . ": " . $stringifiedAdd;
    $result = "{$performanceDel}\n{$performanceAdd}";
    return $result;
}

/**
 * Builds a tree of difference according to the stylish output.
 *
 * @param Array $data Data to stringify
 * @param Int $level Depth of iteration
 * @return String
 */
function performTree(array $data, int $level = 0): string
{
    $indent = getIndent($level);
    $accum = array_map(function ($item) use ($level, $indent) {
        $type = getTypeNode($item);
        if ($type === 'node') {
            $name = getName($item);
            $children = getChildrenNode($item);
            $value = performTree($children, $level + 1);
            return $indent . UNCHANGED_PREFIX . $name . ": " . $value;
        } elseif ($type === 'leaf') {
            return performLeaf($item, $level);
        } else {
            return performNested($item, $level);
        }
    }, $data);
    $result = implode("\n", ["{", ...$accum, "{$indent}}"]);
    return $result;
}

/**
 * Returns final result.
 *
 * @param Array $difference Difference between two sets of data
 * @return String
 */
function outputStylish(array $difference): string
{
    $tree = performTree($difference);
    return $tree;
}
