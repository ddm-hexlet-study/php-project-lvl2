<?php

namespace Differ\Differ;

use function Differ\Parsers\parseFilePath;

function fixBooleanValue(mixed $value): string
{
    $stringValue = '';
    if (is_bool($value)) {
        $stringValue = $value === true ? 'true' : 'false';
    } else {
        $stringValue = $value;
    }
    return $stringValue;
}
function outputFormat(array $data): string
{
    $accumStr = '';
    foreach ($data as $key => [$deleted, $added]) {
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
function genDiff(string $path1, string $path2)
{
    $array1 = parseFilePath($path1);
    $array2 = parseFilePath($path2);
    $accumArr = [];
    foreach ($array1 as $key => $val) {
        $stringValue = fixBooleanValue($val);
        if (array_key_exists($key, $array2)) {
            $accumArr[$key] = [$stringValue, $array2[$key]];
        } else {
            $accumArr[$key] = [$stringValue, null];
        }
    }
    foreach ($array2 as $key => $val) {
        $stringValue = fixBooleanValue($val);
        if (!array_key_exists($key, $array1)) {
            $accumArr[$key] = [null, $stringValue];
        }
    }
    ksort($accumArr);
    $resultStr = outputFormat($accumArr);
    return $resultStr;
}
