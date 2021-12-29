<?php

declare(strict_types=1);

namespace App\Util\Command;

use App\Util\CodeQuality as Check;

class PrePushCommand extends AbstractHookCommand
{
    public static function getChecks(): array
    {
        return [
            'Parallel PHP lint' => [Check\ShellCommandCheck::class, 'vendor/bin/parallel-lint src/ 2>&1'],
            'YAML lint' => [Check\ShellCommandCheck::class, 'bin/console lint:yaml ./config --parse-tags'],
            // 'Container lint' => [Check\ShellCommandCheck::class, 'php bin/console lint:container 2>&1', true],
            'Code style fixes' => [Check\ShellCommandCheck::class, 'vendor/bin/php-cs-fixer fix --config .php_cs.dist 2>&1'],
            'Unit tests' => [Check\ShellCommandCheck::class, 'bin/phpunit --exclude-group=functional 2>&1'],
            'Functional tests' => [Check\ShellCommandCheck::class, 'bin/phpunit --group=functional 2>&1'],
            'PHPStan' => [Check\ShellCommandCheck::class, 'vendor/bin/phpstan analyze src/ --level=max 2>&1'],
            'Doctrine schema validation' => [Check\ShellCommandCheck::class, 'bin/console doctrine:schema:validate --skip-sync'],
            // 'Deptrac' => [Check\ShellCommandCheck::class, 'vendor/bin/deptrac analyze --no-progress --no-banner 2>&1', true],
            'Normalize composer.json' => [Check\ShellCommandCheck::class, 'composer normalize --indent-size=4 --indent-style=space --dry-run 2>&1'],
            'Composer validation' => [Check\ShellCommandCheck::class, 'composer validate --strict'],
        ];
    }

    protected function configure(): void
    {
        $this->setName('pre-push');
    }
}
