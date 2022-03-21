<?php

namespace Differ\Formatters\Plain;

use function Differ\Tree\getName;
use function Differ\Tree\getType;
use function Differ\Tree\getChildrenNode;
use function Differ\Tree\getChildrenNested;
use function Differ\Tree\getStatusLeaf;
use function Differ\Tree\getValueLeaf;
use function Functional\flatten;

function stringify(mixed $value): string
{
    if (is_array($value)) {
        $result = "[complex value]";
        return $result;
    }
    if (!isset($value)) {
        $stringValue = "null";
    } elseif (is_bool($value)) {
        $stringValue = $value === true ? 'true' : 'false';
    } else {
        $stringValue = $value;
    }
    $result = match ($stringValue) {
        'true', 'false', 'null' => $stringValue,
        default => "'{$stringValue}'"
    };
    return $result;
}

function performNested(mixed $nested, string $property)
{
    ['deleted' => $deleted, 'added' => $added] = getChildrenNested($nested);
    $valueOld = stringify($deleted);
    $valueNew = stringify($added)   ;
    return "Property '{$property}' was updated. From {$valueOld} to {$valueNew}";
}

function performLeaf(mixed $leaf, string $property)
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

function performTree(mixed $data, string $property = '')
{
    $accum = array_map(function ($item) use ($property) {
        $name = getName($item);
        $type = getType($item);
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
    $result = array_filter($flatResult, fn($item) => !empty($item));
    return $result;
}

function outputPlain(mixed $difference): string
{
    $tree = performTree($difference);
    return implode("\n", $tree);
}
