<?php

namespace Differ\Differ;

use stdClass;

use function Differ\Parsers\parseFilePath;
use function Differ\Formatters\Formatters\formatDiff;

function fixValueType(mixed $value): string
{
    $stringValue = '';
    if (!isset($value)) {
        $stringValue = "null";
    } elseif (is_bool($value)) {
        $stringValue = $value === true ? 'true' : 'false';
    } else {
        $stringValue = $value;
    }
    return $stringValue;
}

function objToArr(object $obj)
{
    $tmp = (array) $obj;
    $res = [];
    $res = array_map(function ($item) {
        if (is_object($item)) {
            return objToArr($item);
        } else {
            return $item;
        }
    }, $tmp);
    return $res;
}

function accumDifference(object $obj1, object $obj2, string $formatter = 'stylish')
{
    $accum = [];
    foreach ($obj1 as $key => $val) {
        if (property_exists($obj2, $key)) {
            if (is_object($obj1->$key) && is_object($obj2->$key)) {
                $accum[$key] = accumDifference($obj1->$key, $obj2->$key);
            } else {
                $value1 = is_object($obj1->$key) ? objToArr($obj1->$key) : fixValueType($obj1->$key);
                $value2 = is_object($obj2->$key) ? objToArr($obj2->$key) : fixValueType($obj2->$key);
                $accum[$key] = json_encode(['deleted' => $value1, 'added' => $value2]);
            }
        } else {
            $value1 = is_object($obj1->$key) ? objToArr($obj1->$key) : fixValueType($obj1->$key);
            $accum[$key] = json_encode(['deleted' => $value1, 'added' => null]);
        }
    }
    foreach ($obj2 as $key => $val) {
        if (!property_exists($obj1, $key)) {
            $value2 = is_object($obj2->$key) ? objToArr($obj2->$key) : fixValueType($obj2->$key);
            $accum[$key] = json_encode(['deleted' => null, 'added' => $value2]);
        }
    }
    return $accum;
}

function genDiff(string $path1, string $path2, string $format = 'stylish')
{
    $arr1 = parseFilePath($path1);
    $arr2 = parseFilePath($path2);
    $difference = accumDifference($arr1, $arr2);
    $result = formatDiff($difference, $format);
    //print_r($result);
    return $result;
}
