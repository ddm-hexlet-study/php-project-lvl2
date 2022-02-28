<?php

namespace Differ\Formatters\Json;

function diffToArr(mixed $data)
{
    $accum = [];
    ksort($data);
    foreach ($data as $key => $item) {
        if (is_array($item)) {
            $accum[$key] = diffToArr($item);
        } else {
            $data = json_decode($item, true);
            ['deleted' => $deleted, 'added' => $added] = $data;
            if ($deleted === $added) {
                $accum[$key] = $deleted;
            } elseif ($deleted === null) {
                $accum["+ {$key}"] = $added;
            } elseif ($added === null) {
                $accum["- {$key}"] = $deleted;
            } else {
                $accum["- {$key}"] = $deleted;
                $accum["+ {$key}"] = $added;
            }
        }
    }
    $res = json_encode($accum);
    //print_r($res);
    return $accum;
}

function outputJson(array $data)
{
    $arr = diffToArr($data);
    $res = json_encode($arr);
    //print_r($res);
    return $res;
}
