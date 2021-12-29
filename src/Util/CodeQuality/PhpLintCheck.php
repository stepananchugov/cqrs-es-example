<?php

declare(strict_types=1);

namespace App\Util\CodeQuality;

class PhpLintCheck implements CheckInterface
{
    public function getName(): string
    {
        return 'PHP linter';
    }

    public function execute(array $paths): CheckResult
    {
        $errors = [];

        foreach ($paths as $path) {
            exec('php -l '.$path.' 2>&1', $output, $return);
            $pathErrors = array_filter($output, static function (string $line): bool {
                return false === strpos($line, 'No syntax errors');
            });

            if (\count($pathErrors) > 0) {
                $errors[$path] = $pathErrors;
            }
        }

        return new CheckResult(0 === \count($errors), $errors);
    }
}
