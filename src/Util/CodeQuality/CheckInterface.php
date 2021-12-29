<?php

declare(strict_types=1);

namespace App\Util\CodeQuality;

interface CheckInterface
{
    public function getName(): string;

    public function execute(array $paths): CheckResult;
}
