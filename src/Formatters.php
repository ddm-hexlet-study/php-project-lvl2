<?php

namespace Differ\Formatters\Formatters;

use function Differ\Formatters\Stylish\outputStylish;
use function Differ\Formatters\Plain\outputPlain;

function formatDiff(array $data, string $format)
{
    $resultStr = match ($format) {
        'plain' => outputPlain($data),
        default => outputStylish($data),
    };
    return $resultStr;
}
