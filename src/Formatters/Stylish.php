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

function getPrefix(mixed $leaf): string
{
    $status = getStatusLeaf($leaf);
    $prefix = match ($status) {
        'added' => ADDED_PREFIX,
        'deleted' => DELETED_PREFIX,
        default => UNCHANGED_PREFIX,
    };
    return $prefix;
}

function getIndent(int $level): string
{
    return str_repeat(' ', $level * INDENT_LENGTH);
}

function stringifyBool(mixed $value): string
{
    $stringValue = '';
    if (!isset($value)) {
        $stringValue = "null";
    } elseif (is_bool($value)) {
        $stringValue = $value === true ? 'true' : 'false';
    } else {
        $stringValue = $value;
    }
    return $stringValue;
}

function stringifyNonScalar(mixed $node, $level): string
{
    $keys = array_keys($node);
    $outerIndent = getIndent($level);
    $accum = array_map(function ($item) use ($node, $level) {
        $innerIndent = getIndent($level + 1);
        if (is_array($node[$item])) {
            $value = stringifyNonScalar($node[$item], $level + 1);
            return "{$innerIndent}{$item}: {$value}";
        } else {
            $value = stringifyBool($node[$item]);
            return "{$innerIndent}{$item}: {$value}";
        }
    }, $keys);
    $result = implode("\n", ["{", ...$accum, "{$outerIndent}}"]);
    return $result;
}

function performLeaf(mixed $leaf, $level): string
{
    $name = getName($leaf);
    $prefix = getPrefix($leaf);
    $value = getValueLeaf($leaf);
    $res = is_array($value) ? stringifyNonScalar($value, $level + 1) : stringifyBool($value);
    $indent = getIndent($level);
    $performance = "{$indent}{$prefix}{$name}: {$res}";
    return $performance;
}

function performNested(mixed $nested, $level): string
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

function performTree(mixed $difference, int $level = 0): string
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
    }, $difference);
    $result = implode("\n", ["{", ...$accum, "{$indent}}"]);
    return $result;
}

function outputStylish(mixed $difference): string
{
    $tree = performTree($difference);
    return $tree;
}
