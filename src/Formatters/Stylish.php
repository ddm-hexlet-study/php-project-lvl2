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
 * Turns mixed type value into a string.
 *
 * @param Mixed $data Data to stringify
 * @param Int $level Depth of iteration
 * @return String
 */
function stringify(mixed $data, int $level = 0): string
{
    $type = gettype($data);
    switch ($type) {
        case 'NULL':
            $stringValue = "null";
            break;
        case 'boolean':
            $stringValue = $data === true ? 'true' : 'false';
            break;
        case 'array':
            $keys = array_keys($data);
            $outerIndent = getIndent($level + 1);
            $accum = array_map(function ($item) use ($data, $level) {
                $innerIndent = getIndent($level + 2);
                $nextLevel = is_array($data[$item]) ? $level + 1 : $level;
                $value = stringify($data[$item], $nextLevel);
                return "{$innerIndent}{$item}: {$value}";
            }, $keys);
            $stringValue = implode("\n", ["{", ...$accum, "{$outerIndent}}"]);
            break;
        default:
            $stringValue = $data;
    }
    return $stringValue;
}

/**
 * Builds a tree of difference according to the stylish output.
 *
 * @param Array $data Data to stringify
 * @param Int $level Depth of iteration
 * @return String
 */
function performStylish(array $data, int $level = 0): string
{
    $indent = getIndent($level);
    $accum = array_map(function ($item) use ($level, $indent) {
        $name = $item['name'];
        $type = $item['type'];
        switch ($type) {
            case 'nested':
                $children = $item['children'];
                $value = performStylish($children, $level + 1);
                return "{$indent}    {$name}: {$value}";
            case 'added':
                $value = stringify($item['value'], $level);
                return "{$indent}  + {$name}: {$value}";
            case 'deleted':
                $value = stringify($item['value'], $level);
                return "{$indent}  - {$name}: {$value}";
            case 'unchanged':
                $value = stringify($item['value'], $level);
                return "{$indent}    {$name}: {$value}";
            case 'changed':
                ['old' => $oldValue, 'new' => $newValue] = $item['value'];
                $stringifiedOld = stringify($oldValue, $level);
                $performanceOld = "{$indent}  - {$name}: {$stringifiedOld}";
                $stringifiedNew = stringify($newValue, $level);
                $performanceNew = "{$indent}  + {$name}: {$stringifiedNew}";
                return "{$performanceOld}\n{$performanceNew}";
            default:
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
    return performStylish($difference);
}
