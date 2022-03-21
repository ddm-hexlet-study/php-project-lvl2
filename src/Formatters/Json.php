<?php

namespace Differ\Formatters\Json;

use function Differ\Tree\getName;
use function Differ\Tree\getTypeNode;
use function Differ\Tree\getChildrenNode;
use function Differ\Tree\getChildrenNested;
use function Differ\Tree\getStatusLeaf;
use function Differ\Tree\getValueLeaf;

const DELETED_PREFIX = '- ';
const ADDED_PREFIX = '+ ';
const UNCHANGED_PREFIX = '';

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
 * Performs a leaf of a tree as an array for function json_encode.
 *
 * @param Array $leaf Data to stringify
 * @return Array
 */
function performLeaf(array $leaf): array
{
    $prefix = getPrefix($leaf);
    $name = $prefix . getName($leaf);
    $value = getValueLeaf($leaf);
    $result = array($name => $value);
    return $result;
}

/**
 * Performs a nested node of a tree as an array for function json_encode.
 *
 * @param Array $nested Data to stringify
 * @return Array
 */
function performNested(array $nested): array
{
    $name = getName($nested);
    ['deleted' => $deletedValue, 'added' => $addedValue] = getChildrenNested($nested);
    $deletedName = DELETED_PREFIX . $name;
    $addedName = ADDED_PREFIX . $name;
    $result = array($deletedName => $deletedValue, $addedName => $addedValue);
    return $result;
}

/**
 * Builds a tree of difference as an array for function json_encode.
 *
 * @param Array $data Data to stringify
 * @return Array
 */
function performTree(array $data): array
{
    $accum = array_map(function ($item) {
        $type = getTypeNode($item);
        if ($type === 'node') {
            $name = getName($item);
            $children = getChildrenNode($item);
            $value = performTree($children);
            $result = array($name => $value);
            $mergedResult = array_map(function ($item) {
                return call_user_func_array('array_merge', $item);
            }, $result);
            return $mergedResult;
        } elseif ($type === 'leaf') {
            return performLeaf($item);
        } else {
            return performNested($item);
        }
    }, $data);
    return $accum;
}

/**
 * Returns final result.
 *
 * @param Array $difference Difference between two sets of data
 * @return String
 */
function outputJson(array $difference): string
{
    $tree = performTree($difference);
    $flattenedTree = array_merge(...$tree);
    $result = json_encode($flattenedTree);
    return $result;
}
