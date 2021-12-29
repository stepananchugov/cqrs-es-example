<?php

declare(strict_types=1);

namespace App\Ui\GraphQL\Adapter;

use App\Application\Command\Catalog\ForeignEvent\SyncForeignPriceCommand;
use App\Ui\GraphQL\Adapter\Exception\InvalidArgumentException;
use App\Ui\GraphQL\Mapping\InputObject\SyncForeignPricesInput;

class SyncForeignPricesInputAdapter implements InputAdapterInterface
{
    public function getInputClass(): string
    {
        return SyncForeignPricesInput::class;
    }

    public function transform(object $input): SyncForeignPriceCommand
    {
        if (!$input instanceof SyncForeignPricesInput) {
            throw InvalidArgumentException::inputTypeMismatch($input, SyncForeignPricesInput::class);
        }

        return (new SyncForeignPriceCommand())->withForeignSeriesId($input->seriesId);
    }
}
