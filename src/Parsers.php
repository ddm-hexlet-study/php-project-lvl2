<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

/**
 * Returns content of a file.
 *
 * @param String $path Path to the file
 * @return String
 */
function getContent(string $path): string
{
    $correctPath = str_starts_with($path, '/') ? $path : __DIR__ . "/../{$path}";
    if (!file_exists($correctPath)) {
        throw new \Exception("{$correctPath} doesn't exist");
    }
    $data = file_get_contents($correctPath);
    return $data;
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
        $data = getContent($path);
        switch ($fileExtension) {
            case '.json':
                return json_decode($data, true);
            case '.yaml':
            case '.yml':
                return Yaml::parse($data);
            default:
                throw new \Exception("{$path} is not readable");
        }
    };
        return $parsedData($fileExtension);
}
