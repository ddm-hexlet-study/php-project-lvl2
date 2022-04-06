<?php

namespace Differ\Formatters\Stylish;

/**
 * Returns indent due to the depth of iteration.
 *
 * @param Int $level Depth of iteration
 * @return String
 */
function getIndent(int $level): string
{
    return str_repeat('    ', $level);
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
 * Turns mixed value data into string.
 *
 * @param Mixed $value Data to stringify
 * @param Int $level Depth of iteration
 * @return String
 */
function stringify(mixed $value, int $level): string
{
    $result = is_array($value) ? stringifyNonScalar($value, $level + 1) : stringifyBool($value);
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
        $name = $item['name'];
        $type = $item['type'];
        if ($type === 'nested') {
            $children = $item['children'];
            $value = performTree($children, $level + 1);
            return "{$indent}    {$name}: {$value}";
        } elseif ($type === 'added') {
            $value = stringify($item['value'], $level);
            return "{$indent}  + {$name}: {$value}";
        } elseif ($type === 'deleted') {
            $value = stringify($item['value'], $level);
            return "{$indent}  - {$name}: {$value}";
        } elseif ($type === 'unchanged') {
            $value = stringify($item['value'], $level);
            return "{$indent}    {$name}: {$value}";
        } elseif ($type === 'changed') {
            ['old' => $oldValue, 'new' => $newValue] = $item['value'];
            $stringifiedOld = stringify($oldValue, $level);
            $performanceOld = "{$indent}  - {$name}: {$stringifiedOld}";
            $stringifiedNew = stringify($newValue, $level);
            $performanceNew = "{$indent}  + {$name}: {$stringifiedNew}";
            return "{$performanceOld}\n{$performanceNew}";
        } else {
            throw new \Exception("Unknown node format");
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
