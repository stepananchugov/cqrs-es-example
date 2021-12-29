<?php

declare(strict_types=1);

namespace App\Util\Command;

use App\Util\CodeQuality\ApiBCBreakCheck;
use App\Util\CodeQuality\ShellCommandCheck;

final class PreCommitCommand extends AbstractHookCommand
{
    public static function getChecks(): array
    {
        return [
            ApiBCBreakCheck::class,
            'Unit tests' => [ShellCommandCheck::class, 'bin/phpunit --exclude-group=functional 2>&1'],
            'Code style fixes' => [ShellCommandCheck::class, 'vendor/bin/php-cs-fixer fix --config .php_cs.dist 2>&1'],
            'Parallel PHP lint' => [ShellCommandCheck::class, 'vendor/bin/parallel-lint src/ 2>&1'],
        ];
    }

    protected function configure(): void
    {
        $this
            ->setName('pre-commit')
        ;
    }
}
