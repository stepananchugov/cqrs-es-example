<?php

declare(strict_types=1);

namespace App\Application\Seed;

interface SeedInterface
{
    public function provideCommands(): \Generator;
}
