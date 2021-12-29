<?php

declare(strict_types=1);

namespace App\Util\CodeQuality;

final class CheckResult
{
    private bool $successful;

    private array $messages;

    public function __construct(bool $successful, array $messages = [])
    {
        $this->successful = $successful;
        $this->messages = $messages;
    }

    public function isSuccessful(): bool
    {
        return $this->successful;
    }

    public function messages(): array
    {
        return $this->messages;
    }
}
