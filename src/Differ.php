<?php

namespace Differ\Differ;

use function Differ\Parsers\parseFilePath;
use function Differ\Formatters\Formatters\formatDiff;
use function Differ\Tree\createNode;
use function Differ\Tree\createNested;
use function Differ\Tree\createLeaf;
use function Functional\sort;

function accumDifference(array $firstData, array $secondData, string $formatter = 'stylish'): array
{
    $keys = array_unique(array_merge(array_keys($firstData), array_keys($secondData)));
    $sortedKeys = sort($keys, fn ($left, $right) => strcmp($left, $right));
    $tree = array_map(function ($key) use ($firstData, $secondData) {
        $belongsFirst = array_key_exists($key, $firstData);
        $belongsSecond = array_key_exists($key, $secondData);
        if (!$belongsFirst) {
            $data = [$key, 'added', $secondData[$key]];
            $node = createLeaf(...$data);
        } elseif (!$belongsSecond) {
            $data = [$key, 'deleted', $firstData[$key]];
            $node = createLeaf(...$data);
        } elseif (is_array($firstData[$key]) && is_array($secondData[$key])) {
            $node = createNode($key, accumDifference($firstData[$key], $secondData[$key]));
        } elseif ($firstData[$key] !== $secondData[$key]) {
            $node = createNested($key, $firstData[$key], $secondData[$key]);
        } else {
            $node = createLeaf($key, 'unchanged', $firstData[$key]);
        }
        return $node;
    }, $sortedKeys);
    return $tree;
}

function genDiff(string $path1, string $path2, string $format = 'stylish'): string
{
    $arr1 = parseFilePath($path1);
    $arr2 = parseFilePath($path2);
    $difference = accumDifference($arr1, $arr2);
    $result = formatDiff($difference, $format);
    return $result;
}
