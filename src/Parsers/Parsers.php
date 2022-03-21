<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

/**
 * Parses Yaml file.
 *
 * @param String $path Path to the file
 * @return Array
 */
function readYamlFile(string $path): array
{
    $correctPath = str_starts_with($path, '/') ? $path : "{__DIR__}/../{$path}";
    $dataYaml = file_get_contents($correctPath);
    if ($dataYaml === false) {
        return [];
    }
    $resultArr = Yaml::parse($dataYaml);
    return $resultArr;
}

/**
 * Parses Json file.
 *
 * @param String $path Path to the file
 * @return Array
 */
function readJsonFile(string $path): array
{
    $correctPath = str_starts_with($path, '/') ? $path : "{__DIR__}/../{$path}";
    $dataJson = file_get_contents($correctPath);
    if ($dataJson === false) {
        return [];
    }
    $resultArr = json_decode($dataJson, true);
    return $resultArr;
}

/**
 * Parses file according to its type.
 *
 * @param String $path Path to the file
 * @return Array
 */
function parseFilePath(string $path): array
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
