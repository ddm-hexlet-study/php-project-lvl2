<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

use function Functional\map;

function readYamlFile(string $path)
{
    $resultArr = [];
    $correctPath = str_starts_with($path, '/') ? $path : "{__DIR__}/../{$path}";
    $dataYaml = file_get_contents($correctPath);
    $resultArr = Yaml::parse($dataYaml);
    return $resultArr;
}
function readJsonFile(string $path)
{
    $resultArr = [];
    $correctPath = str_starts_with($path, '/') ? $path : "{__DIR__}/../{$path}";
    $dataJson = file_get_contents($correctPath);
    $resultArr = json_decode($dataJson, true);
    return $resultArr;
}

function parseFilePath(string $path)
{
    $fileExtension = strrchr($path, '.');
    $parsedData = function ($fileExtension) use ($path) {
        if ($fileExtension === '.json') {
            return readJsonFile($path);
        } elseif ($fileExtension === '.yaml' || $fileExtension === '.yml') {
            return readYamlFile($path);
        }
    };
    return $parsedData($fileExtension);
}
