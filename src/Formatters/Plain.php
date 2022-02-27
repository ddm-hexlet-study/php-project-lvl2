<?php

namespace Differ\Formatters\Plain;

function arrToStr(array $arr, int $level = 1)
{
    $res = "{";
    $endSpaces = str_repeat('    ', $level);
    $nextLevel = $level + 1;
    $innerSpaces = str_repeat('    ', $nextLevel);
    foreach ($arr as $key => $val) {
        if (!is_array($val)) {
            $res .= "\n{$innerSpaces}{$key}: {$val}";
        } else {
            $res .= "\n{$innerSpaces}{$key}: " . arrToStr($val, $nextLevel);
        }
    }
    $res .= "\n{$endSpaces}}";
    return $res;
}
function correctValue(mixed $value)
{
    if (is_array($value)) {
        $result = "[complex value]";
    } else {
        $result = match($value) {
            'true', 'false', 'null' => $value, default => "'{$value}'"
        };
    }
    return $result;
}
function outputPlain(mixed $data, string $property = ''): string
{
    $accumStr = "";
    ksort($data);
    foreach ($data as $key => $item) {
        $prop = $property === '' ? $key : "{$property}.{$key}";
        if (is_array($item)) {
            $innerBlock = outputPlain($item, $prop);
            $accumStr .= "{$innerBlock}";
        } else {
            $data = json_decode($item, true);
            ['deleted' => $deleted, 'added' => $added] = $data;
            if ($added === null) {
                $accumStr .= "Property '{$prop}' was removed\n";
            } elseif ($deleted === null) {
                $value = correctValue($added);
                $accumStr .= "Property '{$prop}' was added with value: {$value}\n";
            } elseif ($added !== $deleted) {
                $valueOld = correctValue($deleted);
                $valueNew = correctValue($added);
                $accumStr .= "Property '{$prop}' was updated. From {$valueOld} to {$valueNew}\n";
            }
        }
    }
    return $accumStr;
}
