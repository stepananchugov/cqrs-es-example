<?php

declare(strict_types=1);

namespace App\Util\CodeQuality;

class PostInstallHook
{
    public static function postInstall(): void
    {
        $script = './bin/quality-checker pre-commit';
        exec('echo \''.$script.'\' > .git/hooks/pre-commit');
        exec('chmod +x .git/hooks/pre-commit');

        $script = './bin/quality-checker pre-push';
        exec('echo \''.$script.'\' > .git/hooks/pre-push');
        exec('chmod +x .git/hooks/pre-push');
    }
}
