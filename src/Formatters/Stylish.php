<?php

namespace Differ\Formatters\Stylish;

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

function outputStylish(mixed $data, int $level = 0): string
{
    $accumStr = "{";
    $endSpaces = str_repeat('    ', $level);
    $nextLevel = $level + 1;
    $innerSpaces = str_repeat('    ', $nextLevel);
    ksort($data);
    foreach ($data as $key => $item) {
        if (is_array($item)) {
            $innerBlock = outputStylish($item, $nextLevel);
            $accumStr .= "\n{$innerSpaces}{$key}: {$innerBlock}";
        } else {
            $data = json_decode($item, true);
            ['deleted' => $deleted, 'added' => $added] = $data;
        
            if (is_array($deleted)) {
                $deleted = arrToStr($deleted, $nextLevel);
            }
            if (is_array($added)) {
                $added = arrToStr($added, $nextLevel);
            }
            if ($deleted === $added) {
                $prefix = $innerSpaces;
                $accumStr .= "\n{$prefix}{$key}: {$deleted}";
            } elseif ($deleted === null) {
                $prefix = substr_replace($innerSpaces, '+', -2, 1);
                $accumStr .= "\n{$prefix}{$key}: {$added}";
            } elseif ($added === null) {
                $prefix = substr_replace($innerSpaces, '-', -2, 1);
                $accumStr .= "\n{$prefix}{$key}: {$deleted}";
            } else {
                $prefix1 = substr_replace($innerSpaces, '-', -2, 1);
                $prefix2 = substr_replace($innerSpaces, '+', -2, 1);
                $accumStr .= "\n{$prefix1}{$key}: {$deleted}\n{$prefix2}{$key}: {$added}";
            }
        }
    }
    $accumStr .= "\n{$endSpaces}}";
    return $accumStr;
}
