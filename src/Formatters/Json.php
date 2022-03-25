<?php

namespace Differ\Formatters\Json;

/**
 * Returns final result.
 *
 * @param Array $difference Difference between two sets of data
 * @return String
 */
function outputJson(array $difference): string
{
    return json_encode($difference);
}
