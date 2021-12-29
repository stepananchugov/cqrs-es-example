<?php

declare(strict_types=1);

namespace App\Util\CodeQuality;

class ShellCommandCheck implements CheckInterface
{
    private string $command;

    public function __construct(string $command)
    {
        $this->command = $command;
    }

    public function getName(): string
    {
        throw new \RuntimeException('Shell command checks must be instantiated with the array parameter interface');
    }

    public function execute(array $paths): CheckResult
    {
        exec($this->command, $output, $returnCode);

        if (0 !== $returnCode) {
            return new CheckResult(false, $output);
        }

        return new CheckResult(true);
    }
}
