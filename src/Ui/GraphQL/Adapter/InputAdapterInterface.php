<?php

declare(strict_types=1);

namespace App\Ui\GraphQL\Adapter;

interface InputAdapterInterface
{
    public function getInputClass(): string;

    public function transform(object $input): object;
}
