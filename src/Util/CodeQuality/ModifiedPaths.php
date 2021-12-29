<?php

declare(strict_types=1);

namespace App\Util\CodeQuality;

final class ModifiedPaths
{
    public static function createFromVCSStatus(): array
    {
        exec("git status --porcelain | grep -e '^[AM]\\(.*\\).php$' | cut -c 3- | tr '\n' ' '", $output, $returnCode);

        if (!\array_key_exists(0, $output)) {
            return [];
        }

        return array_filter(explode(' ', $output[0]));
    }
}
