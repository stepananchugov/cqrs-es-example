<?php

declare(strict_types=1);

namespace App\Ui\GraphQL;

use App\Infrastructure\Share\MessageBus\HandledResult;
use App\Ui\GraphQL\Adapter\AdapterRegistry;
use Overblog\GraphQLBundle\Definition\Resolver\MutationInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MutationResolver implements MutationInterface
{
    private MessageBusInterface $commandBus;

    private AdapterRegistry $adapterRegistry;

    public function __construct(MessageBusInterface $commandBus, AdapterRegistry $inputAdapterRegistry)
    {
        $this->commandBus = $commandBus;
        $this->adapterRegistry = $inputAdapterRegistry;
    }

    /**
     * @return mixed
     */
    public function __invoke(object $input)
    {
        $transformed = $this->adapterRegistry->transform($input);

        if ($transformed instanceof \Generator) {
            $result = [];

            while ($transformed->valid()) {
                $envelope = $this->commandBus->dispatch($transformed->current());
                $itemResult = (new HandledResult($envelope))->getResult();

                if (\is_array($itemResult)) {
                    $result += $itemResult;
                }

                $transformed->next();
            }

            return $result;
        }

        $this->commandBus->dispatch($transformed);

        return ['success' => true];
    }
}
