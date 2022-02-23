<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function readYamlFile(string $path): array
{
    $resultArr = [];
    $correctPath = str_starts_with($path, '/') ? $path : "{__DIR__}/../{$path}";
    $dataYaml = file_get_contents($correctPath);
    $resultArr = Yaml::parse($dataYaml);
    return $resultArr;
}
function readJsonFile(string $path): array
{
    $resultArr = [];
    $correctPath = str_starts_with($path, '/') ? $path : "{__DIR__}/../{$path}";
    print_r($correctPath);
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
    //print_r($parsedData);
    return $parsedData($fileExtension);
}
