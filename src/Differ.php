<?php

namespace Differ\Differ;

use function Differ\Parsers\parse;
use function Differ\Formatters\Formatters\formatDiff;
use function Functional\sort;

/**
 * Returns parsed content of a file as an array.
 *
 * @param String $path Path to the file
 * @return Array
 */
function getFileContent(string $path): array
{
    $correctPath = realpath($path);
    if ($correctPath === false) {
        throw new \Exception("{$path} doesn't exist");
    }
    $data = file_get_contents($correctPath);
    $extension = pathinfo($correctPath, PATHINFO_EXTENSION);
    $result = ['type' => $extension, 'data' => $data];
    return $result;
}

/**
 * Calculates difference between two arrays.
 *
 * @param Array $firstData First array
 * @param Array $secondData Second array
 * @return Array
 */
function accumDifference(array $firstData, array $secondData): array
{
    $keys = array_unique(array_merge(array_keys($firstData), array_keys($secondData)));
    $sortedKeys = sort($keys, fn ($left, $right) => strcmp($left, $right));
    $tree = array_map(function ($key) use ($firstData, $secondData) {
        $belongsFirst = array_key_exists($key, $firstData);
        $belongsSecond = array_key_exists($key, $secondData);
        if (!$belongsFirst) {
            $node = ['name' => $key, 'type' => 'added', 'value' => $secondData[$key]];
        } elseif (!$belongsSecond) {
            $node = ['name' => $key, 'type' => 'deleted', 'value' => $firstData[$key]];
        } elseif (is_array($firstData[$key]) && is_array($secondData[$key])) {
            $node = ['name' => $key, 'type' => 'nested', 'children' => accumDifference($firstData[$key], $secondData[$key])];
        } elseif ($firstData[$key] !== $secondData[$key]) {
            $node = ['name' => $key, 'type' => 'changed', 'value' => ['old' => $firstData[$key], 'new' => $secondData[$key]]];
        } else {
            $node = ['name' => $key, 'type' => 'unchanged', 'value' => $firstData[$key]];
        }
        return $node;
    }, $sortedKeys);
    return $tree;
}

/**
 * Outputs difference between two json or yaml files.
 *
 * @param String $path1 Path to the first file
 * @param String $path2 Path to the second file
 * @param String $format Output format
 * @return String
 */
function genDiff(string $path1, string $path2, string $format = 'stylish'): string
{
    $arr1 = parse(getFileContent($path1));
    $arr2 = parse(getFileContent($path2));
    $difference = accumDifference($arr1, $arr2);
    $result = formatDiff($difference, $format);
    return $result;
}
