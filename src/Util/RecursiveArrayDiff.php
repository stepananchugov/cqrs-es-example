<?php

declare(strict_types=1);

namespace App\Util;

class RecursiveArrayDiff
{
    public static function create(array $left, array $right): array
    {
        $result = [];

        foreach ($left as $key => $value) {
            if (\is_array($value)) {
                if (!isset($right[$key])) {
                    $result[$key] = $value;
                } elseif (!\is_array($right[$key])) {
                    $result[$key] = $value;
                } else {
                    $new_diff = self::create($value, $right[$key]);

                    if (0 !== \count($new_diff)) {
                        $result[$key] = $new_diff;
                    }
                }
            } elseif ((!isset($right[$key]) || $right[$key] !== $value) && !(null === $right[$key] && null === $value)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
