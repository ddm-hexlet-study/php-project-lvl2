<?php

namespace Differ\Differ;

function genDiff(string $path1, string $path2)
{
    $correctPath1 = str_starts_with($path1, '/') ? $path1 : "{__DIR__}/../{$path1}";
    $correctPath2 = str_starts_with($path2, '/') ? $path2 : "{__DIR__}/../{$path2}";
    $json1 = file_get_contents($correctPath1);
    $json2 = file_get_contents($correctPath2);
    $array1 = json_decode($json1, true);
    $array2 = json_decode($json2, true);
    $accumArr = [];
    foreach ($array1 as $key => $val) {
        if (is_bool($val)) {
            $stringValue = $val === true ? 'true' : 'false';
        } else {
            $stringValue = $val;
        }
        if (array_key_exists($key, $array2)) {
            $accumArr[$key] = [$stringValue, $array2[$key]];
        } else {
            $accumArr[$key] = [$stringValue, null];
        }
    }
    foreach ($array2 as $key => $val) {
        if (is_bool($val)) {
            $stringValue = $val === true ? 'true' : 'false';
        } else {
            $stringValue = $val;
        }
        if (!array_key_exists($key, $array1)) {
            $accumArr[$key] = [null, $stringValue];
        }
    }
    ksort($accumArr);
    $accumStr = '';
    foreach ($accumArr as $key => [$deleted, $added]) {
        if ($deleted === $added) {
            $accumStr .= "    {$key}: {$deleted}\n";
        } elseif ($deleted === null) {
            $accumStr .= "  + {$key}: {$added}\n";
        } elseif ($added === null) {
            $accumStr .= "  - {$key}: {$deleted}\n";
        } else {
            $accumStr .= "  - {$key}: {$deleted}\n  + {$key}: {$added}\n";
        }
    }
    $accumStr = "{\n{$accumStr}}\n";
    return $accumStr;
}

