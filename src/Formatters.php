<?php

namespace Differ\Formatters\Formatters;

use function Differ\Formatters\Stylish\outputStylish;
use function Differ\Formatters\Plain\outputPlain;
use function Differ\Formatters\Json\outputJson;

function formatDiff(array $data, string $format)
{
    $resultStr = match ($format) {
        'plain' => outputPlain($data),
        'json' => outputJson($data),
        default => outputStylish($data),
    };
    return $resultStr;
}
