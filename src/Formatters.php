<?php

namespace Differ\Formatters\Formatters;

use function Differ\Formatters\Stylish\outputStylish;
use function Differ\Formatters\Plain\outputPlain;
use function Differ\Formatters\Json\outputJson;

/**
 * Turns a difference tree into a string according to a given format.
 *
 * @param Array $data Data to stringify
 * @return String
 */
function formatDiff(array $data, string $format): string
{
    $result = match ($format) {
        'plain' => outputPlain($data),
        'json' => outputJson($data),
        default => outputStylish($data),
    };
    return $result;
}
