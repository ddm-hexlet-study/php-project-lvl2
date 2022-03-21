<?php

namespace Differ\Formatters\Plain;

use function Differ\Tree\getName;
use function Differ\Tree\getTypeNode;
use function Differ\Tree\getChildrenNode;
use function Differ\Tree\getChildrenNested;
use function Differ\Tree\getStatusLeaf;
use function Differ\Tree\getValueLeaf;
use function Functional\flatten;

function stringify(mixed $value): string
{
    if (is_array($value)) {
        $stringValue = "[complex value]";
        return $stringValue;
    }
    if (!isset($value)) {
        $stringValue = 'null';
        return $stringValue;
    } elseif (is_bool($value)) {
        $stringValue = $value === true ? 'true' : 'false';
        return $stringValue;
    } else {
        $stringValue = $value;
    }
    $type = gettype($stringValue);
    $result = match ($type) {
        "string" => "'{$stringValue}'",
        default => $stringValue
    };
    return $result;
}

function performNested(array $nested, string $property): string
{
    ['deleted' => $deleted, 'added' => $added] = getChildrenNested($nested);
    $valueOld = stringify($deleted);
    $valueNew = stringify($added)   ;
    return "Property '{$property}' was updated. From {$valueOld} to {$valueNew}";
}

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
    $result = array_filter($flatResult, fn($item) => !empty($item));
    return $result;
}

function outputPlain(array $difference): string
{
    $tree = performTree($difference);
    return implode("\n", $tree);
}
